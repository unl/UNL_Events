ALTER TABLE eventdatetime ADD COLUMN `canceled` tinyint(1) unsigned NOT NULL DEFAULT 0;
ALTER TABLE recurringdate ADD COLUMN `canceled` tinyint(1) unsigned NOT NULL DEFAULT 0;

-- Rollback
-- ALTER TABLE eventdatetime DROP COLUMN `canceled`;
-- ALTER TABLE recurringdate DROP COLUMN `canceled`;
