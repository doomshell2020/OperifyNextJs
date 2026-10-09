import axios, { InternalAxiosRequestConfig } from 'axios';
import { API_URL } from './apiConfig';

export { API_URL } from './apiConfig';

const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

type SessionRequest = InternalAxiosRequestConfig & { _retry?: boolean; _session?: string | null };
let pendingRefresh: { token: string; promise: Promise<string> } | null = null;

const sessionChanged = (request: SessionRequest) =>
  typeof window !== 'undefined' && request._session !== localStorage.getItem('refreshToken');
const cancelledSession = () => new axios.CanceledError('The active company session changed');

// Request interceptor to dynamically inject the access token
apiClient.interceptors.request.use(
  (config) => {
    if (typeof window !== 'undefined') {
      (config as SessionRequest)._session = localStorage.getItem('refreshToken');
      const token = localStorage.getItem('accessToken');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle token expiry / 401 errors
apiClient.interceptors.response.use(
  (response) => {
    if (sessionChanged(response.config as SessionRequest)) return Promise.reject(cancelledSession());
    return response;
  },
  async (error) => {
    const originalRequest = error.config as SessionRequest | undefined;
    if (!originalRequest) return Promise.reject(error);
    if (sessionChanged(originalRequest)) return Promise.reject(cancelledSession());
    
    // If unauthorized error occurs and request has not been retried
    if (error.response?.status === 401 && !originalRequest._retry && !['/auth/login', '/auth/refresh'].includes(originalRequest.url || '')) {
      originalRequest._retry = true;
      
      try {
        const refreshToken = localStorage.getItem('refreshToken');
        if (!refreshToken) {
          throw new Error('No refresh token available');
        }
        
        // Share refresh work across simultaneous expired dashboard requests.
        // A refresh for the previous company must never replace switched tokens.
        if (!pendingRefresh || pendingRefresh.token !== refreshToken) {
          const promise = axios.post(`${API_URL}/auth/refresh`, { refreshToken }).then(res => {
            if (localStorage.getItem('refreshToken') !== refreshToken) throw cancelledSession();
            const token = res.data.data.accessToken as string;
            localStorage.setItem('accessToken', token);
            return token;
          });
          pendingRefresh = { token: refreshToken, promise };
          void promise.finally(() => {
            if (pendingRefresh?.promise === promise) pendingRefresh = null;
          }).catch(() => {});
        }
        const accessToken = await pendingRefresh.promise;
        if (sessionChanged(originalRequest)) throw cancelledSession();
        originalRequest.headers.Authorization = `Bearer ${accessToken}`;
        
        return apiClient(originalRequest);
      } catch (refreshError) {
        if (sessionChanged(originalRequest) || axios.isCancel(refreshError)) return Promise.reject(cancelledSession());
        // Clear storage and redirect to login if refresh fails
        if (typeof window !== 'undefined') {
          localStorage.removeItem('accessToken');
          localStorage.removeItem('refreshToken');
          window.location.href = '/login';
        }
        return Promise.reject(refreshError);
      }
    }
    
    return Promise.reject(error);
  }
);

export default apiClient;
