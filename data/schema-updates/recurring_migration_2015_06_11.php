<?php

include __DIR__ . '/../config.inc.php';

use UNL\UCBCN\ActiveRecord\Database;
use UNL\UCBCN\Event\Occurrences;

$db = Database::getDB();

# add the column to the database
$sql = "ALTER TABLE `recurringdate` ADD COLUMN `event_datetime_id` int(10) DEFAULT NULL;";

$stmt = $db->prepare($sql);
$stmt->execute();
echo 'Migration: recurringdate.event_datetime_id column created.' . PHP_EOL;

echo 'Resetting recurrences' . PHP_EOL;

$event_datetimes = new Occurrences(array(
	'all_recurring' => TRUE
));
foreach ($event_datetimes as $datetime) {
	echo 'Resetting for EDT # ' . $datetime->id . PHP_EOL;
	$datetime->deleteRecurrences();
	$datetime->insertRecurrences();
}