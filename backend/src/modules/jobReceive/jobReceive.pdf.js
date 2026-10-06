const {buildSubcontractorChallanHtml}=require('../jobChallan/jobChallan.subcontractor.pdf');
const {render}=require('../../utils/legacyPdf');
function build(data) {
  const r=data.receipt,s=data.site_details || {},settings=data.sitesetting || {},vendor=data.challan.vendor || {};
  const html=buildSubcontractorChallanHtml({
    return_receipt:true,packages:r.remarks || '01 Box',original_challan_no:data.challan.challan_no,
    sitesetting:{first_name:vendor.name},site_details:{address:vendor.address,gst_no:vendor.gst_no},
    challan:{challan_no:'RC-'+r.id,jc_date:r.receive_date,vehicle_no:r.vehicle_no,work_description:'JC No: '+data.challan.challan_no,
      vendor:{name:settings.first_name || s.company_name || 'Tirupati Plastomatics (P) Ltd.',address:[s.address1,s.address2].filter(Boolean).join(', ') || s.address,gst_no:s.gst_no || s.gst || '08AAACT5317J1ZA'},
      job_challan_items:[{item_name:r.item_name,quantity:r.received_qty,unit_name:r.unit_name,hsn_code:r.hsn_code,rate:r.rate || 0,tax_rate:r.tax_rate || 0,tax_amount:r.tax_amount || 0,amount:r.amount || Number(r.received_qty)*Number(r.rate || 0)+Number(r.tax_amount || 0)}]}
  });
  return html;
}
module.exports={build,generate:data=>render(build(data))};
