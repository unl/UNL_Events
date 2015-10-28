<?php
	$user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
?>

<h1>Are you sure?</h1>
<p>Deleting a calendar would delete all of the events associated with it.</p>
<form method="post" action="<?php echo $context->calendar->getDeleteCalendarFinalURL() ?>" class="delete-form">
<button class="wdn-button wdn-button-brand" type="submit">Delete</button>
</form>
