<?php
include __DIR__ . '/../config.inc.php';

use UNL\UCBCN\ActiveRecord\Database;

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE);

$mysqli = Database::getDB();

$sql = "
UPDATE event LEFT JOIN eventdatetime ON event.id = eventdatetime.event_id
SET event.imagedata = NULL, event.imagemime = NULL
WHERE event.datecreated <= DATE_SUB(NOW(),INTERVAL 2 YEAR)
  AND (eventdatetime.starttime IS NULL OR 
  	  (eventdatetime.recurringtype = 'none' AND eventdatetime.starttime <= DATE_SUB(NOW(),INTERVAL 2 YEAR)) OR 
      (eventdatetime.recurringtype <> 'none' AND eventdatetime.recurs_until <= DATE_SUB(NOW(),INTERVAL 2 YEAR)))
  AND event.imagedata IS NOT NULL
";

if (!($result = $mysqli->query(trim($sql)))) {
	echo 'Purged images error - ' . $mysqli->errno . ': ' . $mysqli->error . "\n\n";
} else {
	echo 'Purged images from ' . $mysqli->affected_rows . ' events older than two years ago. ' . "\n\n";
}
