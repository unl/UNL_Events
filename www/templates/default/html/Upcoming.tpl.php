<div class="dcf-d-grid dcf-grid-cols-12 dcf-col-gap-vw dcf-row-gap-6">
    <?php if($context->options['format'] !== "partial"): ?>
        <aside class="dcf-col-span-12 dcf-col-span-4@md">
            <nav>
                <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
            </nav>
            <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
            <?php echo $savvy->render($context, 'filters.tpl.php'); ?>
        </aside>
    <?php endif; ?>
    <section id="updatecontent" class="day_cal dcf-col-span-12 dcf-col-span-8@md">
        <?php
            if ($context->isHomepage()) {
                $featuredEvents = $context->calendar->getFeaturedEvents(1, 6);
                if ($featuredEvents) {
                    echo $savvy->render($featuredEvents, 'hcalendar/FeaturedSummary.tpl.php');
                }
            }
        ?>
        <?php echo $savvy->render($context, 'hcalendar/Upcoming.tpl.php'); ?>
    </section>
    <template id="loadingContent">
        <div id="results">
            <p class="dcf-txt-xs unl-dark-gray">
                <span class="dcf-bold">Loading Upcoming</span>
            </p>
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Upcoming Events</h1>
            <p class="dcf-txt-xs unl-dark-gray">
                Loading Results
            </p>
        </div>
        <div class='dcf-d-flex dcf-jc-center dcf-ai-center dcf-h-12'>
            <div class='dcf-progress-spinner'></div>
        </div>
    </template>
    <template id="errorContent">
        <div id="results">
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Upcoming Results</h1>
            <p class="dcf-txt-xs unl-dark-gray">
                Error loading results, please try again later.
            </p>
        </div>
    </template>
</div>
