'use client';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/contexts/AuthContext';
import { usePermission } from '@/contexts/PermissionContext';
import apiClient from '@/services/apiClient';

/** For controls PHP renders without an explicit in_array permission check.
 * Explicitly guarded Add/Edit/Delete buttons continue to use hasPermission.
 * Deny while the catalog is unavailable, matching the API's configured labels.
 */
export function useLegacyActionAccess() {
  const { user } = useAuth();
  const { hasPermission } = usePermission();
  const query = useQuery<{ configured: string[] }>({
    queryKey: ['legacy-action-labels', user?.id, user?.db], enabled: !!user,
    queryFn: async () => (await apiClient.get('/permission/action-labels')).data.data,
  });
  return (controller: string, action: string) => {
    if (!user) return false;
    if (Number(user.role_id) === 101) return true;
    const key = `legacy:admin/${controller}/${action}`.toLowerCase();
    return !!query.data && (!query.data.configured.includes(key) || hasPermission(key));
  };
}
