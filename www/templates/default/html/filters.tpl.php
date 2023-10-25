<?php
    const CHECKED_INPUT = 'checked="checked"';

    // Get previous inputs without using context
    $query = $context->options['q'] ?? "";
    $selected_audience = $context->audience_filter;
    $selected_type = $context->event_type_filter;
?>

<form id="filter_form" class="dcf-form dcf-mt-5">
    <input
        type="hidden"
        id="filter_hidden_q"
        name="q"
        value="<?php echo htmlentities($query); ?>"
    />
    <input
        type="hidden"
        id="filter_hidden_audience"
        name="audience"
        value="<?php echo htmlentities($selected_audience); ?>"
    />
    <input
        type="hidden"
        id="filter_hidden_type"
        name="type"
        value="<?php echo htmlentities($selected_type); ?>"
    />
    <div class="dcf-d-flex dcf-flex-nowrap dcf-flex-row dcf-jc-between dcf-ai-center">
        <p class="dcf-mb-0 dcf-txt-lg">Filter Results</p>
        <button id="filter_reset" class="dcf-btn dcf-btn-tertiary" type="button">
            Clear
        </button>
    </div>

    <hr class="dcf-mt-3 dcf-mb-5">

    <fieldset id="audience_filter" class="dcf-collapsible-fieldset" hidden
        style="padding-bottom: 0px;"
        <?php if (empty($selected_audience)): ?>
            data-start-expanded="false"
        <?php endif;?>
    >
        <legend>Target Audience</legend>
        <div class="events-h-max-filter dcf-overflow-y-auto dcf-pb-4" style="margin-right: calc(-3.16vw + 1px);">
            <?php foreach ($context->getAudiences() as $single_audience): ?>
                <?php
                    $target_audience_id = 'audience_filter_' . $single_audience->id;
                    $splitQuery = explode(',', strtolower($selected_audience));
                    $splitQuery = array_map('trim', $splitQuery);
                    $in_query = in_array(strtolower($single_audience->name), $splitQuery);
                ?>
                <div class="dcf-input-checkbox">
                    <input
                        id="<?php echo $target_audience_id; ?>"
                        class="audience_filter_checkbox"
                        type="checkbox"
                        value="<?php echo $single_audience->name; ?>"
                        <?php
                            if ($in_query !== false) {
                                echo CHECKED_INPUT;
                            }
                        ?>
                    >
                    <label for="<?php echo $target_audience_id; ?>">
                        <?php echo $single_audience->name; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset id="type_filter" class="events-filter-fieldset dcf-collapsible-fieldset" hidden
        <?php if (empty($selected_type)): ?>
            data-start-expanded="false"
        <?php endif;?>
    >
        <legend>Event Type</legend>
        <div class="events-filter-fieldset-contents events-h-max-filter dcf-overflow-y-auto dcf-pb-4">
            <?php foreach ($context->getEventTypes() as $single_event_type): ?>
                <?php
                    $event_type_id = 'event_type_filter_' . $single_event_type->id;
                    $splitQuery = explode(',', strtolower($selected_type));
                    $splitQuery = array_map('trim', $splitQuery);
                    $in_query = in_array(strtolower($single_event_type->name), $splitQuery);
                ?>
                <div class="dcf-input-checkbox">
                    <input
                        id="<?php echo $event_type_id; ?>"
                        class="type_filter_checkbox"
                        type="checkbox"
                        value="<?php echo $single_event_type->name; ?>"
                        <?php
                            if ($in_query !== false) {
                                echo CHECKED_INPUT;
                            }
                        ?>
                    >
                    <label for="<?php echo $event_type_id; ?>">
                        <?php echo $single_event_type->name; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>
</form>

<?php
    $page->addScript(
        $frontend->getURL().
        'templates/default/html/js/filters.min.js?v='.
        UNL\UCBCN\Frontend\Controller::$version
    );
