<?php
	$calendar = $context->calendar;
?>
<section class="wdn-grid-set">
	<div class="wdn-col-one-fourth">
		<h3>My Calendars</h3>
	</div>
	<div class="wdn-col-three-fourths">
		<?php echo $calendar->name ?> &gt; Create Event
		<div class="wdn-grid-set">
			<form action="/manager/<?php echo $calendar->shortname ?>/create/" method="POST">
	            <div class="wdn-col-two-thirds">
					<legend>Details</legend>
					<fieldset>
						<label for="title">Title*</label>
						<input type="text" id="title" name="title" />

						<label for="subtitle">Subtitle</label>
						<input type="text" id="subtitle" name="subtitle" />

						<label for="description">Description</label>
						<textarea id="description" name="description"></textarea>

						<label for="type">Type</label>
						<select id="type" name="type">
						<?php foreach ($context->getEventTypes() as $type) { ?>
							<option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
						<?php } ?>
						</select>
					</fieldset>

					<legend>Location, Date, and Time</legend>
					<fieldset>
						<label for="location">Location*</label>
						<select id="location" name="location">
						<?php foreach ($context->getLocations() as $location) { ?>
							<option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
						<?php } ?>
						</select>

						<label for="room">Room</label>
						<input type="text" id="room" name="room" />

						<label for="start-date">Start Date &amp; Time</label>
						<input id="start-date" name="start_date" type="text" class="datepicker" />
						<select id="start-time-hour" name="start_time_hour">
							<option value=""></option>
						<?php for ($i = 1; $i <= 12; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
						<?php } ?>
						</select>

						<select id="start-time-minute" name="start_time_minute">
							<option value=""></option>
							<option value="0">00</option>
							<option value="5">05</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
							<option value="30">30</option>
							<option value="35">35</option>
							<option value="40">40</option>
							<option value="45">45</option>
							<option value="50">50</option>
							<option value="55">55</option>
						</select>

						<select id="start-time-am-pm" name="start_time_am_pm">
							<option value=""></option>
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select>

						<label for="end-date">End Date &amp; Time</label>
						<input id="end-date" name="end_date" type="text" class="datepicker" />
						<select id="end-time-hour" name="end_time_hour">
							<option value=""></option>
						<?php for ($i = 1; $i <= 12; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
						<?php } ?>
						</select>

						<select id="end-time-minute" name="end_time_minute">
							<option value=""></option>
							<option value="0">00</option>
							<option value="5">05</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
							<option value="30">30</option>
							<option value="35">35</option>
							<option value="40">40</option>
							<option value="45">45</option>
							<option value="50">50</option>
							<option value="55">55</option>
						</select>

						<select id="end-time-am-pm" name="end_time_am_pm">
							<option value=""></option>
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select>

						<input type="checkbox" name="recurring" id="recurring"> 
						<label for="recurring">This is a recurring event</label>
						<br>

						<label for="recurring-type">Recurs:</label>
						<select id="recurring-type" name="recurring_type">
							<option value="daily">Daily</option>
							<option value="weekly">Weekly</option>
							<option value="monthly">Monthly</option>
							<option value="annually">Yearly</option>
						</select>

						<label for="recurs-until-date">Recurs Until:</label>
						<input id="recurs-until-date" name="recurs_until_date" type="text" class="datepicker" />
						<select id="recurs-until-time-hour" name="recurs_until_time_hour">
							<option value=""></option>
						<?php for ($i = 1; $i <= 12; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
						<?php } ?>
						</select>

						<select id="recurs-until-time-minute" name="recurs_until_time_minute">
							<option value=""></option>
							<option value="0">00</option>
							<option value="5">05</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
							<option value="30">30</option>
							<option value="35">35</option>
							<option value="40">40</option>
							<option value="45">45</option>
							<option value="50">50</option>
							<option value="55">55</option>
						</select>

						<select id="recurs-until-time-am-pm" name="recurs_until_time_am_pm">
							<option value=""></option>
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select>

						<label for="recurring-monthly-type">Monthly Recurring Type:</label>
						<select id="recurring-monthly-type" name="recurring_monthly_type">
							<option value="first">first</option>
							<option value="second">second</option>
							<option value="third">third</option>
							<option value="fourth">fourth</option>
							<option value="last">last</option>
							<option value="date">date</option>
							<option value="lastday">lastday</option>
						</select>

						<label for="directions">Directions</label>
						<textarea id="directions" name="directions"></textarea>

						<label for="additional-public-info">Additional Public Info</label>
						<textarea id="additional-public-info" name="additional_public_info"></textarea>
					</fieldset>

					<button class="wdn-button wdn-button-brand" type="submit">Submit Event</button>
				</div>

				<div class="wdn-col-one-third">
					<div class="visual-island">
						<div class="vi-header">
							Sharing
						</div>
						<ol> 
							<li> 
								<input type="radio" value="private" name="private_public" id="sharing-private" checked="checked"> 
								<label for="sharing-private">Private</label> 
							</li> 
							<li> 
								<input type="radio" value="public" name="private_public" id="sharing-public"> 
								<label for="sharing-public">Public</label> 
							</li> 
						</ol>

						<input type="checkbox" name="send_to_main" id="send-to-main"> 
						<label for="send-to-main">Consider for main calendar</label>
					</div>

					<div class="visual-island">
						<div class="vi-header">
							Contact Info
						</div>

						<label for="contact-name">Name</label>
						<input type="text" id="contact-name" name="contact_name" />

						<label for="contact-phone">Phone</label>
						<input type="text" id="contact-phone" name="contact_phone" />

						<label for="contact-email">Email</label>
						<input type="text" id="contact-email" name="contact_email" />
					</div>

					<div class="visual-island">
						<label for="website">Event Website</label>
						<input type="text" id="website" name="website" />
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<script type="text/javascript">
//<![CDATA[
WDN.initializePlugin('jqueryui', [function () {
var $ = require('jquery');

$('.datepicker').datepicker();

}]);
//]]>
</script>
