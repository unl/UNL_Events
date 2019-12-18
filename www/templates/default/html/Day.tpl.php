
<div class="dcf-grid dcf-col-gap-vw dcf-row-gap-6">
    <aside class="dcf-col-100% dcf-col-33%-start@md">
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
    </aside>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Day.tpl.php'); ?>
    </section>
</div>
