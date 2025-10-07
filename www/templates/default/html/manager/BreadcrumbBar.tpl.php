<nav class="dcf-relative dcf-overflow-hidden dcf-mb-6 dcf-pr-8 dcf-txt-md" id="breadcrumbs" aria-labelledby="breadcrumbs-label" <?php if (isset($context->search)) echo 'class="search"' ?>>
    <!-- WDN: see glossary item 'breadcrumbs' -->
    <p id="breadcrumbs-label" class="dcf-sr-only">Breadcrumbs</p>
    <ul class="dcf-mb-0">
        <?php foreach ($context->crumbs as $text => $url): ?>
        <li>
            <?php if ($url != NULL): ?>
            <a class="unl-prerender" href="<?php echo $url; ?>"><?php echo $text ?></a>
            <?php else: ?>
            <a class="unl-prerender"><?php echo $text ?></a>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php if (isset($context->search)): ?>
    <div class="dcf-absolute dcf-pin-top dcf-pin-right" id="toggle-search">
        <button class="dcf-btn dcf-btn-primary" id="show-search">
            <svg class="dcf-h-5 dcf-w-5 dcf-fill-current" aria-hidden="true" focusable="false" height="16" width="16" viewBox="0 0 48 48">
                <path d="M18 36a17.9 17.9 0 0 0 11.27-4l15.31 15.41a2 2 0 0 0 2.84-2.82L32.08 29.18A18 18 0 1 0 18 36zm0-32A14 14 0 1 1 4 18 14 14 0 0 1 18 4z"></path>
            </svg>
            <span class="dcf-sr-only">Search</span>
        </button>
    </div>
    <?php endif; ?>
</nav>
