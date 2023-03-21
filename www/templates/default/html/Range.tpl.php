<div class="dcf-grid dcf-col-gap-vw">
    <aside class="dcf-col-100% dcf-col-33%-start@md">
        <div class="dcf-sticky dcf-pin-top">
            <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
        </div>
    </aside>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Range.tpl.php'); ?>
    </section>
</div>
