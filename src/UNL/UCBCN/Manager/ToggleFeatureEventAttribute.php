<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\NotFoundException;
use UNL\UCBCN\Permission;
use UNL\UCBCN\UnexpectedValueException;
use UNL\UCBCN\User\PermissionException;

class ToggleFeatureEventAttribute extends PostHandler
{
	public $options = array();
	public $calendar;
	public $event;

	public function __construct($options = array())
	{
		$this->options = $options + $this->options;
		$this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
		if ($this->calendar === FALSE) {
			throw new NotFoundException("That calendar could not be found.", 404);
		}

		$this->event = Event::getByID($this->options['event_id']);
		if ($this->event === FALSE) {
			throw new \Exception("That event could not be found.", 404);
		}
	}

	public function handlePost(array $get, array $post, array $files)
	{
		$type = $post['type'];

		if ($type == 'feature') {
			$user = Auth::getCurrentUser();
			if (!$user->hasPermission(Permission::EVENT_FEATURE_ID, $this->calendar->id)) {
				throw new PermissionException("You do not have permission to feature events on this calendar.", 403);
			}
			$this->event->updateFeaturedWithCalendar($this->calendar, $post['featured'] === 'true');

		} else if ($type == 'pin') {
			$user = Auth::getCurrentUser();
			if (!$user->hasPermission(Permission::EVENT_FEATURE_ID, $this->calendar->id)) {
				throw new PermissionException("You do not have permission to pin events on this calendar.", 403);
			}

			$this->event->updatePinnedWithCalendar($this->calendar, $post['pinned'] === 'true');

		} else {
			throw new UnexpectedValueException("Invalid feature action.", 400);
		}
	}
}
