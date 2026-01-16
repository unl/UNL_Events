<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Calendar\Audiences;
use UNL\UCBCN\Permission;

class EventForm extends PostHandler
{
	const MODE_CREATE = 'MODE_CREATE';
	const MODE_UPDATE = 'MODE_UPDATE';

	public $options = array();
	public $calendar;
	public $event;
	public $post;
	public $mode;

	public function __construct($options = array())
	{
		$this->options = $options + $this->options;
		$this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

		if ($this->calendar === FALSE) {
			throw new EventFormException("That calendar could not be found.", 404);
		}

		$user = $this->options['user'] ?? Auth::getCurrentUser();
		if (!$user->hasPermission(Permission::EVENT_CREATE_ID, $this->calendar->id)) {
			throw new EventFormException("You do not have permission to create an event on this calendar.", 403);
		}
	}

	public function getEventTypes()
	{
		return new EventTypes(array('order_name' => true));
	}

	public function getAudiences()
	{
		return new Audiences(array('order_name' => true));
	}

	protected function setEventData($post_data)
	{
		$this->event->title = empty($post_data['title']) ? null : $post_data['title'];
		$this->event->subtitle = empty($post_data['subtitle']) ? null : $post_data['subtitle'];
		$this->event->description = empty($post_data['description']) ? null : $this->sanitizeWordCharacters($post_data['description']);
		$this->event->canceled = !empty($post_data['canceled']) ? 1 : 0;

		$this->event->listingcontactname = empty($post_data['contact_name']) ? null : $post_data['contact_name'];
		$this->event->listingcontactphone = empty($post_data['contact_phone']) ? null : $post_data['contact_phone'];
		$this->event->listingcontactemail = empty($post_data['contact_email']) ? null : $post_data['contact_email'];
		$this->event->listingcontacturl = empty($post_data['contact_website']) ? null : $post_data['contact_website'];
		$this->event->listingcontacttype = empty($post_data['contact_type']) ? null : $post_data['contact_type'];

		$this->event->webpageurl = empty($post_data['website']) ? null : $post_data['website'];
		$this->event->approvedforcirculation = !empty($post_data['private_public']) && $post_data['private_public'] == 'private' ? 0 : 1;

		# for extraneous data aside from the event (location, type, etc)
		$this->post = $post_data;
	}

	protected function validateEventImage($post_data, $files) {
		if (!empty($post_data['cropped_image_data'])) {
			$this->setCroppedImage($post_data);
		} else if ($this->mode === self::MODE_UPDATE && array_key_exists('remove_image', $post_data) && $post_data['remove_image'] == 'on') {
			$this->removeImage($post_data);
		} else if (isset($files['imagedata']) && is_uploaded_file($files['imagedata']['tmp_name'])) {
			$this->setUploadImage();
		} else if (isset($files['imagedata']) && $files['imagedata']['error'] == UPLOAD_ERR_INI_SIZE) {
			throw new ValidationException('Your image file size was too large. It must be 2 MB or less. Try a tool like <a target="_blank" href="http://www.imageoptimizer.net">Image Optimizer</a>.');
		} else if ($this->mode === self::MODE_CREATE && $post_data['send_to_main'] === 'on') {
			throw new ValidationException('An image is required for events considered for main UNL Calendar.');
		} else if ($this->mode === self::MODE_UPDATE && empty($this->event->imagedata) && ($this->on_main_calendar || isset($post_data['send_to_main']))) {
			throw new ValidationException('An image is required for events considered for main UNL Calendar.');
		}
	}

	private function setCroppedImage($post_data) {
		$image_parts = explode(";base64,", $post_data['cropped_image_data']);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		$raw_image_base64 = base64_decode($image_parts[1]);

		// Compress image if possible
		if ($image_type == 'jpeg' || $image_type == 'gif' || $image_type == 'png') {
			ob_start();
			$image = imagecreatefromstring($raw_image_base64);
			if ($image_type == 'jpeg') {
				imagejpeg($image, null, 90);
			} elseif ($image_type == 'gif') {
				imagegif($image);
			} elseif ($image_type == 'png') {
				imagepng($image, null, 9, PNG_NO_FILTER);
			}

			$image_base64 = ob_get_clean();
			imagedestroy($image);
		} else {
			$image_base64 = $raw_image_base64;
		}

		$this->event->imagemime = 'image/' . $image_type;
		$this->event->imagedata = $image_base64;
	}

	private function removeImage($post_data) {
		if ($this->on_main_calendar || isset($post_data['send_to_main'])) {
			throw new ValidationException('Image can not be removed. Image is required for events considered for main UNL Calendar.');
		} else {
			$this->event->imagemime = null;
			$this->event->imagedata = null;
		}
	}

	private function setUploadImage() {
		$uploadFile = new FileUpload('imagedata', FileUpload::TYPE_IMAGE);
		if ($uploadFile->isValid()) {
			$uploadFile->compressImage();
			$this->event->imagemime = $uploadFile->getType();
			$this->event->imagedata = file_get_contents($uploadFile->getPath());
		} else {
			$message = 'Your uploaded image has error(s): <ul>';
			foreach($uploadFile->getValidationErrors() as $error) {
				$message .= '<li>' . $error . '</li>';
			}
			$message .= '</ul>';
			throw new ValidationException($message);
		}
	}

	/**
     * Sanitizes text by replacing Word's special characters with standard equivalents
     *
     * @param string $text The text to sanitize
     * @return string The sanitized text
     */
    private function sanitizeWordCharacters($text) {
        // Define replacements for common Word special characters
        $replacements = [
            // Smart quotes
            '\u2018' => "'",  // Left single quote
            '\u2019' => "'",  // Right single quote
            '\u201A' => "'",  // Single low-9 quote
            '\u201B' => "'",  // Single high-reversed-9 quote
            '\u201C' => '"',  // Left double quote
            '\u201D' => '"',  // Right double quote
            '\u201E' => '"',  // Double low-9 quote
            '\u201F' => '"',  // Double high-reversed-9 quote

            // Dashes and hyphens
            '\u2010' => '-',  // Hyphen
            '\u2011' => '-',  // Non-breaking hyphen
            '\u2012' => '-',  // Figure dash
            '\u2013' => '-',  // En dash
            '\u2014' => '-',  // Em dash
            '\u2015' => '-',  // Horizontal bar

            // Spaces
            '\u00A0' => ' ',  // Non-breaking space
            '\u2003' => ' ',  // Em space
            '\u2002' => ' ',  // En space
            '\u2009' => ' ',  // Thin space

            // Other common characters
            '\u2026' => '...',  // Ellipsis
            '\u2022' => '*',    // Bullet
            '\u00B7' => '*',    // Middle dot
            '\u00A9' => '(c)',  // Copyright
            '\u00AE' => '(R)',  // Registered trademark
            '\u2122' => '(TM)', // Trademark
        ];

        // Apply all replacements
        foreach ($replacements as $search => $replace) {
            $text = str_replace(json_decode('"' . $search . '"'), $replace, $text);
        }

        // Additional safety: remove any remaining non-ASCII characters
		// that might cause database issues, but preserve newlines and tabs
		$text = preg_replace('/[^\x20-\x7E\r\n\t]/', '', $text);

        return $text;
    }
}
