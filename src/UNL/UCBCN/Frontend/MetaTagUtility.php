<?php
namespace UNL\UCBCN\Frontend;

class MetaTagUtility {

    public $url;
    public $title;
    public $description;
    public $image;

    public function __construct($url, $title, $description="", $image="")
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
    }

    public function getMetaTags(): string
    {
        $metaTagOutput = "";
    
        $metaTagOutput .= '<meta name="description" content="' . $this->description . '">' . PHP_EOL;

        $metaTagOutput .= '<meta property="og:url" content="' . $this->url . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:type" content="website">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:title" content="' . $this->title . '">' . PHP_EOL;

        if (isset($this->description) && !empty($this->description)) {
            $metaTagOutput .= '<meta property="og:description" content="' . $this->description . '">' . PHP_EOL;
        }
        if (isset($this->image) && !empty($this->image)) {
            $metaTagOutput .= '<meta property="og:image" content="' . $this->image . '" />' . PHP_EOL;
        }

        $metaTagOutput .= '<meta property="twitter:domain" content="' . $this->getSiteURL() . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="twitter:url" content="' . $this->url . '">' . PHP_EOL;
        $metaTagOutput .= '<meta name="twitter:title" content="' . $this->title . '">' . PHP_EOL;

        if (isset($this->description) && !empty($this->description)) {
            $metaTagOutput .= '<meta name="twitter:description" content="' . $this->description . '">' . PHP_EOL;
        }
        if (isset($this->image) && !empty($this->image)) {
            $metaTagOutput .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
            $metaTagOutput .= '<meta property="twitter:image" content="' . $this->image . '" />' . PHP_EOL;
        } else {
            $metaTagOutput .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
        }
        

        return $metaTagOutput;
    }

    public static function getSiteURL(): string
    {
        return \UNL\UCBCN\Frontend\Controller::$url;
    }
}