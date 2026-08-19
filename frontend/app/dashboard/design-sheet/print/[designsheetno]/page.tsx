'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { designsheetService } from '../../../../../services/designsheet.service';
import { formatDate } from '../../../../../utils/dateFormatter';

export default function PrintDesignSheetPage() {
  const { designsheetno } = useParams() as { designsheetno: string };
  const [data, setData] = useState<any>(null);

  useEffect(() => {
     if (designsheetno) {
         designsheetService.getDesignSheetForView(designsheetno).then(res => {
             setData(res);
             setTimeout(() => window.print(), 1000);
         });
     }
  }, [designsheetno]);

  if (!data) return <div className="p-10 text-center font-bold">Loading Print View...</div>;

  const { designsheet, designsheetdetails, sitesetting, site_details } = data;

  return (
    <div className="p-8 bg-white max-w-4xl mx-auto text-black font-sans print:p-0">
      <div className="text-center mb-6 border-b-2 border-black pb-4">
        {sitesetting?.logo && (
            <img src={`/${sitesetting.logo}`} alt="Logo" className="h-16 mx-auto mb-2" />
        )}
        <h1 className="text-2xl font-bold uppercase">{site_details?.company_name || 'COMPANY NAME'}</h1>
        <p className="text-sm">{site_details?.address1}</p>
        <p className="text-sm">GSTIN: {site_details?.gst_no} | STATE: {site_details?.state_code}</p>
        <h2 className="text-xl font-bold mt-4 underline uppercase">Production Sheet</h2>
      </div>

      <div className="grid grid-cols-2 gap-4 mb-6 text-sm">
        <div><span className="font-bold">Production Sheet No:</span> {designsheet?.designsheetno}</div>
        <div><span className="font-bold">Date:</span> {designsheet?.datefrom ? formatDate(designsheet.datefrom) : ''}</div>
        <div><span className="font-bold">Contract No:</span> {designsheet?.contract_no}</div>
        <div><span className="font-bold">Item Name:</span> {designsheet?.item_name}</div>
        <div><span className="font-bold">Total Quantity:</span> {designsheet?.quantity}</div>
      </div>

      <table className="w-full text-left border-collapse border border-black text-sm">
        <thead>
          <tr>
            <th className="border border-black p-2 bg-gray-100">S.No.</th>
            <th className="border border-black p-2 bg-gray-100">Item Group</th>
            <th className="border border-black p-2 bg-gray-100">Item Name</th>
            <th className="border border-black p-2 bg-gray-100">Item Qty per KM</th>
            <th className="border border-black p-2 bg-gray-100">Total Item Qty</th>
          </tr>
        </thead>
        <tbody>
          {designsheetdetails?.map((item: any, idx: number) => (
            <tr key={item.id}>
              <td className="border border-black p-2 text-center">{idx + 1}</td>
              <td className="border border-black p-2 text-center">{item.is_group === '1' ? 'YES' : 'NO'}</td>
              <td className="border border-black p-2">{item.item_name}</td>
              <td className="border border-black p-2 text-right">{item.km_item_qty}</td>
              <td className="border border-black p-2 text-right">{item.item_qty} {item.uom}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <div className="mt-16 flex justify-between text-sm font-bold">
        <div>Prepared By</div>
        <div>Checked By</div>
        <div>Authorized Signatory</div>
      </div>
    </div>
  );
}
