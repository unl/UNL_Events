# create the subscription_has_calendar table
CREATE TABLE `subscription_has_calendar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) unsigned NOT NULL,
  `calendar_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# migrate the current things in searchcriteria over to the new table

# drop the stupid column of stupid
ALTER TABLE `subscription` DROP COLUMN `searchcriteria`;