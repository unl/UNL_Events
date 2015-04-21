<section class="wdn-grid-set">
    <div class="wdn-col-one-fourth">
        <h3>My Calendars</h3>
    </div>
    <div class="wdn-col-three-fourths">
    <form action="<?php echo $context->calendar->id == NULL ? $context->calendar->getNewURL() : $context->calendar->getEditURL() ?>" method="POST">
        <fieldset>
            <label for="name">Name*</label>
            <input type="text" id="name" name="name" value="<?php echo $context->calendar->name ?>" />

            <label for="shortname">Shortname*</label>
            <input type="text" id="shortname" name="shortname" value="<?php echo $context->calendar->shortname ?>" />

            <label for="website">Website</label>
            <input type="text" id="website" name="website" value="<?php echo $context->calendar->website ?>" />

            <label for="event-release-preference">Event Release Preference</label>
            <select id="event-release-preference" name="event_release_preference">
                <option value="" <?php if ($context->calendar->eventreleasepreference == NULL) echo 'selected="selected"' ?>></option>
                <option value="immediate" <?php if ($context->calendar->eventreleasepreference == 1) echo 'selected="selected"' ?>>Immediate</option>
                <option value="pending" <?php if ($context->calendar->eventreleasepreference == 0) echo 'selected="selected"' ?>>Pending</option>
            </select>

            <label for="email-lists">Email Lists (separated by commas)</label>
            <textarea id="email-lists" name="email_lists"><?php echo $context->calendar->emaillists ?></textarea>

            <input type="checkbox" name="recommend_within_account" id="recommend-within-account" <?php if ($context->calendar->recommendationswithinaccount) echo 'checked="checked"' ?>> 
            <label for="recommend-within-account">Allow event recommendations within this account</label>
            <br>
        </fieldset>

        <button class="wdn-button wdn-button-brand" type="submit">
            <?php echo $context->calendar->id == NULL ? 'Create Calendar' : 'Save Calendar' ?>
        </button>
    </form>
</section>