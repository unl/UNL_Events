DELIMITER //

-- Delete the procedure if it is already created
DROP PROCEDURE IF EXISTS ongoing_to_recurring //

-- Create the procedure to convert ongoing events to daily recurring ones
CREATE PROCEDURE ongoing_to_recurring()
BEGIN

  -- Set up cursor variables
  DECLARE done INT DEFAULT FALSE;
  DECLARE current_event_datetime int;
  DECLARE event_cursor CURSOR FOR SELECT id FROM eventdatetime WHERE DATEDIFF(endtime, starttime) > 1 AND recurringtype = 'None' AND (starttime > CURDATE() OR endtime > CURDATE());
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  -- Open the cursor
  OPEN event_cursor;

  -- Loop through all the items in the cursor and if we can't find anymore exit the loop
  read_loop: LOOP
    FETCH event_cursor INTO current_event_datetime;
    IF done THEN
      LEAVE read_loop;
    END IF;

    -- Set up event date time variables
    SET @event_id = 0;
    SET @starttime = 0;
    SET @endtime = 0;
    SET @date_diff = 0;

    -- Set the variable values with the current cursor position
    SELECT
      (@event_id:=event_id) as event_id,
      (@starttime:=starttime) as starttime,
      (@endtime:=endtime) as endtime,
      (@date_diff:=DATEDIFF(endtime, starttime)) as date_diff
    FROM eventdatetime
    WHERE id = current_event_datetime;

    -- Create a new recurring date for as many days as are set in the ongoing event
    SET @i = 0;
    WHILE @i <= @date_diff DO
      INSERT INTO recurringdate
        SET
          recurringdate = DATE(@starttime + INTERVAL @i DAY),
          event_id = @event_id,
          recurrence_id = @i,
          ongoing = 0,
          unlinked = 0,
          event_datetime_id = current_event_datetime,
          canceled = 0;
      SET @i = @i + 1;
    END WHILE;

    -- Update the current cursor row with new recurring date data
    UPDATE eventdatetime
    SET
      endtime = CONCAT(DATE(@starttime), ' ', TIME(@endtime)),
      recurringtype = 'daily',
      recurs_until = @endtime
    WHERE id = current_event_datetime;

  -- Close the cursor and exit the loop/procedure
  END LOOP read_loop;
  CLOSE event_cursor;
END; //
DELIMITER ;

-- Call the procedure
CALL ongoing_to_recurring;