<?php
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\Calendar\Audiences;
use UNL\UCBCN\Calendar\EventTypes;

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

    /**
     * Gets list of all event types
     *
     * @return bool|EventTypes - false if no event type, otherwise return recordList of all event types
     */
    public function getEventTypes()
    {
        return new EventTypes(array('order_name' => true));
    }

    /**
     * Gets an array of event types from filter value
     *
     * @return array<string> Array of the event types in the filter
     */
    public function getSplitEventType(): array
    {
        return $this->getSplitFilters($this->event_type_filter);
    }

    /**
     * Returns the count of the items in the event type filter
     * - This is only here because savvy will mess up the array
     *
     * @return int
     */
    public function getEventTypeCount(): int
    {
        return count($this->getSplitEventType());
    }

    /**
     * Gets an SQL string to be used in the where clause
     *
     * @param string $sqlEventTypeTable Name of the event type table or alias
     * @return string Array of the event types in the filter
     */
    public function getEventTypeSQL(string $sqlEventTypeTable): string
    {

        return $this->getFilterSQL($sqlEventTypeTable, "name", $this->event_type_filter);
    }

    /**
     * formats a string to be appended to url params
     * - Does not modify original url_params
     *
     * @param string $url_params String of the url params already established
     * @return string formatted url param string
     */
    public function getEventTypeURLParam(string $url_params): string
    {
        return $this->getFilterURLParam($url_params, "type", $this->event_type_filter);
    }

    /**
     * returns nicely formatted string of the event types from the search query
     *
     * @return string formatted string following best grammar practices
     */
    public function getFormattedEventTypes(): string
    {
        return $this->getFormattedFilter($this->event_type_filter);
    }


    /**
     * Gets list of all audiences
     *
     * @return bool|Audiences - false if no audiences, otherwise return recordList of all audiences
     */
    public function getAudiences()
    {
        return new Audiences(array('order_name' => true));
    }

    /**
     * Gets an array of audiences from filter value
     *
     * @return array<string> Array of the audiences in the filter
     */
    public function getSplitAudiences(): array
    {
        return $this->getSplitFilters($this->audience_filter);
    }

    /**
     * Returns the count of the items in the audience filter
     * - This is only here because savvy will mess up the array
     *
     * @return int
     */
    public function getAudienceCount(): int
    {
        return count($this->getSplitAudiences());
    }

    /**
     * Gets an SQL string to be used in the where clause
     *
     * @param string $sqlAudienceTable Name of the audience table or alias
     * @return string Array of the audiences in the filter
     */
    public function getAudienceSQL(string $sqlAudienceTable): string
    {
        return $this->getFilterSQL($sqlAudienceTable, "name", $this->audience_filter);
    }

    /**
     * formats a string to be appended to url params
     * - Does not modify original url_params
     *
     * @param string $url_params String of the url params already established
     * @return string formatted url param string
     */
    public function getAudienceURLParam(string $url_params): string
    {
        return $this->getFilterURLParam($url_params, "audience", $this->event_type_filter);
    }

    /**
     * returns nicely formatted string of the audiences from the search query
     *
     * @return string formatted string following best grammar practices
     */
    public function getFormattedAudiences(): string
    {
        return $this->getFormattedFilter($this->audience_filter);
    }

    /**
     * Gets an array of strings from filter value
     * - Splits by comma
     *
     * @param string $filter_value value of the filter to split
     * @return array<string> Array of the audiences in the filter
     */
    private function getSplitFilters(string $filter_value): array
    {
        if (empty($filter_value)) {
            return array();
        }

        // splits the filter by comma
        $filter_explode = explode(',', $filter_value);
        $filter_explode = array_map('trim', $filter_explode);

        return $filter_explode;
    }

    /**
     *  Gets an SQL string to be used in the where clause
     *
     * @param string $sql_table Name of the sql table or alias
     * @param string $sql_column Name of the column for that data type (usually name)
     * @param string $filter_value Value of the filters to be searched for
     * @return string Array of the audiences in the filter
     */
    private function getFilterSQL(string $sql_table, string $sql_column, string $filter_value): string
    {
        $sql = "";

        // splits the audiences by comma and creates the SQL for those
        if (!empty($filter_value)) {
            $filter_explode = $this->getSplitFilters($filter_value);

            $sql .= '(';
            foreach ($filter_explode as $index => $single_filter) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= $sql_table . '.' . $sql_column . ' = \'' . self::escapeString($single_filter) . '\'';
            }
            $sql .= ') ';
        }

        return $sql;
    }

    /**
     * formats a string to be appended to url params
     * - Does not modify original url_params
     *
     * @param string $url_params String of the url params already established
     * @param string $param_name Name of the url param to assign values to
     * @param string $filter_value Value of the filter to be used to the URL
     * @return string formatted url param string
     */
    private function getFilterURLParam(string $url_params, string $param_name, string $filter_value): string
    {
        $url = "";

        if (!empty($filter_value)) {
            if (empty($url_params)) {
                $url_params .= "?";
            } else {
                $url_params .= "&";
            }
            $url .= $param_name . '=' . urlencode($filter_value);
        }

        return $url;
    }

    /**
     * returns nicely formatted string of the audiences from the search query
     *
     * @param string $filter_value Value of the filter to be formatted
     * @return string formatted string following best grammar practices
     */
    private function getFormattedFilter(string $filter_value): string
    {
        $output_string = '';
        $filter_explode = $this->getSplitFilters($filter_value);
        $last_index = count($filter_explode) - 1;

        foreach ($filter_explode as $index => $single_filter) {
            if ($index > 0 && $last_index >= 2) {
                $output_string .= ', ';
            } elseif ($index > 0) {
                $output_string .= ' ';
            }
            if ($index === $last_index && $index > 0) {
                $output_string .= 'or ';
            }
            $output_string .= ucwords($single_filter);
        }

        return $output_string;
    }
}