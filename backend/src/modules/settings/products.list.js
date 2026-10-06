const {QueryTypes} = require('sequelize');
const {paginationInput,paginationResult,listOrder} = require('../../utils/listPagination');
module.exports = async (db, input = {}, exportAll = false) => {
  const {page,limit,offset} = paginationInput(input,'products');
  const clauses=[],values={};
  const search=String(input.search || input.item_name || '').trim();
  if (search) {clauses.push('i.item_name LIKE :search');values.search=`%${search}%`;}
  for(const field of ['category_id','location_name','cname','itemtype','status']) {
    if(input[field]) {clauses.push(`i.${field}=:${field}`);values[field]=input[field];}
  }
  // PHP's unfiltered index starts with active raw materials. Its AJAX search
  // permits all types/statuses; keep that distinction explicit in the request.
  if (!input.search_mode) {
    if (!clauses.length) {clauses.push("i.itemtype='RawMaterial'");}
    clauses.push("i.status='Y'");
  }
  const from=`FROM st_additem i LEFT JOIN st_categorymaster c ON c.id=i.category_id
    LEFT JOIN st_measurementunits u ON u.id=i.uom LEFT JOIN st_taxmaster t ON t.id=i.tax
    WHERE ${clauses.length ? clauses.join(' AND ') : '1=1'}`;
  const [count]=await db.query(`SELECT COUNT(*) AS total ${from}`,{replacements:values,type:QueryTypes.SELECT});
  const sort=listOrder(input.sort,input.direction || 'asc',{id:'i.id',item_name:'i.item_name',sale_price:'i.sale_price',category_name:'c.category_name',status:'i.status'},'i.item_name');
  const order=exportAll ? `i.category_id ${input.search_mode ? 'ASC' : 'DESC'},i.item_name ASC,i.id ASC` : `${sort.column} ${sort.direction},i.id ASC`;
  const data=await db.query(`SELECT i.*,c.category_name,u.unit_name AS uom_name,t.tax AS tax_percentage,
    COALESCE((SELECT ROUND(SUM(s.quantity),2) FROM st_stock_register s WHERE s.item_id=i.id AND s.store_type IN ('0','1','3')),0)-
    COALESCE((SELECT ROUND(SUM(s.quantity),2) FROM st_stock_register s WHERE s.item_id=i.id AND s.store_type IN ('2','4')),0) AS current_stock ${from}
    ORDER BY ${order} ${exportAll ? '' : 'LIMIT :limit OFFSET :offset'}`,{replacements:{...values,limit,offset},type:QueryTypes.SELECT});
  return {data,...paginationResult(count.total,page,limit,data.length)};
};
