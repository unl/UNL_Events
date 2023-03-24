<div class="dcf-grid dcf-col-gap-vw dcf-row-gap-6">
    <aside class="dcf-col-100% dcf-col-33%-start@md">
        <nav>
            <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
        </nav>
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
        <?php echo $savvy->render($context, 'filters.tpl.php'); ?>
        <div class="dcf-txt-xs">
            <?php echo $context->getParsedDates(); ?>
        </div>
    </aside>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Search.tpl.php'); ?>
        <?php echo $savvy->render($context, 'prev_next_buttons.tpl.php'); ?>
    </section>
</div>
