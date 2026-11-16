const P=require('../../utils/legacyPdf');
const R=require('./maintenance.repository');
const cap=value=>P.ucfirst(String(value ?? '').toLowerCase());
const headers=['No.','Date','Machine Name','Type Of Breakdown','Time(Hrs)','Assigned To','Shift Incharge','Maintenance Incharge','Production Head','Status','Remark'];
const widths=[29,55,120,74,33,76,76,76,76,49,140];
// viewpdf.ctp prints stored personnel values, whereas the listing resolves user names.
const values=(r,i)=>[i+1,P.date(r.datefrom),P.ucfirst(r.machine_name),cap(r.breakdown_type),r.total_time,cap(r.assigned_to),cap(r.shift_incharge),cap(r.maintenance_incharge),cap(r.production_head),P.ucfirst(r.maintenance_status),cap(r.remark)];
function html(rows,query={}) {
  const e=P.escapeHtml, short=v=>v && v!=='1970-01-01'?P.date(R.date(v)).replace(/\d{4}$/,s=>s.slice(2)):'';
  return P.document('<h3>Maintenance Details</h3><p>Assigned By- '+e(P.ucfirst(query.assigned_to))+' &nbsp; Maintenance Status-'+e(P.ucfirst(query.m_status))+' &nbsp; Date From-'+e(short(query.datefrom))+' &nbsp; Date To-'+e(short(query.dateto))+'</p><p class="right">Print Date-'+e(short(R.today()))+'</p><table class="grid"><colgroup>'+widths.map(w=>'<col style="width:'+w/804*100+'%">').join('')+'</colgroup><thead><tr>'+headers.map(h=>'<th>'+e(h)+'</th>').join('')+'</tr></thead><tbody>'+rows.map((r,i)=>'<tr>'+values(r,i).map(v=>'<td>'+e(v)+'</td>').join('')+'</tr>').join('')+'</tbody></table>', '@page{size:A4 landscape;margin:10mm 10mm 20mm}h3{text-align:center;font-size:14px}th,td{overflow-wrap:anywhere}');
}
module.exports={headers,values,html,generate:async(db,query)=>P.render(html(await R.rows(db,query),query),{landscape:true,timeout:120000})};
