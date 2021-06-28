<?php
namespace UNL\UCBCN;

class FileUpload
{
	private $imageInfo;
	private $mimeType;
	private $type;
	private $upload;
	private $errors = array();

	const MAX_SIZE_BYTES = 2097152;
	const TYPE_IMAGE = 'TYPE_IMAGE';

	public function __construct($uploadIdentifier, $type = self::TYPE_IMAGE)
	{
		if (isset($_FILES[$uploadIdentifier])) {
			$this->upload = $_FILES[$uploadIdentifier];
			$this->type = $type;

			$this->mimeType = $this->upload['type'];

			if ($this->type === self::TYPE_IMAGE) {
				$this->imageInfo = @getimagesize($this->upload['tmp_name']);
			}
		}
	}

	public function isValid() {
		$this->validate();
		return $this->hasErrors();
	}

	public function getValidationErrors() {
		return $this->errors;
	}

	public function getPath() {
		return $this->upload["tmp_name"];
	}

	public function getType() {
		return $this->upload["type"];
	}

	public function compressImage() {
		$source = $this->upload['tmp_name'];
		$mime = isset($this->imageInfo['mime']) ? $this->imageInfo['mime'] : NULL;

		if ($mime == 'image/jpeg') {
			$image = imagecreatefromjpeg($source);
			imagejpeg($image, $source, 90);
		} elseif ($mime == 'image/gif') {
			$image = imagecreatefromgif($source);
			imagegif($image, $source);
		} elseif ($mime == 'image/png') {
			$image = imagecreatefrompng($source);
			imagepng($image, $source, 9, PNG_NO_FILTER);
	    } else {
			return; // invalid mime, bail
        }

		imagedestroy($image);
	}

	private function validate() {
		$this->errors = array();
		$this->validateFile();

		if ($this->hasErrors()) {
			switch ($this->type) {
				case self::TYPE_IMAGE:
					$this->validateImage();
					break;

				default:
					// do nothing
			}
		}
	}

	private function hasErrors() {
		return count($this->errors) == 0;
	}

	private function allowedMimeTypes() {
		switch ($this->type) {
			case self::TYPE_IMAGE:
			default:
				return array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');
				break;
		}
	}

	private function validateFile() {
		if (!file_exists($this->upload['tmp_name'])) {
			$this->errors[] = 'The upload file is missing.';
		} else {
			if (!in_array($this->mimeType, $this->allowedMimeTypes())) {
				$this->errors[] = 'Invalid upload mime type (' . $this->mimeType . '), limited to ' . implode(', ', $this->allowedMimeTypes()) . '.';
			} elseif ($this->upload["size"] > self::MAX_SIZE_BYTES) {
				$this->errors[] = 'The upload file (' . $this->formatBytes($this->upload["size"]) . ') exceeds ' . $this->formatBytes(self::MAX_SIZE_BYTES);
			}
		}
	}

	private function validateImage() {
		$width =  isset($this->imageInfo[0]) ? intval($this->imageInfo[0]) : -1;
		$height = isset($this->imageInfo[1]) ? intval( $this->imageInfo[1]) : -1;
		if ($width < -1 || $height < -1) {
			return;  // size check failed so bail
		}
		if ($width < 150 || $height < 150) {
			$this->errors[] = 'Image width or height (' . $width . 'x' . $height . ') should be greater than 150 pixels.';
		}
	}

	private function formatBytes($size, $precision = 2) {
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
}
