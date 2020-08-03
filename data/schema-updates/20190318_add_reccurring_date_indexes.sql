CREATE  INDEX `going_lookup_idx` ON `recurringdate` (`event_datetime_id`, `ongoing`, `recurrence_id`);
CREATE  INDEX `event_date_lookup_idx` ON `recurringdate` (`event_id`, `recurringdate`);


