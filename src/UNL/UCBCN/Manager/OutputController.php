<?php
namespace UNL\UCBCN\Manager;

use Michelf\SmartyPants;
use Misd\Linkify\Linkify;

class OutputController extends \Savvy
{
    protected $theme = 'default';
    protected $controller = false;
    protected $linkify = false;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        \Savvy_ClassToTemplateMapper::$classname_replacement = __NAMESPACE__ . '\\';
        parent::__construct();
        $this->linkify = new Linkify();
        $this->initialize($this->controller->options);
    }

    public function initialize($options = array()) {
        switch ($options['format']) {
            case 'html':
                // Always escape output, use $context->getRaw('var'); to get the raw data.
                $this->setEscape(function($data) {
                    $data = SmartyPants::defaultTransform($data);
                    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8', false);
                });
                header('Content-type:text/html;charset=UTF-8');
                $this->setTemplateFormatPaths('html/manager');
                break;
            default:
                throw new UnexpectedValueException('Invalid/unsupported output format', 500);
        }
    }
    
    public function linkify($text, $options = array())
    {
        return $this->linkify->process($text, $options);
    }

    /**
     * Set a specific theme for this instance
     * 
     * @param string $theme Theme name, which corresponds to a directory in www/
     * 
     * @throws Exception
     */
    public function setTheme($theme)
    {
        if (!is_dir($this->getWebDir() . '/themes/'.$theme)) {
            throw new Exception('Invalid theme, there are no files in '.$dir);
        }
        $this->theme = $theme;
    }

    /**
     * Set the array of template paths necessary for this format
     * 
     * @param string $format Format to use
     */
    public function setTemplateFormatPaths($format)
    {
        $themes = $this->getTemplateFormatPaths($format);

        $this->setTemplatePath($themes);
    }
    
    public function getTemplateFormatPaths($format)
    {
        $web_dir = $this->getWebDir();

        // The 'default' theme is always on the path as a fallback
        $themes = array(
            $web_dir . '/templates/default/', //add the default as a path so that we can reference other formats when rendering
            $web_dir . '/templates/default/' . $format
        );

        // If we've customized the theme, add that directory to the path
        if ($this->theme != 'default') {
            $themes[] = $web_dir . '/templates/' . $this->theme . '/' . $format;
        }
        
        return $themes;
    }

    /**
     * Get the path to the root web directory
     *
     * @return string
     */
    protected function getWebDir()
    {
        return dirname(dirname(dirname(dirname(__DIR__)))) . '/www';
    }

    public function setReplacementData($field, $data)
    {
        if (is_array($this->getConfig('filters'))) {
            foreach ($this->getConfig('filters') as $filter) {
                $filter[0]->setReplacementData($field, $data);
            }
        }
    }

    /**
     * 
     * @param timestamp $expires timestamp
     * 
     * @return void
     */
    function sendCORSHeaders($expires = null)
    {
        // Specify domains from which requests are allowed
        header('Access-Control-Allow-Origin: *');

        // Specify which request methods are allowed
        header('Access-Control-Allow-Methods: GET, OPTIONS');

        // Additional headers which may be sent along with the CORS request
        // The X-Requested-With header allows jQuery requests to go through

        header('Access-Control-Allow-Headers: X-Requested-With');

        // Set the ages for the access-control header to 20 days to improve speed/caching.
        header('Access-Control-Max-Age: 1728000');

        if (isset($expires)) {
            // Set expires header for 24 hours to improve speed caching.
            header('Expires: '.date('r', $expires));
        }
    }

    /**
     * This function converts a string stored in the database to html output.
     * & becomes &amp; etc.
     *
     * @param $text
     * @internal param string $t Normally a varchar string from the database.
     *
     * @return String encoded for output to html.
     */
    function dbStringToHtml($text)
    {
        return nl2br($text);
    }

}
