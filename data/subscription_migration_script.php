<?php
include __DIR__ . '/../config.inc.php';

use UNL\UCBCN\ActiveRecord\Database;
use UNL\UCBCN\Calendar\Subscriptions;
use UNL\UCBCN\Calendar\SubscriptionHasCalendar;

$db = Database::getDB();

# create the subscription_has_calendar table
$sql = "CREATE TABLE `subscription_has_calendar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) unsigned NOT NULL,
  `calendar_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$stmt = $db->prepare($sql);
$stmt->execute();
echo 'Migration: subscription_has_calendar table created.' . PHP_EOL;

# migrate the current things in searchcriteria over to the new table
$sql = "SELECT id, calendar_id, searchcriteria FROM subscription";
$stmt = $db->prepare($sql);
$stmt->execute();
echo "Retreiving current subscriptions from table." . PHP_EOL;
$stmt->bind_result($id, $cal_id, $searchcriteria);

$calendars_to_add = array();
while ($row = $stmt->fetch()) {
	if (!empty($searchcriteria)) {
		# parse the searchcriteria into the appropriate cal_ids
		foreach(preg_split("/[= ]/", $searchcriteria) as $part) {
			if (is_numeric($part) && (int)$part != 0) {
				$calendars_to_add[] = array(
					"calendar_id" => (int)$part,
					"subscription_id" => $id
				);
				echo "Adding calendar " . $part . " to subscription " . $id . PHP_EOL;
			}
		}
	}
}

foreach($calendars_to_add as $calendar) {
	$sub_has_calendar = new SubscriptionHasCalendar;
	$sub_has_calendar->subscription_id = $calendar['subscription_id'];
	$sub_has_calendar->calendar_id = $calendar['calendar_id'];
	$sub_has_calendar->insert();
}

# drop the stupid column of stupid
$sql = "ALTER TABLE `subscription` DROP COLUMN `searchcriteria`";

$stmt = $db->prepare($sql);
$stmt->execute();
echo 'Column searchcriteria dropped.' . PHP_EOL;