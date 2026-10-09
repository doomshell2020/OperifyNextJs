'use client';
import { usePermission } from '@/contexts/PermissionContext';
/** PermissionHelper::permissioncheck grants actions only through saved URLs. */
export function useLegacyActionAccess() {
  const { hasPermission } = usePermission();
  return (controller: string, action: string) =>
    hasPermission(`legacy:admin/${controller}/${action}`.toLowerCase());
}
