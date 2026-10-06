const {QueryTypes} = require('sequelize');

// CommanHelper::rawreverseitemqty and reciveitem: all statuses, latest receipt
// by ledger ID at/before the reverse creation date, restricted to design items.
async function reverseCost(db, contractId) {
  const rows = await db.query(`SELECT s.id,s.reverse_id,s.created,s.quantity,a.item_name,
    g.goods_id,g.issue_date AS grn_date,g.rate,t.tax AS tax_percentage
    FROM st_stock_register s LEFT JOIN st_additem a ON a.id=s.item_id
    LEFT JOIN st_stock_register g ON g.id=(SELECT r.id FROM st_stock_register r
      WHERE r.item_id=s.item_id AND r.store_type='1' AND r.issue_date <= DATE(s.created)
      ORDER BY r.id DESC LIMIT 1)
    LEFT JOIN st_taxmaster t ON t.id=g.tax_id
    WHERE s.contract_id=:contractId AND s.store_type='3'
      AND EXISTS (SELECT 1 FROM designsheetdetails d WHERE d.contract_id=s.contract_id AND d.item_id=s.item_id)
    ORDER BY s.item_id,s.id DESC`, {replacements:{contractId},type:QueryTypes.SELECT});
  const items = rows.map(row => {
    // Preserve the source's string multiplier, including its one-digit tax behavior.
    const rate = Number(row.rate || 0) * parseFloat(`1.${row.tax_percentage ?? ''}`);
    return {...row,rate,cost:Number(row.quantity || 0)*Number(row.rate || 0)*parseFloat(`1.${row.tax_percentage ?? ''}`)};
  });
  return {items,total:items.reduce((sum,row)=>sum+row.cost,0)};
}
module.exports = reverseCost;
