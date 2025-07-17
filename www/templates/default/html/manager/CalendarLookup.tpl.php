<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Calendar Lookup" => null
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');

    $token_inputs = '<input ' .
        'type="hidden" '.
        'name="' . $controller->getCSRFHelper()->getTokenNameKey() . '" ' .
        'value="' . $controller->getCSRFHelper()->getTokenName() . '" ' .
    '> ' .
    '<input ' .
        'type="hidden"' .
        'name="' . $controller->getCSRFHelper()->getTokenValueKey() . '" ' .
        'value="' . $controller->getCSRFHelper()->getTokenValue() . '" ' .
    '> ';
?>
<h1>Calendar Lookup</h1>

<div class="dcf-d-grid dcf-grid-cols-1 dcf-grid-cols-2@md">
    <form class="dcf-form" method="post">
        <?php echo $token_inputs; ?>
        <label for="lookupTerm">Calendar Shortname</label>
        <div class="dcf-input-group">
            <input
                id="lookupTerm"
                name="lookupTerm"
                type="text"
                value="<?php echo $context->post['lookupTerm'] ?? ""; ?>"
                required=""
            >
            <button class="dcf-btn dcf-btn-primary" id="lookup-submit" name="submit" type="submit">Search</button>
        </div>
        <span class="dcf-form-help">
            <span class="dcf-d-inline-block">
                This shortname can be found by navigating to the calendar's page and copying the value from the URL.
            </span>
            <span class="dcf-d-inline-block">
                To look up https://events.unl.edu/wdn/,
                search for wdn. This search is case sensitive.
            </span>
        </span>
    </form>
</div>

<?php if (isset($context->calendar) && $context->calendar !== false): ?>
    <div class="dcf-mt-5">
        <h2>Users on the '<?php echo $context->calendar->name; ?>' calendar</h2>
        <div class="dcf-d-grid dcf-grid-cols-1 dcf-grid-cols-3@lg dcf-grid-cols-2@sm dcf-row-gap-4 dcf-col-gap-vw">
            <?php foreach($context->getUsers() as $user): ?>
                <div>
                    <p class="dcf-bold dcf-mb-0">
                        <a href="https://directory.unl.edu/people/<?php echo $user->uid; ?>" target="_blank">
                            <?php echo $user->uid; ?>
                        </a>
                    </p>
                    <ul>
                        <?php foreach($context->getUserPermissions($user->uid) as $permission): ?>
                            <li class="dcf-txt-sm dcf-mb-0"><?php echo $permission->name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
