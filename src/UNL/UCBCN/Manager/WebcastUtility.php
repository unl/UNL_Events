<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Webcast as Webcast;
use UNL\UCBCN\Webcasts;

class WebcastUtility
{
	public static function addWebcast(array $post_data, $user)
	{
		// These need to match webcast table
		$allowed_fields = array(
			'title',
			'url',
			'additionalinfo',
		);

		$location = new Webcast;

		foreach ($allowed_fields as $field) {
			$value = $post_data['new_v_location'][$field];
			if (!empty($value)) {
				$location->$field = $value;
			}
		}

		if (array_key_exists('v_location_save', $post_data) && $post_data['v_location_save'] == 'on') {
			$location->user_id = $user->uid;
		}

		$location->insert();

		return $location;
	}

	public static function getUserWebcasts()
	{
		$user = Auth::getCurrentUser();
		return new Webcasts(array('user_id' => $user->uid));
	}
}
