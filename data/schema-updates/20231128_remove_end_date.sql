ALTER TABLE `eventdatetime` ADD COLUMN `timemode` enum('REGULAR', 'STARTTIMEONLY', 'ENDTIMEONLY', 'ALLDAY', 'TBD') DEFAULT 'REGULAR' NOT NULL AFTER `endtime`;

-- TODO: Update all multi-day events and change them to recurring