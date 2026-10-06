const V=require('../../utils/receiptValidation');
module.exports=async(db,po,items,transaction)=>{
  if(!po?.vendor_id || !Array.isArray(items) || !items.length)throw V.invalid('Supplier and PO items are required.');
  const vendors=await V.select(db,'SELECT id FROM vendors WHERE id=:id LIMIT 1',{id:po.vendor_id},transaction);
  if(!vendors.length)throw V.invalid('Your entered Supplier does not exist.');
  const details=[];
  for(const item of items){
    const quantity=V.number(item.item_qty,'PO quantity',true),price=V.number(item.item_amt,'PO rate');
    const products=await V.select(db,'SELECT id FROM st_additem WHERE id=:id LIMIT 1',{id:item.item_id},transaction);
    if(!products.length)throw V.invalid('PO item does not exist.');
    const [tax]=item.tax_id ? await V.select(db,'SELECT tax FROM st_taxmaster WHERE id=:id LIMIT 1',{id:item.tax_id},transaction) : [];
    const percentage=V.number(tax?.tax ?? 0,'Tax percentage');
    const base=quantity*price;
    // The existing Next form uses 0=INC / 1=EXC; PHP performs these same formulas.
    const included=String(item.tax_cal)==='0';
    const amount=included ? base-base*(100/(100+percentage)) : base*percentage/100;
    details.push({...item,item_qty:quantity,item_amt:price,item_base_price:base,tax_percentage:percentage,item_tax_amt:amount,item_total_amount:included ? base : base+amount});
  }
  return {po:{...po,postatus:'O',total_qty:details.reduce((sum,row)=>sum+row.item_qty,0),total_tax:details.reduce((sum,row)=>sum+row.item_tax_amt,0),total_amt:details.reduce((sum,row)=>sum+row.item_total_amount,0)},items:details};
};
