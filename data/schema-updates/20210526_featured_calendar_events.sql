ALTER TABLE calendar_has_event ADD COLUMN `featured` tinyint(1) unsigned NOT NULL DEFAULT 0, ADD COLUMN `pinned` tinyint(1) unsigned NOT NULL DEFAULT 0;
INSERT INTO `permission` (`id`, `name`, `description`, `standard`) VALUES (7,'Event Feature','Event: Feature',1);

-- Rollback
-- ALTER TABLE calendar_has_event DROP COLUMN `featured`,  DROP COLUMN `pinned`;
-- DELETE FROM `permission` where id = 7;
-- DELETE FROM `user_has_permission` where permission_id = 7;
