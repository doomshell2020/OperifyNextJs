const path=require('node:path'),fs=require('node:fs'),assert=require('node:assert/strict');
require('dotenv').config({path:path.join(__dirname,'../.env'),quiet:true});
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize'),{QueryTypes}=require('sequelize');
(async()=>{
 const evidence=[];
 for(const tenant of ['tirupati_tppl','tirupati_kcpl']){
  const db=await getTenantSequelize(tenant);const query=db.query.bind(db);db.query=(sql,options)=>{assert.match(sql,/^\s*(SELECT|SHOW)\b/i);return query(sql,options);};
  const counts={};
  const queries={
   bom_missing_contract:'SELECT COUNT(*) AS total FROM bom_finisedproduct b LEFT JOIN contracts c ON c.id=b.contract_id WHERE c.id IS NULL',
   bom_missing_product:'SELECT COUNT(*) AS total FROM bom_finisedproduct b LEFT JOIN st_additem p ON p.id=b.product_id WHERE p.id IS NULL',
   designsheet_missing_contract:'SELECT COUNT(*) AS total FROM designsheet d LEFT JOIN contracts c ON c.id=d.contract_id WHERE c.id IS NULL',
   designsheet_missing_product:'SELECT COUNT(*) AS total FROM designsheet d LEFT JOIN st_additem p ON p.id=d.item_id WHERE p.id IS NULL',
   designsheet_orphan_details:'SELECT COUNT(*) AS total FROM designsheetdetails d LEFT JOIN designsheet h ON h.id=d.designsheet_id WHERE h.id IS NULL',
   products_missing_category:"SELECT COUNT(*) AS total FROM st_additem p LEFT JOIN st_categorymaster c ON c.id=p.category_id WHERE p.status='Y' AND p.category_id IS NOT NULL AND c.id IS NULL",
   products_missing_uom:"SELECT COUNT(*) AS total FROM st_additem p LEFT JOIN st_measurementunits u ON u.id=p.uom WHERE p.status='Y' AND u.id IS NULL",
   po_orphan_details:'SELECT COUNT(*) AS total FROM st_purchaseorderdetails d LEFT JOIN st_purchaseorder h ON h.id=d.poprimary_id WHERE h.id IS NULL',
   inspection_orphan_details:'SELECT COUNT(*) AS total FROM grn_inspection_details d LEFT JOIN grn_inspection h ON h.inspection_id=d.inspection_id WHERE h.id IS NULL',
   stock_missing_product:'SELECT COUNT(*) AS total FROM st_stock_register s LEFT JOIN st_additem p ON p.id=s.item_id WHERE p.id IS NULL',
   duplicate_stock_available:'SELECT COUNT(*) AS total FROM (SELECT item_id FROM st_stock_available GROUP BY item_id HAVING COUNT(*)>1) duplicates',
   conflicting_design_groups:`SELECT COUNT(*) AS total FROM (SELECT d.designsheet_id,p.category_id FROM designsheetdetails d JOIN st_additem p ON p.id=d.item_id GROUP BY d.designsheet_id,p.category_id HAVING SUM(d.is_group=1)>1 OR (SUM(d.is_group=1)>0 AND SUM(COALESCE(d.is_group,0)!=1)>0)) conflicts`
  };
  for(const [name,sql]of Object.entries(queries)){const [row]=await db.query(sql,{type:QueryTypes.SELECT});counts[name]=Number(row.total);}
  const products=await db.query('SHOW COLUMNS FROM st_additem',{type:QueryTypes.SELECT});
  const cache=await db.query('SHOW COLUMNS FROM st_stock_available',{type:QueryTypes.SELECT});
  const affected={};
  for(const name of ['bom_missing_contract','bom_missing_product','products_missing_category','products_missing_uom','stock_missing_product']) {
    const match=queries[name].match(/FROM \w+ (\w+) /);
    affected[name]=await db.query(queries[name].replace('COUNT(*) AS total',`${match[1]}.id`)+` ORDER BY ${match[1]}.id LIMIT 50`,{type:QueryTypes.SELECT});
  }
  affected.conflicting_design_groups=await db.query(`SELECT d.designsheet_id,p.category_id FROM designsheetdetails d JOIN st_additem p ON p.id=d.item_id GROUP BY d.designsheet_id,p.category_id HAVING SUM(d.is_group=1)>1 OR (SUM(d.is_group=1)>0 AND SUM(COALESCE(d.is_group,0)!=1)>0) ORDER BY d.designsheet_id,p.category_id LIMIT 50`,{type:QueryTypes.SELECT});
  evidence.push({tenant,counts,affectedSamples:affected,missingPhpProductColumns:['sub_location','consumble'].filter(name=>!products.some(row=>row.Field===name)),stockAvailableType:cache.find(row=>row.Field==='stock_available')?.Type});
  await db.close();
 }
 await centralSequelize.close();fs.writeFileSync(path.join(__dirname,'../../docs/phase1-relationships.json'),JSON.stringify(evidence,null,2));console.log(JSON.stringify(evidence,null,2));
})().catch(error=>{console.error(error.message);process.exitCode=1;});
