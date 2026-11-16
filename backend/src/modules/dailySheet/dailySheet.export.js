const ExcelJS=require('exceljs');
const R=require('./dailySheet.repository');
const {date}=require('../../utils/legacyPdf');
const headers=['Date','Machine Name','Contract Name','Finished Product','PO NO.','Process Name','Planned Qty(KM)','Day Production(KM)','Night Production(KM)','Total Production(KM)','Reading 8.00AM(Current Day)','Reading 8.00PM(Current Day)','Reading 8.00AM(Next Day)','Total Manpower'];
const round=v=>Math.sign(v)*Math.round((Math.abs(v)+Number.EPSILON*Math.abs(v))*100)/100;
function values(r) {
  return [date(r.production_date),r.machine_name || '',(r.title || '')+'('+(r.workorder || '')+')',r.item_name || '',r.po_id,r.process_name || '',...['plan_qty','production_shift_a','production_shift_b'].map(k=>round(Number(r[k] || 0))),round(Number(r.production_shift_a || 0)+Number(r.production_shift_b || 0)),...['reading8am','reading8pm','nextday8am'].map(k=>round(Number(r[k] || 0))),Number(r.manpower_night || 0)+Number(r.manpower_day || 0)];
}
async function excel(db,query,res) {
  R.conditions(query);
  res.type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.set('Content-Disposition','attachment; filename="Daily_Sheet_'+date(R.today())+'.xlsx"');
  res.set('Cache-Control','no-store');
  const book=new ExcelJS.stream.xlsx.WorkbookWriter({stream:res,useStyles:true,useSharedStrings:false});
  let sheet,number=0,rowCount=0;
  const startSheet=()=>{
    sheet=book.addWorksheet(++number===1?'Worksheet':'Worksheet '+number);
    sheet.columns=headers.map((header,i)=>({header,width:[12,25,25,30,12,18,15,15,15,15,15,15,9.14,15][i]}));
    sheet.getRow(1).commit();rowCount=1;
  };
  startSheet();
  await db.transaction({readOnly:true,isolationLevel:'REPEATABLE READ'},async t=>{
    for(let offset=0;!res.destroyed;offset+=1000) {
      const rows=await R.rows(db,query,{limit:1000,offset},t);
      for(const item of rows) {
        if(rowCount===1048576) {sheet.commit();startSheet();}
        const row=sheet.addRow(values(item));
        for(let i=7;i<=13;i++) row.getCell(i).numFmt='0.00';
        row.commit();rowCount++;
      }
      if(rows.length<1000) break;
    }
  });
  sheet.commit();await book.commit();
}
module.exports={headers,values,excel};
