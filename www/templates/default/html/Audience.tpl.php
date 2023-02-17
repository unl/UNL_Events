<div class="dcf-grid dcf-col-gap-vw">
    <section class="dcf-col-100% dcf-col-33%-start@md">
        <h2 class="results clear-top">
            <span class="dcf-subhead dcf-d-block">
                <?php echo $context->count().' search results for audience(s)'; ?>
            </span>
            <a class="permalink dcf-d-block"
                <?php
                    if (empty($context->search_query)) {
                        echo 'href="'.$context->getURL().'"> \'Any\'';
                    } else {
                        echo 'href="'.$context->getURL().'">'.$context->getFormattedAudiences();
                    }
                ?>
            </a>
        </h2>
    </section>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Audience.tpl.php'); ?>
    </section>
</div>
