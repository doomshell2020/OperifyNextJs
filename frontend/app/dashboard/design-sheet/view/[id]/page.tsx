'use client';

import {useParams} from 'next/navigation';
import {useQuery} from '@tanstack/react-query';
import Link from 'next/link';
import toast from 'react-hot-toast';
import {designsheetService} from '@/services/designsheet.service';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useLegacyActionAccess} from '@/components/ui/useLegacyActionAccess';
import {formatContractDate} from '@/utils/dateFormatter';
import {formatQty} from '@/utils/formatters';
import {openModulePdf} from '@/services/pdf.service';

export default function DesignSheetDetailsPage() {
  const params = useParams<{id:string}>();
  const id = params.id;
  const can = useLegacyActionAccess();
  const {data, isLoading, isError} = useQuery({queryKey:['designsheet-details',id],queryFn:()=>designsheetService.getDesignSheetDetailsById(id)});
  if (isLoading) return <p role="status">Loading design sheet…</p>;
  if (isError || !data?.designsheet) return <div role="alert"><p>Unable to load this design sheet. It may not exist or you may not have access.</p><Link href="/dashboard/design-sheet">Back to Design Sheets</Link></div>;
  const sheet = data.designsheet;
  return <main className="max-w-5xl mx-auto p-6 space-y-6">
    <LegacyPageHeader title="Design Sheet Details" />
    <div className="flex justify-between gap-3">
      <Link href="/dashboard/design-sheet" className="legacy-button">Back</Link>
      {(can('designsheet','index') || can('designsheet','viewdesignsheet')) && <button type="button" className="legacy-button" onClick={()=>void openModulePdf(`/designsheets/records/${encodeURIComponent(id)}/pdf`)}>Print PDF</button>}
    </div>
    <div className="grid grid-cols-2 gap-4 text-sm">
      <p><b>Design Sheet No:- </b>{sheet.designsheetno}</p><p><b>Issue Date:- </b>{formatContractDate(sheet.datefrom)}</p>
      <p><b>Contract:- </b>{sheet.contract_no}</p><p><b>Finished Product:- </b>{sheet.item_name}</p>
      <p><b>Quantity:- </b>{formatQty(sheet.quantity)} KM</p>
    </div>
    <h2 className="text-center font-bold">Raw Material</h2>
    <table><thead><tr>{['S.No.','Item Name','Qty(Per KM)','Total Qty','UOM'].map(label=><th key={label}>{label}</th>)}</tr></thead>
      <tbody>{data.designsheetdetails.map((item: {id:number;item_name:string;km_item_qty:number;item_qty:number;uom:string},index:number)=><tr key={item.id}><td>{index+1}.</td><td>{item.item_name}</td><td>{formatQty(item.km_item_qty)}</td><td>{formatQty(item.item_qty)}</td><td>{item.uom}</td></tr>)}</tbody>
    </table>
    <div className="space-y-2"><h2 className="font-bold">Uploaded Files and Revisions</h2>
      {['design_sheet','r1','r2','r3','r4','r5'].filter(field=>sheet[field]).map(field=><p key={field}><button type="button" className="text-blue-600 underline" onClick={()=>void designsheetService.downloadFile(sheet.id,field).catch(()=>toast.error('Unable to download file'))}>{field==='design_sheet'?'Download Design Sheet':`Download ${field.toUpperCase()}`}</button></p>)}
      {!['design_sheet','r1','r2','r3','r4','r5'].some(field=>sheet[field]) && <p>No uploaded files.</p>}
    </div>
  </main>;
}
