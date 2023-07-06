-- Unneeded table
DROP TABLE `webcastlink`;

-- Edit webcast table to have columns (id, title, url, additionalpublicinfo, and user_id)
ALTER TABLE `webcast` DROP COLUMN `event_id`;
ALTER TABLE `webcast` DROP COLUMN `dateavailable`;
ALTER TABLE `webcast` DROP COLUMN `status`;
ALTER TABLE `webcast` DROP COLUMN `playertype`;
ALTER TABLE `webcast` DROP COLUMN `bandwidth`;
ALTER TABLE `webcast` ADD `url` longtext NOT NULL AFTER `title`;
ALTER TABLE `webcast` MODIFY `title` VARCHAR(100) DEFAULT "" NOT NULL;
ALTER TABLE `webcast` ADD `user_id` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `webcast` ADD `calendar_id` VARCHAR(100) DEFAULT NULL;

-- Edit eventdatetime table to have webcast_id that is nullable and location_id to be nullable
-- Also adds event specific additional public info for webcast
ALTER TABLE `eventdatetime` MODIFY `location_id` int(10) unsigned DEFAULT NULL;
ALTER TABLE `eventdatetime` ADD `webcast_id` int(10) unsigned DEFAULT NULL AFTER `location_id`;
ALTER TABLE `eventdatetime` ADD `webcast_additionalpublicinfo` longtext DEFAULT NULL AFTER `additionalpublicinfo`;
ALTER TABLE `eventdatetime` ADD `location_additionalpublicinfo` longtext DEFAULT NULL AFTER `additionalpublicinfo`;

-- Allows locations to be saved to a calendar
ALTER TABLE `location` ADD `calendar_id` VARCHAR(100) DEFAULT NULL;

-- Add organizer type to events
ALTER TABLE `event` ADD `listingcontacturl` longtext DEFAULT NULL AFTER `listingcontactemail`;
ALTER TABLE `event` ADD `listingcontacttype` ENUM('person', 'organization') DEFAULT NULL AFTER `listingcontacturl`;

ALTER TABLE `calendar_has_event` MODIFY `source` ENUM('checked consider event', 'create event form', 'recommended', 'search', 'subscription', 'create event api', 'create event api v2') DEFAULT null;
