const ExcelJS = require('exceljs');
const { QueryTypes } = require('sequelize');
const { logoSrc, date } = require('../../utils/legacyPdf');

async function workbookFor(db, name, columns, dailyDate) {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet(name);
  sheet.columns = columns.map(([header,key,width])=>({header,key,width}));
  sheet.spliceRows(1,0,[],...(dailyDate ? [[]] : []));
  sheet.mergeCells(1,1,1,columns.length);
  const [site] = await db.query("SELECT * FROM sitesettings_details WHERE status='Y' LIMIT 1", {type:QueryTypes.SELECT});
  sheet.getCell('A1').value = site?.ac_holder || '';
  sheet.getCell('A1').font = {bold:true,size:14};
  sheet.getCell('A1').alignment = {horizontal:'center',vertical:'middle'};
  sheet.getRow(1).height = 50;
  const logo = logoSrc(site || {});
  if (logo) {
    const extension = logo.match(/^data:image\/(\w+)/)?.[1];
    if (['png','jpeg','gif'].includes(extension)) {
      const image = workbook.addImage({base64:logo,extension});
      sheet.addImage(image,{tl:{col:0,row:0},ext:{width:80,height:40}});
    }
  }
  sheet.getRow(dailyDate ? 3 : 2).font = {bold:true};
  if(dailyDate) {
    sheet.getCell('B2').value = `Date:${date(dailyDate)}`;
    sheet.getCell('B2').font = {bold:true};
    sheet.views = [{state:'frozen',ySplit:3}];
  }
  for(const column of sheet.columns) if(['opening','received','issued','reverse','return_qty','dispatched','closing'].includes(column.key))column.numFmt='0.00';
  return {workbook,sheet};
}
async function send(workbook,res,filename) {
  res.setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.setHeader('Content-Disposition',`attachment; filename="${filename}"`);
  res.setHeader('Cache-Control','max-age=0');
  await workbook.xlsx.write(res);
  res.end();
}
async function detailed(db,filters,rows,res) {
  const columns=[['#','sno',5],['Item ID','item_id',15],[filters.product_id ? 'DATE' : 'Date','date',15],
    ...(!filters.product_id ? [['Product Name','item_name',30],['Category','category_name',20]] : []),
    ['Opening Stock','opening',15],['Received Stock','received',15],['Dispatched Stock','dispatched',15],['Closing Stock','closing',15],
    ...(!filters.product_id ? [['Physical Stock','physical',15]] : [])];
  const {workbook,sheet}=await workbookFor(db,'Stock Register',columns);
  rows.forEach((row,index)=>sheet.addRow({sno:index+1,item_id:row.item_id,date:date(row.date_range),item_name:row.item_name,category_name:row.category_name,opening:Number(row.opening_stock),received:Number(row.received_stock),dispatched:Number(row.dispatched_stock),closing:Number(row.closing_stock),physical:''}));
  await send(workbook,res,filters.product_id ? 'Export_Summary_Stock_Item.xlsx' : 'Export_Consolidated_Stock_Register.xlsx');
}
async function daily(db,filters,rows,res) {
  // PHP's export helpers use created < midnight for reverse/return, unlike its daily page.
  const extras = rows.length ? await db.query(`SELECT item_id,
    ROUND(SUM(CASE WHEN store_type='3' THEN quantity ELSE 0 END),2) AS reverse_qty,
    ROUND(SUM(CASE WHEN store_type='4' AND status!='N' THEN quantity ELSE 0 END),2) AS return_qty
    FROM st_stock_register WHERE created < :date AND item_id IN (:ids) GROUP BY item_id`,{replacements:{date:filters.date,ids:rows.map(row=>row.item_id)},type:QueryTypes.SELECT}) : [];
  const lookup=new Map(extras.map(row=>[String(row.item_id),row]));
  const columns=[['ID','item_id',8],['Product Name','item_name',50],['Category','category_name',15],['Opening Stock','opening',15],['Received Stock','received',15],['Issued Stock','issued',15],['Reverse Stock','reverse',15],['Return Stock','return_qty',15],['Closing Stock','closing',15]];
  const {workbook,sheet}=await workbookFor(db,'Daily Stock',columns,filters.date);
  for(const row of rows) {
    const extra=lookup.get(String(row.item_id)) || {};
    const received=Number(row.received_stock),issued=Number(row.issued_stock),reverse=Number(extra.reverse_qty)||0,returned=Number(extra.return_qty)||0;
    if(!filters.category_ids?.length && received===0 && issued===0 && reverse===0 && returned===0)continue;
    sheet.addRow({item_id:row.item_id,item_name:row.item_name,category_name:row.category_name,opening:received-issued,received,issued,reverse,return_qty:returned,closing:received-issued});
  }
  await send(workbook,res,`Stock_report-${filters.date}.xlsx`);
}
module.exports={detailed,daily};
