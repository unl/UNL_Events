ALTER TABLE `calendar` ADD COLUMN `defaulttimezone` VARCHAR(30) NOT NULL DEFAULT 'America/Chicago' AFTER `theme`;
ALTER TABLE `eventdatetime`
 ADD COLUMN `timezone` VARCHAR(30) NOT NULL DEFAULT 'America/Chicago' AFTER `endtime`,
  ADD COLUMN `starttime_timestamp` int(11) NULL AFTER `starttime`,
  ADD COLUMN `endtime_timestamp` int(11) NULL AFTER `endtime`,
  ADD COLUMN `recurs_until_timestamp` int(11) NULL AFTER `recurs_until`;
ALTER TABLE `recurringdate` ADD COLUMN `recurringdate_timestamp` int(11) NOT NULL AFTER `recurringdate`;


UPDATE `eventdatetime` set
  `starttime_timestamp` = UNIX_TIMESTAMP(`starttime`),
  `endtime_timestamp` = UNIX_TIMESTAMP(`endtime`),
  `recurs_until_timestamp` = UNIX_TIMESTAMP(`recurs_until`)
WHERE `timezone` = 'America/Chicago';

UPDATE `recurringdate` set  `recurringdate_timestamp` = UNIX_TIMESTAMP(`recurringdate`);