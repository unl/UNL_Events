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
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">

        <h2>Event Details</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="dcf-form-group">
                <label for="title">Title <small class="dcf-required">Required</small></label>
                <input id="title" name="title" type="text" class="dcf-w-100%" value="<?php echo $event->title; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="subtitle">Subtitle</label>
                <input id="subtitle" name="subtitle" type="text" class="dcf-w-100%" value="<?php echo $event->subtitle; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="description">Description <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
                <textarea id="description" name="description" rows="4" ><?php echo $event->description; ?></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="type">Type</label>
                <select class="dcf-w-100%" id="type" name="type">
                    <?php foreach ($context->getEventTypes() as $type) { ?>
                        <option <?php if (isset($post['type']) && $post['type'] == $type->id) echo SELECTED_INPUT ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="dcf-form-group">
                <div class="dcf-input-checkbox">
                    <input id="canceled" name="canceled" type="checkbox" value="1" <?php if ($event->isCanceled()) { echo CHECKED_INPUT; } ?>>
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
                    <legend class="dcf-pb-2">Privacy</legend>
                    <div class="dcf-input-radio">
                        <input id="sharing-private" name="private_public" type="radio" value="private" <?php if (!empty($post['private_public']) && $post['private_public'] == 'private') { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-private">Private</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="sharing-public" name="private_public" type="radio" value="public" <?php if (!empty($post['private_public']) && $post['private_public'] != 'private') { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-public">Public</label>
                    </div>
                </fieldset>
                <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                    <legend class="dcf-pb-2">Consider for Main <abbr title="University of Nebraska–Lincoln"">UNL</abbr> Calendar <small class="dcf-required">Required</small></legend>
                    <div class="dcf-input-radio">
                        <input id="send_to_main_on" name="send_to_main" type="radio" value="on" <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'on') { echo CHECKED_INPUT; } ?>/>
                        <label for="send_to_main_on">Yes</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="send_to_main_off" name="send_to_main" type="radio" value="off" <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'off') { echo CHECKED_INPUT; } ?>/>
                        <label for="send_to_main_off">No</label>
                    </div>
                </fieldset>
            </div>
            <hr>
        </section>

        <h2>Contact Info</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="details dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw">
                <div class="dcf-form-group">
                    <label for="contact-name">Name <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
                    <input id="contact-name" name="contact_name" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_name'])) { echo $post['contact_name']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-email">Email</label>
                    <input id="contact-email" name="contact_email" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_email'])) { echo $post['contact_email']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-phone">Phone</label>
                    <input id="contact-phone" name="contact_phone" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_phone'])) { echo $post['contact_phone']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="website">Event Website</label>
                    <input id="website" name="website" type="text" class="dcf-w-100%" value="<?php echo $event->webpageurl ?>" />
                </div>
            </div>
            <hr>
        </section>
        <button class="dcf-btn dcf-btn-primary" type="submit">Submit Event</button>
        <button id="google-microdata-button" class="dcf-btn-toggle-modal dcf-btn unl-cream unl-cream@dark dcf-mt-3" title="Learn More" style="background-color:var(--bg-brand-eta); border-color: var(--bg-brand-eta);" type="button" data-toggles-modal="google-microdata-modal" disabled>
            ! Your event does not reach google microdata requirements !
        </button>

        <div class="dcf-modal" id="google-microdata-modal" hidden>
            <div class="dcf-modal-wrapper">
                <div class="dcf-modal-header">
                    <h2>Info About Google Microdata</h2>
                    <button class="dcf-btn-close-modal">Close</button>
                </div>
                <div class="dcf-modal-content">
                    Info about google microdata
                    <div class="dcf-mt-5" id="google-microdata-modal-output">
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<?php
$page->addScript($base_frontend_url .'templates/default/html/js/manager-create-event.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);
