const test=require('node:test'),assert=require('node:assert/strict');
const reverseCost=require('../src/modules/contract/contract.reverse-cost');
test('contract reverse costing retains source receipt cutoff, design scope, all statuses and legacy tax multiplier',async()=>{
  const result=await reverseCost({query:async(sql,options)=>{
    assert.match(sql,/r\.issue_date <= DATE\(s\.created\)/);
    assert.match(sql,/ORDER BY r\.id DESC LIMIT 1/);
    assert.match(sql,/EXISTS \(SELECT 1 FROM designsheetdetails/);
    assert.doesNotMatch(sql,/status\s*=/);
    assert.equal(options.replacements.contractId,130);
    return [{quantity:2,rate:100,tax_percentage:'18'},{quantity:1,rate:100,tax_percentage:'5'},{quantity:3,rate:null,tax_percentage:null}];
  }},130);
  assert.equal(result.items[0].rate,118);
  assert.equal(result.items[1].rate,150);
  assert.equal(result.items[2].cost,0);
  assert.equal(result.total,386);
});
