"use client";

import React, { useState, useEffect } from 'react';
import { useMutation, useQueryClient, useQuery } from '@tanstack/react-query';
import purchaseOrderService, { PurchaseOrderItem } from '../../services/purchaseOrder.service';
import { Loader, X } from 'lucide-react';
import toast from 'react-hot-toast';
import { DatePicker } from '../ui/DatePicker';

interface DeliveryNoteModalProps {
  poId: number;
  onClose: () => void;
}

export function DeliveryNoteModal({ poId, onClose }: DeliveryNoteModalProps) {
  const queryClient = useQueryClient();

  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order-delivery-data', poId],
    queryFn: () => purchaseOrderService.getDeliveryData(poId),
    enabled: !!poId,
  });

  const [remark, setRemark] = useState('');
  const [schedules, setSchedules] = useState<any[]>(
    Array(4).fill(null).map(() => ({ inwarddate: '', items: {} }))
  );

  useEffect(() => {
    if (data) {
      if (data.po.remark) setRemark(data.po.remark);
      
      const newSchedules = Array(4).fill(null).map(() => ({ inwarddate: '', items: {} as Record<string, number> }));
      
      data.items.forEach((item: any) => {
        for (let i = 0; i < 4; i++) {
          newSchedules[i].items[item.item_id] = 0;
        }
      });

      const savedSchedules = data.schedules || [];
      if (savedSchedules.length > 0) {
        // Group by date
        const dateGroups = Array.from(new Set(savedSchedules.map((s: any) => s.delivery_date)));
        
        dateGroups.forEach((date: any, index: number) => {
          if (index < 4) {
            newSchedules[index].inwarddate = date.split('T')[0];
            const itemsForDate = savedSchedules.filter((s: any) => s.delivery_date === date);
            itemsForDate.forEach((s: any) => {
              newSchedules[index].items[s.item_id] = Number(s.item_qty);
            });
          }
        });
        
        if (savedSchedules[0] && savedSchedules[0].remark) {
           setRemark(savedSchedules[0].remark);
        }
      }

      setSchedules(newSchedules);
    }
  }, [data]);

  const addDeliveryMutation = useMutation({
    mutationFn: (payload: any) => purchaseOrderService.addDeliveryNote(poId, payload),
    onSuccess: () => {
      toast.success('Delivery Schedule added successfully.');
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-delivery-data', poId] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-details', poId] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-revision-data', poId] });
      onClose();
    },
    onError: (error: any) => toast.error(error.response?.data?.message || 'Unable to save delivery schedule.')
  });

  const handleDateChange = (index: number, val: string) => {
    let isDuplicate = false;
    for (let i = 0; i < 4; i++) {
      if (i !== index && schedules[i].inwarddate === val && val !== '') {
        isDuplicate = true;
        break;
      }
    }
    if (isDuplicate) {
      alert("Each date must be unique. Please select different dates.");
      return;
    }
    const newSchedules = [...schedules];
    newSchedules[index].inwarddate = val;
    setSchedules(newSchedules);
  };

  const handleQtyChange = (dateIndex: number, itemId: number, val: string) => {
    const qty = parseFloat(val) || 0;
    
    let sum = 0;
    for (let i = 0; i < 4; i++) {
      if (i === dateIndex) {
        sum += qty;
      } else {
        sum += schedules[i].items[itemId] || 0;
      }
    }
    
    const item = data?.items.find((i: any) => i.item_id === itemId);
    const totalQty = Number(item?.order_qty) || 0;

    if (sum > totalQty) {
      alert(`Total schedule quantity can not be greater then ${totalQty}`);
      const maxQty = totalQty - (sum - qty);
      const newSchedules = [...schedules];
      newSchedules[dateIndex].items[itemId] = maxQty;
      setSchedules(newSchedules);
      return;
    }

    const newSchedules = [...schedules];
    newSchedules[dateIndex].items[itemId] = qty;
    setSchedules(newSchedules);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!data) return;

    let formValid = true;
    
    data.items.forEach((item: any) => {
      let sum = 0;
      let dateValid = true;
      for (let i = 0; i < 4; i++) {
        const qty = schedules[i].items[item.item_id] || 0;
        if (qty > 0 && !schedules[i].inwarddate) {
          alert(`Date${i + 1} cannot be blank`);
          dateValid = false;
          formValid = false;
          break;
        }
        sum += qty;
      }
      if (dateValid && sum < Number(item.order_qty)) {
        alert(`Total quantity cannot be less than ${item.order_qty}`);
        formValid = false;
      }
    });

    if (!formValid) return;

    const payloadSchedules = schedules.map(s => {
      return {
        inwarddate: s.inwarddate,
        items: Object.keys(s.items).map(itemId => ({ item_id: Number(itemId), qty: s.items[Number(itemId)] }))
      }
    });

    addDeliveryMutation.mutate({
      po_number: data.po.po_number,
      vendor_id: (data.po as any).vendor_id,
      remark,
      schedules: payloadSchedules
    });
  };

  if (isError || (!isLoading && !data)) {
    return <div className="fixed inset-0 z-[10000] flex items-center justify-center bg-black/40 p-4"><div className="bg-white p-6 w-full max-w-[900px]" role="alert"><p>Unable to load the delivery schedule.</p><button type="button" onClick={onClose}>Close</button></div></div>;
  }

  if (!poId || isLoading || !data) {
    return (
      <div className="fixed inset-0 z-[10000] flex items-center justify-center bg-black/40">
        <Loader className="w-8 h-8 animate-spin text-white" />
      </div>
    );
  }

  return (
    <div className="fixed inset-0 z-[10000] flex justify-center items-start overflow-y-auto bg-black/40 pt-10 pb-10">
      <div className="bg-white rounded w-[95%] max-w-[1200px] shadow-lg relative">
        <div className="bg-[#00c0ef] border-[#0097bc] text-white p-3 border-b flex justify-between items-center rounded-t">
          <h3 className="m-0 text-[18px] font-normal flex items-center gap-2">
             <i className="fa fa-plus-square"></i> Delivery Note For Purchase Order id : {data.po.po_number}
          </h3>
          <button onClick={onClose} className="text-white hover:text-gray-200">
             <X className="w-5 h-5" />
          </button>
        </div>
        
        <form onSubmit={handleSubmit} className="p-4" style={{ fontFamily: '"Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif' }}>
          <div className="flex flex-wrap -mx-3 mb-4">
            <div className="w-full md:w-1/4 px-3 mb-4 md:mb-0">
              <label className="block text-sm font-bold mb-2">PO Date <strong className="text-red-600">*</strong></label>
              <input type="text" readOnly value={new Date(data.po.po_date).toLocaleDateString('en-GB').replace(/\//g, '-')} className="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" />
            </div>
            <div className="w-full md:w-1/4 px-3 mb-4 md:mb-0">
              <label className="block text-sm font-bold mb-2">Expected Delivery Date<strong className="text-red-600">*</strong></label>
              <input type="text" readOnly value={new Date(data.po.delivery_date).toLocaleDateString('en-GB').replace(/\//g, '-')} className="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" />
            </div>
            <div className="w-full md:w-1/4 px-3">
              <label className="block text-sm font-bold mb-2">Supplier <strong className="text-red-600">*</strong></label>
              <input type="text" readOnly value={data.po.vendor_name} className="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" />
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-sm font-bold mb-2">Items</label>
            <div className="overflow-x-auto">
              <table className="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                  <tr className="bg-[#c8c8c8] text-[#333333]">
                    <th className="border border-gray-300 p-2 text-left">Item</th>
                    <th className="border border-gray-300 p-2 text-left">PO Qty</th>
                    <th className="border border-gray-300 p-2 text-left">Date1</th>
                    <th className="border border-gray-300 p-2 text-left">Qty</th>
                    <th className="border border-gray-300 p-2 text-left">Date2</th>
                    <th className="border border-gray-300 p-2 text-left">Qty</th>
                    <th className="border border-gray-300 p-2 text-left">Date3</th>
                    <th className="border border-gray-300 p-2 text-left">Qty</th>
                    <th className="border border-gray-300 p-2 text-left">Date4</th>
                    <th className="border border-gray-300 p-2 text-left">Qty</th>
                  </tr>
                </thead>
                <tbody>
                  {data.items.map((item: any, index: number) => (
                    <tr key={item.item_id} className={index % 2 === 0 ? "bg-[#f2f2f2]" : ""}>
                      <td className="border border-gray-300 p-2 w-[19%]">
                        <input type="text" readOnly value={item.item_name} className="w-full px-2 py-1 border border-gray-300 rounded bg-gray-100 outline-none" />
                      </td>
                      <td className="border border-gray-300 p-2 w-[9%]">
                         <input type="text" readOnly value={Number(item.order_qty).toFixed(2)} className="w-full px-2 py-1 border border-gray-300 rounded bg-gray-100 outline-none" />
                      </td>
                      {[0, 1, 2, 3].map(i => (
                        <React.Fragment key={i}>
                          <td className="border border-gray-300 p-2 w-[9%]">
                            <DatePicker
                              aria-label={`Delivery date ${i + 1}`}
                              min={data.po.po_date?.split('T')[0]} 
                              max={data.po.delivery_date?.split('T')[0]}
                              value={schedules[i].inwarddate} 
                              onChange={e => handleDateChange(i, e.target.value)}
                              className="w-full px-2 py-1 border border-gray-300 rounded outline-none" 
                            />
                          </td>
                          <td className="border border-gray-300 p-2 w-[9%]">
                            <input 
                              type="number" 
                              step="0.01"
                              min="0"
                              readOnly={data.schedules?.some(row=>row.status==='N' && Number(row.item_id)===Number(item.item_id) && row.delivery_date?.split('T')[0]===schedules[i].inwarddate)}
                              value={schedules[i].items[item.item_id] || ''} 
                              onChange={e => handleQtyChange(i, item.item_id, e.target.value)}
                              className="w-full px-2 py-1 border border-gray-300 rounded outline-none" 
                              required={schedules[i].inwarddate !== ''}
                            />
                          </td>
                        </React.Fragment>
                      ))}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          <div className="mb-4">
             <label className="block text-sm font-bold mb-2">Remark</label>
             <textarea value={remark} onChange={e => setRemark(e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded outline-none" rows={3}></textarea>
          </div>

          <div className="flex justify-end gap-2 border-t pt-4">
             <button type="button" onClick={onClose} className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded border border-gray-300 font-semibold text-sm">Back</button>
             <button type="submit" disabled={addDeliveryMutation.isPending} className="px-4 py-2 bg-[#00c0ef] hover:bg-[#0097bc] text-white rounded font-semibold text-sm">Submit</button>
          </div>
        </form>
      </div>
    </div>
  );
}
