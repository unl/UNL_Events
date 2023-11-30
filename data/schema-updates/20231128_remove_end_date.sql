ALTER TABLE `eventdatetime` ADD COLUMN `timemode` enum('REGULAR', 'KICKOFF', 'DEADLINE', 'ALLDAY', 'TBD') DEFAULT 'REGULAR' NOT NULL AFTER `endtime`;

-- TODO: Update all multi-day events and change them to recurring