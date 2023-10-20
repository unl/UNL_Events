<?php
    $offset = $context->offset ?? $context->options['offset'];
    $limit = $context->limit ?? $context->options['limit'];

    $previous_offset = $offset - $limit;
    $next_offset     = $offset + $limit;

    $previous_link = $context->getURL();
    $next_link     = $context->getURL();

    $previous_link_parts = parse_url($previous_link);
    $next_link_parts = parse_url($next_link);

    if (isset($previous_link_parts['query'])) {
        parse_str($previous_link_parts['query'], $previous_link_params);
    } else {
        $previous_link_params = array();
    }

    if (isset($next_link_parts['query'])) {
        parse_str($next_link_parts['query'], $next_link_params);
    } else {
        $next_link_params = array();
    }

    if ($limit != 100) {
        $previous_link_params['limit'] = $limit;
        $next_link_params['limit'] = $limit;
    }

    if ($previous_offset > 0) {
        $previous_link_params['offset'] = $previous_offset;
    }

    if ($next_offset <= $context->count()) {
        $next_link_params['offset'] = $next_offset;
    }

    $previous_link_parts['query'] = http_build_query($previous_link_params);
    $next_link_parts['query'] = http_build_query($next_link_params);

    $previous_link = $previous_link_parts['scheme']
        . '://'
        . $previous_link_parts['host']
        . $previous_link_parts['path']
        . '?'
        . $previous_link_parts['query'];
    $next_link = $next_link_parts['scheme']
        . '://'
        . $next_link_parts['host']
        . $next_link_parts['path']
        . '?'
        . $next_link_parts['query'];
?>

<?php if($context->count() > 0 && !($previous_offset < 0 && $next_offset > $context->count())): ?>
    <div class="dcf-d-flex dcf-flex-row dcf-flex-nowrap dcf-jc-between dcf-ai-end dcf-mt-3">
        <?php if ($previous_offset < 0): ?>
            <?php // We wanted to be able to disable this but you can not disable a link ?>
            <button
                class="dcf-btn dcf-btn-secondary"
                disabled
            >
                Previous <?php echo $limit; ?>
            </button>
        <?php else: ?>
            <a
                class="dcf-btn dcf-btn-secondary"
                href="<?php echo $previous_link; ?>"
            >
                Previous <?php echo $limit; ?>
            </a>
        <?php endif; ?>

        <p class="dcf-txt-xs">Only Displaying <?php echo $limit; ?> Results at a time</p>

        <?php if ($next_offset > $context->count()): ?>
            <?php // We wanted to be able to disable this but you can not disable a link ?>
            <button
                class="dcf-btn dcf-btn-secondary"
                disabled
            >
                Next <?php echo $limit; ?>
            </button>
        <?php else: ?>
            <a
                class="dcf-btn dcf-btn-secondary"
                href="<?php echo $next_link; ?>"
            >
                Next <?php echo $limit; ?>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
