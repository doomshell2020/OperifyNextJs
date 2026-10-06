// AppController.php sets Cake's limit to 50; Cake caps requested limits at 100.
// Indent/index uses the bundled DataTables iDisplayLength=50 and client-side paging.
const policies = {contract:50, designsheet:50, purchaseOrder:50, grnInspection:50, grn:50, products:50};
function paginationInput(input = {}, module) {
  const defaultLimit = policies[module];
  if (!defaultLimit) throw new Error(`Unknown pagination module: ${module}`);
  const integer = (value, fallback) => /^-?\d+$/.test(String(value ?? '')) && Number.isSafeInteger(Number(value)) ? Number(value) : fallback;
  const page = Math.max(1, integer(input.page,1));
  const limit = Math.max(1,Math.min(100,integer(input.limit,defaultLimit)));
  const offset = (page - 1) * limit;
  if (!Number.isSafeInteger(offset)) {const error=new Error('Invalid page');error.status=400;throw error;}
  return {page,limit,offset};
}
function paginationResult(total, page, limit, current) {
  total = Number(total);
  const totalPages = Math.ceil(total / limit);
  if (page > Math.max(1,totalPages)) {const error=new Error('Page not found');error.status=404;error.code='PAGE_NOT_FOUND';throw error;}
  return {total,page,limit,totalPages,current,hasPrevious:page>1,hasNext:page<totalPages};
}
function listOrder(sort, direction, allowed, defaultColumn) {
  const key = String(sort || '').split('.').at(-1);
  const column = Object.hasOwn(allowed, key) ? allowed[key] : defaultColumn;
  const order = String(direction || 'desc').toLowerCase() === 'asc' ? 'ASC' : 'DESC';
  return {column,direction:order};
}
module.exports={paginationInput,paginationResult,listOrder};
