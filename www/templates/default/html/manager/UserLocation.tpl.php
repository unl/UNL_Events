<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "User Locations" => null
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1>User Locations</h1>
