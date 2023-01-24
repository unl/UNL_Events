<?php
    // Gets the event and audience
    $event = $context->eventdatetime->getEvent();
    $event_target_audience = $event->getAudiences();

    // creates a list of target audience
    $audience_text = "";
    foreach ($event_target_audience as $audience) {
        $current_audience = $audience->getAudience();

        if (!empty($audience_text)) {
            $audience_text .= ", ";
        }

        $audience_text .= $current_audience->name;
    }
?>

<?php if (!empty($audience_text)): ?>
<span class="target-audience">
    <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
        <path d="M23.48,3.126l-2.805-2.83c-0.094-0.095-0.222-0.148-0.355-0.148c-0.133,0-0.261,0.053-0.354,0.147l-5.009,5.04 C13.451,4.473,11.751,4,10,4C4.486,4,0,8.486,0,14c0,5.515,4.486,10,10,10s10-4.485,10-10c0-1.848-0.52-3.635-1.478-5.202 l4.956-4.968C23.673,3.636,23.674,3.321,23.48,3.126z M19,14c0,4.962-4.037,9-9,9s-9-4.038-9-9s4.037-9,9-9 c1.485,0,2.927,0.383,4.222,1.075l-1.335,1.343L10.86,5.313c-0.142-0.147-0.357-0.193-0.547-0.117C10.124,5.272,10,5.456,10,5.66 V7.5c-3.584,0-6.5,2.916-6.5,6.5c0,3.584,2.916,6.5,6.5,6.5s6.5-2.916,6.5-6.5h1.591c0.201,0,0.383-0.121,0.461-0.306 c0.078-0.186,0.037-0.4-0.104-0.544l-2.116-2.154l1.458-1.462C18.569,10.892,19,12.419,19,14z M10,11c-1.654,0-3,1.346-3,3 s1.346,3,3,3s3-1.346,3-3h2.5c0,3.033-2.468,5.5-5.5,5.5S4.5,17.033,4.5,14S6.968,8.5,10,8.5V11z M10,16c-1.103,0-2-0.897-2-2 c0-1.103,0.897-2,2-2v1.5c0,0.276,0.224,0.5,0.5,0.5H12C12,15.103,11.103,16,10,16z M15.274,10.64 c-0.194,0.194-0.195,0.508-0.003,0.704L16.898,13H11V6.9l1.521,1.58c0.094,0.097,0.222,0.152,0.356,0.153 c0.13-0.034,0.264-0.052,0.358-0.147l7.085-7.128l2.1,2.119L15.274,10.64z"></path>
        <g>
            <path fill="none" d="M0 0H24V24H0z"></path>
        </g>
    </svg>
    <span class="dcf-sr-only">Target Audiences:</span>
    <?php echo($audience_text); ?>
</span>
<?php endif; ?>