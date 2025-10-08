<?php
// 404 if invalid calendar
if (!isset($context->options['calendar']) || !isset($context->options['calendar']->name)) {
    header("HTTP/1.0 404 Not Found");
    die();
}
/**
 * This template file is for the icalendar and ics output formats.
 */
ob_start(); ?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//UNL_UCBCN//NONSGML UNL Event Publisher//EN
X-WR-CALNAME:<?php echo $context->options['calendar']->name."\n"; ?>
CALSCALE:GREGORIAN
METHOD:PUBLISH
<?php echo $savvy->render($context->output); ?>
END:VCALENDAR
<?php
// Convert all line endings: line endings are windows-style, carriage-return, followed by a line feed
$out = ob_get_contents();
ob_clean();
$out = explode("\n", $out);
foreach ($out as $line) {
    // remove any empty lines
    if (empty(trim($line ?? ''))) { continue; }
    echo \UNL\UCBCN\Frontend\Util::ical_split($line) . "\r\n";
}

function icalFormatString($string) {
    $formattedString = preg_replace("/\r\n|\n|\r/", '\n', strip_tags(trim($string ?? '')));
    $formattedString = preg_replace("/([,;:])/", '\\\$1', $formattedString);
    return $formattedString;
}


