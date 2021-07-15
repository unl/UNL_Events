<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Permission;

class EventForm extends PostHandler
{
	public $options = array();
	public $calendar;
	public $event;
	public $post;

	public function __construct($options = array())
	{
		$this->options = $options + $this->options;
		$this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

		if ($this->calendar === FALSE) {
			throw new EventFormException("That calendar could not be found.", 404);
		}

		$user = Auth::getCurrentUser();
		if (!$user->hasPermission(Permission::EVENT_CREATE_ID, $this->calendar->id)) {
			throw new EventFormException("You do not have permission to create an event on this calendar.", 403);
		}
	}

	public function getEventTypes()
	{
		return new EventTypes(array());
	}

	protected function setEventData($post_data)
	{
		$this->event->title = empty($post_data['title']) ? NULL : $post_data['title'];
		$this->event->subtitle = empty($post_data['subtitle']) ? NULL : $post_data['subtitle'];
		$this->event->description = empty($post_data['description']) ? NULL : $post_data['description'];

		$this->event->listingcontactname = empty($post_data['contact_name']) ? NULL : $post_data['contact_name'];
		$this->event->listingcontactphone = empty($post_data['contact_phone']) ? NULL : $post_data['contact_phone'];
		$this->event->listingcontactemail = empty($post_data['contact_email']) ? NULL : $post_data['contact_email'];

		$this->event->webpageurl = empty($post_data['website']) ? NULL : $post_data['website'];
		$this->event->approvedforcirculation = !empty($post_data['private_public']) && $post_data['private_public'] == 'private' ? 0 : 1;

		# for extraneous data aside from the event (location, type, etc)
		$this->post = $post_data;
	}
}
