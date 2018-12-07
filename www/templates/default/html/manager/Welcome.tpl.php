<?php
    $crumbs = new stdClass;

    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        'Welcome' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="dcf-txt-center">Welcome to UNL Events</h1>

<p>
Welcome to the UNL Events Manager! UNL Events is a calendar system that 
allows the University to create, publish, and share events. As a user of UNL Events,
you can create your own calendar for your department, organization, or even yourself, 
and publish your events to your website, the UNL Today calendar, or other platforms.
You can also subscribe to other calendars to pull in events that are relevant to your interests.
</p>

<h3 class="wdn-brand">Getting Started</h3>
Here are some tips to get going with UNL Events:
<div class="dcf-grid">
    <ul class="helpful">
    <li>
        If this is your first time here, you may not have access to any calendars. 
        You can create one with the "New Calendar" button, or request another user
        to give you access.
    </li>
    <li>
        Find the calendar you'd like to work with under "Your Calendars." Once on a certain calendar,
        you can create an event on it by clicking "New Event."
    </li>
    <li>
        The "Pending" tab is a "staging" area for events. You can 
        "approve" these events to show on your live calendar by clicking "Move to Upcoming" in the
        dropdown to the right of them. They'll then be in the "Upcoming" tab, which shows the upcoming events
        on your calendar. These events will move to "Past" once they are completed.
    </li>
    </ul>
</div>

Enjoy using UNL Events!
