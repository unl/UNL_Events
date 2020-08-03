ALTER TABLE `calendar` ADD COLUMN `defaulttimezone` VARCHAR(30) NOT NULL DEFAULT 'America/Chicago' AFTER `theme`;
ALTER TABLE `eventdatetime` ADD COLUMN `timezone` VARCHAR(30) NOT NULL DEFAULT 'America/Chicago' AFTER `endtime`;
DROP TABLE `ongoingcheck`;