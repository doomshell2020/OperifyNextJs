const V=require('../../utils/receiptValidation');
async function update(db,indentId,data,userId){
  V.date(data.issue_date,'Issue date');
  if(!data.machine_id || !String(data.issued_name || '').trim())throw V.invalid('Machine and issued name are required.');
  if(!Array.isArray(data.items))throw V.invalid('Indent items are required.');
  return db.transaction(async transaction=>{
    const [header]=await V.select(db,'SELECT * FROM indentpo WHERE indent_id=:indentId FOR UPDATE',{indentId},transaction);
    if(!header)throw V.invalid('Indent not found.',404);
    const rows=await V.select(db,'SELECT id,item_id FROM st_stock_register WHERE indent_id=:indentId ORDER BY id FOR UPDATE',{indentId},transaction);
    const seen=new Set();
    for(const item of data.items){
      const id=String(item.id);if(seen.has(id))throw V.invalid('This Item Already added');seen.add(id);
      const row=rows.find(row=>String(row.id)===id && String(row.item_id)===String(item.item_id));
      if(!row)throw V.invalid('The item does not belong to this indent.');
      V.number(item.quantity,'Issue quantity');
    }
    if(data.items.length!==rows.length)throw V.invalid('All existing indent items are required.');
    await V.write(db,'UPDATE indentpo SET user_id=:userId,updated=NOW(),issued_name=:issued_name,machine_id=:machine_id,issue_date=:issue_date WHERE id=:id',{...data,id:header.id,userId},transaction);
    for(const item of data.items)await V.write(db,"UPDATE st_stock_register SET quantity=:quantity,issue_date=:issue_date,store_type='2' WHERE id=:id AND indent_id=:indentId",{...item,issue_date:data.issue_date,indentId},transaction);
    return {indent_id:indentId};
  });
}
async function remove(db,indentId){
 return db.transaction(async transaction=>{
  const [header]=await V.select(db,'SELECT id FROM indentpo WHERE indent_id=:indentId FOR UPDATE',{indentId},transaction);
  if(!header)throw V.invalid('Indent not found.',404);
  await V.select(db,'SELECT id FROM st_stock_register WHERE indent_id=:indentId ORDER BY id FOR UPDATE',{indentId},transaction);
  await db.query('DELETE FROM st_stock_register WHERE indent_id=:indentId',{replacements:{indentId},transaction,type:require('sequelize').QueryTypes.DELETE});
  await db.query('DELETE FROM indentpo WHERE id=:id',{replacements:{id:header.id},transaction,type:require('sequelize').QueryTypes.DELETE});
 });
}
module.exports={update,remove};
