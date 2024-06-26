<?php
namespace UNL\UCBCN\Frontend;

class MetaTagUtility {

    public $url;
    public $title;
    public $description;
    public $image;
    public $imageDefault;
    public $label1;
    public $data1;
    public $label2;
    public $data2;

    // Gets all the different parts of the meta tags that each website uses
    public function __construct($url, $title, $description, $options=array())
    {
        $this->imageDefault =  $this->getSiteURL(). "wdn/templates_5.3/includes/global/favicon/512.png";

        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
        $this->image = $options['image'] ?? "";
        $this->label1 = $options['label1'] ?? "";
        $this->data1 = $options['data1'] ?? "";
        $this->label2 = $options['label2'] ?? "";
        $this->data2 = $options['data2'] ?? "";

        $this->description = (strlen($description) > 100) ? substr($description,0,100).'...' : $description;
    }

    // Build the HTML for the meta tags and returns it
    public function getMetaTags(): string
    {
        $metaTagOutput = "";

        $metaTagOutput .= '<meta name="description" content="' . $this->description . '">' . PHP_EOL;

        $metaTagOutput .= '<meta property="og:site_name" content="UNL Events">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:url" content="' . $this->url . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:type" content="website">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:title" content="' . $this->title . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:description" content="' . $this->description . '">' . PHP_EOL;

        if (!empty($this->image)) {
            $metaTagOutput .= '<meta property="og:image" content="' . $this->image . '" />' . PHP_EOL;
        } else {
            $metaTagOutput .= '<meta property="og:image" content="' . $this->imageDefault . '" />' . PHP_EOL;

        }

        // Twitter tags
        if (!empty($this->image)) {
            $metaTagOutput .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
        } else {
            $metaTagOutput .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
        }

        $metaTagOutput .= '<meta property="twitter:domain" content="' . $this->getSiteURL() . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="twitter:site" content="' . $this->url . '">' . PHP_EOL;
        $metaTagOutput .= '<meta property="twitter:url" content="' . $this->url . '">' . PHP_EOL;
        $metaTagOutput .= '<meta name="twitter:title" content="' . $this->title . '">' . PHP_EOL;
        $metaTagOutput .= '<meta name="twitter:description" content="' . $this->description . '">' . PHP_EOL;

        if (!empty($this->image)) {
            $metaTagOutput .= '<meta property="twitter:image" content="' . $this->image . '" />' . PHP_EOL;
        } else {
            $metaTagOutput .= '<meta property="twitter:image" content="' . $this->imageDefault . '" />' . PHP_EOL;
        }

        if (!empty($this->label1) && !empty($this->data1)) {
            $metaTagOutput .= '<meta name="twitter:label1" content="' . $this->label1 . '" />' . PHP_EOL;
            $metaTagOutput .= '<meta name="twitter:data1" content="' . $this->data1 . '" />' . PHP_EOL;
        }

        if (!empty($this->label2) && !empty($this->data2)) {
            $metaTagOutput .= '<meta name="twitter:label2" content="' . $this->label2 . '" />' . PHP_EOL;
            $metaTagOutput .= '<meta name="twitter:data2" content="' . $this->data2 . '" />' . PHP_EOL;
        }

        return $metaTagOutput;
    }

    public static function getSiteURL(): string
    {
        return \UNL\UCBCN\Frontend\Controller::$url;
    }
}
