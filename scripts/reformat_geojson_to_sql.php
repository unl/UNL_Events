<?php
$input_file_name = "";
$output_dir = "./data";
$output_sql = "";
$api_key = ""; // Find this in the google console and make temporary key, This is only for the zip code

if (empty($api_key)) {
    echo "\033[31m You need to get API Key from the google console for zip codes \033[0m\n";
}

// Loop through args and get input file and output dir
foreach ($argv as $index => $arg) {
    if ($index === 0) {
        continue;
    }

    if (empty($input_file_name)) {
        if ($arg == "--help") {
            echo "Usage: php74 scripts/reformat_geojson_to_csv.php [options] <file> [<out dir>]\n";
            echo "  Options:\n";
            echo "    --help to get help text\n";
            echo "  <file>:\n";
            echo "    File path for .geojson file\n";
            echo "  [<out dir>]:\n";
            echo "    Optional output directory\n";
            echo "    Defaults to ./data\n";
            die();
        }

        if (strpos($arg, ".geojson") !== strlen($arg) - 8) {
            echo "\033[31m Error Invalid .geojson File \033[0m\n";
            die();
        }
        $input_file_name = $arg;
    } else {
        if (strpos($arg, ".") !== false) {
            echo "\033[31m Error Invalid Output Directory \033[0m\n";
            die();
        }
        $output_dir = $arg;
    }
}

// If we did not get an input file then error
if (empty($input_file_name)) {
    echo "\033[31m Missing .geojson File \033[0m\n";
    die();
}

// Read the JSON file
$json = file_get_contents($input_file_name);

// Decode the JSON file
$json_data = json_decode($json, true);

if (!isset($json_data['features']) || !is_array($json_data['features']) || empty($json_data['features'])) {
    echo "\033[31m Could Not Find Features Or Features is invalid \033[0m\n";
    die();
}

// Loop through features and extract data
foreach ( $json_data['features'] as $index => $building) {
    if (!isset($building['properties']) || !is_array($building['properties']) || empty($building['properties'])) {
        echo "\033[31m Missing Properties On Feature \033[0m\n";
        continue;
    }

    // Helps know that things are happening
    echo $index+1 . ":" . count($json_data['features']) . " - " . $building['properties']['NAME'] . "\n";

    $zip = NULL;
    if (!empty($api_key)) {
        // Use google geocoding to get zip code
        $google_data = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($building['properties']['Address']) . '%20lincoln%20Nebraska&key=' . $api_key);

        // extract zip code if we got data back
        if ($google_data !== false) {
            $google_json_data = json_decode($google_data, true);
            foreach ($google_json_data['results'][0]['address_components'] as $component) {
                if ($component['types'][0] == "postal_code") {
                    $zip = $component['long_name'];
                    break;
                }
            }
        } else {
            echo "\033[31m Error Getting Zip Code \033[0m\n";
        }
    }

    // Puts in the SQL for the particular input
    $output_sql .= "CALL updateOrInsertLocation(";
    $output_sql .= "'" . str_replace("'", "''", $building['properties']['NAME']) . "', ";
    $output_sql .= "'" . str_replace("'", "''", $building['properties']['Address']) . "', ";
    $output_sql .= "'" . 'Lincoln' . "', ";
    $output_sql .= "'" . 'NE' . "', ";
    if (!empty($api_key)) {
        $output_sql .= "'" . $zip . "', ";
    }else{
        $output_sql .= "null, ";
    }
    $output_sql .= "'" . 'https://maps.unl.edu/' . $building['properties']['ABBREV'] . "', ";
    $output_sql .= "'" . $building['properties']['ABBREV'] . "', ";
    $output_sql .= "'" . '1' . "'";
    $output_sql .= ");\n";
}

// Adds procedure and the transaction
// We make a procedure so we can check if the value exists in the database and if it does we can update
// -  if not we can insert a new record
// We also do a transaction so we can make sure all the locations are updated or none
$output_sql = "
DROP PROCEDURE IF EXISTS updateOrInsertLocation;
DELIMITER //

CREATE PROCEDURE updateOrInsertLocation(
    IN in_name VARCHAR(100),
    IN in_streetaddress1 VARCHAR(255),
    IN in_city VARCHAR(100),
    IN in_state VARCHAR(2),
    IN in_zip VARCHAR(10),
    IN in_mapurl longtext,
    IN in_additionalpublicinfo VARCHAR(255),
    IN in_standard TINYINT
)
BEGIN
    SELECT @A:=id FROM location WHERE name = in_name AND standard = 1 LIMIT 1;
    IF EXISTS (SELECT id FROM location WHERE name = in_name AND standard = 1) THEN
        UPDATE location
        SET streetaddress1 = in_streetaddress1,
            city = in_city,
            state = in_state,
            zip = in_zip,
            mapurl = in_mapurl,
            additionalpublicinfo = in_additionalpublicinfo
        WHERE id = @A;
    ELSE
        INSERT INTO location (name, streetaddress1, city, state, zip, mapurl, additionalpublicinfo, standard)
        VALUES (in_name, in_streetaddress1, in_city, in_state, in_zip, in_mapurl, in_additionalpublicinfo, in_standard);
    END IF;

END//

DELIMITER ;

START transaction;

" . $output_sql . "
COMMIT;

DROP PROCEDURE updateOrInsertLocation;
";

// Write to file
file_put_contents(realpath($output_dir) . '/formatted_geojson_to_sql.sql', $output_sql, LOCK_EX);