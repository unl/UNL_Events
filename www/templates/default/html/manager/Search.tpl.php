<form>
    <label for="events_search">Search</label>
    <input type="text" name="search_term" id="events_search" value="<?php echo $context->search_term ?>" />
</form>

<?php
foreach ($context->events as $event) {
    echo $event->id . '<br />';
}
?>

