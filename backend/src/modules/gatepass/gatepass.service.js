const {getTenantSequelize}=require('../../config/sequelize');
const L=require('../jobChallan/legacyStore');
const idsOf=value=>[...new Set((Array.isArray(value)?value:String(value || '').split(',')).filter(Boolean).map(L.positiveId))];
class GatepassService {
  async list(dbName,query={}) {
    const db=await getTenantSequelize(dbName);
    const rows=await L.select(db,'SELECT g.*,s.name AS vendor_name,s.address AS vendor_address FROM gatepasses g LEFT JOIN sub_contractors s ON s.id=g.sub_contractor_id ORDER BY g.id DESC');
    return L.paginate(L.filterRows(rows,query,'date'),query,20);
  }
  async options(dbName,editId) {
    const db=await getTenantSequelize(dbName);
    const used=await L.select(db,'SELECT jc_id FROM gatepasses WHERE jc_id IS NOT NULL AND id != :id',{id:editId?L.positiveId(editId):0});
    const usedIds=new Set(used.flatMap(row=>idsOf(row.jc_id)));
    const jcs=(await L.select(db,"SELECT id,challan_no,sub_contractors_id FROM job_challans WHERE status != 'Deleted' ORDER BY id DESC")).filter(row=>!usedIds.has(Number(row.id)));
    const vendors=await L.select(db,"SELECT id,name FROM sub_contractors WHERE status='Active' ORDER BY name LIMIT 200");
    const products=await L.select(db,"SELECT a.id,a.item_name,u.unit_name FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom WHERE a.status='Y' AND a.itemtype='RawMaterial' ORDER BY a.item_name");
    const field=(await L.columns(db,'gatepasses')).find(col=>col.Field==='jc_id');
    return {jcs,vendors,products,multiple_jcs:!/^int|^bigint|^smallint/.test(field?.Type || '')};
  }
  async jcData(dbName,jcIds,t) {
    const db=await getTenantSequelize(dbName), ids=idsOf(jcIds);
    if (!ids.length) L.fail('Please select a Job Challan.');
    const jcs=await L.select(db,`SELECT * FROM job_challans WHERE id IN (:ids) AND status != 'Deleted' ORDER BY id ${t?'FOR UPDATE':''}`,{ids},t);
    if (jcs.length!==ids.length) L.fail('Invalid Job Challan');
    const items=await L.select(db,`SELECT i.item_id,a.item_name,i.quantity,j.challan_no AS jc_no,'KG' AS unit FROM job_challan_items i JOIN job_challans j ON j.id=i.challan_id LEFT JOIN st_additem a ON a.id=i.item_id WHERE i.challan_id IN (:ids) ORDER BY i.challan_id,i.id`,{ids},t);
    return {sub_contractor_id:jcs.find(row=>row.sub_contractors_id)?.sub_contractors_id,vehicle_no:jcs.find(row=>row.vehicle_no)?.vehicle_no || '',items};
  }
  async detail(dbName,id) {
    const db=await getTenantSequelize(dbName);
    const [gatepass]=await L.select(db,'SELECT g.*,s.name AS vendor_name,s.address AS vendor_address FROM gatepasses g LEFT JOIN sub_contractors s ON s.id=g.sub_contractor_id WHERE g.id=:id',{id:L.positiveId(id)});
    if (!gatepass) L.fail('Gate Pass not found',404);
    gatepass.items=await L.select(db,'SELECT i.*,a.item_name FROM gatepass_items i LEFT JOIN st_additem a ON a.id=i.item_id WHERE i.gatepass_id=:id ORDER BY i.id',{id:gatepass.id});
    gatepass.jcs=gatepass.jc_id ? await L.select(db,'SELECT id,challan_no FROM job_challans WHERE id IN (:ids)',{ids:idsOf(gatepass.jc_id)}) : [];
    const [site_details]=await L.select(db,"SELECT * FROM sitesettings_details WHERE status='Y' LIMIT 1");
    const [sitesetting]=await L.select(db,'SELECT * FROM sitesettings LIMIT 1');
    return {gatepass,site_details,sitesetting};
  }
  async save(dbName,payload,editId) {
    const db=await getTenantSequelize(dbName);
    return db.transaction(async t=>{
      let existing;
      if (editId) {
        [existing]=await L.select(db,'SELECT * FROM gatepasses WHERE id=:id FOR UPDATE',{id:L.positiveId(editId)},t);
        if (!existing) L.fail('Gate Pass not found',404);
      }
      const type=payload.gatepass_type || existing?.gatepass_type || 'JC Based';
      if (!['JC Based','Miscellaneous'].includes(type)) L.fail('Invalid Gate Pass type');
      const header={gatepass_type:type,date:L.date(payload.date || L.today()),return_date:payload.return_date?L.date(payload.return_date,'Return date'):null,vehicle_no:String(payload.vehicle_no || ''),remarks:String(payload.remarks || ''),modified:new Date()};
      let items=Array.isArray(payload.items)?payload.items:[];
      if (type==='Miscellaneous') {
        header.company_name=String(payload.company_name || '').trim();header.sub_contractor_id=null;header.jc_id=null;
        if (!header.company_name) L.fail('Please enter the Company Name.');
        if (!items.length) L.fail('Please add at least one raw material item.');
        const valid=[];
        for (const row of items) {
          const item_id=L.positiveId(row.item_id),quantity=L.number(row.quantity,'Quantity',true);
          const [item]=await L.select(db,"SELECT a.id,u.unit_name FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom WHERE a.id=:id AND a.itemtype='RawMaterial'",{id:item_id},t);
          if (!item) L.fail('Only Raw Material items are allowed in MISCELLANEOUS mode.');
          valid.push({...row,item_id,quantity,unit:item.unit_name || 'KG'});
        }
        items=valid;
      } else {
        const ids=idsOf(payload.jc_id);
        const field=(await L.columns(db,'gatepasses')).find(col=>col.Field==='jc_id');
        if (ids.length>1 && /^(int|bigint|smallint)/.test(field?.Type || '')) L.fail('Multiple JCs require the reviewed gatepasses.jc_id migration. No records were changed.',409);
        const data=await this.jcData(dbName,ids,t);
        const passes=await L.select(db,'SELECT id,jc_id FROM gatepasses WHERE jc_id IS NOT NULL AND id != :id FOR UPDATE',{id:existing?.id || 0},t);
        if (passes.some(row=>idsOf(row.jc_id).some(id=>ids.includes(id)))) L.fail('A Gate Pass has already been created for one of these Job Challans.',409);
        header.jc_id=ids.join(',');header.company_name=null;header.sub_contractor_id=L.positiveId(payload.sub_contractor_id || data.sub_contractor_id);
        if (!(await L.select(db,'SELECT id FROM sub_contractors WHERE id=:id',{id:header.sub_contractor_id},t)).length) L.fail('Invalid Company');
        // Quantities and material identities come from the selected JC. Only its
        // description/remarks are editable in the PHP form.
        const remaining=[...items];
        items=data.items.map(line=>{
          const idx=remaining.findIndex(row=>Number(row.item_id)===Number(line.item_id) && (!row.jc_no || String(row.jc_no)===String(line.jc_no)));
          const supplied=idx>=0?remaining.splice(idx,1)[0]:{};
          const description=String(supplied.description || '').replace(/^JC No: [^\n]+\n?/,'');
          return {...line,description:`JC No: ${line.jc_no}${description?'\n'+description:''}`,remarks:supplied.remarks || ''};
        });
      }
      let id=existing?.id;
      if (id) {
        const fields=Object.keys(header);
        await db.query(`UPDATE gatepasses SET ${fields.map(key=>L.identifier(key)+'=:'+key).join(',')} WHERE id=:id`,{replacements:{...header,id},transaction:t});
        await db.query('DELETE FROM gatepass_items WHERE gatepass_id=:id',{replacements:{id},transaction:t});
      } else {
        id=await L.insert(db,'gatepasses',{...header,gatepass_no:null},t);
        await db.query('UPDATE gatepasses SET gatepass_no=:no WHERE id=:id',{replacements:{no:'GP-'+String(id).padStart(4,'0'),id},transaction:t});
      }
      for (const row of items) await L.insert(db,'gatepass_items',{gatepass_id:id,item_id:row.item_id,quantity:row.quantity,unit:row.unit || 'KG',description:row.description || '',remarks:row.remarks || ''},t);
      // Gate passes never move stock in the PHP implementation.
      return {id};
    });
  }
}
module.exports=new GatepassService();
