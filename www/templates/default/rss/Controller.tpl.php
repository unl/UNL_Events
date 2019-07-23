<?php
// 404 if invalid calendar
if (!isset($context->options['calendar']) || !isset($context->options['calendar']->name)) {
  header("HTTP/1.0 404 Not Found");
  die();
}
?>
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title><?php echo $context->options['calendar']->name; ?> Events</title>
        <link><?php echo $context->options['calendar']->getURL(); ?></link>
        <description>Events for <?php echo $context->options['calendar']->name; ?></description>
        <language>en-us</language>
        <generator>UNL_UCBCN_Frontend-3.0</generator>
        <lastBuildDate><?php echo date('r'); ?></lastBuildDate>
        <?php
        echo $savvy->render($context->output);
        ?>
    </channel>
</rss>