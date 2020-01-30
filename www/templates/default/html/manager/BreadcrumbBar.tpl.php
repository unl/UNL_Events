<nav id="breadcrumbs" <?php if (isset($context->search)) echo 'class="search"' ?>>
    <!-- WDN: see glossary item 'breadcrumbs' -->
    <h3 class="wdn_list_descriptor dcf-sr-only">Breadcrumbs</h3>
    
    <ul>
        <?php foreach ($context->crumbs as $text => $url): ?>
        <li>
            <?php if ($url != NULL): ?>
            <a href="<?php echo $url; ?>"><?php echo $text ?></a>
            <?php else: ?>
            <a><?php echo $text ?></a>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <?php if (isset($context->search)): ?>
    <div id="toggle-search">
        <button id="show-search">
            <svg class="dcf-h-5 dcf-w-5 dcf-fill-current" aria-hidden="true" focusable="false" height="16" width="16" viewBox="0 0 48 48">
                <path d="M18 36a17.9 17.9 0 0 0 11.27-4l15.31 15.41a2 2 0 0 0 2.84-2.82L32.08 29.18A18 18 0 1 0 18 36zm0-32A14 14 0 1 1 4 18 14 14 0 0 1 18 4z"></path>
            </svg>
            <span class="dcf-sr-only">Search</span>
        </button>
    </div>
    <?php endif; ?>
</nav>
