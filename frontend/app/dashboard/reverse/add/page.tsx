"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import { reverseIndentService } from "../../../../services/reverseIndent.service";
import { indentpoService } from "../../../../services/indentpo.service";
import AsyncSelect from "react-select/async";
import { format } from "date-fns";
import { Save, ArrowLeft, RefreshCw } from "lucide-react";
import Link from "next/link";

export default function AddReverseIndentPage() {
  const router = useRouter();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [nextId, setNextId] = useState("");
  
  const [formData, setFormData] = useState({
    reverse_id: "",
    issue_date: format(new Date(), 'yyyy-MM-dd'),
    contract_id: null as number | null,
    finishedproduct_id: null as number | null,
    machine_id: null as number | null,
    received_name: ""
  });

  const [contractOptions, setContractOptions] = useState([]);
  const [productOptions, setProductOptions] = useState([]);
  const [machineOptions, setMachineOptions] = useState([]);
  const [designSheetItems, setDesignSheetItems] = useState<any[]>([]);

  useEffect(() => {
    reverseIndentService.getNextReverseId().then(id => {
      setNextId(`I-${id}`);
      setFormData(prev => ({ ...prev, reverse_id: `I-${id}` }));
    }).catch(() => {});
  }, []);

  const loadContracts = (inputValue: string) => {
    return indentpoService.searchContracts(inputValue).then(data => 
      data.map((c: any) => ({ label: `${c.title} (${c.workorder})`, value: c.id }))
    );
  };

  const loadMachines = (inputValue: string) => {
    return indentpoService.searchMachines(inputValue).then(data =>
      data.map((m: any) => ({ label: m.machine_name, value: m.id }))
    );
  };

  const handleContractChange = (selected: any) => {
    const cid = selected?.value || null;
    setFormData(prev => ({ ...prev, contract_id: cid, finishedproduct_id: null }));
    setProductOptions([]);
    setDesignSheetItems([]);
    
    if (cid) {
      indentpoService.getContractProducts(cid).then(data => {
        setProductOptions(data.map((p: any) => ({ label: p.item_name, value: p.id })));
      });
    }
  };

  const handleProductChange = (selected: any) => {
    const pid = selected?.value || null;
    setFormData(prev => ({ ...prev, finishedproduct_id: pid }));
    
    if (formData.contract_id && pid) {
      indentpoService.getDesignSheetDetails(formData.contract_id, pid).then(data => {
        setDesignSheetItems(data.map((item: any) => ({
          item_id: item.item_id,
          item_name: item.item_name,
          uom: item.uom,
          quantity: ""
        })));
      });
    } else {
      setDesignSheetItems([]);
    }
  };

  const handleItemQtyChange = (index: number, val: string) => {
    setDesignSheetItems(prev => {
      const updated = [...prev];
      updated[index].quantity = val;
      return updated;
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.contract_id || !formData.finishedproduct_id || !formData.machine_id || !formData.received_name) {
      alert("Please fill all required fields (Contract, Product, Machine, Received By).");
      return;
    }

    const hasAnyQty = designSheetItems.some(i => i.quantity && parseFloat(i.quantity) > 0);
    if (!hasAnyQty) {
      alert("Please enter a received quantity for at least one item.");
      return;
    }

    setIsSubmitting(true);
    try {
      await reverseIndentService.saveReverseIndent({
        ...formData,
        items: designSheetItems
      });
      router.push("/dashboard/reverse");
    } catch (err) {
      alert("Failed to save Reverse Indent.");
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Add Reverse Indent</h1>
          <p className="text-sm text-slate-500 mt-1">Generate a new Reverse Indent to return materials.</p>
        </div>
        <Link href="/dashboard/reverse">
          <button className="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors border border-slate-300 bg-transparent hover:bg-slate-50 text-slate-700 px-4 py-2">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to List
          </button>
        </Link>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Reverse Id No. <span className="text-red-500">*</span></label>
              <input 
                type="text" 
                value={nextId}
                readOnly
                className="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-600 focus:outline-none"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Issued Date <span className="text-red-500">*</span></label>
              <input 
                type="date" 
                value={formData.issue_date}
                onChange={e => setFormData({ ...formData, issue_date: e.target.value })}
                required
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Contract Name <span className="text-red-500">*</span></label>
              <AsyncSelect 
                loadOptions={loadContracts}
                onChange={handleContractChange}
                placeholder="Search Contract..."
                className="text-sm"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Product <span className="text-red-500">*</span></label>
              <select
                className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                value={formData.finishedproduct_id || ""}
                onChange={(e) => handleProductChange({ value: e.target.value })}
                required
              >
                <option value="">-- Select Product --</option>
                {productOptions.map((p: any) => (
                  <option key={p.value} value={p.value}>{p.label}</option>
                ))}
              </select>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Machine Name <span className="text-red-500">*</span></label>
              <AsyncSelect 
                loadOptions={loadMachines}
                onChange={(selected: any) => setFormData({ ...formData, machine_id: selected?.value || null })}
                placeholder="Search Machine..."
                className="text-sm"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Received By <span className="text-red-500">*</span></label>
              <input 
                type="text" 
                value={formData.received_name}
                onChange={e => setFormData({ ...formData, received_name: e.target.value })}
                required
                placeholder="Enter Name"
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

          </div>

          {designSheetItems.length > 0 && (
            <div className="pt-6 border-t border-slate-200">
              <h3 className="font-semibold text-slate-800 mb-4">Items (from Design Sheet)</h3>
              <div className="border border-slate-200 rounded-lg overflow-hidden">
                <table className="w-full text-sm text-left">
                  <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                      <th className="px-4 py-3 font-medium w-[55%]">Raw Material</th>
                      <th className="px-4 py-3 font-medium w-[30%]">Received Qty</th>
                      <th className="px-4 py-3 font-medium w-[15%]">UOM</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-200">
                    {designSheetItems.map((item, index) => (
                      <tr key={index} className="hover:bg-slate-50">
                        <td className="px-4 py-3">
                          <input type="text" value={item.item_name} readOnly className="w-full px-2 py-1 bg-transparent border-none focus:outline-none" />
                        </td>
                        <td className="px-4 py-3">
                          <input 
                            type="number" 
                            step="any"
                            value={item.quantity}
                            onChange={(e) => handleItemQtyChange(index, e.target.value)}
                            className="w-full px-2 py-1 border border-slate-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                          />
                        </td>
                        <td className="px-4 py-3">
                          <input type="text" value={item.uom} readOnly className="w-full px-2 py-1 bg-transparent border-none focus:outline-none" />
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          <div className="flex justify-end pt-4">
            <button 
              type="submit" 
              disabled={isSubmitting}
              className="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white hover:bg-blue-700 px-6 py-2.5 disabled:opacity-50"
            >
              {isSubmitting ? (
                <>
                  <RefreshCw className="w-4 h-4 mr-2 animate-spin" />
                  Saving...
                </>
              ) : (
                <>
                  <Save className="w-4 h-4 mr-2" />
                  Save & Finalize
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
