<div class="dcf-grid dcf-col-gap-vw">
    <aside class="dcf-col-100% dcf-col-33%-start@md">
        <nav>
            <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
        </nav>
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
    </aside>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Range.tpl.php'); ?>
    </section>
</div>
