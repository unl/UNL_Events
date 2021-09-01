ALTER TABLE event ADD COLUMN `canceled` tinyint(1) unsigned NOT NULL DEFAULT 0;

-- Rollback
-- ALTER TABLE event DROP COLUMN `canceled`;
