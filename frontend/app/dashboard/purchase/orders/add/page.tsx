'use client';

import React, { useState, useEffect, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { useForm, useFieldArray, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import purchaseOrderService from '@/services/purchaseOrder.service';
import { settingsService, Supplier, Product } from '@/services/settings.service';
import { Plus, Trash2, Search, ArrowLeft, Loader2, Save, FileText, ShoppingCart, CreditCard, Building2, UserPlus, Info, Eye, X } from 'lucide-react';
import toast from 'react-hot-toast';
import { ProductSearchSelect } from '@/components/ProductSearchSelect';

const poSchema = z.object({
  poDate: z.string().min(1, "Generated Date is required"),
  deliveryDate: z.string().min(1, "Delivery Date is required"),
  vendorId: z.string().min(1, "Supplier is required"),
  contract: z.string().optional(),
  project: z.string().optional(),
  paymentTerms: z.string().optional(),
  remark: z.string().optional(),
  items: z.array(
    z.object({
      item_id: z.string().min(1, "Item is required"),
      item_name: z.string(),
      qty: z.number({ invalid_type_error: "Required" }).min(0.001, "Qty must be > 0"),
      uom: z.string().optional(),
      weight: z.number().optional().default(0),
      volume: z.number().optional().default(0),
      unit_price: z.number().min(0, "Price cannot be negative"),
      tax_id: z.string(),
      tax_percentage: z.number(),
      tax_cal: z.enum(['0', '1']), // 1 = Exclusive, 0 = Inclusive
    })
  ).min(1, "At least one item is required to create a PO")
});

type POFormValues = z.infer<typeof poSchema>;

export default function AddPurchaseOrderPage() {
  const router = useRouter();
  
  const [poNumber, setPoNumber] = useState('');
  const [vendors, setVendors] = useState<Supplier[]>([]);
  const [taxes, setTaxes] = useState<{id: number, tax: number}[]>([]);
  const [availableProducts, setAvailableProducts] = useState<Product[]>([]);
  const [isInitializing, setIsInitializing] = useState(true);

  // Vendor Search State
  const [vendorSearchOpen, setVendorSearchOpen] = useState(false);
  const [vendorSearchTerm, setVendorSearchTerm] = useState('');
  const vendorRef = useRef<HTMLDivElement>(null);

  // Vendor Add State
  const [isAddVendorOpen, setIsAddVendorOpen] = useState(false);
  const [newVendorForm, setNewVendorForm] = useState<Partial<Supplier>>({ name: '', address: '', contact_no: '', email: '', gst_number: '', pancard_number: '', tin_no: '', tds: '0', contact_person: '', type: 'Vendor', description: '' });
  const [isAddingVendor, setIsAddingVendor] = useState(false);
  const [vendorAddError, setVendorAddError] = useState('');

  // LPR Modal State
  const [lprModalOpen, setLprModalOpen] = useState(false);
  const [lprHistory, setLprHistory] = useState<any[]>([]);
  const [lprItemName, setLprItemName] = useState('');
  
  const openLprModal = async (itemId: string, itemName: string) => {
    try {
      setLprItemName(itemName);
      setLprHistory([]); // clear old data
      setLprModalOpen(true);
      const history = await purchaseOrderService.getItemHistory(itemId);
      setLprHistory(history);
    } catch (e) {
      console.error(e);
      toast.error("Failed to load Last Purchase History");
    }
  };

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (vendorRef.current && !vendorRef.current.contains(event.target as Node)) {
        setVendorSearchOpen(false);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // Form Setup
  const { register, control, handleSubmit, setValue, getValues, watch, formState: { errors, isSubmitting } } = useForm<POFormValues>({
    resolver: zodResolver(poSchema),
    defaultValues: {
      poDate: new Date().toISOString().split('T')[0],
      deliveryDate: '',
      vendorId: '',
      contract: '',
      project: '',
      paymentTerms: '',
      remark: '',
      items: [{ item_id: '', item_name: '', qty: 0, uom: '', weight: 0, volume: 0, unit_price: 0, tax_id: '', tax_percentage: 0, tax_cal: '1' }]
    }
  });

  const { fields, append, remove, update } = useFieldArray({
    control,
    name: 'items'
  });

  // Watchers for Calculations and Auto-fills
  const watchItems = useWatch({ control, name: 'items' });
  const watchVendorId = useWatch({ control, name: 'vendorId' });
  const selectedVendorDetails = vendors.find(v => v.id.toString() === watchVendorId);

  useEffect(() => {
    if (watchVendorId && vendors.length > 0) {
      const v = vendors.find(v => v.id.toString() === watchVendorId);
      if (v) {
        setVendorSearchTerm(v.name);
        // Auto-fill payment terms if the vendor has it in description or just a default
        if (!getValues('paymentTerms')) {
          setValue('paymentTerms', v.description || '');
        }
      }
    }
  }, [watchVendorId, vendors, setValue, getValues]);

  useEffect(() => {
    const loadInitialData = async () => {
      try {
        const [poRes, suppliersRes, taxesRes, productsRes] = await Promise.all([
          purchaseOrderService.getNextPoNumber(),
          settingsService.getSuppliers({}),
          settingsService.getTaxes(),
          settingsService.getProducts({ itemtype: 'RawMaterial' })
        ]);
        setPoNumber(poRes);
        setVendors(suppliersRes);
        setTaxes(taxesRes);
        setAvailableProducts(productsRes);
      } catch (error) {
        console.error(error);
        toast.error("Failed to load necessary data");
      } finally {
        setIsInitializing(false);
      }
    };
    loadInitialData();
  }, []);

  // Calculations
  const calculateTotals = () => {
    let subtotal = 0;
    let totalTax = 0;
    let grandTotal = 0;

    watchItems.forEach(item => {
      const qty = Number(item.qty) || 0;
      const price = Number(item.unit_price) || 0;
      const taxPerc = Number(item.tax_percentage) || 0;
      const baseAmount = qty * price;

      if (item.tax_cal === '1') {
        // Exclusive
        const taxAmt = (baseAmount * taxPerc) / 100;
        subtotal += baseAmount;
        totalTax += taxAmt;
        grandTotal += (baseAmount + taxAmt);
      } else {
        // Inclusive
        const taxAmt = baseAmount - (baseAmount * (100 / (100 + taxPerc)));
        subtotal += baseAmount;
        totalTax += taxAmt;
        grandTotal += baseAmount;
      }
    });

    return { subtotal, totalTax, grandTotal };
  };

  const { subtotal, totalTax, grandTotal } = calculateTotals();

  // Handlers
  const handleProductSelect = async (index: number, productStrId: string) => {
    const product = availableProducts.find(p => p.id.toString() === productStrId);
    if (!product) {
      update(index, { ...getValues(`items.${index}`), item_id: '', item_name: '', uom: '', unit_price: 0, tax_id: '', tax_percentage: 0 });
      return;
    }
    
    let lprPrice = product.cost_price || 0;
    try {
      const history = await purchaseOrderService.getItemHistory(product.id.toString());
      if (history && history.length > 0) {
        lprPrice = history[0].price;
      }
    } catch (e) {
      console.error("Failed to fetch LPR for product", e);
    }

    const taxMatch = taxes.find(t => t.tax === product.tax);
    update(index, {
      ...getValues(`items.${index}`),
      item_id: product.id.toString(),
      item_name: product.item_name,
      uom: product.uom_name || '',
      unit_price: Number(lprPrice),
      tax_id: taxMatch ? taxMatch.id.toString() : '',
      tax_percentage: taxMatch ? taxMatch.tax : 0,
    });
  };

  const handleAddVendor = async () => {
    try {
      setIsAddingVendor(true);
      setVendorAddError('');
      await settingsService.createSupplier(newVendorForm);
      const suppliersRes = await settingsService.getSuppliers({});
      setVendors(suppliersRes);
      
      const newestVendor = suppliersRes.find(v => v.name === newVendorForm.name);
      if (newestVendor) {
        setValue('vendorId', newestVendor.id.toString(), { shouldValidate: true });
        setVendorSearchTerm(newestVendor.name);
      }
      
      setIsAddVendorOpen(false);
      setNewVendorForm({ name: '', address: '', contact_no: '', email: '', gst_number: '', pancard_number: '', tin_no: '', tds: '0', contact_person: '', type: 'Vendor', description: '' });
      toast.success('Supplier added successfully');
    } catch (err: any) {
      setVendorAddError(err?.response?.data?.message || 'Failed to create supplier');
    } finally {
      setIsAddingVendor(false);
    }
  };

  const onSubmit = async (data: POFormValues) => {
    try {
      const validItems = data.items.filter(i => i.item_id && i.qty > 0);
      
      const payload = {
        po: {
          purchaseorder_id: poNumber,
          vendor_id: data.vendorId,
          added_time: data.poDate,
          delivery_date: data.deliveryDate,
          payment_terms: data.paymentTerms,
          remark: data.remark + (data.contract ? ` | Contract: ${data.contract}` : '') + (data.project ? ` | Project: ${data.project}` : ''),
          total_qty: validItems.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0),
          total_tax: totalTax,
          total_amt: grandTotal,
        },
        items: validItems.map(i => {
          const qty = Number(i.qty) || 0;
          const price = Number(i.unit_price) || 0;
          const taxPerc = Number(i.tax_percentage) || 0;
          const baseAmt = qty * price;
          
          let taxAmt = 0;
          let totalAmt = 0;
          if (i.tax_cal === '1') {
            taxAmt = (baseAmt * taxPerc) / 100;
            totalAmt = baseAmt + taxAmt;
          } else {
            taxAmt = baseAmt - (baseAmt * (100 / (100 + taxPerc)));
            totalAmt = baseAmt;
          }

          return {
            item_id: i.item_id,
            tax_id: i.tax_id,
            item_qty: qty,
            item_amt: price,
            item_base_price: baseAmt,
            tax_percentage: taxPerc,
            item_tax_amt: taxAmt,
            item_total_amount: totalAmt,
            uom: i.uom,
            weight: Number(i.weight) || 0,
            volume: Number(i.volume) || 0
          };
        })
      };

      await purchaseOrderService.createPurchaseOrder(payload);
      toast.success('Purchase Order created successfully!');
      router.push('/dashboard/purchase/orders');
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Failed to create Purchase Order');
    }
  };

  if (isInitializing) {
    return (
      <div className="flex h-[80vh] items-center justify-center">
        <div className="flex flex-col items-center gap-4 text-blue-600">
          <Loader2 className="w-10 h-10 animate-spin" />
          <p className="font-medium animate-pulse text-gray-600">Initializing Workspace...</p>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="max-w-[1600px] mx-auto p-4 md:p-6 lg:p-8 font-sans space-y-6 bg-gray-50 min-h-screen">
      
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <div>
          <div className="flex items-center gap-3 mb-1">
            <h1 className="text-2xl font-bold text-gray-800 tracking-tight">Create Purchase Order</h1>
            <span className="px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-md border border-gray-200">DRAFT</span>
          </div>
          <p className="text-sm text-gray-500 flex items-center gap-2">
            Generated PO Number: <span className="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{poNumber}</span>
          </p>
        </div>
        <div className="flex items-center gap-3">
          <button type="button" onClick={() => router.back()} className="px-4 py-2 h-[42px] bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm flex items-center justify-center gap-2 focus:ring-2 focus:ring-offset-1 focus:ring-gray-200">
            <ArrowLeft className="w-4 h-4" /> Back
          </button>
          <button type="submit" disabled={isSubmitting} className="px-5 py-2 h-[42px] bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
            {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Save & Finalize
          </button>
        </div>
      </div>

      {/* Form Content */}
      <div className="space-y-6">
        
        {/* Purchase Information Card */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div className="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
              <FileText className="w-5 h-5 text-blue-600" />
              <h2 className="text-lg font-semibold text-gray-800">Purchase Information</h2>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">PO Number</label>
                <input type="text" className="w-full h-[42px] border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-500 cursor-not-allowed shadow-sm focus:outline-none" value={poNumber} readOnly />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Generated Date <span className="text-red-500">*</span></label>
                <input type="date" className={`w-full h-[42px] border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${errors.poDate ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'}`} {...register('poDate')} />
                {errors.poDate && <p className="text-red-500 text-xs mt-1 absolute">{errors.poDate.message}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date <span className="text-red-500">*</span></label>
                <input type="date" className={`w-full h-[42px] border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${errors.deliveryDate ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'}`} {...register('deliveryDate')} />
                {errors.deliveryDate && <p className="text-red-500 text-xs mt-1 absolute">{errors.deliveryDate.message}</p>}
              </div>
            </div>
          </div>

          {/* Vendor & Contract Information */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div className="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
              <Building2 className="w-5 h-5 text-blue-600" />
              <h2 className="text-lg font-semibold text-gray-800">Vendor & Contract</h2>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
              <div>
                <label className="flex items-center justify-between text-sm font-medium text-gray-700 mb-1">
                  <span>Supplier <span className="text-red-500">*</span></span>
                  <button type="button" onClick={() => setIsAddVendorOpen(true)} className="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                    <UserPlus className="w-3 h-3" /> Add New
                  </button>
                </label>
                <div className="relative mb-2" ref={vendorRef}>
                  <div className="relative">
                    <input 
                      type="text"
                      className={`w-full h-[42px] border rounded-lg pl-3 pr-10 py-2 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition-shadow bg-white ${errors.vendorId ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'}`}
                      placeholder="Search & Select Supplier..."
                      value={vendorSearchTerm}
                      onChange={(e) => {
                        setVendorSearchTerm(e.target.value);
                        setVendorSearchOpen(true);
                        if (e.target.value === '') {
                          setValue('vendorId', '', { shouldValidate: true });
                        }
                      }}
                      onFocus={() => setVendorSearchOpen(true)}
                    />
                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                      <Search className="h-4 w-4" />
                    </div>
                  </div>
                  
                  {vendorSearchOpen && (
                    <div className="absolute top-full left-0 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 flex flex-col overflow-hidden">
                      <div className="overflow-y-auto max-h-60 py-1">
                        {vendors.filter(v => v.name.toLowerCase().includes(vendorSearchTerm.toLowerCase())).length > 0 ? (
                          vendors.filter(v => v.name.toLowerCase().includes(vendorSearchTerm.toLowerCase())).map(v => (
                            <div 
                              key={v.id} 
                              className={`px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm transition-colors ${watchVendorId === v.id.toString() ? 'bg-blue-50/50 font-semibold text-blue-700' : 'text-gray-700'}`}
                              onClick={() => {
                                setValue('vendorId', v.id.toString(), { shouldValidate: true });
                                setVendorSearchTerm(v.name);
                                setVendorSearchOpen(false);
                              }}
                            >
                              {v.name}
                            </div>
                          ))
                        ) : (
                          <div className="px-4 py-3 text-sm text-gray-500 text-center italic">No suppliers found</div>
                        )}
                      </div>
                    </div>
                  )}
                </div>
                {errors.vendorId && <p className="text-red-500 text-xs mb-2">{errors.vendorId.message}</p>}
                
                {selectedVendorDetails && (
                  <div className="p-3.5 bg-blue-50/50 rounded-lg border border-blue-100 text-sm">
                    <div className="grid grid-cols-2 gap-y-3 gap-x-4">
                      <div><span className="text-gray-500 text-[10px] font-bold block uppercase tracking-wider">GST Number</span> <span className="font-medium text-gray-800">{selectedVendorDetails.gst_number || 'N/A'}</span></div>
                      <div><span className="text-gray-500 text-[10px] font-bold block uppercase tracking-wider">Contact</span> <span className="font-medium text-gray-800">{selectedVendorDetails.contact_no || 'N/A'}</span></div>
                      <div className="col-span-2"><span className="text-gray-500 text-[10px] font-bold block uppercase tracking-wider">Address</span> <span className="text-gray-700 leading-snug">{selectedVendorDetails.address || 'N/A'}</span></div>
                    </div>
                  </div>
                )}
              </div>
              
              <div className="space-y-5">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Contract Reference</label>
                  <input type="text" placeholder="e.g. C-12345" className="w-full h-[42px] border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" {...register('contract')} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                  <input type="text" placeholder="e.g. Infrastructure Upgrade" className="w-full h-[42px] border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" {...register('project')} />
                </div>
              </div>
            </div>
          </div>

          {/* Purchase Items Table */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-5 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-gray-50">
              <div className="flex items-center gap-2 mb-3 sm:mb-0">
                <ShoppingCart className="w-5 h-5 text-blue-600" />
                <h2 className="text-lg font-semibold text-gray-800">Purchase Items</h2>
              </div>
              <button 
                type="button" 
                onClick={() => append({ item_id: '', item_name: '', qty: 0, uom: '', weight: 0, volume: 0, unit_price: 0, tax_id: '', tax_percentage: 0, tax_cal: '1' })}
                className="px-4 py-2 h-[42px] bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center justify-center gap-2 focus:ring-2 focus:ring-offset-1 focus:ring-gray-200"
              >
                <Plus className="w-4 h-4" /> Add Row
              </button>
            </div>
            
            <div className="overflow-x-auto w-full pb-4">
              <table className="w-full text-sm text-left min-w-[1300px]">
                <thead className="bg-gray-100/70 border-b border-gray-200 text-gray-600 uppercase text-xs font-semibold tracking-wider">
                  <tr>
                    <th className="px-4 py-3 w-64 sticky left-0 bg-gray-100 z-20 shadow-[1px_0_0_0_#e5e7eb]">Item</th>
                    <th className="px-3 py-3 w-28 text-right">Qty</th>
                    <th className="px-3 py-3 w-24">UOM</th>
                    <th className="px-3 py-3 w-28 text-right">Weight</th>
                    <th className="px-3 py-3 w-28 text-right">Volume</th>
                    <th className="px-3 py-3 w-36 text-right">Unit Price</th>
                    <th className="px-3 py-3 w-40 text-right">Tax settings</th>
                    <th className="px-4 py-3 w-36 text-right">Total</th>
                    <th className="px-4 py-3 w-16 text-center sticky right-0 bg-gray-100 z-20 shadow-[-1px_0_0_0_#e5e7eb]">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {fields.map((field, index) => {
                    const qty = Number(watchItems[index]?.qty) || 0;
                    const price = Number(watchItems[index]?.unit_price) || 0;
                    const taxPerc = Number(watchItems[index]?.tax_percentage) || 0;
                    const taxCal = watchItems[index]?.tax_cal || '1';
                    
                    const baseAmt = qty * price;
                    let rowTotal = 0;
                    if (taxCal === '1') rowTotal = baseAmt + ((baseAmt * taxPerc) / 100);
                    else rowTotal = baseAmt;

                    return (
                      <tr key={field.id} className="bg-white hover:bg-gray-50/50 transition-colors group align-top">
                        <td className="p-3 sticky left-0 bg-white group-hover:bg-gray-50/50 z-10 shadow-[1px_0_0_0_#f3f4f6]">
                          <ProductSearchSelect 
                            products={availableProducts}
                            value={watchItems[index]?.item_id || ''}
                            onChange={(val) => handleProductSelect(index, val)}
                            error={errors.items?.[index]?.item_id?.message}
                          />
                          {errors.items?.[index]?.item_id && <span className="text-[10px] text-red-500 block mt-1">{errors.items[index]?.item_id?.message}</span>}
                        </td>
                        <td className="p-3">
                          <input type="number" min="0" step="any" className={`w-full h-[40px] border rounded-md px-3 py-2 text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm ${errors.items?.[index]?.qty ? 'border-red-300' : 'border-gray-300'}`} {...register(`items.${index}.qty` as const, { valueAsNumber: true })} />
                          {errors.items?.[index]?.qty && <span className="text-[10px] text-red-500 block mt-1 text-right">{errors.items[index]?.qty?.message}</span>}
                        </td>
                        <td className="p-3">
                          <input type="text" className="w-full h-[40px] border border-gray-200 bg-gray-50 rounded-md text-gray-500 font-medium text-center focus:outline-none cursor-not-allowed shadow-sm" {...register(`items.${index}.uom` as const)} readOnly tabIndex={-1} />
                        </td>
                        <td className="p-3">
                          <input type="number" min="0" step="any" className="w-full h-[40px] border border-gray-300 rounded-md px-3 py-2 text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" {...register(`items.${index}.weight` as const, { valueAsNumber: true })} />
                        </td>
                        <td className="p-3">
                          <input type="number" min="0" step="any" className="w-full h-[40px] border border-gray-300 rounded-md px-3 py-2 text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" {...register(`items.${index}.volume` as const, { valueAsNumber: true })} />
                        </td>
                        <td className="p-3">
                          <div className="relative">
                            <span className="absolute left-3 top-2.5 text-gray-400 font-medium pointer-events-none">₹</span>
                            <input type="number" min="0" step="any" className="w-full h-[40px] border border-gray-300 rounded-md pl-7 pr-3 py-2 text-sm text-right font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" {...register(`items.${index}.unit_price` as const, { valueAsNumber: true })} />
                          </div>
                          {watchItems[index]?.item_id && (
                             <div className="flex items-center justify-start gap-1 mt-1.5">
                               <div className="text-[10px] text-gray-700 font-semibold uppercase tracking-wide">LPR: ₹{watchItems[index]?.unit_price}</div>
                               <button 
                                 type="button" 
                                 onClick={() => openLprModal(watchItems[index].item_id, watchItems[index].item_name)}
                                 className="text-red-500 hover:text-red-700 p-0.5 rounded-full hover:bg-red-50 transition-colors"
                                 title="View Last Purchase History"
                               >
                                 <Eye className="w-3.5 h-3.5" />
                               </button>
                             </div>
                          )}
                        </td>
                        <td className="p-3">
                           <div className="flex flex-col gap-2">
                             <select className="w-full h-[40px] border border-gray-300 rounded-md px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm" {...register(`items.${index}.tax_id` as const)} onChange={(e) => {
                               const selectedTax = taxes.find(t => t.id.toString() === e.target.value);
                               setValue(`items.${index}.tax_percentage`, selectedTax ? selectedTax.tax : 0);
                             }}>
                               <option value="">0% GST</option>
                               {taxes.map(t => (
                                 <option key={t.id} value={t.id}>{t.tax}% GST</option>
                               ))}
                             </select>
                             <div className="flex bg-gray-100 rounded p-0.5 text-[10px] font-bold border border-gray-200">
                               <label className={`flex-1 text-center py-1 rounded cursor-pointer transition-colors ${watchItems[index]?.tax_cal === '1' ? 'bg-white shadow-sm text-blue-700 border-gray-200' : 'text-gray-500'}`}>
                                 <input type="radio" className="sr-only" value="1" {...register(`items.${index}.tax_cal` as const)} /> EXC
                               </label>
                               <label className={`flex-1 text-center py-1 rounded cursor-pointer transition-colors ${watchItems[index]?.tax_cal === '0' ? 'bg-white shadow-sm text-blue-700 border-gray-200' : 'text-gray-500'}`}>
                                 <input type="radio" className="sr-only" value="0" {...register(`items.${index}.tax_cal` as const)} /> INC
                               </label>
                             </div>
                           </div>
                        </td>
                        <td className="p-3 text-right">
                          <span className="font-bold text-gray-800 bg-blue-50 px-3 py-2 h-[40px] flex items-center justify-end rounded-md border border-blue-100 tabular-nums">
                            ₹{rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                          </span>
                        </td>
                        <td className="p-3 text-center sticky right-0 bg-white group-hover:bg-gray-50/50 z-10 shadow-[-1px_0_0_0_#f3f4f6]">
                          <button type="button" onClick={() => remove(index)} className="p-2 w-[40px] h-[40px] mx-auto flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-md transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" title="Delete Row">
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
            {errors.items?.root && (
              <div className="p-4 bg-red-50 border-t border-red-100 text-red-700 text-sm flex items-center gap-2 font-medium">
                <Info className="w-5 h-5" /> {errors.items.root.message}
              </div>
            )}
          </div>

          {/* Footer Section */}
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {/* Left Side: Payment Terms & Remarks */}
            <div className="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div className="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                  <CreditCard className="w-5 h-5 text-blue-600" />
                  <h2 className="text-lg font-semibold text-gray-800">Payment Terms</h2>
                </div>
                <textarea 
                  className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow h-28 resize-none shadow-sm" 
                  placeholder="Specify payment milestones or conditions..."
                  {...register('paymentTerms')}
                />
              </div>
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div className="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                  <Info className="w-5 h-5 text-blue-600" />
                  <h2 className="text-lg font-semibold text-gray-800">Internal Remarks</h2>
                </div>
                <textarea 
                  className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow h-28 resize-none shadow-sm" 
                  placeholder="Additional notes (internal use only)..."
                  {...register('remark')}
                />
              </div>
            </div>

            {/* Right Sidebar (Summary Card) */}
            <div className="lg:col-span-4">
              <div className="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div className="bg-gray-800 p-6 text-white shadow-inner">
              <h2 className="text-lg font-bold tracking-wide flex items-center gap-2">
                <ShoppingCart className="w-5 h-5 opacity-70" /> Order Summary
              </h2>
              <p className="text-gray-400 text-xs mt-1.5 font-medium uppercase tracking-wider">Live Calculation</p>
            </div>
            
            <div className="p-6 space-y-4">
              <div className="flex justify-between items-center text-sm">
                <span className="text-gray-500 font-medium">Subtotal</span>
                <span className="font-semibold text-gray-800 tabular-nums">₹{subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
              </div>
              <div className="flex justify-between items-center text-sm">
                <span className="text-gray-500 font-medium">Total Tax</span>
                <span className="font-semibold text-gray-800 tabular-nums">₹{totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
              </div>
              <div className="flex justify-between items-center text-sm">
                <span className="text-gray-500 font-medium">Discount</span>
                <span className="font-semibold text-gray-400 tabular-nums">- ₹0.00</span>
              </div>
              <div className="flex justify-between items-center text-sm">
                <span className="text-gray-500 font-medium">Round Off</span>
                <span className="font-semibold text-gray-400 tabular-nums">₹0.00</span>
              </div>
              
              <div className="border-t border-gray-200 pt-5 mt-3 border-dashed">
                <div className="flex justify-between items-end">
                  <span className="text-gray-800 font-bold uppercase tracking-wider text-sm">Grand Total</span>
                  <span className="text-[28px] leading-none font-black text-blue-700 tracking-tight tabular-nums">₹{grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                </div>
              </div>
            </div>

            <div className="p-6 bg-gray-50 border-t border-gray-200">
              <button 
                type="submit" 
                disabled={isSubmitting}
                className="w-full py-3.5 h-[52px] bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-all shadow-md hover:shadow-lg disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 focus:ring-2 focus:ring-offset-2 focus:ring-blue-600"
              >
                {isSubmitting ? (
                  <><Loader2 className="w-5 h-5 animate-spin" /> Processing...</>
                ) : (
                  <><Save className="w-5 h-5" /> Save Purchase Order</>
                )}
              </button>
            </div>
            </div>
          </div>
        </div>
      </div>

      {/* Add Vendor Modal */}
      {isAddVendorOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" onClick={() => setIsAddVendorOpen(false)} />
          <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto z-50">
            <div className="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-gray-100 rounded-t-2xl z-10">
              <h2 className="text-base font-bold text-gray-800">Add Supplier</h2>
              <button type="button" onClick={() => setIsAddVendorOpen(false)} className="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                <span className="sr-only">Close</span>
                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
            <div className="p-6 space-y-4 text-left">
              {vendorAddError && <div className="text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2">{vendorAddError}</div>}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">Supplier / Vendor Name <span className="text-red-500">*</span></label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.name || ''} onChange={e => setNewVendorForm({...newVendorForm, name: e.target.value})} placeholder="Supplier / Vendor Name" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">Contact Person</label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.contact_person || ''} onChange={e => setNewVendorForm({...newVendorForm, contact_person: e.target.value})} placeholder="Contact Person" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">Phone / Mobile</label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.contact_no || ''} onChange={e => setNewVendorForm({...newVendorForm, contact_no: e.target.value})} placeholder="Phone / Mobile" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.email || ''} onChange={e => setNewVendorForm({...newVendorForm, email: e.target.value})} placeholder="Email" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">GST Number</label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.gst_number || ''} onChange={e => setNewVendorForm({...newVendorForm, gst_number: e.target.value})} placeholder="GST Number" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">PAN Card</label>
                  <input type="text" className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.pancard_number || ''} onChange={e => setNewVendorForm({...newVendorForm, pancard_number: e.target.value})} placeholder="PAN Card" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
                  <select className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={newVendorForm.type || 'Vendor'} onChange={e => setNewVendorForm({...newVendorForm, type: e.target.value})}>
                    <option value="Vendor">Vendor</option>
                    <option value="Supplier">Supplier</option>
                    <option value="Both">Both</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">Address</label>
                <textarea rows={2} className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none" value={newVendorForm.address || ''} onChange={e => setNewVendorForm({...newVendorForm, address: e.target.value})} placeholder="Full address..." />
              </div>
              <div className="flex gap-3 pt-2">
                <button type="button" onClick={handleAddVendor} disabled={isAddingVendor || !newVendorForm.name} className="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                  {isAddingVendor ? 'Saving...' : 'Add Supplier'}
                </button>
                <button type="button" onClick={() => setIsAddVendorOpen(false)} className="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {lprModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
          <div className="bg-white rounded-xl shadow-2xl w-[800px] max-w-[90vw] overflow-hidden">
            <div className="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
              <h3 className="text-lg font-semibold text-blue-900">Last Purchase History</h3>
              <button type="button" onClick={() => setLprModalOpen(false)} className="text-blue-500 hover:text-blue-700 transition-colors">
                <X className="w-5 h-5" />
              </button>
            </div>
            <div className="p-6">
              <div className="flex justify-between items-center mb-4">
                <div className="text-sm font-medium text-gray-700">Item Name: <span className="font-semibold">{lprItemName}</span></div>
                <div className="text-sm font-medium text-gray-700">Print Date: {new Date().toLocaleDateString('en-GB')}</div>
              </div>
              <div className="overflow-x-auto border border-gray-200 rounded-lg">
                <table className="w-full text-left border-collapse text-sm">
                  <thead>
                    <tr className="bg-gray-50 border-b border-gray-200">
                      <th className="py-2.5 px-4 font-semibold text-gray-600">PO No.</th>
                      <th className="py-2.5 px-4 font-semibold text-gray-600">PO Date</th>
                      <th className="py-2.5 px-4 font-semibold text-gray-600">Supplier</th>
                      <th className="py-2.5 px-4 font-semibold text-gray-600 text-right">Quantity</th>
                      <th className="py-2.5 px-4 font-semibold text-gray-600 text-right">Price</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {lprHistory.length > 0 ? (
                      lprHistory.map((h, i) => (
                        <tr key={i} className="hover:bg-gray-50">
                          <td className="py-2.5 px-4 text-gray-800">{h.po_number}</td>
                          <td className="py-2.5 px-4 text-gray-600">{h.generated_date ? new Date(h.generated_date).toLocaleDateString('en-GB') : ''}</td>
                          <td className="py-2.5 px-4 text-gray-800">{h.supplier || '-'}</td>
                          <td className="py-2.5 px-4 text-gray-800 text-right">{h.quantity ? Number(h.quantity).toFixed(2) : '0.00'}</td>
                          <td className="py-2.5 px-4 text-gray-800 text-right font-medium">₹{h.price ? Number(h.price).toFixed(2) : '0.00'}</td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={5} className="py-6 px-4 text-center text-gray-500 italic">No purchase history found for this item.</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
            <div className="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
              <button 
                type="button"
                onClick={() => setLprModalOpen(false)}
                className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </form>
  );
}
