ALTER TABLE `eventdatetime` ADD `timeTBD` enum('YES','NO') DEFAULT 'NO' AFTER `endtime`;

-- TODO: Update all multi-day events and change them to recurring