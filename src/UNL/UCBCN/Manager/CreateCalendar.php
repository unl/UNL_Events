<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;

class CreateCalendar
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;

        # check if we are posting to this controller
        if (!empty($_POST)) {
            if (array_key_exists('calendar_shortname', $this->options)) {
                # we are editing an existing calendar
                $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

                if ($this->calendar === FALSE) {
                    throw new \Exception("That calendar could not be found.", 500);
                }

                $this->updateCalendar($_POST);
            } else {
                # we are creating a new calendar
                $this->calendar = $this->createCalendar($_POST);
            }

            header('Location: /manager/' . $this->calendar->shortname . '/');
        }

        # check if we are looking to edit a calendar
        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

            if ($this->calendar === FALSE) {
                throw new \Exception("That calendar could not be found.", 500);
            }
        } else {
            # we are creating a new calendar
            $this->calendar = new Calendar;
        }
    }

    private function updateCalendar($post_data)
    {
        $user = Auth::getCurrentUser();

        $this->calendar->name = $post_data['name'];
        $this->calendar->shortname = $post_data['shortname'];
        $this->calendar->website = $post_data['website'];
        switch ($post_data['event_release_preference']) {
            case '':
                $this->calendar->eventreleasepreference = NULL;
                break;
            case 'immediate':
                $this->calendar->eventreleasepreference = 1;
                break;
            case 'pending':
                $this->calendar->eventreleasepreference = 0;
                break;
            default:
                $this->calendar->eventreleasepreference = NULL;
        }

        $this->calendar->emaillists = $post_data['email_lists'];
        $this->calendar->recommendationswithinaccount = array_key_exists('recommend_within_account', $post_data) && 
            $post_data['recommend_within_account'] == 'on' ? 1 : 0;

        $this->calendar->datelastupdated = date('Y-m-d H:i:s');
        $this->calendar->uidlastupdated = $user->uid;

        $this->calendar->update();
    }

    private function createCalendar($post_data) 
    {
        $user = Auth::getCurrentUser();
        $account = $user->getAccount();

        $calendar = new Calendar;
        $calendar->account_id = $account->id;
        $calendar->name = $post_data['name'];
        $calendar->shortname = $post_data['shortname'];
        $calendar->website = $post_data['website'];
        switch ($post_data['event_release_preference']) {
            case '':
                $calendar->eventreleasepreference = NULL;
                break;
            case 'immediate':
                $calendar->eventreleasepreference = 1;
                break;
            case 'pending':
                $calendar->eventreleasepreference = 0;
                break;
            default:
                $calendar->eventreleasepreference = NULL;
        }

        $calendar->emaillists = $post_data['email_lists'];
        $calendar->recommendationswithinaccount = array_key_exists('recommend_within_account', $post_data) && 
            $post_data['recommend_within_account'] == 'on' ? 1 : 0;

        $calendar->datecreated = date('Y-m-d H:i:s');
        $calendar->uidcreated = $user->uid;
        $calendar->datelastupdated = date('Y-m-d H:i:s');
        $calendar->uidlastupdated = $user->uid;

        $calendar->insert();
        $calendar->addUser($user);

        return $calendar;
    }
}