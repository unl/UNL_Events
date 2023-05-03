<?php
    const CHECKED_INPUT = 'checked="checked"';
    $query = strtolower($context->search_query ?? "");
?>
<div class="dcf-grid dcf-col-gap-vw dcf-row-gap-6">
    <section class="dcf-col-100% dcf-col-33%-start@md">
        <nav>
            <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
        </nav>
        <h2 class="dcf-txt-h4">Filter Results</h2>
        <form id="eventtype_form" class="dcf-form dcf-mt-5">
            <?php $all_eventtypes = $context->getEventTypes(); ?>

            <input type="hidden" id="hidden_query" name="q" value="<?php echo $context->search_query ?? ""; ?>">
            <?php if (isset($context->options['limit'])): ?>
                <input type="hidden" name="limit" value="<?php echo $context->options['limit'] ?? ""; ?>">
            <?php endif; ?>
            <?php if (isset($context->options['offset'])): ?>
                <input type="hidden" name="offset" value="<?php echo $context->options['offset'] ?? ""; ?>">
            <?php endif; ?>

            <fieldset id="filter_event_types" class="dcf-collapsible-fieldset">
                <legend>Event Types</legend>
                <?php foreach ($all_eventtypes as $single_type) : ?>
                    <?php
                        $event_type_id = 'event-type-' . $single_type->id;
                        $splitQuery = explode(',', $query);
                        $splitQuery = array_map('trim', $splitQuery);
                        $in_query = in_array(strtolower($single_type->name), $splitQuery);
                    ?>
                    <div class="dcf-input-checkbox">
                        <input
                            id="<?php echo $event_type_id; ?>"
                            type="checkbox"
                            value="<?php echo $single_type->name; ?>"
                            <?php
                                if ($in_query !== false) {
                                    echo CHECKED_INPUT;
                                }
                            ?>
                        >
                        <label for="<?php echo $event_type_id; ?>">
                            <?php echo $single_type->name; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </fieldset>
        </form>

        <script>
            const form = document.getElementById('eventtype_form');
            const filter_event_types = document.getElementById('filter_event_types');
            window.addEventListener('inlineJSReady', function() {
                WDN.initializePlugin('collapsible-fieldsets');
            }, false);

            filter_event_types.addEventListener('ready', () => {
                const checkboxes = filter_event_types.querySelectorAll('input[type="checkbox"]');
                const hidden_query = document.getElementById('hidden_query');

                // Submit if select changes
                checkboxes.forEach((input) => {
                    input.addEventListener('input', () => {
                        const checkedCheckboxes = form.querySelectorAll('input[type="checkbox"]:checked');
                        hidden_query.value = Array.from(checkedCheckboxes).map((checkbox) => checkbox.value).join(", ");
                        if (checkedCheckboxes.length > 0) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    </section>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/EventType.tpl.php'); ?>

        <?php echo $savvy->render($context, 'prev_next_buttons.tpl.php'); ?>
    </section>
</div>
