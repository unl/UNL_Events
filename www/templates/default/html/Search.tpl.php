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
        <?php echo $savvy->render($context, 'hcalendar/Search.tpl.php'); ?>
        <?php echo $savvy->render($context, 'prev_next_buttons.tpl.php'); ?>
    </section>
    <template id="loadingContent">
        <div id="results">
            <p class="dcf-txt-xs unl-dark-gray">
                <span class="dcf-bold">Loading Search</span>
            </p>
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Search Results</h1>
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
            <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Search Results</h1>
            <p class="dcf-txt-xs unl-dark-gray">
                Error loading results, please try again later.
            </p>
        </div>
    </template>
</div>
