<?php
    $crumbs = new stdClass;

    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        'Welcome' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="wdn-center">Welcome to UNL Events</h1>

<p>
Welcome to the UNL Events system! UNL Events is a calendar system that 
allows the University to create, publish, and share events. As a user of UNL Events,
you can create your own calendar for your department, organization, or even yourself, 
and publish your events to your website, the UNL Today calendar, or other platforms.
You can also subscribe to other calendars to pull in events that are relevant to your interests.
</p>

<div class="wdn-grid-set">
    <div class="bp3-wdn-col-one-half">
    left
    </div>
    <div class="bp3-wdn-col-one-half">
    left
    </div>
</div>