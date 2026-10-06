const ExcelJS=require('exceljs');
const {QueryTypes}=require('sequelize');
module.exports=async(db,input,res)=>{
  const {data}=await require('./products.list')(db,input,true);
  // PHP InhandStock includes all ledger statuses; do not substitute the optional stock cache.
  const stock=data.length ? await db.query(`SELECT item_id,
    ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END),2)-
    ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END),2) AS quantity
    FROM st_stock_register WHERE item_id IN (:ids) GROUP BY item_id`,{replacements:{ids:data.map(row=>row.id)},type:QueryTypes.SELECT}) : [];
  const quantities=new Map(stock.map(row=>[String(row.item_id),Number(row.quantity)||0]));
  const workbook=new ExcelJS.Workbook(),sheet=workbook.addWorksheet('Items');
  sheet.columns=[{header:'Unique Id',key:'id',width:10},{header:'Product Name',key:'item_name',width:40},{header:'Category',key:'category_name',width:25},{header:'Item Type',key:'itemtype',width:15},{header:'UOM',key:'uom_name',width:15},{header:'Current Stock',key:'stock',width:15}];
  for(const row of data)sheet.addRow({...row,category_name:String(row.category_name||'').replace(/^./,char=>char.toUpperCase()),stock:quantities.get(String(row.id))||0});
  const now=new Date();
  const date=`${String(now.getDate()).padStart(2,'0')}-${String(now.getMonth()+1).padStart(2,'0')}-${now.getFullYear()}`;
  res.setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.setHeader('Content-Disposition',`attachment; filename="Items_Summary_${date}.xlsx"`);
  await workbook.xlsx.write(res);res.end();
};
