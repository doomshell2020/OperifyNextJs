-- REVIEW ONLY. Not executed by the application or migration tests.
-- Based on the LOCAL development database (localhost MariaDB), NOT live RDS.
-- The user reports live RDS already has the complete schema. Inspect it first;
-- do not apply this proposal to live RDS if those fields already exist.
-- Apply separately to an approved tenant database after reviewing its schema.
-- Required by MaintenanceController::maintenancestatus / completeMaintenance.
-- Keep the existing 'complete' enum value so historical records are preserved.
ALTER TABLE maintenance
  ADD COLUMN assigned_date DATE NULL,
  ADD COLUMN completion_date DATE NULL,
  ADD COLUMN completion_time VARCHAR(255) NULL,
  ADD COLUMN remarks TEXT NULL,
  MODIFY COLUMN maintenance_status ENUM('pending','assigned','complete','completed') NULL DEFAULT 'pending';
