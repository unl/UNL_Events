<?php
include __DIR__ . '/../config.inc.php';

use UNL\UCBCN\ActiveRecord\Database;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event;
use UNL\UCBCN\Location;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\User;

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE);

$user = User::getByUID('s-tlembur1');

$ath_calendar = Calendar::getByShortname('athletics');
if (!$ath_calendar) {
	echo 'Could not find calendar!' . PHP_EOL;
	exit();
}

echo PHP_EOL . 'Importing Records...' . PHP_EOL;

$athletics = file_get_contents('http://www.huskers.com/rss.dbml?db_oem_id=100&media=schedulesxml');

$xml = new SimpleXMLElement($athletics);

$inserted = 0;
$updated = 0;
$no_change = 0;
$errors = 0;

foreach ($xml->channel->item as $event_xml) {

    $starttime = 0;
    $endtime   = null;

    if ($event_xml->time == 'TBA') {
        // TBA event, how to handle?
    }

    if ($event_xml->homeaway != 'H') {
        //Only import home events
        continue;
    }

    $starttime = date('Y-m-d H:i:s', strtotime($event_xml->date));

    $e = new Event;

    $e->uidcreated             = $user->uid;
    $e->uidlastupdated         = $user->uid;
    $e->approvedforcirculation = 1;
    $e->privatecomment         = 'Imported from athletics rss feed HASH:'.md5($event_xml->guid);

    $location = Location::getByName($event_xml->location);
    if (!$location) {
    	$location = new Location;
    	$location->name = $event_xml->location;
    	$location->save();
    }

    $event_exists = Event::getByPrivateComment('Imported from athletics rss feed HASH:'.md5($event_xml->guid));

    $additional_info = '';
    if (!empty($event_xml->tv)) {
        $additional_info = $event_xml->tv;
    }

    if ($event_exists) {
        $e = Event::getByPrivateComment('Imported from athletics rss feed HASH:'.md5($event_xml->guid));
        addDateTime($e, $starttime, $endtime, $location, $additional_info);
    } else {
        // insert
        $e->description = 'Nebraska Cornhuskers vs. '.$event_xml->opponent;
        $e->title = (string)$event_xml->sport . ' vs. ' . (string)$event_xml->opponent;
        $e->webpageurl = preg_replace('/\&SPSID=[\d]+\&Q_SEASON=[\d]+/', '', $event_xml->link);

        if ($e->insert($calendar, 'create event form')) {
        	$inserted++;
            echo 'Inserted event ' . $e->title . PHP_EOL;
            addDateTime($e, $starttime, $endtime, $location, $additional_info);
            $inserted++;
        } else {
        	echo 'Error inserting event ' . $e->title . PHP_EOL;
        	$errors++;
        }
    }

}

function addDateTime($e, $starttime, $endtime, $location, $additional_info)
{
    $datetimes = $e->getDatetimes();
    if (count($datetimes) == 0) {
        //insert
        echo "Insert event datetime" . PHP_EOL;
        $dt = new Occurrence;
        $dt->event_id = $e->id;
        $dt->starttime = $starttime;
        if ($endtime) {
            $dt->endtime = $endtime;
        }
        $dt->location_id = $location->id;
        $dt->additionalpublicinfo = $additional_info;
        $dt->recurringtype = 'none';
        $dt->insert();
        $inserted++;
        return true;
    }

    $dt = Occurrence::getByEvent_ID($e->id);
    if ($dt) {
	    if ($dt->starttime != $starttime
	        || $dt->location_id != $location->id
	        || $dt->additionalpublicinfo != trim($additional_info)) {

	        //Update
	        echo 'Update Event: ' . $e->id . '-' . $e->title . PHP_EOL;
	        echo "\t Eventdatetime: " . $dt->id . " from " . $dt->starttime . " to: " . $starttime . PHP_EOL;
	        echo "\t Location: from " . $dt->location_id . " to: " . $location->id . PHP_EOL;
	        echo "\t Additional Public info from: '" . $dt->additionalpublicinfo . "' to '" . $additional_info . "'" . PHP_EOL;
	        if ($endtime) {
	            $dt->endtime = $endtime;
	        }
	        $dt->starttime = $starttime;
	        $dt->location_id = $location->id;
	        $dt->additionalpublicinfo = $additional_info;
	        $dt->update();
	        $updated++;
	    } else {
	    	$no_change++;
	    }
	} else {
		echo 'error with datetime for event ' . $e->id . PHP_EOL;
		$errors++;
		return false;
	}

    return true;
}

echo PHP_EOL.'DONE'.PHP_EOL;
echo 'Records inserted: ' . $inserted . PHP_EOL;
echo 'Records updated: ' . $updated . PHP_EOL;
echo 'Unchanged: ' . $no_change . PHP_EOL;
echo 'Errors: ' . $errors . PHP_EOL;