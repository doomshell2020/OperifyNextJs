'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import apiClient from '../services/apiClient';

interface Company {
  id: number;
  school_name: string;
  school_database: string;
}

interface User {
  id: number;
  user_name: string;
  email: string;
  mobile: string;
  role_id: number;
  db: string;
  companies?: Company[];
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (mobile: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  switchCompany: (newDb: string) => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const router = useRouter();

  // Load user on mount
  useEffect(() => {
    const bootstrapAuth = async () => {
      const token = localStorage.getItem('accessToken');
      if (!token) {
        setLoading(false);
        return;
      }

      try {
        const response = await apiClient.get('/auth/me');
        if (response.data.success) {
          setUser(response.data.data.user);
        }
      } catch (err) {
        console.error('Session restoration failed:', err);
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
      } finally {
        setLoading(false);
      }
    };

    bootstrapAuth();
  }, []);

  const login = async (mobile: string, password: string) => {
    setLoading(true);
    try {
      const response = await apiClient.post('/auth/login', { mobile, password });
      if (response.data.success) {
        const { user, accessToken, refreshToken } = response.data.data;
        localStorage.setItem('accessToken', accessToken);
        localStorage.setItem('refreshToken', refreshToken);
        setUser(user);
        
        // Redirect to admin dashboards base on role ID
        router.push('/dashboard');
      }
    } catch (err: any) {
      setUser(null);
      throw err.response?.data?.error?.message || 'Login failed. Please check your credentials.';
    } finally {
      setLoading(false);
    }
  };

  const switchCompany = async (newDb: string) => {
    setLoading(true);
    try {
      const response = await apiClient.post('/auth/switch-company', { newDb });
      if (response.data.success) {
        const { user, accessToken, refreshToken } = response.data.data;
        localStorage.setItem('accessToken', accessToken);
        localStorage.setItem('refreshToken', refreshToken);
        setUser(user);
        // Refresh page so everything re-fetches using new DB
        window.location.reload();
      }
    } catch (err: any) {
      console.error('Company switch failed', err);
      throw err.response?.data?.error?.message || 'Company switch failed.';
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    setLoading(true);
    try {
      await apiClient.post('/auth/logout');
    } catch (err) {
      console.error('Logout request failed:', err);
    } finally {
      localStorage.removeItem('accessToken');
      localStorage.removeItem('refreshToken');
      setUser(null);
      setLoading(false);
      router.push('/login');
    }
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, switchCompany }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
