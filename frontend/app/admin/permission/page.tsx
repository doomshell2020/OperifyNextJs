'use client';

import React, { useState, useEffect } from 'react';
import { permissionService } from '../../../services/permission.service';
import toast from 'react-hot-toast';

export default function PermissionManager() {
  const [managers, setManagers] = useState<any[]>([]);
  const [roles, setRoles] = useState<any[]>([]);
  
  // Add Label state
  const [selectedManagerForLabel, setSelectedManagerForLabel] = useState('');
  const [labelName, setLabelName] = useState('');
  const [url, setUrl] = useState('');

  // Access state
  const [selectedRole, setSelectedRole] = useState('');
  const [groupedModules, setGroupedModules] = useState<Record<string, any[]>>({});
  const [permissions, setPermissions] = useState<Record<string, { is_permission: number }>>({});

  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchManagersAndRoles();
  }, []);

  useEffect(() => {
    if (selectedRole) {
      fetchAccess();
    } else {
      setGroupedModules({});
      setPermissions({});
    }
  }, [selectedRole]);

  const fetchManagersAndRoles = async () => {
    try {
      setLoading(true);
      const [managersData, rolesData] = await Promise.all([
        permissionService.getManagers(),
        permissionService.getRoles()
      ]);
      setManagers(managersData || []);
      setRoles(rolesData || []);
    } catch (err: any) {
      toast.error(err?.response?.data?.error?.message || 'Failed to fetch initial data');
    } finally {
      setLoading(false);
    }
  };

  const fetchAccess = async () => {
    if (!selectedRole) return;
    try {
      setLoading(true);
      const data = await permissionService.getAccess(Number(selectedRole));
      setGroupedModules(data || {});
      
      const newPerms: Record<string, { is_permission: number }> = {};
      Object.values(data || {}).forEach((modules: any) => {
        modules.forEach((mod: any) => {
          newPerms[mod.p_lable_id] = { is_permission: mod.checked ? 1 : 0 };
        });
      });
      setPermissions(newPerms);
    } catch (err: any) {
      toast.error(err?.response?.data?.error?.message || 'Failed to fetch permissions');
    } finally {
      setLoading(false);
    }
  };

  const handleAddLabel = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedManagerForLabel || !labelName || !url) {
      toast.error('Please fill all fields');
      return;
    }
    try {
      await permissionService.addLabel({
        manager_id: Number(selectedManagerForLabel),
        label_name: labelName,
        url
      });
      toast.success('Url added successfully.');
      setLabelName('');
      setUrl('');
      if (selectedRole) fetchAccess(); // refresh list if a role is selected
    } catch (err: any) {
      toast.error(err?.response?.data?.error?.message || 'Failed to add URL');
    }
  };

  const handleGrantPermission = async () => {
    if (!selectedRole) {
      toast.error('Please select a role first');
      return;
    }
    try {
      await permissionService.updateAccess(Number(selectedRole), permissions);
      toast.success('Permissions updated successfully.');
      fetchAccess();
    } catch (err: any) {
      toast.error(err?.response?.data?.error?.message || 'Failed to update permissions');
    }
  };

  const handleAddManager = async () => {
    const name = window.prompt('Enter Manager Name:');
    if (!name) return;
    try {
      await permissionService.addManager(name);
      toast.success('Manager added successfully');
      fetchManagersAndRoles();
    } catch (err: any) {
      toast.error(err?.response?.data?.error?.message || 'Failed to add manager');
    }
  };

  const togglePermission = (p_lable_id: string, checked: boolean) => {
    setPermissions(prev => ({
      ...prev,
      [p_lable_id]: { is_permission: checked ? 1 : 0 }
    }));
  };

  return (
    <div className="p-4 w-full">
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-xl font-normal text-gray-700">Permission Manager</h1>
        <div className="text-sm font-bold text-gray-700 flex items-center gap-1">
          <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
          Home &gt; Permission Manager
        </div>
      </div>

      <div className="bg-white border border-gray-200 shadow-sm p-4 w-full min-h-[500px]">
        {/* First Section */}
        <form onSubmit={handleAddLabel} className="flex flex-wrap items-end gap-6 mb-6">
          <div className="w-[30%]">
            <label className="block text-[13px] font-bold text-gray-700 mb-1">
              Manager Name<span className="text-red-500">*</span>
            </label>
            <select 
              value={selectedManagerForLabel}
              onChange={e => setSelectedManagerForLabel(e.target.value)}
              className="w-full border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400"
            >
              <option value="">--Select Manager--</option>
              {managers.map(m => (
                <option key={m.id} value={m.id}>{m.name}</option>
              ))}
            </select>
          </div>
          <div className="w-[30%]">
            <label className="block text-[13px] font-bold text-gray-700 mb-1">
              Label Name<span className="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              value={labelName}
              onChange={e => setLabelName(e.target.value)}
              className="w-full border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400"
              placeholder="Enter Label Name"
            />
          </div>
          <div className="w-[25%]">
            <label className="block text-[13px] font-bold text-gray-700 mb-1">
              URL<span className="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              value={url}
              onChange={e => setUrl(e.target.value)}
              className="w-full border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400"
              placeholder="Enter URL"
            />
          </div>
          <div>
            <button type="submit" className="bg-[#00c0ef] text-white px-5 py-1.5 text-sm hover:bg-[#00acd6]">
              Submit
            </button>
          </div>
        </form>

        <hr className="border-gray-200 my-6" />

        {/* Second Section */}
        <div className="flex justify-between items-end mb-6">
          <div className="w-[30%]">
            <label className="block text-[13px] font-bold text-gray-700 mb-1">
              User Role<span className="text-red-500">*</span>
            </label>
            <select 
              value={selectedRole}
              onChange={e => setSelectedRole(e.target.value)}
              className="w-full border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400"
            >
              <option value="">--Select--</option>
              {roles.map(r => (
                <option key={r.id} value={r.id}>{r.name}</option>
              ))}
            </select>
          </div>
          <div className="flex items-center gap-3">
            <button onClick={handleGrantPermission} className="bg-[#00c0ef] text-white px-5 py-1.5 text-sm hover:bg-[#00acd6]">
              Grant Permission
            </button>
            <button onClick={handleAddManager} className="bg-[#a00000] font-bold text-white px-4 py-1.5 text-sm hover:bg-[#800000]">
              +Add Manager
            </button>
          </div>
        </div>

        {/* Third Section: Modules list */}
        <div className="w-full border border-[#333]">
          <div className="bg-[#333] text-white text-[13px] font-bold p-2 uppercase tracking-wide">
            MANAGERS NAME
          </div>
          <div className="p-4 bg-white min-h-[100px]">
            {loading ? (
              <p className="text-sm text-gray-500">Loading...</p>
            ) : selectedRole && Object.keys(groupedModules).length > 0 ? (
              <div className="flex flex-col gap-0 border-t border-l border-r border-gray-200">
                {Object.keys(groupedModules)
                  .sort((a, b) => {
                    const nameA = groupedModules[a][0]?.manager_name || '';
                    const nameB = groupedModules[b][0]?.manager_name || '';
                    return nameA.localeCompare(nameB);
                  })
                  .map(managerId => {
                  const modules = groupedModules[managerId];
                  const managerName = modules[0]?.manager_name || 'Unknown Manager';
                  
                  return (
                    <div key={managerId} className="flex border-b border-gray-200">
                      <div className="w-1/3 p-3 bg-[#f9f9f9] border-r border-gray-200 font-bold text-gray-700 text-[13px]">
                        {managerName}
                      </div>
                      <div className="w-2/3 p-3 flex flex-wrap gap-4 items-center">
                        {modules
                          .sort((a: any, b: any) => a.label_name.localeCompare(b.label_name))
                          .map((mod: any) => (
                          <label key={mod.p_lable_id} className="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1">
                            <input 
                              type="checkbox"
                              checked={permissions[mod.p_lable_id]?.is_permission === 1}
                              onChange={(e) => togglePermission(mod.p_lable_id, e.target.checked)}
                              className="w-[14px] h-[14px] cursor-pointer"
                            />
                            <span className="text-[13px] text-gray-700 select-none">{mod.label_name}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  );
                })}
              </div>
            ) : (
              selectedRole ? (
                <p className="text-sm text-gray-500">No permissions found for this role. Add labels first.</p>
              ) : null
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
