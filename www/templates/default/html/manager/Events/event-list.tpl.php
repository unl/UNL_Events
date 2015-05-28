<select class="all-pending-event-tools">
    <option value="">Bulk Action</option>
    <option value="move-to-upcoming">Move to Upcoming</option>
    <option value="recommend">Recommend</option>
    <option value="delete">Delete</option>
</select>
<div class="wdn-grid-set">
    <div class="wdn-col-one-sixth">
        <input type="checkbox" id="select-all">
    </div>
    <div class="wdn-col-one-sixth">
        <h6>Title</h6>
    </div>
    <div class="wdn-col-one-sixth">
        <h6>Dates</h6>
    </div>
    <div class="wdn-col-one-sixth">
        <h6>Location</h6>
    </div>
</div>
<?php foreach($context as $event): ?>
    
<div class="wdn-grid-set">
    <div class="wdn-col-one-sixth">
        <input type="checkbox" class="select-event" data-id="<?php echo $event->id; ?>">
    </div>
    <div class="wdn-col-one-sixth">
        <a href="<?php echo $event->getEditURL($controller->getCalendar()) ?>"><?php echo $event->title; ?></a>
    </div>
    <div class="wdn-col-one-third">
        <?php foreach($event->getDateTimes() as $datetime) { ?>
            <?php echo date('n/j/y @ h:ia', strtotime($datetime->starttime)); ?>
            <?php if (!empty($location = $datetime->getLocation())) echo $location->name; ?>
        <?php } ?>
    </div>
    <div class="wdn-col-one-third">
        <select class="pending-event-tools" data-id="<?php echo $event->id; ?>" data-recommend-url="<?php echo $event->getRecommendURL($controller->getCalendar()) ?>">
            <option value="">Select an Action</option>
            <option value="move-to-upcoming">Move to Upcoming</option>
            <option value="recommend">Recommend</option>
            <option value="delete">Delete</option>
        </select>
    </div>
</div>
<?php endforeach; ?>