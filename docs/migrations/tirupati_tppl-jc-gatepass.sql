-- Required columns from the current PHP source. Reviewed against SHOW COLUMNS.
-- Applied with explicit user approval on 2026-10-06. DO NOT reapply.
-- Original record/stock counts and values verified unchanged.
ALTER TABLE tirupati_tppl.gatepasses MODIFY COLUMN jc_id VARCHAR(255) NULL;
ALTER TABLE tirupati_tppl.job_challan_receives
  ADD COLUMN rate DECIMAL(10,2) NULL,
  ADD COLUMN tax_rate DECIMAL(10,2) NULL,
  ADD COLUMN tax_amount DECIMAL(10,2) NULL,
  ADD COLUMN amount DECIMAL(10,2) NULL;
