'use client';

import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { useMutation } from '@tanstack/react-query';
import { Save, ArrowLeft, Loader2, AlertCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import { useForm, useFieldArray, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';

import { grnService } from '@/services/grn.service';
import { AsyncInspectionSearchSelect } from '@/components/AsyncInspectionSearchSelect';
import { formatQty, formatAmt } from '@/utils/formatters';

const formSchema = z.object({
  inspection_id: z.string().min(1, "Inspection ID is required"),
  purchaseorder_id: z.string().min(1, "Purchase Order is required"),
  vendor_id: z.number(),
  vendor_name: z.string().optional(),
  inwarddate: z.string().min(1, "Inward Date is required"),
  bill_no: z.string().min(1, "Bill Number is required"),
  bill_date: z.string().min(1, "Bill Date is required"),
  remark: z.string().min(1, "Remarks are required"),
  items: z.array(z.object({
    item_id: z.coerce.number(),
    item_name: z.string(),
    received_qty: z.coerce.number().min(0, "Quantity cannot be negative"),
    rate: z.coerce.number(),
    tax_rate: z.coerce.number(),
    tax_id: z.coerce.number().optional(),
    uom: z.string()
  })).refine(items => items.some(i => i.received_qty > 0), {
    message: "At least one item must have a received quantity greater than 0"
  })
});

type FormValues = z.infer<typeof formSchema>;

export default function AddGrnPage() {
  const router = useRouter();
  const [isInspectionLoading, setIsInspectionLoading] = useState(false);

  const {
    register,
    control,
    handleSubmit,
    setValue,
    watch,
    formState: { errors }
  } = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    mode: 'onChange',
    defaultValues: {
      inspection_id: '',
      purchaseorder_id: '',
      inwarddate: new Date().toISOString().split('T')[0],
      bill_no: '',
      bill_date: new Date().toISOString().split('T')[0],
      remark: '',
      items: []
    }
  });

  const { fields, replace } = useFieldArray({
    control,
    name: "items"
  });

  const inspection_id = watch('inspection_id');
  const items = useWatch({ control, name: "items" });

  useEffect(() => {
    if (!inspection_id) {
      replace([]);
      setValue('purchaseorder_id', '');
      setValue('vendor_id', 0);
      setValue('vendor_name', undefined);
      return;
    }

    let isMounted = true;
    const fetchInspectionDetails = async () => {
      setIsInspectionLoading(true);
      try {
        const details = await grnService.getInspectionForGrn(inspection_id);
        if (isMounted && details && details.inspection) {
          setValue('purchaseorder_id', details.inspection.po_id);
          setValue('vendor_id', details.inspection.vendor_id);
          setValue('vendor_name', details.inspection.vendor_name);
          setValue('bill_no', details.inspection.bill_no);
          if (details.inspection.bill_date) {
            setValue('bill_date', String(details.inspection.bill_date).split('T')[0]);
          }
          if (details.inspection.inwarddate) {
            setValue('inwarddate', String(details.inspection.inwarddate).split('T')[0]);
          }
          
          const newItems = details.items.map((i: any) => ({
            item_id: i.item_id,
            item_name: i.item_name,
            received_qty: Number(i.quantity) || 0,
            rate: Number(i.rate) || 0,
            tax_rate: Number(i.item_tax || 0),
            tax_id: i.tax_id,
            uom: i.uom || 'KG'
          }));
          replace(newItems);
        } else if (isMounted) {
          toast.error("Inspection not found or already processed");
          replace([]);
        }
      } catch (err) {
        if (isMounted) toast.error("Failed to load inspection details");
        replace([]);
      } finally {
        if (isMounted) setIsInspectionLoading(false);
      }
    };

    fetchInspectionDetails();
    return () => { isMounted = false; };
  }, [inspection_id, replace, setValue]);

  // Derived Totals
  const totalQty = (items || []).reduce((sum, item) => sum + (Number(item.received_qty) || 0), 0);
  const totalAmountPreTax = (items || []).reduce((sum, item) => sum + ((Number(item.received_qty) || 0) * (Number(item.rate) || 0)), 0);
  const totalTax = (items || []).reduce((sum, item) => {
    const qty = Number(item.received_qty) || 0;
    const rate = Number(item.rate) || 0;
    const taxRate = Number(item.tax_rate) || 0;
    return sum + (qty * rate * (taxRate / 100));
  }, 0);
  const totalAmountPostTax = totalAmountPreTax + totalTax;

  const mutation = useMutation({
    mutationFn: (data: FormValues) => grnService.create(data),
    onSuccess: () => {
      toast.success('Goods Received Note created successfully and stock updated');
      router.push('/dashboard/purchase/grn');
    },
    onError: (error: any) => {
      toast.error(error.response?.data?.message || 'Failed to create GRN');
    }
  });

  const onSubmit = (data: FormValues) => {
    mutation.mutate(data);
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <div className="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div className="flex items-center gap-4">
          <button type="button" onClick={() => router.back()} className="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-full transition cursor-pointer">
            <ArrowLeft className="w-5 h-5" />
          </button>
          <div>
            <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Create GRN</h1>
            <p className="text-sm text-slate-500 font-medium">Generate a new Goods Received Note from Inspection</p>
          </div>
        </div>
        <div className="flex items-center gap-3">
          <button type="button" onClick={() => router.back()} className="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-md text-sm font-medium transition cursor-pointer shadow-sm">
            Cancel
          </button>
          <button 
            type="submit" 
            disabled={mutation.isPending}
            className="flex items-center gap-2 px-6 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition cursor-pointer shadow-md disabled:opacity-70"
          >
            {mutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            {mutation.isPending ? 'Saving...' : 'Submit GRN'}
          </button>
        </div>
      </div>

      {Object.keys(errors).length > 0 && (
        <div className="bg-red-50 border border-red-200 p-4 rounded-xl flex items-start gap-3">
          <AlertCircle className="w-5 h-5 text-red-600 mt-0.5" />
          <div>
            <h4 className="text-sm font-bold text-red-800">Please fix the following errors:</h4>
            <ul className="list-disc pl-5 mt-1 text-xs text-red-700 space-y-1">
              {Object.entries(errors).map(([key, error]: any) => {
                if (key === 'items' && Array.isArray(error)) {
                  return error.map((e: any, idx: number) => {
                    if (!e) return null;
                    return Object.entries(e).map(([k, err]: any) => (
                      <li key={`${idx}-${k}`}>Row {idx + 1} ({k}): {err.message}</li>
                    ));
                  });
                }
                if (key === 'items' && error?.root) {
                  return <li key="items-root">{error.root.message}</li>
                }
                return <li key={key}>{error?.message || "Invalid value"}</li>;
              })}
            </ul>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div className="p-4 border-b border-slate-200 bg-slate-50/50">
              <h3 className="font-semibold text-slate-800">Basic Information</h3>
            </div>
            <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-1.5 relative">
                <label className="block text-sm font-medium text-slate-700">Inspection ID <span className="text-red-500">*</span></label>
                <AsyncInspectionSearchSelect 
                  value={inspection_id}
                  onChange={(v) => setValue('inspection_id', v, { shouldValidate: true })}
                  error={errors.inspection_id?.message}
                  disabled={isInspectionLoading}
                />
              </div>

              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700">Purchase Order ID</label>
                <input type="text" {...register('purchaseorder_id')} readOnly className="w-full h-10 border border-slate-200 bg-slate-50 text-slate-500 rounded-md px-3 text-sm focus:outline-none" />
              </div>

              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700">Inward Date <span className="text-red-500">*</span></label>
                <input type="date" {...register('inwarddate')} className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-sm ${errors.inwarddate ? 'border-red-500 focus:ring-red-500' : 'border-slate-300'}`} />
              </div>

              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700">Bill Number <span className="text-red-500">*</span></label>
                <input type="text" {...register('bill_no')} className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-sm ${errors.bill_no ? 'border-red-500 focus:ring-red-500' : 'border-slate-300'}`} />
              </div>
              
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700">Bill Date <span className="text-red-500">*</span></label>
                <input type="date" {...register('bill_date')} className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-sm ${errors.bill_date ? 'border-red-500 focus:ring-red-500' : 'border-slate-300'}`} />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
              <h3 className="font-semibold text-slate-800">Received Items <span className="text-red-500">*</span></h3>
              {isInspectionLoading && <Loader2 className="w-4 h-4 animate-spin text-cyan-600" />}
            </div>
            
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm border-collapse">
                <thead>
                  <tr className="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider">Item</th>
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider w-32">Received Qty</th>
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider w-24">UOM</th>
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider w-28 text-right">Unit Price</th>
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider w-24 text-right">Tax (%)</th>
                    <th className="p-3 font-semibold uppercase text-xs tracking-wider w-32 text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {fields.length > 0 ? fields.map((field, index) => {
                    const qty = Number(items[index]?.received_qty) || 0;
                    const rate = Number(items[index]?.rate) || 0;
                    const taxRate = Number(items[index]?.tax_rate) || 0;
                    
                    const amount = qty * rate;
                    const taxAmt = amount * (taxRate / 100);
                    const total = amount + taxAmt;
                    
                    return (
                      <tr key={field.id} className="border-b border-slate-100 hover:bg-slate-50/50">
                        <td className="p-3">
                          <input type="text" readOnly {...register(`items.${index}.item_name`)} className="w-full border-none bg-transparent focus:outline-none text-slate-800 text-sm font-medium" />
                        </td>
                        <td className="p-3">
                          <input type="number" min="0" step="any" {...register(`items.${index}.received_qty`)} className="w-full h-8 border border-slate-300 rounded px-2 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" />
                        </td>
                        <td className="p-3">
                          <input type="text" readOnly {...register(`items.${index}.uom`)} className="w-full border-none bg-transparent focus:outline-none text-slate-600 text-sm" />
                        </td>
                        <td className="p-3 text-right text-slate-600">
                          {formatAmt(rate)}
                        </td>
                        <td className="p-3 text-right text-slate-600">
                          {taxRate}
                        </td>
                        <td className="p-3 text-right font-medium text-slate-800">
                          {formatAmt(total)}
                        </td>
                      </tr>
                    )
                  }) : (
                    <tr>
                      <td colSpan={6} className="p-8 text-center text-slate-500">
                        Select an Inspection ID to load items
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
          
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div className="p-4 border-b border-slate-200 bg-slate-50/50">
              <h3 className="font-semibold text-slate-800">Remarks <span className="text-red-500">*</span></h3>
            </div>
            <div className="p-4">
              <textarea 
                {...register('remark')} 
                rows={4}
                className={`w-full border rounded-md p-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-sm resize-none ${errors.remark ? 'border-red-500 focus:ring-red-500' : 'border-slate-300'}`}
                placeholder="Enter any additional notes or remarks..."
              ></textarea>
            </div>
          </div>
        </div>
        
        <div className="lg:col-span-1">
          <div className="bg-slate-50 rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
            <div className="p-4 border-b border-slate-200 bg-white">
              <h3 className="font-semibold text-slate-800">GRN Summary</h3>
            </div>
            
            <div className="p-5 space-y-4">
              <div className="flex justify-between items-center py-2 border-b border-slate-200/50">
                <span className="text-sm text-slate-500">Vendor</span>
                <span className="text-sm font-medium text-slate-800 text-right">{watch('vendor_name') || '-'}</span>
              </div>
              <div className="flex justify-between items-center py-2 border-b border-slate-200/50">
                <span className="text-sm text-slate-500">Total Items</span>
                <span className="text-sm font-medium text-slate-800">{items.filter(i => Number(i.received_qty) > 0).length}</span>
              </div>
              <div className="flex justify-between items-center py-2 border-b border-slate-200/50">
                <span className="text-sm text-slate-500">Total Qty</span>
                <span className="text-sm font-medium text-slate-800">{formatQty(totalQty)}</span>
              </div>
              <div className="flex justify-between items-center py-2 border-b border-slate-200/50">
                <span className="text-sm text-slate-500">Amount (Pre-Tax)</span>
                <span className="text-sm font-medium text-slate-800">₹ {formatAmt(totalAmountPreTax)}</span>
              </div>
              <div className="flex justify-between items-center py-2 border-b border-slate-200/50">
                <span className="text-sm text-slate-500">Total Tax</span>
                <span className="text-sm font-medium text-slate-800">₹ {formatAmt(totalTax)}</span>
              </div>
              <div className="flex justify-between items-center pt-2">
                <span className="font-bold text-slate-700">Net Amount</span>
                <span className="text-lg font-extrabold text-cyan-600">₹ {formatAmt(totalAmountPostTax)}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  );
}
