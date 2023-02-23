<div class="dcf-grid dcf-col-gap-vw">
    <section class="dcf-col-100% dcf-col-33%-start@md">
        <h2 class="results clear-top">
            <span class="dcf-subhead dcf-d-block">
                <?php echo $context->count().' search results from all calendars for audience(s)'; ?>
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
        <?php if($context->count() > 0): ?>
            <div class="dcf-d-flex dcf-flex-row dcf-flex-nowrap dcf-jc-between dcf-ai-end dcf-mt-3">
                <?php
                    $previous_offset = $context->offset - $context->limit;
                    $next_offset     = $context->offset + $context->limit;

                    $previous_link = $context->getURL();
                    $next_link     = $context->getURL();

                    if ($context->limit != 100) {
                        $previous_link .= '&limit=' . $context->limit;
                        $next_link     .= '&limit=' . $context->limit;
                    }

                    if ($previous_offset > 0) {
                        $previous_link .= '&offset=' . $previous_offset;
                    }

                    if ($next_offset < $context->count()) {
                        $next_link .= '&offset=' . $next_offset;
                    }

                ?>

                <?php if ($previous_offset < 0): ?>
                    <?php // We wanted to be able to disable this but you can not disable a link ?>
                    <button
                        class="dcf-btn dcf-btn-secondary"
                        disabled
                    >
                        Previous <?php echo $context->limit; ?>
                    </button>
                <?php else: ?>
                    <a
                        class="dcf-btn dcf-btn-secondary"
                        href="<?php echo $previous_link; ?>"
                    >
                        Previous <?php echo $context->limit; ?>
                    </a>
                <?php endif; ?>

                <p class="dcf-txt-xs">Only Displaying <?php echo $context->limit; ?> Results at a time</p>

                <?php if ($next_offset > $context->count()): ?>
                    <?php // We wanted to be able to disable this but you can not disable a link ?>
                    <button
                        class="dcf-btn dcf-btn-secondary"
                        disabled
                    >
                        Next <?php echo $context->limit; ?>
                    </button>
                <?php else: ?>
                    <a
                        class="dcf-btn dcf-btn-secondary"
                        href="<?php echo $next_link; ?>"
                    >
                        Next <?php echo $context->limit; ?>
                    </a>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </section>
</div>
