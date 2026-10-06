const { getTenantSequelize } = require('../../config/sequelize');
const L = require('../jobChallan/legacyStore');
const manualFields = ['is_manual','manual_challan_no','reference_jc_no','challan_date','vehicle_no','jc_type','other_company_name','receive_no'];
class ReceiveService {
  async options(dbName) {
    const db = await getTenantSequelize(dbName), eligible = [];
    for (const sender of await L.linked(dbName)) {
      const rows = await L.select(db, `SELECT j.id,j.challan_no,j.jc_date FROM ${L.table('job_challans',sender.database_name)} j JOIN ${L.table('sub_contractors',sender.database_name)} s ON s.id=j.sub_contractors_id
        WHERE s.database_name=:dbName AND j.status NOT IN ('Completed','Cancelled','Deleted') AND COALESCE((SELECT SUM(quantity) FROM ${L.table('job_challan_items',sender.database_name)} WHERE challan_id=j.id),0) > COALESCE((SELECT SUM(received_qty) FROM ${L.table('job_challan_receives',sender.database_name)} WHERE challan_id=j.id),0) ORDER BY j.id DESC`, { dbName });
      eligible.push(...rows.map(row => ({ ...row, key: `${row.id}|${sender.database_name}`, sender_db: sender.database_name, sender_name: sender.name })));
    }
    const products = await L.select(db, "SELECT a.id,a.item_name,a.itemtype,u.unit_name FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom WHERE a.status='Y' ORDER BY a.item_name");
    const vendors = await L.select(db, 'SELECT id,name,gst_no FROM sub_contractors ORDER BY name');
    const fields = new Set((await L.columns(db,'job_challan_receives')).map(column=>column.Field));
    return { eligible, products, vendors, manual_supported:manualFields.every(field=>fields.has(field)) };
  }
  async details(dbName, query) {
    const db = await getTenantSequelize(dbName);
    const [id, senderDb] = String(query.challan_id || '').split('|');
    if (query.jc_type === 'Others') {
      const [jc] = await L.select(db, 'SELECT * FROM job_challans WHERE challan_no=:no LIMIT 1', { no: query.reference_jc_no || '' });
      if (!jc) L.fail('Reference JC Not Found',404);
      return this.details(dbName, { challan_id: `${jc.id}|${dbName}` });
    }
    const jc = await L.source(dbName,senderDb,id);
    const items = await this.pending(db,jc.id,senderDb);
    const [company] = senderDb === dbName ? await L.select(db,'SELECT name AS cname FROM sub_contractors WHERE id=:id',{id:jc.sub_contractors_id}) : await L.select(db,`SELECT cname FROM ${L.table('st_companymaster',senderDb)} WHERE main_branch='Y' LIMIT 1`);
    return { ...jc, from_company: company?.cname || '', items };
  }
  async pending(db,id,senderDb,t) {
    const rows = await L.select(db,`SELECT i.item_id,a.item_name,a.item_isbn,c.category_name AS category,u.unit_name,SUM(i.quantity) AS original_qty,
      COALESCE((SELECT SUM(r.received_qty) FROM ${L.table('job_challan_receives',senderDb)} r WHERE r.challan_id=:id AND r.item_id=i.item_id),0) AS received_qty
      FROM ${L.table('job_challan_items',senderDb)} i LEFT JOIN ${L.table('st_additem',senderDb)} a ON a.id=i.item_id LEFT JOIN ${L.table('st_categorymaster',senderDb)} c ON c.id=a.category_id LEFT JOIN ${L.table('st_measurementunits',senderDb)} u ON u.id=a.uom
      WHERE i.challan_id=:id GROUP BY i.item_id,a.item_name,a.item_isbn,c.category_name,u.unit_name ORDER BY i.item_id`,{id},t);
    return rows.map(row=>({...row,pending_qty:Math.max(0,L.round(Number(row.original_qty)-Number(row.received_qty)))}));
  }
  async list(dbName,query={}) {
    const db=await getTenantSequelize(dbName), rows=[];
    const fields = new Set((await L.columns(db,'job_challan_receives')).map(column=>column.Field));
    if (manualFields.every(field=>fields.has(field))) rows.push(...await L.select(db,`SELECT r.id AS challan_id,r.id AS receive_id,r.manual_challan_no AS challan_no,r.reference_jc_no,r.challan_date AS jc_date,r.receive_date,r.vehicle_no,r.item_id,a.item_name,r.dispatch_qty,r.received_qty AS total_received,r.sub_contractors_id,COALESCE(r.other_company_name,s.name,'Manual') AS sender_name,1 AS is_manual,'local' AS sender_db FROM job_challan_receives r LEFT JOIN st_additem a ON a.id=r.item_id LEFT JOIN sub_contractors s ON s.id=r.sub_contractors_id WHERE r.is_manual=1 ORDER BY r.id DESC`));
    for (const sender of await L.linked(dbName)) {
      const incoming=await L.select(db,`SELECT j.id AS challan_id,j.challan_no,j.jc_date,j.vehicle_no,j.status,j.sub_contractors_id,i.item_id,a.item_name,SUM(i.quantity) AS dispatch_qty,
        COALESCE((SELECT SUM(received_qty) FROM ${L.table('job_challan_receives',sender.database_name)} r WHERE r.challan_id=j.id AND r.item_id=i.item_id),0) AS total_received
        FROM ${L.table('job_challans',sender.database_name)} j JOIN ${L.table('sub_contractors',sender.database_name)} s ON s.id=j.sub_contractors_id JOIN ${L.table('job_challan_items',sender.database_name)} i ON i.challan_id=j.id LEFT JOIN ${L.table('st_additem',sender.database_name)} a ON a.id=i.item_id
        WHERE s.database_name=:dbName AND j.status != 'Cancelled' GROUP BY j.id,j.challan_no,j.jc_date,j.vehicle_no,j.status,j.sub_contractors_id,i.item_id,a.item_name ORDER BY j.id DESC`,{dbName});
      rows.push(...incoming.map(row=>({...row,sender_db:sender.database_name,sender_name:sender.name})));
    }
    return L.paginate(L.filterRows(rows.map(row=>({...row,pending_qty:Math.max(0,L.round(Number(row.dispatch_qty)-Number(row.total_received)))})),query,'jc_date'),query);
  }
  async create(dbName,payload,localReturnId) {
    const db=await getTenantSequelize(dbName);
    const manual=payload.jc_type==='Others' && !localReturnId;
    const [rawId,senderDb]=localReturnId ? [localReturnId,dbName] : String(payload.challan_id || '').split('|');
    if (!manual && !localReturnId && senderDb===dbName) L.fail('Select an incoming JC from a linked company');
    // Qualified tables share the same MySQL connection and transaction.
    return db.transaction(async t=>{
      const saved=[];
      if (manual) {
        const columns = new Set((await L.columns(db,'job_challan_receives')).map(column=>column.Field));
        if (!manualFields.every(field=>columns.has(field))) L.fail('Manual receive columns are missing in this company. Apply the reviewed JC schema migration first.',409);
        const challan_no=String(payload.challan_no || '').trim(), challan_date=L.date(payload.challan_date,'Challan date');
        const jc_type=payload.manual_jc_type || 'In JC';
        if (!['In JC','Others'].includes(jc_type)) L.fail('Invalid manual JC type');
        const from=jc_type==='In JC' ? L.positiveId(payload.from_company) : null;
        const company=jc_type==='Others' ? String(payload.other_company_name || '').trim() : null;
        if (!challan_no || (jc_type==='Others' && !company)) L.fail('Please fill all required Challan details.');
        if (from && !(await L.select(db,'SELECT id FROM sub_contractors WHERE id=:id',{id:from},t)).length) L.fail('Invalid company');
        const items=(Array.isArray(payload.manual_items)?payload.manual_items:[]).filter(row=>row.item_id && Number(row.receive_qty)>0);
        if (!items.length) L.fail('Please enter a valid receive quantity for at least one item.');
        const receive_no='MANUAL-'+Math.floor(Date.now()/1000);
        for (const row of items) {
          const item_id=L.positiveId(row.item_id), received_qty=L.number(row.receive_qty,'Receive quantity',true);
          if (!(await L.select(db,"SELECT id FROM st_additem WHERE id=:id AND status='Y'",{id:item_id},t)).length) L.fail('Invalid or inactive item');
          const dispatch_qty=row.dispatch_qty===undefined ? received_qty : L.number(row.dispatch_qty,'Dispatch quantity');
          saved.push(await L.insert(db,'job_challan_receives',{challan_id:null,receive_no,jc_type,other_company_name:company,is_manual:1,manual_challan_no:challan_no,reference_jc_no:payload.reference_jc_no || null,challan_date,sub_contractors_id:from,vehicle_no:payload.vehicle_no || '',item_id,dispatch_qty,received_qty,receive_date:challan_date,remarks:'Manual Receipt',created:new Date()},t));
          await L.movement(db,{challan_id:0,item_id,quantity:received_qty,issue_date:challan_date,sub_contractors_id:from,store_type:'1'},t);
        }
      } else {
        const jc=await L.source(dbName,senderDb,rawId,t,db);
        if (['Completed','Cancelled','Deleted'].includes(jc.status)) L.fail('This Job Challan cannot be received.',409);
        const pending=await this.pending(db,jc.id,senderDb,t);
        const items=(Array.isArray(payload.items)?payload.items:[]).filter(row=>row.item_id && Number(row.receive_qty)>0);
        if (!items.length) L.fail('Please enter a receive quantity for at least one item.');
        for (const row of items) {
          const item_id=L.positiveId(row.item_id), received_qty=L.number(row.receive_qty,'Receive quantity',true), line=pending.find(item=>Number(item.item_id)===item_id);
          if (!line) L.fail('Job Challan item not found');
          if (received_qty>line.pending_qty) L.fail(`Receive Qty cannot be greater than Pending Qty for ${line.item_name} (Pending: ${line.pending_qty}).`,409);
          line.pending_qty=L.round(line.pending_qty-received_qty);
          const receive_date=localReturnId ? L.date(row.receive_date || payload.receive_date) : L.today();
          const rate=L.number(row.rate,'Rate'),tax_rate=L.number(row.tax_rate,'Tax rate'),tax_amount=L.round(received_qty*rate*tax_rate/100);
          const receipt={challan_id:jc.id,item_id,dispatch_qty:Number(line.original_qty),received_qty,receive_date,vehicle_no:row.vehicle_no || payload.vehicle_no || '',sub_contractors_id:jc.sub_contractors_id,remarks:row.remarks || '',rate,tax_rate,tax_amount,amount:L.round(received_qty*rate+tax_amount),created:new Date()};
          const cols=await L.columns(db,'job_challan_receives',senderDb);
          if (receipt.vehicle_no && !cols.some(col=>col.Field==='vehicle_no')) L.fail('The sender company is missing the receive vehicle column. Apply the reviewed JC schema migration first.',409);
          if (localReturnId && (rate || tax_rate) && ['rate','tax_rate','tax_amount','amount'].some(name=>!cols.some(col=>col.Field===name))) L.fail('Return valuation columns are missing. Apply the reviewed JC schema migration before entering charges.',409);
          let localItemId=item_id;
          if (!localReturnId) {
            const [local]=await L.select(db,'SELECT id FROM st_additem WHERE item_name=:name LIMIT 1',{name:line.item_name},t);
            if (!local) L.fail(`Item '${line.item_name}' does not exist in local Item Master. Please create it first.`);
            localItemId=local.id;
          }
          saved.push(await L.insert(db,'job_challan_receives',receipt,t,senderDb));
          await L.movement(db,{challan_id:jc.id,item_id:localItemId,quantity:received_qty,issue_date:receive_date,sub_contractors_id:jc.sub_contractors_id,store_type:'1'},t);
        }
        const [sum]=await L.select(db,`SELECT COALESCE((SELECT SUM(quantity) FROM ${L.table('job_challan_items',senderDb)} WHERE challan_id=:id),0) AS dispatched,COALESCE((SELECT SUM(received_qty) FROM ${L.table('job_challan_receives',senderDb)} WHERE challan_id=:id),0) AS received`,{id:jc.id},t);
        const status=Number(sum.received)===0?'Pending':Number(sum.received)<Number(sum.dispatched)?'Partially Returned':'Completed';
        await db.query(`UPDATE ${L.table('job_challans',senderDb)} SET status=:status WHERE id=:id`,{replacements:{status,id:jc.id},transaction:t});
      }
      return { ids:saved };
    });
  }
  async returnPdf(dbName,id,senderDb) {
    const db=await getTenantSequelize(dbName), sourceDb=senderDb || dbName;
    if (sourceDb!==dbName && !(await L.linked(dbName)).some(row=>row.database_name===sourceDb)) L.fail('Unauthorized sender',403);
    const [receipt]=await L.select(db,`SELECT r.*,a.item_name,a.item_isbn AS hsn_code,u.unit_name FROM ${L.table('job_challan_receives',sourceDb)} r LEFT JOIN ${L.table('st_additem',sourceDb)} a ON a.id=r.item_id LEFT JOIN ${L.table('st_measurementunits',sourceDb)} u ON u.id=a.uom WHERE r.id=:id`,{id:L.positiveId(id)});
    if (!receipt || !receipt.challan_id) L.fail('Return transaction not found',404);
    const detail=await require('../jobChallan/jobChallan.service').getDetail(dbName,receipt.challan_id,sourceDb);
    if (['Cancelled','Deleted'].includes(detail.challan.status)) L.fail('Cannot generate Return Challan for a cancelled or deleted Job Challan.');
    return {...detail,receipt};
  }
}
module.exports=new ReceiveService();
