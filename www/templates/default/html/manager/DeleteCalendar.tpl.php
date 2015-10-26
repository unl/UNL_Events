<?php
	$user = \UNL\UCBCN\Manager\Auth::getCurrentUser();

	$calendar = $context->calendar;
	
?>

<h1>Are you sure?</h1>
<p>Deleting a calendar would delete all of the events associated with it.</p>
<form method="post" action="<?php echo $user->getDeleteCalendarFinalURL($calendar) ?>" class="delete-form">
<button class="wdn-button wdn-button-brand" type="submit">Delete</button>
</form>
<a class="wdn-button wdn-button-brand" href="<?php echo $user->getEditPermissionsURL($calendar) ?>">Edit Permissions</a>
