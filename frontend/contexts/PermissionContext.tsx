"use client";

import React, { createContext, useContext, ReactNode } from 'react';
import { useAuth } from './AuthContext';

interface PermissionContextType {
  hasPermission: (permissionKey: string) => boolean;
  permissions: string[];
}

const PermissionContext = createContext<PermissionContextType | undefined>(undefined);

export const PermissionProvider = ({ children }: { children: ReactNode }) => {
  const { user } = useAuth();
  
  // Extract permissions from user object if it exists
  const permissions = user?.permissions || [];

  const hasPermission = (permissionKey: string) => {
    // If role is superadmin, bypass checks
    if (user?.role_id == 101) return true;
    
    return permissions.includes(permissionKey);
  };

  return (
    <PermissionContext.Provider value={{ hasPermission, permissions }}>
      {children}
    </PermissionContext.Provider>
  );
};

export const usePermission = () => {
  const context = useContext(PermissionContext);
  if (context === undefined) {
    throw new Error('usePermission must be used within a PermissionProvider');
  }
  return context;
};
