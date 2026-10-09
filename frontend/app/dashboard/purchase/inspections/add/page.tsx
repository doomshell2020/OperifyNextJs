'use client';

import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Save, Loader2, AlertCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import { useForm, useFieldArray, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';

import grnInspectionService, { type InspectionCreatePayload } from '@/services/grnInspection.service';
import { inspectionAmounts } from '@/utils/grnInspectionAmounts';
import { AsyncPoSearchSelect } from '@/components/AsyncPoSearchSelect';
import { formatQty, formatAmt } from '@/utils/formatters';
import { DatePicker } from '../../../../../components/ui/DatePicker';

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
    order_base: z.number(),
    order_tax: z.number(),
    order_amount: z.number(),
    tax_id: z.number().nullable(),
    delivery_schedule_id: z.number().nullable(),
    uom: z.string()
  })).refine(items => items.some(i => i.received_qty > 0), {
    message: "At least one item must have a received quantity greater than 0"
  }).superRefine((items, ctx) => {
    items.forEach((item, index) => {
      if (item.received_qty > Math.max(0, item.pending_qty)) {
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
  const [loadedPo, setLoadedPo] = useState('');
  const [poLoadError, setPoLoadError] = useState<string | null>(null);
  const [poLoadAttempt, setPoLoadAttempt] = useState(0);
  const [deliveryDate, setDeliveryDate] = useState<string | null>(null);

  const {
    register,
    control,
    handleSubmit,
    setValue,
    clearErrors,
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

  const po_id = useWatch({ control, name: 'po_id' });
  const items = useWatch({ control, name: "items" });

  // Fetch next inspection ID on mount
  useEffect(() => {
    grnInspectionService.getNextId().then(res => {
      if (res.success) setValue('inspection_id', res.nextId);
    });
  }, [setValue]);

  // Handle PO Selection
  useEffect(() => {
    replace([]);
    clearErrors('items');
    setValue('vendor_id', undefined);
    setValue('vendor_name', undefined);
    if (!po_id) {
      return;
    }

    let isMounted = true;
    const fetchPoDetails = async () => {
      setIsPoLoading(true);
      try {
        const details = await grnInspectionService.getPoDetails(po_id);
        if (isMounted && details && details.po) {
          setValue('vendor_id', Number(details.po.vendor_id));
          setValue('vendor_name', details.po.vendor_name);
          setDeliveryDate(details.po.delivery_date ? String(details.po.delivery_date).split('T')[0] : null);

          const newItems = details.items.map(i => ({
            item_id: Number(i.item_id),
            item_name: i.item_name,
            order_qty: Number(i.order_qty),
            pending_qty: Number(i.pending_qty),
            received_qty: Number(i.received_qty),
            rate: Number(i.rate),
            tax_rate: Number(i.tax_rate || 0),
            order_base: Number(i.order_base), order_tax: Number(i.order_tax || 0), order_amount: Number(i.order_amount), tax_id: i.tax_id == null ? null : Number(i.tax_id),
            delivery_schedule_id: i.delivery_schedule_id == null ? null : Number(i.delivery_schedule_id),
            uom: i.uom
          }));
          replace(newItems);
          clearErrors('items');
          setLoadedPo(po_id);
        } else if (isMounted) {
          toast.error("PO not found or already closed");
          setPoLoadError('PO not found or already closed. Please select an open PO.');
          replace([]);
        }
      } catch {
        if (isMounted) {
          toast.error("Failed to load PO details");
          setPoLoadError('Failed to load PO items. Please select the PO again.');
          replace([]);
        }
      } finally {
        if (isMounted) setIsPoLoading(false);
      }
    };

    fetchPoDetails();
    return () => { isMounted = false; };
  }, [po_id, replace, setValue, clearErrors, poLoadAttempt]);

  const resetPoDetails = () => {
    setLoadedPo('');
    setPoLoadError(null);
    setIsPoLoading(false);
    setDeliveryDate(null);
    clearErrors('items');
  };

  // Derived Totals
  const totalQty = (items || []).reduce((sum, item) => sum + (Number(item.received_qty) || 0), 0);
  const totalAmountPreTax=(items || []).reduce((sum,item)=>sum+inspectionAmounts(item).cost_price,0);
  const totalTax=(items || []).reduce((sum,item)=>sum+inspectionAmounts(item).tax,0);
  const netAmount=(items || []).reduce((sum,item)=>sum+inspectionAmounts(item).amount,0);

  const submitMutation = useMutation({
    mutationFn: (payload: InspectionCreatePayload) => grnInspectionService.createInspection(payload),
    onSuccess: () => {
      toast.success('GRN Inspection created successfully');
      queryClient.invalidateQueries({ queryKey: ['grn-inspection'] });
      router.push('/dashboard/purchase/inspections');
    },
    onError: () => toast.error('Failed to create GRN Inspection')
  });

  const onSubmit = (data: FormValues) => {
    const validItems = data.items.filter(i => i.received_qty > 0).map(i => {
      const valuation = inspectionAmounts(i);
      return {
        item_id: i.item_id,
        quantity: i.received_qty,
        rate: i.rate,
        tax_id:i.tax_id,
        delivery_schedule_id: i.delivery_schedule_id,
        ...valuation
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
    <main suppressHydrationWarning className="legacy-form-page max-w-7xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">
      <LegacyPageHeader title="GRN Inspection"/>
      <form onSubmit={(event) => {
        if (po_id && (isPoLoading || loadedPo !== po_id || poLoadError)) {
          event.preventDefault();
          return;
        }
        void handleSubmit(onSubmit)(event);
      }} className="legacy-form legacy-form-inspection">
        {/* Header */}


        {/* Global form errors */}
        {!!po_id && loadedPo === po_id && !isPoLoading && (errors.items?.root || errors.items?.message) && (
          <div className="mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2">
            <AlertCircle className="w-5 h-5 flex-shrink-0" />
            <span className="text-sm font-medium">{errors.items?.root?.message || errors.items?.message}</span>
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
                onChange={(v) => {
                  resetPoDetails();
                  if (v === po_id) setPoLoadAttempt(value => value + 1);
                  setValue('po_id', v, { shouldDirty: true });
                }}
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
              <DatePicker dateFormat="dd-MM-yyyy"
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
              <DatePicker dateFormat="dd-MM-yyyy"
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
                      <td colSpan={10} className="p-8 text-center text-slate-500 italic">
                        {poLoadError || 'No items found for this PO.'}
                        {poLoadError && <button type="button" className="ml-3 text-cyan-700 underline" onClick={() => {
                          resetPoDetails();
                          setPoLoadAttempt(value => value + 1);
                        }}>Retry loading items</button>}
                      </td>
                    </tr>
                  )}

                  {fields.map((field, idx) => {
                    const currentItem = items?.[idx];
                    const rate = Number(currentItem?.rate) || 0;
                    const taxRate = Number(currentItem?.tax_rate) || 0;

                    const valuation = currentItem ? inspectionAmounts(currentItem) : { cost_price: 0, tax: 0, amount: 0 };
                    const amt = valuation.cost_price;
                    const taxAmt = valuation.tax;
                    const totalAmt = valuation.amount;

                    const hasError = !!errors.items?.[idx]?.received_qty;

                    return (
                      <tr key={field.id} className="hover:bg-slate-50/50 transition-colors">
                        <td className="p-3 font-medium text-slate-700">
                          {field.item_name}
                          <input type="hidden" {...register(`items.${idx}.item_id` as const, { valueAsNumber: true })} />
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
                            min={0}
                            max={Math.max(0, field.pending_qty)}
                            disabled={field.pending_qty <= 0}
                            {...register(`items.${idx}.received_qty` as const, { valueAsNumber: true })}
                            onBlur={(e) => {
                              const val = Number(e.target.value);
                              if (val > Math.max(0, field.pending_qty)) {
                                setValue(`items.${idx}.received_qty`, Math.max(0, field.pending_qty), { shouldValidate: true });
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

<div className="legacy-form-footer"><button
            type="submit"
            disabled={submitMutation.isPending || isPoLoading || (!!po_id && loadedPo !== po_id) || !!poLoadError}
            className="flex items-center justify-center gap-2 px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium shadow-sm transition disabled:opacity-50 w-full sm:w-auto"
          >
            {submitMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Submit
          </button></div>
</form>
    </main>
  );
}
