'use client';
import { LegacyAutocompleteInput } from '@/components/ui/LegacyAutocompleteInput';

import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import React, { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { useMutation, useQuery } from '@tanstack/react-query';
import { Briefcase, Loader, Plus, Trash2 } from 'lucide-react';
import toast from 'react-hot-toast';
import contractService, { CreateContractPayload, FinishedProductInput } from '../../../../../services/contract.service';
import { DatePicker } from '../../../../../components/ui/DatePicker';

type ProductRow = FinishedProductInput & { itemName: string; existing?: boolean };

function toDateInput(value?: string) {
  if (!value) return '';
  return value.split('T')[0];
}

export default function EditContractPage() {
  const router = useRouter();
  const { id } = useParams() as { id: string };

  const { data: formData, isLoading: formLoading } = useQuery({
    queryKey: ['contract-form-data'],
    queryFn: () => contractService.getFormData(),
    staleTime: 10 * 60 * 1000
  });

  const { data: details, isLoading: detailsLoading } = useQuery({
    queryKey: ['contract-details', id],
    queryFn: () => contractService.getDetails(id),
    enabled: !!id
  });

  const [supplierName, setSupplierName] = useState('');
  const [title, setTitle] = useState('');
  const [workorder, setWorkorder] = useState('');
  const [cost, setCost] = useState('');
  const [issuedate, setIssuedate] = useState('');
  const [operationCost, setOperationCost] = useState('');
  const [labourCost, setLabourCost] = useState('');
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [description, setDescription] = useState('');
  const [products, setProducts] = useState<ProductRow[]>([
    { product_id: '', quantity: '', price: '', itemName: '' }
  ]);

  useEffect(() => {
    if (!details) return;
    setSupplierName(details.contract.vendor_name || '');
    setTitle(details.contract.title || '');
    setWorkorder(details.contract.workorder || '');
    setCost(details.contract.cost || '');
    setIssuedate(toDateInput(details.contract.issuedate));
    setOperationCost(details.contract.operation_cost || '');
    setLabourCost(details.contract.labour_cost || '');
    setStartDate(toDateInput(details.contract.contract_start_date));
    setEndDate(toDateInput(details.contract.contract_end_date));
    setDescription(details.contract.description || '');
    setProducts(
      details.items?.length
        ? details.items.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity || '',
            price: item.price || '',
            itemName: item.item_name || '',
            existing:true
          }))
        : [{ product_id: '', quantity: '', price: '', itemName: '' }]
    );
  }, [details]);

  const updateMutation = useMutation({
    mutationFn: (payload: CreateContractPayload) => contractService.updateContract(id, payload),
    onSuccess: () => {
      toast.success('Contract successfully updated.');
      router.push('/dashboard/contracts');
    },
    onError: (err: any) => {
      toast.error(err?.response?.data?.message || 'Failed to update contract');
    }
  });

  const handleProductChange = (index: number, field: keyof FinishedProductInput | 'itemName', value: string) => {
    const updated = [...products];
    if (field === 'itemName') {
      updated[index].itemName = value;
      const matchedItem = formData?.items.find(item => item.name === value);
      if (matchedItem) {
        const isDuplicate = updated.some((product, idx) => idx !== index && String(product.product_id) === String(matchedItem.id));
        if (isDuplicate) {
          toast.error('You have already added this finished product!');
          updated[index].itemName = '';
          updated[index].product_id = '';
        } else {
          updated[index].product_id = matchedItem.id;
        }
      } else {
        updated[index].product_id = '';
      }
    } else {
      updated[index][field] = value;
    }
    setProducts(updated);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const matchedSupplier = formData?.vendors.find(v => v.name === supplierName);
    if (!matchedSupplier) {
      toast.error('Please select a valid Supplier from the list');
      return;
    }

    if (!title || !workorder || !cost || !operationCost || !labourCost || !issuedate || !startDate || !endDate) {
      toast.error('Please fill all required contract fields');
      return;
    }

    const validProducts = products.filter(product => product.product_id && product.quantity);
    if (validProducts.length === 0) {
      toast.error('Please add at least one finished product with a valid name and quantity');
      return;
    }

    const productIds = validProducts.map(product => String(product.product_id));
    if (new Set(productIds).size !== productIds.length) {
      toast.error('You cannot add the same finished product multiple times');
      return;
    }

    updateMutation.mutate({
      supplier_id: matchedSupplier.id,
      title,
      workorder,
      cost,
      issuedate,
      operation_cost: operationCost,
      labour_cost: labourCost,
      contract_start_date: startDate,
      contract_end_date: endDate,
      description,
      finished_products: validProducts.map(product => ({
        product_id: product.product_id,
        quantity: product.quantity,
        price: product.price
      }))
    });
  };

  if (formLoading || detailsLoading) {
    return (
      <div className="flex h-64 items-center justify-center">
        <Loader className="w-8 h-8 animate-spin text-cyan-600" />
      </div>
    );
  }

  return (
    <main className="legacy-form-page max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <LegacyPageHeader title="Edit Contract"/>


      <div className="bg-white border-t-[3px] border-t-[#00bcd4] border-x border-b border-slate-200 shadow-sm overflow-hidden">
        <div className="bg-white px-4 py-3 border-b border-slate-200 flex items-center gap-2 font-bold text-slate-700 text-sm">
          <Plus className="w-4 h-4 bg-slate-800 text-white p-0.5 rounded-sm" />
          Edit Contract
        </div>

        <form onSubmit={handleSubmit} className="legacy-form legacy-form-contract p-6 space-y-6 bg-white">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Supplier Name <span className="text-red-500">*</span></label>
              <LegacyAutocompleteInput type="text" list="suppliers-list" value={supplierName} onChange={e => setSupplierName(e.target.value)} placeholder="Enter Supplier Name" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
              <datalist id="suppliers-list">
                {formData?.vendors.map(v => <option key={v.id} value={v.name} />)}
              </datalist>
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Title <span className="text-red-500">*</span></label>
              <input type="text" value={title} onChange={e => setTitle(e.target.value)} placeholder="Enter Title" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Work Order <span className="text-red-500">*</span></label>
              <input type="text" value={workorder} onChange={e => setWorkorder(e.target.value)} placeholder="Enter Work Order" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Issue Date <span className="text-red-500">*</span></label>
              <DatePicker dateFormat="dd-MM-yyyy" value={issuedate} onChange={e => setIssuedate(e.target.value)} className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Cost <span className="text-red-500">*</span></label>
              <input type="text" value={cost} onChange={e => setCost(e.target.value)} placeholder="Enter Cost" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Operational Cost <span className="text-red-500">*</span></label>
              <input type="text" value={operationCost} onChange={e => setOperationCost(e.target.value)} placeholder="Enter Operational Cost" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Labour Cost <span className="text-red-500">*</span></label>
              <input type="text" value={labourCost} onChange={e => setLabourCost(e.target.value)} placeholder="Enter Labour Cost" className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">Start Date <span className="text-red-500">*</span></label>
              <DatePicker dateFormat="dd-MM-yyyy" value={startDate} onChange={e => setStartDate(e.target.value)} className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
            <div>
              <label className="text-[11px] font-bold text-slate-600 block mb-1">End Date <span className="text-red-500">*</span></label>
              <DatePicker dateFormat="dd-MM-yyyy" value={endDate} onChange={e => setEndDate(e.target.value)} className="w-full px-3 py-1.5 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" required />
            </div>
          </div>

          <div className="overflow-x-auto border border-slate-300 mt-4">
            <table className="w-full text-left border-collapse text-sm">
              <thead>
                <tr className="bg-[#333] text-white">
                  <th className="px-4 py-2 font-semibold">Finished Product</th>
                  <th className="px-4 py-2 font-semibold w-40">Qty</th>
                  <th className="px-4 py-2 font-semibold w-40">Price</th>
                  <th className="px-4 py-2 font-semibold w-24 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                {products.map((product, idx) => (
                  <tr key={idx} className="border-b border-slate-200">
                    <td className="px-2 py-2">
                      <LegacyAutocompleteInput type="text" list="items-list" placeholder="Enter Item Name" value={product.itemName} readOnly={product.existing} onChange={e => handleProductChange(idx, 'itemName', e.target.value)} className="w-full px-2 py-1.5 border border-yellow-400 focus:border-cyan-500 outline-none text-sm" />
                    </td>
                    <td className="px-2 py-2">
                      <input type="number" placeholder="Qty" value={product.quantity} readOnly={product.existing} onChange={e => handleProductChange(idx, 'quantity', e.target.value)} className="w-full px-2 py-1.5 border border-slate-300 focus:border-cyan-500 outline-none text-sm" />
                    </td>
                    <td className="px-2 py-2">
                      <input type="number" placeholder="Price" value={product.price} readOnly={product.existing} onChange={e => handleProductChange(idx, 'price', e.target.value)} className="w-full px-2 py-1.5 border border-slate-300 focus:border-cyan-500 outline-none text-sm" />
                    </td>
                    <td className="px-2 py-2 text-center">
                      <button type="button" onClick={() => setProducts(products.filter((_, i) => i !== idx))} disabled={products.length === 1} className="p-1.5 text-rose-500 hover:bg-rose-50 rounded disabled:opacity-30 transition cursor-pointer">
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
            <datalist id="items-list">
              {formData?.items.map(item => <option key={item.id} value={item.name} />)}
            </datalist>
            <div className="px-4 py-2 bg-slate-50 border-t border-slate-300">
              <button type="button" onClick={() => setProducts([...products, { product_id: '', quantity: '', price: '', itemName: '' }])} className="text-xs font-bold text-cyan-600 hover:text-cyan-700 flex items-center gap-1 cursor-pointer">
                <Plus className="w-3.5 h-3.5" />
                Add More Product
              </button>
            </div>
          </div>

          <div>
            <label className="text-[11px] font-bold text-slate-600 block mb-1">Description</label>
            <textarea placeholder="Enter Description" value={description} onChange={e => setDescription(e.target.value)} rows={4} className="w-full px-3 py-2 border border-slate-300 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition text-sm text-slate-800" />
          </div>

          <div className="flex justify-end pt-2">
            <button type="submit" disabled={updateMutation.isPending} className="px-8 py-2 bg-[#00bcd4] hover:bg-cyan-500 text-white rounded text-sm font-bold shadow transition cursor-pointer disabled:opacity-70 flex items-center gap-2">
              {updateMutation.isPending && <Loader className="w-4 h-4 animate-spin" />}
              Edit
            </button>
          </div>
        </form>
      </div>
    </main>
  );
}
