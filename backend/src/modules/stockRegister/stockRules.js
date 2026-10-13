// Rules shared with CakePHP StockregisterController::_getConsolidatedStock.
const RECEIPT_TYPES = ['0', '1', '3'];
const DISPATCH_TYPES = ['2', '4'];
const receiptDate = alias => `DATE(COALESCE(${alias}delivery_date, ${alias}issue_date, ${alias}created))`;
const dispatchDate = alias => `DATE(COALESCE(${alias}issue_date, ${alias}created))`;
const eligibleProduct = alias => `${alias}itemtype = 'RawMaterial' AND ${alias}status = 'Y' AND ${alias}category_id != 25`;
module.exports = { RECEIPT_TYPES, DISPATCH_TYPES, receiptDate, dispatchDate, eligibleProduct };
