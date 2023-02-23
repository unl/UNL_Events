<div class="dcf-grid dcf-col-gap-vw">
    <section class="dcf-col-100% dcf-col-33%-start@md">
        <h2 class="results clear-top">
            <span class="dcf-subhead dcf-d-block">
                <?php echo $context->count() . ' search results from all calendars for event type(s)'; ?>
            </span>
            <a class="permalink dcf-d-block"
                <?php
                    if (empty($context->search_query)) {
                        echo 'href="'.$context->getURL().'"> \'Any\'';
                    } else {
                        echo 'href="'.$context->getURL().'">'.$context->getFormattedEventTypes();
                    }
                ?>
            </a>
        </h2>
    </section>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/EventType.tpl.php'); ?>

        <?php if($context->count() > 0): ?>
            <div class="dcf-d-flex dcf-flex-row dcf-flex-nowrap dcf-jc-between dcf-ai-end dcf-mt-3">
                <?php 
                    $previous_offset = $context->search_offset - $context->search_limit;
                    $next_offset     = $context->search_offset + $context->search_limit;

                    $previous_link = $context->getURL();
                    $next_link     = $context->getURL();

                    if ($context->search_limit != 100) {
                        $previous_link .= '&q_limit=' . $context->search_limit;
                        $next_link     .= '&q_limit=' . $context->search_limit;
                    }

                    if ($previous_offset != 0) {
                        $previous_link .= '&q_offset=' . $previous_offset;
                    }

                    $next_link     .= '&q_offset=' . $next_offset;
                ?>
                <?php if ($previous_offset < 0): ?>
                    <?php // We wanted to be able to disable this but you can not disable a link ?>
                    <button 
                        class="dcf-btn dcf-btn-primary"
                        disabled
                    >
                        Previous <?php echo $context->search_limit; ?> 
                    </button>
                <?php else: ?>
                    <a
                        class="dcf-btn dcf-btn-primary"
                        href="<?php echo $previous_link; ?>"
                    >
                        Previous <?php echo $context->search_limit; ?> 
                    </a>
                <?php endif; ?>
                    
                <p>Only Displaying <?php echo $context->search_limit; ?> Results at a time</p>
                <a
                    class="dcf-btn dcf-btn-primary"
                    href="<?php echo $next_link; ?>"
                >
                    Next <?php echo $context->search_limit; ?> 
                </a>
            </div>
        <?php endif; ?>
    </section>
</div>
