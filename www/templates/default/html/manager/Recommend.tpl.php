<?php echo $context->calendar->name ?> &gt; Recommend <?php echo $context->event->title; ?>

<div class="wdn-grid-set">
    <form action="" method="POST">
        <div class="wdn-col-full">
            <fieldset>
                <div class="wdn-grid-set">
                    <div class="wdn-col-one-half">
                        &nbsp;
                    </div>
                    <div class="wdn-col-one-fourth">
                        Pending
                    </div>
                    <div class="wdn-col-one-fourth">
                        Approved
                    </div>
                </div>

                <?php foreach($context->getRecommendableCalendars() as $id => $arr) { ?>
                    <div class="wdn-grid-set">
                        <?php $calendar = $arr['calendar']; ?>
                        <div class="wdn-col-one-half">
                            <?php echo $calendar->name; ?>
                        </div>
                        <div class="wdn-col-one-fourth">
                        <?php if ($arr['status'] == 'pending') { ?>
                            X
                        <?php } else if ($arr['can_pending']) { ?>
                            <input type="radio" name="calendar_<?php echo $calendar->id ?>" value="pending">
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                        </div>

                        <div class="wdn-col-one-fourth">
                        <?php if ($arr['status'] == 'posted' || $arr['status'] == 'archived') { ?>
                            X
                        <?php } else if ($arr['can_posted']) { ?>
                            <input type="radio" name="calendar_<?php echo $calendar->id ?>" value="posted">
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset>

            <button class="wdn-button wdn-button-brand" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>