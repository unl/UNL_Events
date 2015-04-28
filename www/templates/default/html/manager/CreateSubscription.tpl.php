<?php echo $context->calendar->name ?> &gt; Add a Subscription

<div class="wdn-grid-set">
    <form action="<?php echo $context->subscription->id == NULL ? $context->subscription->getNewURL($context->calendar) : $context->subscription->getEditURL($context->calendar) ?>" method="POST">
        <div class="wdn-col-two-thirds">
            <fieldset>
                <label for="title">Title*</label>
                <input type="text" id="title" name="title" value="<?php echo $context->subscription->name ?>" />

                <label for="calendars">Events Posted to Calendar(s):</label>
                <select id="calendars" name="calendars[]" multiple="multiple">
                <?php foreach($context->getAvailableCalendars() as $calendar) { ?>
                    <option value="<?php echo $calendar->id ?>"><?php echo $calendar->name ?></option>
                <?php } ?>
                </select>
            </fieldset>

            <button class="wdn-button wdn-button-brand" type="submit">
                <?php echo $context->subscription->id == NULL ? 'Add Subscription' : 'Save Subscription' ?>
            </button>
        </div>
        <div class="wdn-col-one-third">
            <div class="visual-island">
                Automatically approve events?
                <ol> 
                    <li> 
                        <input type="radio" value="no" name="auto_approve" id="auto-approve-no" checked="checked"> 
                        <label for="auto-approve-no">No (send to pending)</label> 
                    </li> 
                    <li> 
                        <input type="radio" value="yes" name="auto_approve" id="auto-approve-yes"> 
                        <label for="sharing-public">Yes (send to upcoming)</label> 
                    </li> 
                </ol>
            </div>
        </div>
    </form>
</div>