'use client';

import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Save, ArrowLeft, Loader2, AlertCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import { useForm, useFieldArray, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';

import grnInspectionService from '@/services/grnInspection.service';
import purchaseOrderService from '@/services/purchaseOrder.service';
import { AsyncPoSearchSelect } from '@/components/AsyncPoSearchSelect';
import { formatQty, formatAmt } from '@/utils/formatters';

const formSchema = z.object({
  po_id: z.string().min(1, "Purchase Order is required"),
  inspection_id: z.string(),
  inwarddate: z.string().min(1, "Inward Date is required"),
  bill_no: z.string().min(1, "Bill Number is required"),
  bill_date: z.string().min(1, "Bill Date is required"),
  remark: z.string().min(1, "Remarks are required"),
  vendor_id: z.number().optional(),
  vendor_name: z.string().optional(),
  items: z.array(z.object({
    item_id: z.number(),
    item_name: z.string(),
    order_qty: z.number(),
    pending_qty: z.number(),
    received_qty: z.number().min(0, "Quantity cannot be negative").transform(v => Number(v) || 0),
    rate: z.number(),
    tax_rate: z.number(),
    uom: z.string()
  })).refine(items => items.some(i => i.received_qty > 0), {
    message: "At least one item must have a received quantity greater than 0"
  }).superRefine((items, ctx) => {
    items.forEach((item, index) => {
      if (item.received_qty > item.pending_qty) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: "Qty exceeds pending",
          path: [index, "received_qty"]
        });
      }
    });
  })
});

type FormValues = z.infer<typeof formSchema>;

export default function AddGrnInspectionPage() {
  const router = useRouter();
  const queryClient = useQueryClient();
  const [isPoLoading, setIsPoLoading] = useState(false);
  const [deliveryDate, setDeliveryDate] = useState<string | null>(null);

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
      po_id: '',
      inspection_id: '',
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

  const po_id = watch('po_id');
  const items = useWatch({ control, name: "items" });

  // Fetch next inspection ID on mount
  useEffect(() => {
    grnInspectionService.getNextId().then(res => {
      if (res.success) setValue('inspection_id', res.nextId);
    });
  }, [setValue]);

  // Handle PO Selection
  useEffect(() => {
    if (!po_id) {
      replace([]);
      setValue('vendor_id', undefined);
      setValue('vendor_name', undefined);
      setDeliveryDate(null);
      return;
    }

    let isMounted = true;
    const fetchPoDetails = async () => {
      setIsPoLoading(true);
      try {
        const details = await purchaseOrderService.getDetails(po_id);
        if (isMounted && details && details.po) {
          setValue('vendor_id', details.po.vendor_id);
          setValue('vendor_name', details.po.vendor_name);
          setDeliveryDate(details.po.delivery_date ? String(details.po.delivery_date).split('T')[0] : null);
          
          const newItems = details.items.map(i => ({
            item_id: i.item_id,
            item_name: i.item_name,
            order_qty: Number(i.order_qty),
            pending_qty: Number(i.pending_qty),
            received_qty: 0,
            rate: Number(i.rate),
            tax_rate: Number(i.tax_percentage || 0),
            uom: i.uom || 'KG'
          }));
          replace(newItems);
        } else if (isMounted) {
          toast.error("PO not found or already closed");
          replace([]);
        }
      } catch (err) {
        if (isMounted) toast.error("Failed to load PO details");
        replace([]);
      } finally {
        if (isMounted) setIsPoLoading(false);
      }
    };

    fetchPoDetails();
    return () => { isMounted = false; };
  }, [po_id, replace, setValue]);

  // Derived Totals
  const totalQty = (items || []).reduce((sum, item) => sum + (Number(item.received_qty) || 0), 0);
  const totalAmountPreTax = (items || []).reduce((sum, item) => sum + ((Number(item.received_qty) || 0) * (Number(item.rate) || 0)), 0);
  const totalTax = (items || []).reduce((sum, item) => {
    const amt = (Number(item.received_qty) || 0) * (Number(item.rate) || 0);
    return sum + (amt * (Number(item.tax_rate) || 0) / 100);
  }, 0);
  const netAmount = totalAmountPreTax + totalTax;

  const submitMutation = useMutation({
    mutationFn: (payload: any) => grnInspectionService.createInspection(payload),
    onSuccess: () => {
      toast.success('GRN Inspection created successfully');
      queryClient.invalidateQueries({ queryKey: ['grn-inspection'] });
      router.push('/dashboard/purchase/inspections');
    },
    onError: () => toast.error('Failed to create GRN Inspection')
  });

  const onSubmit = (data: FormValues) => {
    const validItems = data.items.filter(i => i.received_qty > 0).map(i => {
      const amount = i.received_qty * i.rate;
      const taxAmt = amount * (i.tax_rate / 100);
      return {
        item_id: i.item_id,
        quantity: i.received_qty,
        rate: i.rate,
        tax: taxAmt,
        amount: amount
      };
    });

    const payload = {
      inspection: {
        po_id: data.po_id,
        inspection_id: data.inspection_id,
        vendor_id: data.vendor_id,
        inwarddate: data.inwarddate,
        bill_no: data.bill_no,
        bill_date: data.bill_date,
        remark: data.remark,
        total_qty: totalQty,
        total_tax: totalTax,
        total_amt: netAmount
      },
      items: validItems
    };

    submitMutation.mutate(payload);
  };

  return (
    <main suppressHydrationWarning className="max-w-7xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">
      <form onSubmit={handleSubmit(onSubmit)}>
        {/* Header */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-5 rounded-xl border border-slate-200 shadow-sm gap-4">
          <div className="flex items-center gap-3">
            <button type="button" onClick={() => router.back()} className="p-2 hover:bg-slate-100 rounded-full transition">
              <ArrowLeft className="w-5 h-5 text-slate-600" />
            </button>
            <div>
              <h1 className="text-xl font-bold text-slate-900">Add GRN Inspection</h1>
              <p className="text-sm text-slate-500">Create a new Goods Received Note Inspection</p>
            </div>
          </div>
          <button 
            type="submit"
            disabled={submitMutation.isPending || isPoLoading}
            className="flex items-center justify-center gap-2 px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium shadow-sm transition disabled:opacity-50 w-full sm:w-auto"
          >
            {submitMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Save Inspection
          </button>
        </div>

        {/* Global form errors */}
        {errors.items?.root && (
          <div className="mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2">
            <AlertCircle className="w-5 h-5 flex-shrink-0" />
            <span className="text-sm font-medium">{errors.items.root.message}</span>
          </div>
        )}

        {/* Basic Info Section */}
        <div className="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="p-4 border-b border-slate-200 bg-slate-50/50">
            <h3 className="font-semibold text-slate-800">Basic Information</h3>
          </div>
          <div className="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            
            {/* Purchase Order */}
            <div className="space-y-1.5 relative">
              <label className="block text-sm font-medium text-slate-700">Purchase Order <span className="text-red-500">*</span></label>
              <AsyncPoSearchSelect 
                value={po_id}
                onChange={(v) => setValue('po_id', v, { shouldValidate: true })}
                error={errors.po_id?.message}
                disabled={isPoLoading}
              />
              {deliveryDate && (
                <p className="text-sm font-medium text-red-500 mt-1">
                  Estimated Delivery Date is:- {deliveryDate}
                </p>
              )}
              {errors.po_id && <p className="text-xs text-red-600 absolute -bottom-5">{errors.po_id.message}</p>}
            </div>

            {/* Inspection No */}
            <div className="space-y-1.5">
              <label className="block text-sm font-medium text-slate-700">Inspection No. (Auto)</label>
              <input 
                type="text" 
                {...register('inspection_id')}
                disabled 
                className="w-full h-10 border border-slate-200 bg-slate-50 text-slate-500 rounded-md px-3 text-sm focus:outline-none" 
              />
            </div>

            {/* Inward Date */}
            <div className="space-y-1.5 relative">
              <label className="block text-sm font-medium text-slate-700">Inward Date <span className="text-red-500">*</span></label>
              <input 
                type="date" 
                {...register('inwarddate')}
                className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white ${errors.inwarddate ? 'border-red-500' : 'border-slate-300'}`} 
              />
              {errors.inwarddate && <p className="text-xs text-red-600 absolute -bottom-5">{errors.inwarddate.message}</p>}
            </div>

            {/* Vendor (Read only via PO) */}
            <div className="space-y-1.5">
              <label className="block text-sm font-medium text-slate-700">Vendor</label>
              <input 
                type="text" 
                {...register('vendor_name')}
                disabled 
                placeholder="Select a PO first..."
                className="w-full h-10 border border-slate-200 bg-slate-50 text-slate-500 rounded-md px-3 text-sm focus:outline-none" 
              />
            </div>

            {/* Bill No */}
            <div className="space-y-1.5 relative">
              <label className="block text-sm font-medium text-slate-700">Bill No. <span className="text-red-500">*</span></label>
              <input 
                type="text" 
                {...register('bill_no')}
                placeholder="Enter bill number"
                className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white ${errors.bill_no ? 'border-red-500' : 'border-slate-300'}`} 
              />
              {errors.bill_no && <p className="text-xs text-red-600 absolute -bottom-5">{errors.bill_no.message}</p>}
            </div>

            {/* Bill Date */}
            <div className="space-y-1.5 relative">
              <label className="block text-sm font-medium text-slate-700">Bill Date <span className="text-red-500">*</span></label>
              <input 
                type="date" 
                {...register('bill_date')}
                className={`w-full h-10 border rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white ${errors.bill_date ? 'border-red-500' : 'border-slate-300'}`} 
              />
              {errors.bill_date && <p className="text-xs text-red-600 absolute -bottom-5">{errors.bill_date.message}</p>}
            </div>

          </div>
        </div>

        {/* Items Section */}
        {po_id && (
          <div className="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
              <h3 className="font-semibold text-slate-800">Item Details</h3>
              {isPoLoading && <Loader2 className="w-5 h-5 text-cyan-600 animate-spin" />}
            </div>
            
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm min-w-[800px]">
                <thead className="bg-slate-100/50">
                  <tr>
                    <th className="p-3 font-semibold text-slate-600">Item</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-24">Ord. Qty</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-24">Pend. Qty</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-32">Rcvd. Qty</th>
                    <th className="p-3 font-semibold text-slate-600">UOM</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-28">Unit Price</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-28">Total Price</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-24">Tax Rate</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-28">Tax Amt</th>
                    <th className="p-3 font-semibold text-slate-600 text-right w-32">Total Amt</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {fields.length === 0 && !isPoLoading && (
                    <tr>
                      <td colSpan={10} className="p-8 text-center text-slate-500 italic">No items found for this PO.</td>
                    </tr>
                  )}
                  
                  {fields.map((field, idx) => {
                    const currentItem = items?.[idx];
                    const rcvdQty = Number(currentItem?.received_qty) || 0;
                    const rate = Number(currentItem?.rate) || 0;
                    const taxRate = Number(currentItem?.tax_rate) || 0;
                    
                    const amt = rcvdQty * rate;
                    const taxAmt = amt * (taxRate / 100);
                    const totalAmt = amt + taxAmt;
                    
                    const hasError = !!errors.items?.[idx]?.received_qty;

                    return (
                      <tr key={field.id} className="hover:bg-slate-50/50 transition-colors">
                        <td className="p-3 font-medium text-slate-700">
                          {field.item_name}
                          <input type="hidden" {...register(`items.${idx}.item_id` as const)} />
                          <input type="hidden" {...register(`items.${idx}.item_name` as const)} />
                          <input type="hidden" {...register(`items.${idx}.order_qty` as const, { valueAsNumber: true })} />
                          <input type="hidden" {...register(`items.${idx}.pending_qty` as const, { valueAsNumber: true })} />
                          <input type="hidden" {...register(`items.${idx}.rate` as const, { valueAsNumber: true })} />
                          <input type="hidden" {...register(`items.${idx}.tax_rate` as const, { valueAsNumber: true })} />
                          <input type="hidden" {...register(`items.${idx}.uom` as const)} />
                        </td>
                        <td className="p-3 text-right text-slate-600">{field.order_qty}</td>
                        <td className="p-3 text-right text-orange-600 font-medium">{field.pending_qty}</td>
                        <td className="p-3 align-top">
                          <input 
                            type="number"
                            step="any"
                            max={field.pending_qty}
                            {...register(`items.${idx}.received_qty` as const, { valueAsNumber: true })}
                            onBlur={(e) => {
                              const val = Number(e.target.value);
                              if (val > field.pending_qty) {
                                setValue(`items.${idx}.received_qty`, field.pending_qty, { shouldValidate: true });
                                toast.error(`Quantity cannot exceed pending quantity (${field.pending_qty})`);
                              }
                            }}
                            className={`w-full text-right h-8 border rounded px-2 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white ${hasError ? 'border-red-500 bg-red-50' : 'border-slate-300'}`}
                          />
                          {hasError && <p className="text-[10px] text-red-600 mt-1 text-right">{errors.items?.[idx]?.received_qty?.message}</p>}
                        </td>
                        <td className="p-3 text-slate-600">{field.uom}</td>
                        <td className="p-3 text-right text-slate-600">{formatAmt(rate)}</td>
                        <td className="p-3 text-right text-slate-600 bg-slate-50/50">{formatAmt(amt)}</td>
                        <td className="p-3 text-right text-slate-600">{taxRate}%</td>
                        <td className="p-3 text-right text-slate-600 bg-slate-50/50">{formatAmt(taxAmt)}</td>
                        <td className="p-3 text-right text-slate-900 font-semibold bg-slate-50/50">{formatAmt(totalAmt)}</td>
                      </tr>
                    );
                  })}
                </tbody>
                <tfoot className="bg-slate-100/50 border-t border-slate-200">
                  <tr>
                    <td colSpan={3} className="p-4 text-right font-bold text-slate-700 uppercase tracking-wider text-xs">Total:</td>
                    <td className="p-4 text-right font-bold text-cyan-700 text-base">{formatQty(totalQty)}</td>
                    <td colSpan={2}></td>
                    <td className="p-4 text-right font-bold text-slate-800">{formatAmt(totalAmountPreTax)}</td>
                    <td></td>
                    <td className="p-4 text-right font-bold text-slate-800">{formatAmt(totalTax)}</td>
                    <td className="p-4 text-right font-bold text-cyan-700 text-lg">{formatAmt(netAmount)}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        )}

        {/* Remarks Section */}
        <div className="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-10">
           <div className="p-4 border-b border-slate-200 bg-slate-50/50">
            <h3 className="font-semibold text-slate-800">Remarks</h3>
          </div>
          <div className="p-6 relative">
            <textarea 
              {...register('remark')}
              rows={4}
              placeholder="Enter inspection remarks..."
              className={`w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-y bg-white shadow-sm ${errors.remark ? 'border-red-500' : 'border-slate-300'}`}
            ></textarea>
            {errors.remark && <p className="text-xs text-red-600 absolute bottom-1">{errors.remark.message}</p>}
          </div>
        </div>
      </form>
    </main>
  );
}
