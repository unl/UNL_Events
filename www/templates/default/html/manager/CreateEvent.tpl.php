<?php
    const CHECKED_INPUT = 'checked="checked"';
    const SELECTED_INPUT = 'selected="selected"';

    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;
?>
<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Create Event" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<div>
    <form class="dcf-form" id="create-event-form" action="" method="POST" enctype="multipart/form-data">
        <input
            type="hidden"
            name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>"
            value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>"
        >
        <input
            type="hidden"
            name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>"
            value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
        >

        <h2>Event Details</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="dcf-form-group">
                <label for="title">Title <small class="dcf-required">Required</small></label>
                <input id="title" name="title" type="text" class="dcf-w-100%" value="<?php echo $event->title; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="subtitle">Subtitle</label>
                <input
                    id="subtitle"
                    name="subtitle"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->subtitle; ?>"
                >
            </div>
            <div class="dcf-form-group">
                <label for="description">
                    Description
                    <small class="required-for-main-calendar dcf-required" style="display: none">Required</small>
                </label>
                <textarea id="description" name="description" rows="4" ><?php echo $event->description; ?></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="website">Website</label>
                <input
                    id="website"
                    name="website"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->webpageurl ?>"
                >
            </div>
            <div class="dcf-form-group">
                <label for="type">Type <small class="dcf-required">Required</small></label>
                <select class="dcf-w-100%" id="type" name="type">
                    <option
                        <?php if (!isset($post['type'])) { echo SELECTED_INPUT; }?>
                        disabled="disabled"
                        value=""
                    >
                        Please Select One
                    </option>
                    <?php foreach ($context->getEventTypes() as $type): ?>
                        <option
                            <?php
                                if (isset($post['type']) && $post['type'] == $type->id) {
                                    echo SELECTED_INPUT;
                                }
                            ?>
                            value="<?php echo $type->id; ?>"
                        >
                            <?php echo $type->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <fieldset>
                <legend>Target Audience</legend>
                <div class="target-audience-grid">
                    <?php foreach ($context->getAudiences() as $audience): ?>
                        <?php $target_audience_id = 'target-audience-' . $audience->id; ?>
                        <div class="dcf-input-checkbox">
                            <input
                                id="<?php echo $target_audience_id; ?>"
                                name="<?php echo $target_audience_id; ?>"
                                type="checkbox"
                                value="<?php echo $audience->id; ?>"
                                <?php
                                    if (isset($post[$target_audience_id]) &&
                                        $post[$target_audience_id] == $audience->id) {
                                            echo CHECKED_INPUT;
                                    }
                                ?>
                            >
                            <label for="<?php echo $target_audience_id; ?>">
                                <?php echo $audience->name; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <div class="dcf-form-group">
                <div class="dcf-input-checkbox">
                    <input
                        id="canceled"
                        name="canceled"
                        type="checkbox"
                        value="1"
                        <?php if ($event->isCanceled()) { echo CHECKED_INPUT; } ?>
                    >
                    <label for="canceled">Event Canceled</label>
                </div>
            </div>
            <hr>
        </section>

        <?php echo $savvy->render($context , 'EventFormDateTime.tpl.php'); ?>

        <?php echo $savvy->render($context , 'EventFormImageUpload.tpl.php'); ?>

        <h2>Sharing</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="details dcf-grid dcf-col-gap-vw">
                <fieldset class="dcf-col-100% dcf-col-25%-start@sm dcf-p-0 dcf-b-0">
                    <legend class="dcf-pb-2">
                        Privacy
                        <div class="dcf-popup dcf-d-inline" data-point="true">
                            <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0" type="button">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="dcf-d-block dcf-h-5 dcf-w-5 dcf-fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M11.5,1C5.159,1,0,6.159,0,12.5C0,18.841,5.159,24,11.5,24
                                        S23,18.841,23,12.5C23,6.159,17.841,1,11.5,1z M11.5,23 C5.71,23,1,18.29,1,12.5
                                        C1,6.71,5.71,2,11.5,2S22,6.71,22,12.5C22,18.29,17.29,23,11.5,23z"></path>
                                    <path d="M14.5,19H12v-8.5c0-0.276-0.224-0.5-0.5-0.5h-2
                                        C9.224,10,9,10.224,9,10.5S9.224,11,9.5,11H11v8H8.5 C8.224,19,8,19.224,8,19.5
                                        S8.224,20,8.5,20h6c0.276,0,0.5-0.224,0.5-0.5S14.776,19,14.5,19z"></path>
                                    <circle cx="11" cy="6.5" r="1"></circle>
                                    <g>
                                        <path fill="none" d="M0 0H24V24H0z"></path>
                                    </g>
                                </svg>
                            </button>
                            <div
                                class="
                                    dcf-popup-content
                                    unl-cream
                                    unl-bg-blue
                                    dcf-p-1
                                    dcf-rounded
                                "
                                style="min-width: 25ch;"
                            >
                                <p class="dcf-m-0 dcf-regular">
                                    If private this event will not show up in "All Calendar" results.
                                </p>
                            </div>
                        </div>
                    </legend>
                    <div class="dcf-input-radio">
                        <input
                            id="sharing-private"
                            name="private_public"
                            type="radio"
                            value="private"
                            <?php
                                if (!empty($post['private_public']) &&
                                    $post['private_public'] == 'private'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="sharing-private">Private</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input
                            id="sharing-public"
                            name="private_public"
                            type="radio"
                            value="public"
                            <?php
                                if (!empty($post['private_public']) &&
                                    $post['private_public'] != 'private'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="sharing-public">Public</label>
                    </div>
                </fieldset>
                <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                    <legend class="dcf-pb-2">
                        Consider for Main
                        <abbr title="University of Nebraska–Lincoln">UNL</abbr>
                        Calendar <small class="dcf-required">Required</small>
                    </legend>
                    <div class="dcf-input-radio">
                        <input
                            id="send_to_main_on"
                            name="send_to_main"
                            type="radio"
                            value="on"
                            <?php
                                if (!empty($post['send_to_main']) &&
                                    $post['send_to_main'] == 'on'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="send_to_main_on">Yes</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input
                            id="send_to_main_off"
                            name="send_to_main"
                            type="radio"
                            value="off"
                            <?php
                                if (!empty($post['send_to_main']) &&
                                    $post['send_to_main'] == 'off'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="send_to_main_off">No</label>
                    </div>
                </fieldset>
            </div>
            <hr>
        </section>

        <h2>Organizer Contact Info</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="details dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw">
                <div class="dcf-form-group">
                    <label for="contact-name">
                        Name
                        <small class="required-for-main-calendar dcf-required" style="display: none">Required</small>
                    </label>
                    <input
                        id="contact-name"
                        name="contact_name"
                        type="text"
                        class="dcf-w-100%"
                        value="<?php if (isset($post['contact_name'])) { echo $post['contact_name']; } ?>"
                    >
                </div>
                <div class="dcf-form-group">
                    <label for="contact-email">Email</label>
                    <input
                        id="contact-email"
                        name="contact_email"
                        type="text"
                        class="dcf-w-100%"
                        value="<?php if (isset($post['contact_email'])) { echo $post['contact_email']; } ?>"
                    >
                </div>
                <div class="dcf-form-group">
                    <label for="contact-phone">Phone</label>
                    <input
                        id="contact-phone"
                        name="contact_phone"
                        type="text"
                        class="dcf-w-100%"
                        value="<?php if (isset($post['contact_phone'])) { echo $post['contact_phone']; } ?>"
                    >
                </div>
                <div class="dcf-form-group">
                    <label for="contact-website">Website</label>
                    <input
                        id="contact-website"
                        name="contact_website"
                        type="text"
                        class="dcf-w-100%"
                        value="<?php if (isset($post['contact_website'])) { echo $post['contact_website']; } ?>"
                    >
                </div>
                <fieldset class="dcf-mb-0 dcf-p-0 dcf-b-0" id="contact-type">
                    <legend class="dcf-pb-2"> Organizer Type </legend>
                    <div class="dcf-input-radio">
                        <input
                            id="contact-type-person"
                            name="contact_type"
                            type="radio"
                            value="person"
                            <?php
                                if (!empty($post['contact_type']) &&
                                    $post['contact_type'] == 'person'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="contact-type-person">Person</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input
                            id="contact-type-organization"
                            name="contact_type"
                            type="radio"
                            value="organization"
                            <?php
                                if (!empty($post['contact_type']) &&
                                    $post['contact_type'] == 'organization'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="contact-type-organization">Organization</label>
                    </div>
                </fieldset>
            </div>
            <hr>
        </section>
        <button class="dcf-btn dcf-btn-primary" type="submit">Submit Event</button>
        <button
            id="google-microdata-button"
            class="dcf-btn-toggle-modal dcf-btn unl-bg-blue events-b-blue unl-cream unl-cream@dark dcf-mt-3"
            title="Learn More"
            type="button"
            data-toggles-modal="google-microdata-modal"
            disabled
        >
            ! Your event does not reach microdata requirements !
        </button>

        <div class="dcf-modal" id="google-microdata-modal" hidden>
            <div class="dcf-modal-wrapper">
                <div class="dcf-modal-header">
                    <h2>Info About Microdata</h2>

                    <button class="dcf-btn-close-modal">Close</button>
                </div>
                <div class="dcf-modal-content">
                    <p>
                        Microdata is a way to provide structured data markup
                        on web pages. This structured data helps search engines, like Google,
                        to understand the content and context of the page better.
                        For events.unl.edu, microdata can provide key information about
                        each event such as it's name, date, time, location, description, and more.
                        This helps search engines present our events
                        more prominently in search results, making it easier for users to find
                        relevant events.

                        <a href="https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data">
                            Learn more through Google's documentation.
                        </a>
                    </p>
                    <div class="dcf-mt-5" id="google-microdata-modal-output"></div>
                    <p class="dcf-txt-xs">*If this information is not relevant to you, feel free to disregard it.</p>
                </div>
            </div>
        </div>

    </form>
</div>

<?php
$page->addScript(
    $base_frontend_url .
    'templates/default/html/js/manager-create-event.min.js?v='.
    UNL\UCBCN\Frontend\Controller::$version
);
