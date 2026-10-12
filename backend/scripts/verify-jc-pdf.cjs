// Render existing local documents for visual QA. No database records are changed.
require('../src/config/environment');
const fs=require('node:fs');
const path=require('node:path');
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');
const L=require('../src/modules/jobChallan/legacyStore');
(async()=>{
  const name=process.env.JC_TEST_DB || 'tirupati_tppl',db=await getTenantSequelize(name),query=db.query.bind(db);
  db.query=(sql,options)=>{if(!/^(SELECT|SHOW)\b/i.test(sql.trim()))throw new Error('PDF QA must be read-only');return query(sql,options);};
  try {
    const [jc]=await L.select(db,'SELECT id FROM job_challans ORDER BY id DESC LIMIT 1');
    const [receipt]=await L.select(db,'SELECT r.id FROM job_challan_receives r JOIN job_challans j ON j.id=r.challan_id WHERE j.status NOT IN (\'Cancelled\',\'Deleted\') ORDER BY r.id DESC LIMIT 1');
    if(!jc || !receipt)throw new Error('Existing challan and return receipt are required');
    const detail=await require('../src/modules/jobChallan/jobChallan.service').getPdfDetail(name,jc.id);
    const returned=await require('../src/modules/jobReceive/jobReceive.service').returnPdf(name,receipt.id);
    const folder=path.resolve(__dirname,'../../tmp/jc-local-pdf');fs.mkdirSync(folder,{recursive:true});
    fs.writeFileSync(path.join(folder,'challan.pdf'),await require('../src/modules/jobChallan/jobChallan.pdf').generateJobChallanPDF(detail));
    fs.writeFileSync(path.join(folder,'return-challan.pdf'),await require('../src/modules/jobReceive/jobReceive.pdf').generate(returned));
    console.log(JSON.stringify({company:name,challanId:jc.id,receiptId:receipt.id,folder}));
  }finally{await db.close();await centralSequelize.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
