// Explicit opt-in schema migration. No data backfill or automatic request-time DDL.
require('../src/config/environment');
const { Sequelize, QueryTypes } = require('sequelize');
async function migrate(db, apply=false) {
  const columns=await db.query('SHOW COLUMNS FROM reverseindent',{type:QueryTypes.SELECT});
  const [stock]=await db.query("SHOW COLUMNS FROM st_stock_register LIKE 'store_type'",{type:QueryTypes.SELECT});
  const statements=[];
  // Existing rows are posted/legacy records; do not mark them editable drafts.
  if(!columns.some(row=>row.Field==='status')) statements.push("ALTER TABLE reverseindent ADD COLUMN status ENUM('draft','finalized') DEFAULT 'finalized'");
  if(!stock.Type.includes("'99'")) {
    if(!/^enum\('[0-9]+'(?:,'[0-9]+')*\)$/i.test(stock.Type)) throw new Error('Unexpected stock enum: review schema manually.');
    statements.push(`ALTER TABLE st_stock_register MODIFY COLUMN store_type ${stock.Type.slice(0,-1)},'99') ${stock.Null==='YES'?'NULL':'NOT NULL'} DEFAULT ${stock.Default==null?'NULL':db.escape(stock.Default)}`);
  }
  if(apply) for(const sql of statements) await db.query(sql);
  return {mode:apply?'applied':'preview',statements};
}
module.exports={migrate};
if(require.main===module) {
  const database=process.argv.find(arg=>arg.startsWith('--database='))?.slice(11);
  if(!database || !/^[a-zA-Z0-9_]+$/.test(database)) throw new Error('Provide --database=<tenant>; add --apply to execute.');
  const db=new Sequelize(database,process.env.DB_USER || 'root',process.env.DB_PASSWORD || '',{host:process.env.DB_HOST,dialect:'mysql',logging:false});
  migrate(db,process.argv.includes('--apply')).then(result=>console.log(JSON.stringify({database,...result}))).catch(error=>{console.error(error.message);process.exitCode=1;}).finally(()=>db.close());
}
