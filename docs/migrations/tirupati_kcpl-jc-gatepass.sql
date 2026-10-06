-- Required columns from the current PHP source. Reviewed against SHOW COLUMNS.
-- Applied with explicit user approval on 2026-10-06. DO NOT reapply.
-- Original record/stock counts and values verified unchanged.
ALTER TABLE tirupati_kcpl.gatepasses MODIFY COLUMN jc_id VARCHAR(255) NULL;
ALTER TABLE tirupati_kcpl.job_challan_receives
  ADD COLUMN rate DECIMAL(10,2) NULL,
  ADD COLUMN tax_rate DECIMAL(10,2) NULL,
  ADD COLUMN tax_amount DECIMAL(10,2) NULL,
  ADD COLUMN amount DECIMAL(10,2) NULL,
  ADD COLUMN is_manual TINYINT(1) NULL DEFAULT 0,
  ADD COLUMN manual_challan_no VARCHAR(50) NULL,
  ADD COLUMN reference_jc_no VARCHAR(50) NULL,
  ADD COLUMN challan_date DATE NULL,
  ADD COLUMN vehicle_no VARCHAR(100) NULL,
  ADD COLUMN jc_type VARCHAR(20) NULL DEFAULT 'In JC',
  ADD COLUMN other_company_name VARCHAR(255) NULL,
  ADD COLUMN receive_no VARCHAR(255) NULL;
