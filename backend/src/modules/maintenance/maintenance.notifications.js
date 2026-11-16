// Delivery is invoked only after a real create succeeds, never by read-only QA.
// Configure these credentials on the server; do not reuse PHP's embedded keys.
const crypto=require('node:crypto');
const fs=require('node:fs');
const R=require('./maintenance.repository');
function firebaseCredentials() {
  const file=process.env.MAINTENANCE_FIREBASE_CREDENTIALS || process.env.GOOGLE_APPLICATION_CREDENTIALS;
  const account=file?JSON.parse(fs.readFileSync(file,'utf8').replace(/^\uFEFF/,'')):{};
  return {email:process.env.MAINTENANCE_FIREBASE_CLIENT_EMAIL || account.client_email,key:process.env.MAINTENANCE_FIREBASE_PRIVATE_KEY || account.private_key,project:process.env.MAINTENANCE_FIREBASE_PROJECT_ID || account.project_id};
}
async function post(url,body,token){
  const parsed=new URL(url);if(parsed.protocol!=='https:')throw Error('Notification endpoints must use HTTPS.');
  const response=await fetch(parsed,{method:'POST',redirect:'error',signal:AbortSignal.timeout(10000),headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(body)});
  if(!response.ok)throw Error('Notification provider rejected the request.');
}
async function firebaseToken(email,privateKey){
  const now=Math.floor(Date.now()/1000),base=value=>Buffer.from(JSON.stringify(value)).toString('base64url');
  const unsigned=base({alg:'RS256',typ:'JWT'})+'.'+base({iss:email,scope:'https://www.googleapis.com/auth/firebase.messaging',aud:'https://oauth2.googleapis.com/token',iat:now,exp:now+3600});
  const assertion=unsigned+'.'+crypto.sign('RSA-SHA256',Buffer.from(unsigned),privateKey.replace(/\\n/g,'\n')).toString('base64url');
  const response=await fetch('https://oauth2.googleapis.com/token',{method:'POST',redirect:'error',signal:AbortSignal.timeout(10000),headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({grant_type:'urn:ietf:params:oauth:grant-type:jwt-bearer',assertion})});
  if(!response.ok)throw Error('Push authentication failed.');const result=await response.json();if(!result.access_token)throw Error('Push authentication failed.');return result.access_token;
}
async function send(db,id){
  const tasks=[
    {name:'WhatsApp',run:async()=>{
      const url=process.env.MAINTENANCE_WHATSAPP_URL,key=process.env.MAINTENANCE_WHATSAPP_KEY;
      if(!url || !key)throw Error('Not configured');
      const [row]=await R.select(db,`SELECT m.*,mm.machine_name,u.user_name,u.mobile FROM maintenance m LEFT JOIN machine_master mm ON mm.id=m.machine_id LEFT JOIN users u ON u.id=m.assigned_to WHERE m.id=:id`,{id:R.id(id)});
      const [site]=await R.select(db,"SELECT company_name FROM sitesettings_details WHERE status='Y' LIMIT 1");
      if(!row?.mobile)throw Error('Recipient unavailable');
      const date=require('../../utils/legacyPdf').date(row.datefrom);
      await post(url,{campaignName:'operify_machine_maintenance_request',destination:'91'+row.mobile,templateParams:[site?.company_name,row.machine_name,row.user_name,date,row.total_time,row.remark].map(v=>String(v ?? ''))},key);
    }},
    {name:'Push',run:async()=>{
      const {email,key,project}=firebaseCredentials();
      if(!email || !key || !project)throw Error('Not configured');
      const [device]=await R.select(db,'SELECT d.token FROM users u INNER JOIN users_device d ON d.id=u.device_id WHERE u.id=1 LIMIT 1');
      if(!device?.token)throw Error('Recipient unavailable');
      const [row]=await R.select(db,'SELECT m.maintenance_incharge,mm.machine_name FROM maintenance m LEFT JOIN machine_master mm ON mm.id=m.machine_id WHERE m.id=:id',{id:R.id(id)});
      const token=await firebaseToken(email,key),date=R.today().split('-').reverse().join('-');
      // PHP's message interpolates the submitted incharge ID, not the resolved name.
      await post('https://fcm.googleapis.com/v1/projects/'+encodeURIComponent(project)+'/messages:send',{message:{token:device.token,notification:{title:'Maintenance',body:'A New Maintenance Request added by:'+row.maintenance_incharge+' for Machine:'+row.machine_name+' Dated:'+date+'.'},data:{key1:'value1',key2:'value2'}}},token);
    }}
  ];
  const outcomes=await Promise.allSettled(tasks.map(t=>t.run()));
  return outcomes.flatMap((result,i)=>result.status==='rejected'?[tasks[i].name+' notification was not sent.']:[]);
}
module.exports={send};
