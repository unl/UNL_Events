<?php
    $selected_html = 'selected="selected"';

    // Get previous inputs without using context
    $query = $context->options['q'] ?? "";
    $selected_audience = $context->options['audience'] ?? "";
    $selected_type = $context->options['type'] ?? "";

    // Format inputs
    $query = strtolower($query);
    $selected_audience = strtolower($selected_audience);
    $selected_type = strtolower($selected_type);

    // set up variables for available optgroups
    $audiences_available_names = array(); // These are only here so we can check if we have already stored them
    $audiences_available = array();
    $audiences_unavailable = $context->getAudiences();

    $event_types_available_names = array(); // These are only here so we can check if we have already stored them
    $event_types_available = array();
    $event_types_unavailable = $context->getEventTypes();

    // Loop over all the events and check the events and audiences
    // This is used for the available option groups in drop downs
    foreach ($context as $eventInstance) {
        $event = $eventInstance->event;
        $event_eventType = $event->getFirstType();
        $event_audiences = $event->getAudiences();

        // check and store the event type
        if ($event_eventType !== null && !in_array($event_eventType->name, $event_types_available_names)) {
            $event_types_available_names[] = $event_eventType->name;
            $event_types_available[] = $event_eventType;
        }

        // loop through all the audiences and do the same
        foreach ($event_audiences as $event_audience) {
            $audience = $event_audience->getAudience();

            if (!in_array($audience->name, $audiences_available_names)) {
                $audiences_available_names[] = $audience->name;
                $audiences_available[] = $audience;
            }
        }
    }

    // sort so they are in alphabetical order
    usort($audiences_available, function($a, $b) {
        return strcmp($a->name, $b->name);
    });

    usort($event_types_available, function($a, $b) {
        return strcmp($a->name, $b->name);
    });
?>

<form id="filter_form" class="dcf-form dcf-mt-5">
    <?php if (empty($query)): ?>
        <input type="hidden" name="q" value="<?php echo htmlentities($query); ?>"/>
    <?php endif;?>
    <fieldset>
        <legend>Filter Results</legend>
        <div class="dcf-form-group">
            <label for="type">Type</label>
            <select class="dcf-w-100%" id="type" name="type">
                <option
                    <?php if (empty($selected_type)) { echo $selected_html; } ?>
                    value=""
                >
                    N/A
                </option>
                <optgroup label="Available Types
                    <?php if (empty($event_types_available)) { echo "(None Available)"; }?>"
                >
                    <?php foreach ($event_types_available as $type) { ?>
                        <option
                            <?php if ($selected_type == strtolower($type->name)) { echo $selected_html; } ?>
                            value="<?php echo $type->name; ?>"
                        >
                            <?php echo $type->name; ?>
                        </option>
                    <?php } ?>
                </optgroup>
                <optgroup label="All Types">
                    <?php foreach ($context->getEventTypes() as $type) { ?>
                        <option
                            <?php if ($selected_type == strtolower($type->name) &&
                                empty($event_types_available)) {
                                    echo $selected_html;
                                }
                            ?>
                            value="<?php echo $type->name; ?>"
                        >
                            <?php echo $type->name; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>
        <div class="dcf-form-group">
            <label for="audience">Target Audience</label>
            <select class="dcf-w-100%" id="audience" name="audience">
                <option
                    <?php if (empty($selected_audience)) { echo $selected_html; } ?>
                    value=""
                >
                    N/A
                </option>
                <optgroup label="Available Audiences
                    <?php if (empty($audiences_available)) { echo "(None Available)"; }?>"
                >
                    <?php foreach ($audiences_available as $audience) { ?>
                        <option
                            <?php if ($selected_audience == strtolower($audience->name)) {
                                    echo $selected_html;
                                }
                            ?>
                            value="<?php echo $audience->name; ?>"
                        >
                            <?php echo $audience->name; ?>
                        </option>
                    <?php } ?>
                </optgroup>
                <optgroup label="All Audiences">
                    <?php foreach ($context->getAudiences() as $audience) { ?>
                        <option
                            <?php if ($selected_audience == strtolower($audience->name) &&
                                empty($audiences_available)) {
                                    echo $selected_html;
                                }
                            ?>
                            value="<?php echo $audience->name; ?>"
                        >
                            <?php echo $audience->name; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>
        <button id="filter_reset" class="dcf-btn dcf-btn-secondary" type="button">
            Clear Filters
        </button>
    </fieldset>

</form>
<?php
    $page->addScriptDeclaration("
        const form = document.querySelector('form#filter_form');
        const filter_reset = form.querySelector('#filter_reset');

        // Submit if select changes
        form.querySelectorAll('select').forEach((input) => {
            input.addEventListener('input', () => {
                form.submit();
            });
        });

        // Set inputs to empty and submit if reset button is clicked
        filter_reset.addEventListener('click', (e) => {
            const type_select = form.querySelector('#type');
            const type_audience = form.querySelector('#audience');

            type_select.value = '';
            type_audience.value = '';

            form.submit();
        });
    ");