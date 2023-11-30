<?php
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Event\RecurringDate;

class EventInstance implements RoutableInterface, MetaTagInterface
{
    /**
     * The event date & time record
     *
     * @var \UNL\UCBCN\Event\Occurrence
     */
    public $eventdatetime;

    /**
     * The event details
     *
     * @var \UNL\UCBCN\Event
     */
    public $event;

    /**
     * @var RecurringDate
     */
    public $recurringdate;

    /**
     * Calendar \UNL\UCBCN\Frontend\Calendar Object
     *
     * @var \UNL\UCBCN\Frontend\Calendar
     */
    public $calendar;

    public $options;

    function __construct($options = array())
    {
        if (!isset($options['id'])) {
            throw new InvalidArgumentException('No event specified', 404);
        }

        if (!isset($options['calendar'])) {
            throw new InvalidArgumentException('A calendar must be set', 500);
        }

        $this->calendar = $options['calendar'];

        $this->eventdatetime = Occurrence::getById($options['id']);

        if (false === $this->eventdatetime) {
            throw new UnexpectedValueException('No event with that id exists', 404);
        }

        //Find the requested date, and ensure format
        $requestedDate = date('Y-m-d', strtotime($this->eventdatetime->starttime));
        if (isset($options['y'], $options['m'], $options['d'])) {
            $requestedDate = date('Y-m-d', strtotime($options['y'] . '-' . $options['m'] . '-' . $options['d']));
        }

        //Set the recurring date
        if (Occurrence::RECURRING_TYPE_NONE != $this->eventdatetime->recurringtype &&
            isset($options['recurringdate_id'])
        ) {
            //Set the recurring date by the id
            $this->recurringdate = RecurringDate::getByID($options['recurringdate_id']);
        } else if ($requestedDate != date('Y-m-d', strtotime($this->eventdatetime->starttime))) {
            //Try to find the recurring date by the eventdatetime.id and Y/m/d
            $this->recurringdate = RecurringDate::getByAnyField(
                '\\UNL\\UCBCN\\Event\\RecurringDate',
                'recurringdate',
                $requestedDate,
                'event_id = ' . (int)$this->eventdatetime->event_id . ' AND unlinked = 0'
            );
            // Recurring Event must have a valid recurring date
            if (Occurrence::RECURRING_TYPE_NONE != $this->eventdatetime->recurringtype &&
                empty($this->recurringdate)
            ) {
              throw new UnexpectedValueException('No recurring event exists for day', 404);
            }
        }

        // Always include images with XML and JSON formats
        if (isset($_GET['format']) &&
            (strtolower($_GET['format']) == 'xml' || strtolower($_GET['format']) == 'json')
        ) {
            $options['includeEventImageData'] = true;
        }

        // get event with image data if includeEventImageData is not set or is true
        $this->event = $this->eventdatetime->getEvent(
            !isset($options['includeEventImageData']) || $options['includeEventImageData'] === true
        );
        $this->options = $options;
    }

    /**
     * Get an event instance
     *
     * @param int $id Primary Key for eventdatetime table
     *
     * @return \UNL\UCBCN\Frontend\EventInstance
     */
    public static function getById($id)
    {
        return new self(array('id'=>$id));
    }

    /**
     * @return string - The absolute url for the event instance
     */
    public function getURL()
    {
        return $this->calendar->getURL() .
            date('Y/m/d/', strtotime($this->getStartTime())) .
            $this->eventdatetime->id . '/';
    }

    public function getImageURL()
    {
        if (isset($this->event->imageurl)) {
            return $this->event->imageurl;
        } elseif (isset($this->event->imagedata)) {
            return \UNL\UCBCN\Frontend\Controller::$url . 'images/' . $this->event->id;
        }

        return false;
    }

    /**
     * Determines if this is an ongoing event.
     *
     * An 'ongoing' event is defined as an event that spans more than one day.
     *
     * @return bool
     */
    public function isOngoing()
    {
        if (empty($this->eventdatetime->endtime)) {
            return false;
        }

        $start = date('m-d-Y', strtotime($this->eventdatetime->starttime));
        $end   = date('m-d-Y', strtotime($this->eventdatetime->endtime));

        //It is not an ongoing event if it starts and ends on the same day.
        if ($start == $end) {
            return false;
        }

        return true;
    }

    /**
     * Determines if this event is currently in progress.
     *
     * @return bool
     */
    public function isInProgress()
    {
        $currentTime = time();

        if (strtotime($this->eventdatetime->starttime) > $currentTime) {
            //It has not started yet.
            return false;
        }

        if (strtotime($this->eventdatetime->endtime) < $currentTime) {
            //It already finished.
            return false;
        }

        return false;
    }

    /**
     * Determines if this event is an all day event.
     *
     * @return bool
     */
    public function isAllDay()
    {
        //It must start at midnight to be an all day event
        if (strpos($this->eventdatetime->starttime, '00:00:00') === false) {
            return false;
        }

        //It must end at midnight, or not have an end date.
        if (!empty($this->eventdatetime->endtime) &&
            strpos($this->eventdatetime->endtime, '00:00:00') === false) {
            return false;
        }

        return true;
    }

    /**
     * Get the start time for this event instance
     *
     * Takes into account current recurring date, if present.
     * This should always be used instead of directly accessing $this->eventdatetime->starttime
     *
     * @return string
     */
    public function getStartTime()
    {
        $time = $this->eventdatetime->starttime;

        if ($this->eventdatetime->isRecurring() &&
            isset($this->recurringdate) &&
            $this->recurringdate instanceof \UNL\UCBCN\Event\RecurringDate
        ) {
            $first_recurring_date = $this->recurringdate->getFirstRecordInOngoingSeries();
            if (isset($first_recurring_date->recurringdate)) {
                $time = $first_recurring_date->recurringdate . ' ' . substr($time, 11);
            }
        }

        return $time;
    }

    /**
     * Get the end time for this event instance
     *
     * Takes into account the current recurring date, if present.
     * This should always be used instead of directly accessing $this->eventdatetime->endtime
     */
    public function getEndTime()
    {
        $time = $this->eventdatetime->endtime;

        if (empty($time)) {
            return $time;
        }

        if ($this->eventdatetime->isRecurring() &&
            isset($this->recurringdate) &&
            $this->recurringdate instanceof \UNL\UCBCN\Event\RecurringDate
        ) {
            $diff = strtotime($this->eventdatetime->endtime) - strtotime($this->eventdatetime->starttime);

            $time = date('Y-m-d H:i:s', strtotime($this->getStartTime()) + $diff);
        }

        return $time;
    }

    public function getShortDescription($maxChars = 250)
    {
        // normalize line endings
        $fullDescription = str_replace("\r\n", "\n", $this->event->description);

        // break on paragraphs
        $fullDescription = explode("\n", $fullDescription, 2);

        if (mb_strlen($fullDescription[0]) > $maxChars) {
            // find the maximum number of characters that do not break a word
            preg_match("/.{1,$maxChars}(?:\\b|$)/s", $fullDescription[0], $matches);
            return $matches[0] . ' …';
        }

        return $fullDescription[0];
    }

    // Sets the meta tags for the page
    public function getMetaTags()
    {
        //TODO: Update this to take into account the time modes
        $datetimeString = date('n/d/y @ g:ia', strtotime($this->eventdatetime->starttime));
        if ($this->isAllDay()) {
            $datetimeString = date('n/d/y', strtotime($this->eventdatetime->starttime));
        }

        $title = $this->event->displayTitle($this) . ' - ' . $datetimeString;

        // Add a description if it is set
        $description = 'Event on ' . $datetimeString;
        if (isset($this->event->description) && !empty($this->event->description)) {
            $description = $this->event->description;
        }

        // Add a image if it is set
        $image = '';
        if (isset($this->event->imagedata) && !empty($this->event->imagedata)) {
            $image = MetaTagUtility::getSiteURL() . '?image&amp;id=' . $this->event->id;
        }

        // Build the options
        $options = array(
            'image' => $image,
            'label1' => 'Calendar',
            'data1' => $this->calendar->name,
        );

        $location = $this->eventdatetime->getLocation();
        $webcast = $this->eventdatetime->getWebcast();

        if ($location !== false && $webcast !== false) {
            $options['label2'] = 'In-Person and Online';
            $options['data2'] = $location->name . ' & ' . $webcast->title;
        } elseif ($location !== false) {
            $options['label2'] = 'Location';
            $options['data2'] = $location->name;
        } elseif ($webcast !== false) {
            $options['label2'] = 'Virtual Location';
            $options['data2'] = $webcast->title;
        }

        $metaTagUtility = new MetaTagUtility($this->getURL(), $title, $description, $options);

        return $metaTagUtility->getMetaTags();
    }

    /**
     * Checks to see if the location has all information necessary for Google's microdata.
     *
     * @return bool
     */
    public function microdataCheck()
    {
        // We need a title
        if (!isset($this->event->title) || empty($this->event->title)) {
            return false;
        }

        // We need a start time
        if (!isset($this->eventdatetime->starttime) || empty($this->eventdatetime->starttime)) {
            return false;
        }

        // We need at least a location or a virtual location or both
        if (!isset($this->eventdatetime->location_id) && !isset($this->eventdatetime->webcast_id)) {
            return false;
        }

        // Check if the location valid
        if (isset($this->eventdatetime->location_id)) {
            $location = $this->eventdatetime->getLocation();
            if ($location !== false && !$location->microdataCheck()) {
                return false;
            }
        }

        // Check if the virtual location is valid
        if (isset($this->eventdatetime->webcast_id)) {
            $webcast = $this->eventdatetime->getWebcast();
            if ($webcast !== false && !$webcast->microdataCheck()) {
                return false;
            }
        }

        return true;
    }

    public function toJSONData()
    {
        $timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($this->eventdatetime->timezone);
        $location   = $this->eventdatetime->getLocation();
        $eventTypes = $this->event->getEventTypes();
        $eventAudiences = $this->event->getAudiences();
        $webcast   = $this->eventdatetime->getWebcast();
        $documents  = $this->event->getDocuments();
        $contacts   = $this->event->getPublicContacts();
        $originCalendar = $this->event->getOriginCalendar();
        $data       = array();


        $data['EventID']       = $this->event->id;
        $data['Status']        = $this->event->icalStatus($this);
        $data['EventTitle']    = $this->event->displayTitle($this);
        $data['EventSubtitle'] = $this->event->subtitle;
        //TODO: Add time mode output
        $data['DateTime'] = array(
            'DateTimeID' => $this->eventdatetime->id,
            'Start' => $timezoneDateTime->format($this->getStartTime(),'c'),
            'End'   => $timezoneDateTime->format($this->getEndTime(),'c'),
            'AllDay' => $this->isAllDay(),
            'EventTimezone' => $this->eventdatetime->timezone,
            'CalendarTimezone' => $this->calendar->defaulttimezone,
            'IsRecurring'=> $this->eventdatetime->isRecurring(),
            'RecursUntil'=> $this->eventdatetime->recurs_until,
            'RecursType'=> $this->eventdatetime->recurringtype,
            'RecursMonthType'=> $this->eventdatetime->rectypemonth,
            'RecurrenceID'=> $this->eventdatetime->isRecurring() ? $this->recurringdate->id : null,
        );
        $data['EventStatus']           = $this->event->isCanceled($this) ? 'Canceled' : 'Happening As Scheduled';
        $data['Classification']        = 'Public';
        $data['Languages']['Language'] = 'en-US';
        $data['EventTransparency']     = $this->event->transparency;
        $data['Description']           = $this->event->description;
        $data['ShortDescription']      = $this->event->shortdescription;
        $data['Refreshments']          = $this->event->refreshments;

        $data['Locations'] = array();
        if ($location !== false) {
            $data['Locations'][0] = array(
                'LocationID'    => $location->id,
                'LocationName'  => $location->name,
                'LocationTypes' => array('LocationType' => $location->type),
                'Address' => array(
                    'Room' =>
                        !empty($this->eventdatetime->room) ?
                        $this->eventdatetime->room : $location->room,
                    'BuildingName'         => $location->name,
                    'CityName'             => $location->city,
                    'PostalZone'           => $location->zip,
                    'CountrySubentityCode' => $location->state,
                    'Country' => array(
                        'IdentificationCode' => 'US',
                        'Name'               => 'United States',
                    ),
                ),
                'Phones' => array(
                    0 => array(
                        'PhoneNumber' => $location->phone,
                    )
                ),
                'WebPages' => array(
                    0 => array(
                        'Title' => 'Location Web Page',
                        'URL'   => $location->webpageurl,
                    )
                ),
                'MapLinks' => array(
                    0 => $location->mapurl,
                ),
                'LocationHours'        => $location->hours,
                'Directions' =>
                    !empty($this->eventdatetime->directions) ?
                    $this->eventdatetime->directions : $location->directions,
                'AdditionalPublicInfo' =>
                    !empty($this->eventdatetime->location_additionalpublicinfo) ?
                    $this->eventdatetime->location_additionalpublicinfo : $location->additionalpublicinfo,
            );
        }

        if (!empty($this->eventdatetime->room)) {
            $data['Room'] = $this->eventdatetime->room;
        } elseif ($location !== false) {
            $data['Room'] = $location->room;
        } else {
            $data['Room'] = null;
        }

        if ($eventTypes->count()) {
            $data['EventTypes'] = array();
            foreach ($eventTypes as $eventHasType) {
                $type = $eventHasType->getType();
                if ($type) {
                    $data['EventTypes'][] = array(
                        'EventTypeID'          => $type->id,
                        'EventTypeName'        => $type->name,
                        'EventTypeDescription' => $type->description,
                    );
                }
            }
        }

        if ($eventAudiences->count()) {
            $data['EventAudiences'] = array();
            foreach ($eventAudiences as $targetAudience) {
                $audience = $targetAudience->getAudience();
                if ($audience) {
                    $data['EventAudiences'][] = array(
                        'EventAudienceID'          => $audience->id,
                        'EventAudienceName'        => $audience->name
                    );
                }
            }
        }

        $data['WebPages'] = array();
        $data['WebPages'][] = array(
            'Title' => 'Event Instance URL',
            'URL'   => $this->getURL(),
        );

        if ($this->event->webpageurl) {
            $data['WebPages'][] = array(
                'Title' => 'Event webpage',
                'URL'   => $this->event->webpageurl,
            );
        }

        $data['Webcasts'] = array();
        if ($webcast !== false) {
            $data['Webcasts'][0] = array(
                'WebcastID'    => $webcast->id,
                'WebcastTitle'  => $webcast->title,
                'WebcastURL'  => $webcast->url,
                'AdditionalPublicInfo' =>
                    !empty($this->eventdatetime->webcast_additionalpublicinfo) ?
                    $this->eventdatetime->webcast_additionalpublicinfo : $webcast->additionalinfo,
            );
        }

        if (isset($this->event->imagedata)) {
            $data['Images'][0] = array(
                'Title'       => 'Image',
                'Description' => 'image for event ' . $this->event->id,
                'URL'         => \UNL\UCBCN\Frontend\Controller::$url . '?image&amp;id=' . $this->event->id,
            );
        }

        if ($documents->count()) {
            $data['Documents'] = array();
            foreach ($documents as $document) {
                $data['Documents'][] = array(
                    'Title' => $document->name,
                    'URL'   => $document->url,
                );
            }
        }

        if ($contacts->count()) {
            $data['PublicEventContacts'] = array();
            foreach ($contacts as $contact) {
                $data['PublicEventContacts'][] = array(
                    'PublicEventContactID' => $contact->id,
                    'ContactName' => array(
                        'FullName' => $contact->name,
                    ),
                    'ProfessionalAffiliations' => array(
                        0 => array(
                            'JobTitles' => array(
                                0 => $contact->jobtitle,
                            ),
                            'OrganizationName' => $contact->organization,
                            'OrganizationWebPages' => array(
                                0 => array(
                                    'Title' => $contact->name,
                                    'URL'   => $contact->webpageurl,
                                ),
                            )
                        ),
                    ),
                    'Phones' => array(
                        0 => array(
                            'PhoneNumber' => $contact->phone,
                        ),
                    ),
                    'EmailAddresses' => array(
                        0 => $contact->emailaddress
                    ),
                    'Addresses' => array(
                        0 => array(
                            'StreetName'           => $contact->addressline1,
                            'AdditionalStreetName' => $contact->addressline2,
                            'Room'                 => $contact->room,
                            'CityName'             => $contact->city,
                            'PostalZone'           => $contact->zip,
                            'CountrySubentityCode' => $contact->State,
                            'Country' => array(
                                'IdentificationCode' => 'US',
                                'Name' => 'United States',
                            ),
                        ),
                    ),
                    'WebPages' => array(
                        0 => array(
                            'Title' => $contact->name,
                            'URL'   => $contact->webpageurl,
                        ),
                    ),
                );
            }
        }

        $data['PublicEventContacts'] = array(
            0 => array(
                'ContactName' => array(
                    'FullName' => $this->event->listingcontactname,
                ),
                'Phones' => array(
                    0 => array(
                        'PhoneNumber' => $this->event->listingcontactphone,
                    ),
                ),
                'EmailAddresses' => array(
                    0 => $this->event->listingcontactemail,
                ),
            ),
        );

        if (!empty($this->event->privatecomment)) {
            $data['PrivateComments'] = array(
                0 => $this->event->privatecomment,
            );
        }

        if ($originCalendar instanceof \UNL\UCBCN\Calendar) {
            $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $data['OriginCalendar'] = array(
                'CalendarID' => $originCalendar->id,
                'AccountID' => $originCalendar->account_id,
                'Name' => $originCalendar->name,
                'ShortName' => $originCalendar->shortname,
                'URL' =>   $protocol . $_SERVER['SERVER_NAME'] . '/' . urlencode($originCalendar->shortname)
            );
        }

        return $data;
	}

     /**
     * Formats event data in google microdata standard for events.
     *
     * @return Array
     */
    public function getFormattedMicrodata()
    {
        $timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($this->eventdatetime->timezone);
        $location   = $this->eventdatetime->getLocation();
        $webcast    = $this->eventdatetime->getWebcast();
        $data       = array();
        $type_string = '@type';

        $data['@context'] = 'https://schema.org';
        $data[$type_string] = 'Event';
        $data['name'] = $this->event->title;

        if (isset($this->event->description)) {
            $data['description'] = $this->event->description;
        }

        if (isset($this->event->imageurl)) {
            $data['image'] = array(
                $this->event->imageurl,
            );
        }

        if ($this->isAllDay()) {
            $data['startDate'] =  date('m-d-Y', strtotime( $this->getStartTime()));
        } else {
            $start_date_iso1801 = $timezoneDateTime->format($this->getStartTime(), DATE_ATOM);
            $end_date_iso1801 = $timezoneDateTime->format($this->getEndTime(), DATE_ATOM);
            $data['startDate'] = $start_date_iso1801;
            if ($start_date_iso1801 != $end_date_iso1801) {
                $data['endDate'] = $end_date_iso1801;
            }
        }

        $data['eventStatus'] = $this->event->isCanceled($this) ?
            'https://schema.org/EventCancelled' : 'https://schema.org/EventScheduled';

        if (isset($this->eventdatetime->location_id) && isset($this->eventdatetime->webcast_id)) {
            $data['eventAttendanceMode'] = 'https://schema.org/MixedEventAttendanceMode';
        } elseif (isset($this->eventdatetime->location_id) && !isset($this->eventdatetime->webcast_id)) {
            $data['eventAttendanceMode'] = 'https://schema.org/OfflineEventAttendanceMode';
        } else {
            $data['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
        }

        $data['location'] = array();
        if (isset($this->eventdatetime->location_id) && $location !== false) {
            $location_data = array();
            $location_data[$type_string] = 'Place';
            $location_data['name'] = $location->name;
            $location_data['address'] = array(
                $type_string => 'PostalAddress',
                'streetAddress' => $location->streetaddress1 . ((isset($location->streetaddress2)) ?
                    ' ' . $location->streetaddress2 : ''),
                'postalCode' => $location->zip,
                'addressRegion' => $location->state,
                'addressCountry' => 'US',
            );

            $data['location'][] = $location_data;
        }

        if (isset($this->eventdatetime->webcast_id) && $webcast !== false) {
            $webcast_data = array();
            $webcast_data[$type_string] = 'VirtualLocation';
            $webcast_data['url'] = $webcast->url;

            $data['location'][] = $webcast_data;
        }

        if (isset($this->event->imagedata)) {
            $data['image'] = array();
            $data['image'][] = \UNL\UCBCN\Frontend\Controller::$url . '?image&amp;id=' . $this->event->id;
        }

        if (isset($this->event->listingcontacttype)) {
            $data['organizer'] = array();
            if ($this->event->listingcontacttype === 'person') {
                $data['organizer'][$type_string] = 'Person';
            } else {
                $data['organizer'][$type_string] = 'organization';
            }
            $data['organizer']['name'] = $this->event->listingcontactname;
            $data['organizer']['url'] = $this->event->listingcontacturl;
        }

        return $data;
    }

    public function getMonthWidget()
    {
        return new MonthWidget($this->options);
    }
}
