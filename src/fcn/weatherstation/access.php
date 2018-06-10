<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2018 Maxwell Power
 * @author Maxwell Power <max@acuparse.com>
 * @link http://www.acuparse.com
 * @license AGPL-3.0+
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this code. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * File: src/fcn/updates/access.php
 * Processes an update from an Access
 */

// Process UTC timestamp
$timestamp = (string)mysqli_real_escape_string($conn,
    filter_input(INPUT_GET, 'dateutc', FILTER_SANITIZE_STRING));
$timestamp = str_replace('T', ' ', $timestamp);
$timestamp = strtotime($timestamp . ' UTC');
$timestamp = date("Y-m-d H:i:s", $timestamp);


// Process 5N1 Update
if ($_GET['mt'] === '5N1') {

    if ($_GET['sensor'] === $config->station->sensor_5n1) {

        //Barometer
        $baromin = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'baromin', FILTER_SANITIZE_STRING));
        if ($config->station->baro_offset !== 0) {
            $baromin = $baromin + $config->station->baro_offset;
        }
        // Wind Speed
        $windSpeedMPH = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'windspeedmph', FILTER_SANITIZE_STRING));

        /*
            $windGustMPH = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'windgustmph', FILTER_SANITIZE_STRING));

            $windSpeedAvgMPH = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'windspeedavgmph', FILTER_SANITIZE_STRING));
        */

        // Wind Direction
        $windDirection = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'winddir', FILTER_SANITIZE_STRING));

        /*
            $windGustDirection = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'windgustdir', FILTER_SANITIZE_STRING));
        */

        // Rainfall
        $rainDate = date('Y-m-d');
        $rainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rainin', FILTER_SANITIZE_STRING));
        $dailyRainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'dailyrainin', FILTER_SANITIZE_STRING));

        // Temperature
        $tempF = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'tempf', FILTER_SANITIZE_STRING));
        /*
            $heatIndex = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'heatindex', FILTER_SANITIZE_STRING));
            $feelsLike = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'feelslike', FILTER_SANITIZE_STRING));
            $windChill = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'windchill', FILTER_SANITIZE_STRING));
            $dewptF = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'dewptf', FILTER_SANITIZE_STRING));
        */
        // Humidity
        $humidity = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'humidity', FILTER_SANITIZE_STRING));

        // Add readings to database
        /*
            * INSERT INTO `windgustmph` (`speedMPH`, `timestamp`) VALUES ('$windGustMPH', '$timestamp');
            * INSERT INTO `windgustdir` (`degrees`, `timestamp`) VALUES ('$windGustDirection', '$timestamp);
            * INSERT INTO `windspeedavgmph` (`speedMPH`, `timestamp`) VALUES ('$windSpeedAvgMPH', '$timestamp');
            * INSERT INTO `heatindex` (`tempF`, `timestamp`) VALUES ('$heatIndex', '$timestamp');
            * INSERT INTO `feelslike` (`tempF`, `timestamp`) VALUES ('$feelsLike', '$timestamp);
            * INSERT INTO `windchill` (`tempF`, `timestamp`) VALUES ('$windChill', '$timestamp');
            * INSERT INTO `dewptf` (`tempF`, `timestamp`) VALUES ('$dewptF', '$timestamp');
        */
        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 1) { // Baro. readings not disabled.
            mysqli_multi_query($conn,
                "INSERT INTO `pressure` (`inhg`, `timestamp`) VALUES ('$baromin', '$timestamp');
                    INSERT INTO `windspeed` (`speedMPH`, `timestamp`) VALUES ('$windSpeedMPH' , '$timestamp');
                    INSERT INTO `temperature` (`tempF`, `timestamp`) VALUES ('$tempF', '$timestamp');
                    INSERT INTO `winddirection` (`degrees`, `timestamp`) VALUES ('$windDirection', '$timestamp');
                    INSERT INTO `humidity` (`relH`, `timestamp`) VALUES ('$humidity', '$timestamp');
                    UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`, `last_update`) VALUES ('$dailyRainIN', '$rainDate', '$timestamp') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN', `last_update`='$timestamp'");

            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(ACCESS)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH | Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN | Pressure = $baromin");
            }
        } else { // Baro. readings disabled
            mysqli_multi_query($conn,
                "INSERT INTO `windspeed` (`speedMPH`, `timestamp`) VALUES ('$windSpeedMPH' , '$timestamp');
                    INSERT INTO `temperature` (`tempF`, `timestamp`) VALUES ('$tempF', '$timestamp');
                    INSERT INTO `winddirection` (`degrees`, `timestamp`) VALUES ('$windDirection', '$timestamp');
                    INSERT INTO `humidity` (`relH`, `timestamp`) VALUES ('$humidity', '$timestamp');
                    UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`, `last_update`) VALUES ('$dailyRainIN', '$rainDate', '$timestamp') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN', `last_update`='$timestamp'");

            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(ACCESS)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH | Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN | Pressure (DISABLED) = $baromin");
            }
        }
    }
} // Process Tower Sensors
elseif ($config->station->towers === true && ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn')) {

    // Tower ID
    $towerID = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'sensor', FILTER_SANITIZE_NUMBER_INT));

    // Check if this tower exists
    $sql = "SELECT * FROM `towers` WHERE `sensor` = '$towerID'";
    $count = mysqli_num_rows(mysqli_query($conn, $sql));
    if ($count === 1) {
        $result = mysqli_fetch_array(mysqli_query($conn, $sql));
        $towerName = $result['name'];

        // ProIn Specific Variables
        if ($_GET['mt'] === 'ProIn') {
            $tempF = (float)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'indoortempf', FILTER_SANITIZE_STRING));
            $humidity = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'indoorhumidity', FILTER_SANITIZE_STRING));
        } else {
            // Temperature
            $tempF = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'tempf', FILTER_SANITIZE_STRING));

            // Humidity
            $humidity = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'humidity', FILTER_SANITIZE_STRING));
        }

        // Insert into DB
        mysqli_query($conn,
            "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`, `timestamp`) VALUES ('$tempF', '$humidity', '$towerID', '$timestamp')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(ACCESS)[TOWER][$towerName]: tempF = $tempF | relH = $humidity");
        }
    } // This tower has not been added
    else {
        syslog(LOG_ERR, "(ACCESS)[TOWER][ERROR]: Unknown ID: $towerID. Raw = $myacuriteQuery");
        die();
    }
} // This sensor is not added
else {
    $sensor = $_GET['sensor'];
    if ($_GET['mt'] === 'tower') {
        syslog(LOG_ERR,
            "(ACCESS)[TOWER] ERROR: Towers not enabled or Unknown Tower ID $sensor. Raw = $myacuriteQuery");
    } elseif ($_GET['mt'] === '5N1') {
        syslog(LOG_ERR, "(ACCESS)[5N1][ERROR]: Unknown Sensor ID $sensor. Raw = $myacuriteQuery");
    } else {
        syslog(LOG_ERR, "(ACCESS)[ERROR]: Unknown Sensor $sensor. Raw = $myacuriteQuery");
    }
    die();
}

// Update the time the data was received
$lastUpdate = date("Y-m-d H:i:s");
mysqli_query($conn, "UPDATE `last_update` SET `timestamp` = '$lastUpdate'");

// Build update data
$postData = http_build_query($_POST);
$opts = array(
    'http' =>
        array(
            'method' => 'POST',
            'header' => 'User-Agent:' . $_SERVER['HTTP_USER_AGENT'],
            'content' => $postData
        ),
    'ssl' =>
        array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        )
);
$context = stream_context_create($opts);

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents($config->debug->server->url . '/weatherstation/updateweatherstation?&' . $myacuriteQuery,
        false, $context);
}

// Forward the raw data to MyAcurite
if ($config->upload->myacurite->access_enabled === true) {
    $myacurite = file_get_contents($config->upload->myacurite->access_url . '/weatherstation/updateweatherstation?&' . $myacuriteQuery,
        false, $context);

    // Log the raw data
    if ($config->debug->logging === true) {
        syslog(LOG_DEBUG, "(ACCESS)[MyAcuRite]: Query = $myacuriteQuery | Response = $myacurite");
    }

    // Output the response to the Access
    echo $myacurite;
} // MyAcurite is disabled
else {
    // Output the expected response to the Access
    $accessTimezoneOffset = date('P');
    $myacurite = '{"timezone":"' . $accessTimezoneOffset . '"}';
    // Log the raw data
    if ($config->debug->logging === true) {
        syslog(LOG_DEBUG, "(ACCESS)[Acuparse]: Response = $myacurite");
    }
    echo $myacurite;
}
