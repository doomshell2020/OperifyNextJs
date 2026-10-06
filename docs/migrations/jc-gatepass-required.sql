-- This file only inspects schemas. It does not apply migrations.
-- The reviewed, tenant-specific changes are in:
--   tirupati_tppl-jc-gatepass.sql
--   tirupati_kcpl-jc-gatepass.sql
-- Both migrations were applied after explicit user approval on 2026-10-06.
SHOW COLUMNS FROM tirupati_tppl.gatepasses;
SHOW COLUMNS FROM tirupati_tppl.job_challan_receives;
SHOW COLUMNS FROM tirupati_kcpl.gatepasses;
SHOW COLUMNS FROM tirupati_kcpl.job_challan_receives;
