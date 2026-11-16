const ExcelJS = require('exceljs');
const R = require('./productionOrder.repository');
const { date } = require('../../utils/legacyPdf');
const headers = ['PO NO.','Date Created','Contract Name','Product','Start Date','End Date','Planned Qty','Prepared Qty','Pending Qty','Status'];
// PHP number_format rounds half away from zero (unlike binary toFixed ties).
const rounded = value => Math.sign(value) * Math.round((Math.abs(value) + Number.EPSILON * Math.abs(value)) * 100) / 100;
function values(row) {
  const planned = Number(row.plannedqty || 0), prepared = Number(row.prepared_qty || 0);
  return [row.po_id,date(row.issuedate),(row.title || '')+'('+(row.workorder || '')+')',row.item_name || '',date(row.startdate),date(row.enddate),rounded(planned),rounded(prepared),rounded(planned-prepared),row.status === 'C' ? 'Close' : 'Open'];
}
async function excel(db, query, res) {
  R.conditions(query); // Validate before sending download headers.
  res.type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.set('Content-Disposition','attachment; filename="Production_order_'+date(new Date())+'.xlsx"');
  res.set('Cache-Control','no-store');
  const book = new ExcelJS.stream.xlsx.WorkbookWriter({stream:res,useStyles:true,useSharedStrings:false});
  const sheet = book.addWorksheet('Worksheet');
  sheet.columns = headers.map((header,index)=>({header,width:[5,12,15,15,15,15,15,15,15,15][index]}));
  sheet.getRow(1).commit();
  // A consistent snapshot and bounded batches avoid buffering the full export.
  await db.transaction({readOnly:true,isolationLevel:'REPEATABLE READ'},async transaction=>{
    for (let offset=0; !res.destroyed; offset+=1000) {
      const rows = await R.rows(db,query,{limit:1000,offset},transaction,true);
      for (const row of rows) {
        const output = sheet.addRow(values(row));
        for (let index=7;index<=9;index++) output.getCell(index).numFmt='0.00';
        output.commit();
      }
      if (rows.length<1000) break;
    }
  });
  sheet.commit();
  await book.commit();
}
module.exports = {excel,headers,values};
