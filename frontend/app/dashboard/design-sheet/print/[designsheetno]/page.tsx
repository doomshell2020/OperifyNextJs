'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { designsheetService } from '../../../../../services/designsheet.service';
import { formatContractDate } from '../../../../../utils/dateFormatter';

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

  const logoSrc = 'http://localhost:5000/public/uploads/logos/d80960ce77aede66a5c3c8eef8dfafda.png';

  return (
    <div className="bg-white max-w-4xl mx-auto text-black font-sans text-[8px] leading-[10px] p-8 print:p-0">
      <style>{`
        @media print {
          @page { size: A4 portrait; margin: 10mm; }
          body { margin: 0; background: #fff; }
        }
      `}</style>

      <div className="border border-black">
        <div className="relative h-[96px] border-b border-black">
          <img src={logoSrc} alt="" className="absolute top-[25px] left-4 h-[42px] max-w-[110px] object-contain" />
          <div className="absolute left-4 bottom-[9px] font-bold text-[10px] leading-3">
            {sitesetting?.first_name || site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}
          </div>
          <div className="absolute top-1 right-0 w-1/2 text-center text-[7px] leading-[9px]">
            {site_details?.address1}<br />
            <b>Phone</b> :{site_details?.phone}<br />
            <b>Email</b> : <u>{site_details?.email}</u><br />
            <b>Website</b> : {site_details?.website}
          </div>
        </div>
        <h2 className="text-center text-[10px] leading-[15px] h-[15px] font-bold border-b border-black">Design Sheet Details</h2>
        <table className="w-full border-collapse">
          <tbody>
            <tr>
              <td className="w-1/2 p-[3px]"><b>Design Sheet No:-</b> {designsheet?.designsheetno}</td>
              <td className="w-1/2 p-[3px]"><b>Issue Date:-</b> {designsheet?.datefrom ? formatContractDate(designsheet.datefrom) : ''}</td>
            </tr>
            <tr>
              <td className="w-1/2 p-[3px]"><b>Contract:-</b> {designsheet?.contract_no}</td>
              <td className="w-1/2 p-[3px]"><b>Finished Product:-</b> {designsheet?.item_name}</td>
            </tr>
            <tr>
              <td className="w-1/2 p-[3px]"><b>Quantity:-</b> {designsheet?.quantity} KM</td>
              <td className="w-1/2 p-[3px]"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <h6 className="text-center text-[10px] leading-3 font-bold mt-3 mb-0.5">Raw Material</h6>
      <table className="w-full text-left border-collapse border border-black text-[8px]">
        <thead>
          <tr>
            <th className="border border-black p-[3px] w-[5%]">S.No.</th>
            <th className="border border-black p-[3px] w-[61%]">Item Name</th>
            <th className="border border-black p-[3px] w-[13%] text-right">Qty(Per KM)</th>
            <th className="border border-black p-[3px] w-[11%]">Total Qty</th>
            <th className="border border-black p-[3px] w-[10%]">UOM</th>
          </tr>
        </thead>
        <tbody>
          {designsheetdetails?.map((item: any, idx: number) => (
            <tr key={item.id}>
              <td className="border border-black p-[3px] font-bold">{idx + 1}.</td>
              <td className="border border-black p-[3px]">{item.item_name}</td>
              <td className="border border-black p-[3px] text-right">{Number(item.km_item_qty || 0).toFixed(2)}</td>
              <td className="border border-black p-[3px]">{Number(item.item_qty || 0).toFixed(2)}</td>
              <td className="border border-black p-[3px]">{item.uom}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
