<?php
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\ActiveRecord\RecordList;

class EventListing extends RecordList
{
    /**
     * Calendar \UNL\UCBCN\Frontend\Calendar Object
     *
     * @var \UNL\UCBCN\Frontend\Calendar
     */
    public $calendar;

    public $event_type_filter = '';
    public $audience_filter = '';

    /**
     * Constructor for an individual day.
     *
     * @param array $options Associative array of options to apply.
     */
    public function __construct($options)
    {
        if (!isset($options['calendar'])) {
            throw new InvalidArgumentException('A calendar must be set', 500);
        }

        $this->calendar = $options['calendar'];

        $this->event_type_filter = $options['type'] ?? "";
        $this->audience_filter = $options['audience'] ?? "";

        parent::__construct($options);
    }
    
    public function getDefaultOptions()
    {
        return array(
            'listClass' => __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\EventInstance',
        );
    }

    public function current()
    {
        try {
            if (\LimitIterator::valid()) {
                $options = $this->options + \LimitIterator::current();
                return new $this->options['itemClass']($options);
            }
        } catch (Exception $e) {
            // Exception thrown with current item so skip and process next item
            \LimitIterator::next();
            return $this->current();
        }
    }

    

    public function getSplitEventType(): array
    {
        if (empty($this->event_type_filter)) {
            return array();
        }

        // splits the audiences by comma
        $event_type_explode = explode(',', $this->event_type_filter);
        $event_type_explode = array_map('trim', $event_type_explode);

        return $event_type_explode;
    }

    public function getEventTypeSQL(string $sqlEventTypeTable){
        $sql = "";

        // splits the audiences by comma and creates the SQL for those
        if (!empty($this->event_type_filter)) {
            $event_type_explode = $this->getSplitEventType();

            $sql .= '(';
            foreach ($event_type_explode as $index => $single_event_type) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= $sqlEventTypeTable . '.name = \'' . self::escapeString($single_event_type) . '\'';
            }
            $sql .= ') ';
        }
        return $sql;
    }

    public function getEventTypeURLParam() {
        $url = "";

        if (!empty($this->event_type_filter)) {
            $url .= 'type=' . urlencode($this->event_type_filter);
        }

        return $url;
    }

    public function getSplitAudiences(): array
    {
        if (empty($this->audience_filter)) {
            return array();
        }

        // splits the audiences by comma
        $audiences_explode = explode(',', $this->audience_filter);
        $audiences_explode = array_map('trim', $audiences_explode);

        return $audiences_explode;
    }

    public function getAudienceSQL(string $sqlAudienceTable){
        $sql = "";

        // splits the audiences by comma and creates the SQL for those
        if (!empty($this->audience_filter)) {
            $audiences_explode = $this->getSplitAudiences();

            $sql .= '(';
            foreach ($audiences_explode as $index => $single_audience) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= $sqlAudienceTable . '.name = \'' . self::escapeString($single_audience) . '\'';
            }
            $sql .= ') ';
        }

        return $sql;
    }

    public function getAudienceURLParam() {
        $url = "";

        if (!empty($this->audience_filter)) {
            $url .= 'audience=' . urlencode($this->audience_filter);
        }

        return $url;
    }
}