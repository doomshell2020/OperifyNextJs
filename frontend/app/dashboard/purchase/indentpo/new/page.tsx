"use client";

import { useState, useEffect, useRef } from "react";
import { useRouter } from "next/navigation";
import { indentpoService, IndentpoItem } from "../../../../../services/indentpo.service";
import { ArrowLeft, Save, Loader2, Search, CheckCircle2 } from "lucide-react";
import Link from "next/link";
import { format } from "date-fns";
import { formatQty } from "@/utils/formatters";

export default function CreateIndentPoPage() {
  const router = useRouter();
  
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState("");
  
  // Form fields
  const [indentId, setIndentId] = useState("");
  const [issueDate, setIssueDate] = useState(format(new Date(), "yyyy-MM-dd"));
  const [issuedName, setIssuedName] = useState("");
  
  // Selections
  const [selectedContract, setSelectedContract] = useState<{id: string, title: string, workorder: string} | null>(null);
  const [selectedMachine, setSelectedMachine] = useState<{id: string, machine_name: string} | null>(null);
  const [selectedProduct, setSelectedProduct] = useState<string>("");
  
  // Data
  const [products, setProducts] = useState<any[]>([]);
  const [gridItems, setGridItems] = useState<IndentpoItem[]>([]);
  
  // Autocomplete states
  const [contractQuery, setContractQuery] = useState("");
  const [contractResults, setContractResults] = useState<any[]>([]);
  const [showContractDropdown, setShowContractDropdown] = useState(false);
  
  const [machineQuery, setMachineQuery] = useState("");
  const [machineResults, setMachineResults] = useState<any[]>([]);
  const [showMachineDropdown, setShowMachineDropdown] = useState(false);

  // Refs for click outside
  const contractRef = useRef<HTMLDivElement>(null);
  const machineRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    // Get next indent ID
    indentpoService.getNextIndentId().then(data => {
      setIndentId(data.next_id?.toString() || "");
    }).catch(console.error);
    
    // Click outside listener
    const handleClickOutside = (e: MouseEvent) => {
      if (contractRef.current && !contractRef.current.contains(e.target as Node)) {
        setShowContractDropdown(false);
      }
      if (machineRef.current && !machineRef.current.contains(e.target as Node)) {
        setShowMachineDropdown(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // Contract search
  useEffect(() => {
    if (contractQuery.length >= 2 && !selectedContract) {
      indentpoService.searchContracts(contractQuery).then(res => {
        setContractResults(res);
        setShowContractDropdown(true);
      });
    } else {
      setShowContractDropdown(false);
    }
  }, [contractQuery, selectedContract]);

  // Machine search
  useEffect(() => {
    if (machineQuery.length >= 2 && !selectedMachine) {
      indentpoService.searchMachines(machineQuery).then(res => {
        setMachineResults(res);
        setShowMachineDropdown(true);
      });
    } else {
      setShowMachineDropdown(false);
    }
  }, [machineQuery, selectedMachine]);

  // Load products when contract is selected
  useEffect(() => {
    if (selectedContract) {
      indentpoService.getContractProducts(selectedContract.id.toString()).then(res => {
        setProducts(res);
        setSelectedProduct("");
        setGridItems([]);
      });
    } else {
      setProducts([]);
      setSelectedProduct("");
      setGridItems([]);
    }
  }, [selectedContract]);

  // Load design sheet when product is selected
  useEffect(() => {
    if (selectedContract && selectedProduct) {
      indentpoService.getDesignSheetDetails(selectedContract.id.toString(), selectedProduct).then(res => {
        // initialize issue_qty
        const items = res.map((item: any) => ({
          ...item,
          issue_qty: 0
        }));
        setGridItems(items);
      });
    } else {
      setGridItems([]);
    }
  }, [selectedProduct, selectedContract]);

  const handleContractSelect = (contract: any) => {
    setSelectedContract(contract);
    setContractQuery(`${contract.title} (${contract.workorder})`);
    setShowContractDropdown(false);
  };

  const handleMachineSelect = (machine: any) => {
    setSelectedMachine(machine);
    setMachineQuery(machine.machine_name);
    setShowMachineDropdown(false);
  };

  const handleGridQtyChange = (index: number, value: string) => {
    const newItems = [...gridItems];
    let qty = parseFloat(value);
    
    if (isNaN(qty) || qty < 0) qty = 0;
    
    const maxQty = Math.min(newItems[index].pending_qty, newItems[index].inhand_stock || 0);
    
    if (qty > maxQty) {
      qty = maxQty;
    }
    
    newItems[index].issue_qty = qty;
    setGridItems(newItems);
  };

  const handleGroupItemSelect = (groupItem: IndentpoItem, selectedItemId: string) => {
    if (!selectedItemId) return;
    
    const selectedItem = groupItem.group_items?.find(i => i.id.toString() === selectedItemId);
    if (!selectedItem) return;
    
    const newItem: IndentpoItem = {
      item_id: selectedItem.id,
      raw_material_name: selectedItem.item_name,
      item_name: selectedItem.item_name,
      unit_name: groupItem.unit_name,
      design_qty: groupItem.design_qty,
      issued_qty: groupItem.issued_qty,
      pending_qty: groupItem.pending_qty,
      inhand_stock: selectedItem.inhand_stock,
      issue_qty: 0,
      is_group: 0,
      is_added_from_group: true
    } as any;
    
    setGridItems([...gridItems, newItem]);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (!selectedContract) return setError("Please select a contract");
    if (!selectedProduct) return setError("Please select a product");
    if (!selectedMachine) return setError("Please select a machine");
    if (!issuedName.trim()) return setError("Please enter who it is issued to");
    if (!issueDate) return setError("Please select an issue date");

    const validItems = gridItems.filter(item => item.issue_qty && item.issue_qty > 0);
    if (validItems.length === 0) {
      return setError("Please issue at least one item with a quantity greater than 0");
    }

    setIsSubmitting(true);
    try {
      await indentpoService.saveIndentpo({
        indent_id: indentId,
        contract_id: selectedContract.id,
        finishedproduct_id: selectedProduct,
        machine_id: selectedMachine.id,
        issued_name: issuedName,
        issue_date: issueDate,
        items: validItems
      });
      
      router.push("/dashboard/purchase/indentpo");
    } catch (err: any) {
      setError(err.response?.data?.message || err.message || "Failed to save Indent PO");
      setIsSubmitting(false);
    }
  };

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link href="/dashboard/purchase/indentpo">
            <button className="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-500">
              <ArrowLeft className="w-5 h-5" />
            </button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Create Indent PO</h1>
            <p className="text-sm text-slate-500 mt-1">Issue stock against a contract design sheet.</p>
          </div>
        </div>
        <button
          onClick={handleSubmit}
          disabled={isSubmitting}
          className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none"
        >
          {isSubmitting ? (
            <Loader2 className="w-4 h-4 mr-2 animate-spin" />
          ) : (
            <Save className="w-4 h-4 mr-2" />
          )}
          Save & Finalize
        </button>
      </div>

      {error && (
        <div className="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 text-sm">
          {error}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
          <h2 className="text-lg font-semibold text-slate-800 border-b pb-3">Indent Details</h2>
          
          <div className="grid grid-cols-2 gap-5">
            <div className="space-y-1.5">
              <label className="text-sm font-medium text-slate-700">Indent No</label>
              <input
                type="text"
                readOnly
                value={indentId}
                className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed"
              />
            </div>
            <div className="space-y-1.5">
              <label className="text-sm font-medium text-slate-700">Issue Date *</label>
              <input
                type="date"
                value={issueDate}
                onChange={(e) => setIssueDate(e.target.value)}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                required
              />
            </div>
          </div>

          <div className="space-y-1.5" ref={contractRef}>
            <label className="text-sm font-medium text-slate-700">Contract *</label>
            <div className="relative">
              <input
                type="text"
                placeholder="Search contract by name or workorder..."
                value={contractQuery}
                onChange={(e) => {
                  setContractQuery(e.target.value);
                  setSelectedContract(null);
                }}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-10"
              />
              <Search className="w-4 h-4 text-slate-400 absolute right-3 top-3" />
              
              {showContractDropdown && contractResults.length > 0 && (
                <div className="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                  {contractResults.map((c) => (
                    <div
                      key={c.id}
                      onClick={() => handleContractSelect(c)}
                      className="px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0"
                    >
                      <div className="font-medium text-slate-800">{c.title}</div>
                      <div className="text-xs text-slate-500">{c.workorder}</div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
          <h2 className="text-lg font-semibold text-slate-800 border-b pb-3">Issue Information</h2>
          
          <div className="space-y-1.5">
            <label className="text-sm font-medium text-slate-700">Finished Product *</label>
            <select
              value={selectedProduct}
              onChange={(e) => setSelectedProduct(e.target.value)}
              disabled={!selectedContract || products.length === 0}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none disabled:bg-slate-50 disabled:text-slate-500"
            >
              <option value="">-- Select Product --</option>
              {products.map((p) => (
                <option key={p.product_id} value={p.product_id}>
                  {p.item_name}
                </option>
              ))}
            </select>
          </div>

          <div className="space-y-1.5" ref={machineRef}>
            <label className="text-sm font-medium text-slate-700">Machine Name *</label>
            <div className="relative">
              <input
                type="text"
                placeholder="Search machine..."
                value={machineQuery}
                onChange={(e) => {
                  setMachineQuery(e.target.value);
                  setSelectedMachine(null);
                }}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-10"
              />
              <Search className="w-4 h-4 text-slate-400 absolute right-3 top-3" />
              
              {showMachineDropdown && machineResults.length > 0 && (
                <div className="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                  {machineResults.map((m) => (
                    <div
                      key={m.id}
                      onClick={() => handleMachineSelect(m)}
                      className="px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0"
                    >
                      <div className="font-medium text-slate-800">{m.machine_name}</div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-sm font-medium text-slate-700">Issued By / To *</label>
            <input
              type="text"
              placeholder="Enter name"
              value={issuedName}
              onChange={(e) => setIssuedName(e.target.value)}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
            />
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div className="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 className="text-lg font-semibold text-slate-800">Raw Materials (Design Sheet)</h2>
          {selectedProduct && gridItems.length > 0 && (
            <div className="flex items-center text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-200">
              <CheckCircle2 className="w-4 h-4 mr-1.5" />
              Design Sheet Loaded
            </div>
          )}
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider">
              <tr>
                <th className="px-6 py-4 font-semibold">Raw Material</th>
                <th className="px-6 py-4 font-semibold text-center">Req Qty(As Per Design Sheet)</th>
                <th className="px-6 py-4 font-semibold text-center text-blue-600">Pending Qty</th>
                <th className="px-6 py-4 font-semibold text-center text-green-600">Inhand Stock</th>
                <th className="px-6 py-4 font-semibold text-right">Issue Qty</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {gridItems.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-12 text-slate-500">
                    {!selectedContract || !selectedProduct ? (
                      <p>Select a Contract and Product to load the design sheet.</p>
                    ) : (
                      <p>No materials found in design sheet for this product.</p>
                    )}
                  </td>
                </tr>
              ) : (
                gridItems.map((item, idx) => (
                  <tr key={idx} className={`hover:bg-slate-50 ${Number(item.is_group) === 1 ? 'bg-amber-50/40' : ''}`}>
                    <td className="px-6 py-4">
                      {Number(item.is_group) === 1 ? (
                        <select
                          className="w-full px-2 py-1.5 border border-amber-300 bg-white rounded text-sm font-medium text-amber-900 outline-none focus:ring-2 focus:ring-amber-500"
                          onChange={(e) => {
                            handleGroupItemSelect(item, e.target.value);
                            e.target.value = "";
                          }}
                        >
                          <option value="">{item.item_name || item.raw_material_name}</option>
                          {item.group_items?.map(g => (
                            <option key={g.id} value={g.id}>{g.item_name}</option>
                          ))}
                        </select>
                      ) : (
                        <>
                          <div className="font-medium text-slate-900">{item.item_name || item.raw_material_name}</div>
                          <div className="text-xs text-slate-500">{item.unit_name}</div>
                        </>
                      )}
                    </td>
                    <td className="px-6 py-4 text-center text-slate-700 font-medium">
                      {item.design_qty !== undefined ? formatQty(item.design_qty) : 0}
                    </td>
                    <td className="px-6 py-4 text-center font-bold text-blue-600">
                      {item.pending_qty !== undefined ? formatQty(item.pending_qty) : 0}
                    </td>
                    <td className="px-6 py-4 text-center font-bold text-green-600">
                      {Number(item.is_group) === 1 ? "--" : (item.inhand_stock !== undefined ? formatQty(item.inhand_stock) : 0)}
                    </td>
                    <td className="px-6 py-4 text-right">
                      {Number(item.is_group) === 1 ? (
                        <span className="text-slate-400">--</span>
                      ) : (
                        <input
                          type="number"
                          min="0"
                          step="0.01"
                          value={item.issue_qty === 0 ? "" : item.issue_qty}
                          onChange={(e) => handleGridQtyChange(idx, e.target.value)}
                          className={`w-32 px-3 py-1.5 border rounded-lg text-right outline-none transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none ${
                            item.pending_qty === 0 || (item.inhand_stock || 0) === 0
                              ? "bg-slate-100 cursor-not-allowed border-slate-200 text-slate-400" 
                              : "border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          }`}
                          placeholder="0"
                          disabled={item.pending_qty === 0 || (item.inhand_stock || 0) === 0}
                        />
                      )}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
