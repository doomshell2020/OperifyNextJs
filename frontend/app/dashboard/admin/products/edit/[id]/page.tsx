'use client';

import React, { useState, useEffect } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { useForm } from 'react-hook-form';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService } from '@/services/settings.service';
import { ArrowLeft, Save, Loader2, AlertCircle } from 'lucide-react';
import Link from 'next/link';

type EditItemFormValues = {
  item_name: string;
  category_id: string;
  item_isbn: string;
  sale_price: string;
  discount: string;
  uom: string;
  weight: string;
  volume: string;
  min_order_qty: string;
  itemtype: 'RawMaterial' | 'FinishedProduct';
  finishedprocess_id: string;
  productprocess_id: string[];
};

export default function EditProductPage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const qc = useQueryClient();
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  const { register, handleSubmit, watch, reset, formState: { errors, isSubmitting } } = useForm<EditItemFormValues>({
    defaultValues: {
      item_name: '',
      category_id: '',
      item_isbn: '',
      sale_price: '',
      discount: '',
      uom: '',
      weight: '',
      volume: '',
      min_order_qty: '',
      itemtype: 'RawMaterial',
      finishedprocess_id: '',
      productprocess_id: [],
    }
  });

  const itemtype = watch('itemtype');

  // Fetch dropdown data
  const { data: categories } = useQuery({ queryKey: ['product-cats'], queryFn: () => settingsService.getProductCategoryList() });
  const { data: uoms } = useQuery({ queryKey: ['product-uoms'], queryFn: () => settingsService.getUomList() });
  const { data: processes } = useQuery({ 
    queryKey: ['product-processes'], 
    queryFn: () => settingsService.getFinishedProcessList() 
  });

  // Fetch existing product data
  const { data: product, isLoading: isLoadingProduct } = useQuery({
    queryKey: ['product', id],
    queryFn: () => settingsService.getProduct(id),
    enabled: !!id
  });

  useEffect(() => {
    if (product) {
      reset({
        item_name: product.item_name || '',
        category_id: product.category_id?.toString() || '',
        item_isbn: product.item_isbn || '',
        sale_price: product.sale_price?.toString() || '',
        discount: product.discount?.toString() || '',
        uom: product.uom?.toString() || '',
        weight: product.weight?.toString() || '',
        volume: product.volume?.toString() || '',
        min_order_qty: product.min_order_qty?.toString() || '',
        itemtype: product.itemtype || 'RawMaterial',
        finishedprocess_id: product.finishedprocess_id?.toString() || '',
        productprocess_id: product.productprocess_id ? product.productprocess_id.split(',') : [],
      });
    }
  }, [product, reset]);

  const updateMutation = useMutation({
    mutationFn: (data: EditItemFormValues) => settingsService.updateProduct(id, data),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['products'] });
      qc.invalidateQueries({ queryKey: ['product', id] });
      router.push('/dashboard/admin/products');
    },
    onError: (error: any) => {
      setErrorMsg(error.response?.data?.message || error.message || 'Failed to update product');
    }
  });

  const onSubmit = (data: EditItemFormValues) => {
    setErrorMsg(null);
    updateMutation.mutate(data);
  };

  if (isLoadingProduct) {
    return (
      <div className="flex items-center justify-center p-12">
        <Loader2 className="w-8 h-8 text-blue-500 animate-spin" />
      </div>
    );
  }

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/admin/products" className="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
          <ArrowLeft className="w-5 h-5 text-slate-600" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Edit Item</h1>
          <p className="text-sm text-slate-500 mt-0.5">Update product details</p>
        </div>
      </div>

      {errorMsg && (
        <div className="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
          <AlertCircle className="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
          <p className="text-sm text-red-700">{errorMsg}</p>
        </div>
      )}

      <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
        <AlertCircle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
        <p className="text-sm text-amber-800">
          <strong>Note:</strong> You can only edit the Item Name, Category, and UOM. All other fields are locked and cannot be modified.
        </p>
      </div>

      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">
                Item Name <span className="text-red-500">*</span>
              </label>
              <input
                {...register('item_name', { required: 'Item name is required' })}
                className="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Enter Item name"
              />
              {errors.item_name && <p className="text-red-500 text-xs mt-1">{errors.item_name.message}</p>}
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Category</label>
              <select
                {...register('category_id')}
                className="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              >
                <option value="">---- Select Category ----</option>
                {categories?.map(c => <option key={c.id} value={c.id}>{c.category_name}</option>)}
              </select>
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">HSN No./Item Code</label>
              <input
                type="number"
                disabled
                {...register('item_isbn')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">Sale Price</label>
              <input
                type="number"
                step="0.01"
                disabled
                {...register('sale_price')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">Discount Amount</label>
              <input
                type="number"
                step="0.01"
                disabled
                {...register('discount')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">
                UOM <span className="text-red-500">*</span>
              </label>
              <select
                {...register('uom', { required: 'UOM is required' })}
                className="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              >
                <option value="">---- Select UOM ----</option>
                {uoms?.map(u => <option key={u.id} value={u.id}>{u.unit_name}</option>)}
              </select>
              {errors.uom && <p className="text-red-500 text-xs mt-1">{errors.uom.message}</p>}
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">Weight</label>
              <input
                disabled
                {...register('weight')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">Volume</label>
              <input
                disabled
                {...register('volume')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-400 mb-1.5">Min. Order Qty</label>
              <input
                type="number"
                disabled
                {...register('min_order_qty')}
                className="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
              />
            </div>
            
            <div className="md:col-span-3 pt-4 border-t border-slate-100">
              <label className="block text-sm font-semibold text-slate-400 mb-2">Item Type :</label>
              <div className="flex items-center gap-6 opacity-60">
                <label className="flex items-center gap-2 cursor-not-allowed">
                  <input
                    type="radio"
                    value="RawMaterial"
                    disabled
                    {...register('itemtype')}
                    className="w-4 h-4 text-slate-400 cursor-not-allowed"
                  />
                  <span className="text-sm text-slate-500">RawMaterial</span>
                </label>
                <label className="flex items-center gap-2 cursor-not-allowed">
                  <input
                    type="radio"
                    value="FinishedProduct"
                    disabled
                    {...register('itemtype')}
                    className="w-4 h-4 text-slate-400 cursor-not-allowed"
                  />
                  <span className="text-sm text-slate-500">FinishedProduct</span>
                </label>
              </div>
            </div>

            {itemtype === 'FinishedProduct' && (
              <div className="md:col-span-3 pt-2 opacity-60">
                <label className="block text-sm font-semibold text-slate-400 mb-3">Process Name :</label>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 bg-slate-100 rounded-xl p-4 border border-slate-200">
                  {processes?.map(proc => (
                    <div key={proc.id} className="flex items-center gap-2">
                      <input
                        type="radio"
                        value={proc.id.toString()}
                        disabled
                        {...register('finishedprocess_id')}
                        className="w-4 h-4 text-slate-400 cursor-not-allowed"
                      />
                      <input
                        type="checkbox"
                        value={proc.id.toString()}
                        disabled
                        {...register('productprocess_id')}
                        className="w-4 h-4 rounded border-slate-300 text-slate-400 cursor-not-allowed"
                      />
                      <span className="text-xs font-medium text-slate-500 uppercase tracking-wide">{proc.process_name}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}

          </div>
          
          <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
            <Link href="/dashboard/admin/products" className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
              Cancel
            </Link>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors shadow-sm shadow-blue-600/20"
            >
              {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
              Update Item
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
