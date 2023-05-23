<div class="dcf-grid dcf-col-gap-vw dcf-row-gap-6">
    <?php if($context->options['format'] !== "partial"): ?>
        <aside class="dcf-col-100% dcf-col-33%-start@md">
            <nav>
                <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
            </nav>
            <?php echo $savvy->render($context, 'filters.tpl.php'); ?>
        </aside>
    <?php endif; ?>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/EventType.tpl.php'); ?>

        <?php echo $savvy->render($context, 'prev_next_buttons.tpl.php'); ?>
    </section>
    <template id="loadingContent">
        <div id="results">
            <p class="dcf-txt-xs unl-dark-gray">
                <span class="dcf-bold">Loading Event Types</span>
            </p>
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Event Type Results</h1>
            <p class="dcf-txt-xs unl-dark-gray">
                Loading results from all calendars matching the selected event types
            </p>
        </div>
        <div class='dcf-d-flex dcf-jc-center dcf-ai-center dcf-h-12'>
            <div class='dcf-progress-spinner'></div>
        </div>
    </template>
    <template id="errorContent">
        <div id="results">
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Event Type Results</h1>
            <p class="dcf-txt-xs unl-dark-gray">
                Error loading results, please try again later.
            </p>
        </div>
    </template>
</div>
