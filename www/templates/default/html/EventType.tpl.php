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
</div>
