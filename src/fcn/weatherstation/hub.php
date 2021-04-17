<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2021 Maxwell Power
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
 * File: src/fcn/updates/hub.php
 * Processes an update from a smartHUB
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $myacuriteQuery
 */

$device = 'H';

// Generate update timestamp
$timestamp = date("Y-m-d H:i:s");
$todaysDate = date('Y-m-d');

// Process Iris Update
if ($_GET['sensor'] === $config->station->sensor_iris) {

    // Process Hub Pressure, Wind Speed, Wind Direction, and Rainfall
    if ($_GET['mt'] === '5N1x31') {
        $source = 'I';
        //Barometer
        $baromin = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'baromin', FILTER_SANITIZE_STRING));
        if ($config->station->baro_offset !== 0) {
            $baromin = $baromin + $config->station->baro_offset;
        }

        // Wind Speed
        $windSpeedMPH = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'windspeedmph', FILTER_SANITIZE_STRING));

        // Wind Direction
        $windDirection = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'winddir', FILTER_SANITIZE_STRING));

        // Rainfall
        $rainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rainin', FILTER_SANITIZE_STRING));
        $dailyRainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'dailyrainin', FILTER_SANITIZE_STRING));

        //Other
        $battery = (string)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'battery', FILTER_SANITIZE_STRING));
        $rssi = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rssi', FILTER_SANITIZE_STRING));

        // Enter 5N1x31 readings into DB
        $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
            INSERT INTO `winddirection` (`degrees`, `timestamp`, `device`, `source`) VALUES ('$windDirection', '$timestamp', '$device', '$source');
            UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';
            INSERT INTO `dailyrain` (`dailyrainin`, `date`, `last_update`, `device`, `source`) VALUES ('$dailyRainIN', '$todaysDate', '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';
            INSERT INTO `pressure` (`inhg`, `timestamp`, `device`, `source`) VALUES ('$baromin', '$timestamp', '$device', '$source');
            UPDATE `iris_status` SET `battery`='$battery', `rssi`='$rssi', `last_update`='$timestamp' WHERE `device`='hub';";
        $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(HUB){IRIS}[SQL ERROR]:" . mysqli_error($conn));
        while (mysqli_next_result($conn)) {
            null;
        }

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(HUB): Pressure = $baromin");
            syslog(LOG_DEBUG,
                "(HUB){IRIS}: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN");
            syslog(LOG_DEBUG, "(HUB){IRIS}: Battery: $battery | Signal: $rssi");
        }

        // Update the time the data was received
        last_updated_at();

    } // Done 5N1x31

    // Process Wind Speed, Temperature, Humidity
    elseif ($_GET['mt'] === '5N1x38') {
        $source = 'I';

        //Barometer
        $baromin = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'baromin', FILTER_SANITIZE_STRING));
        if ($config->station->baro_offset !== 0) {
            $baromin = $baromin + $config->station->baro_offset;
        }

        // Temperature
        $tempF = (float)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'tempf', FILTER_SANITIZE_STRING));

        // Wind Speed
        $windSpeedMPH = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'windspeedmph', FILTER_SANITIZE_STRING));

        // Humidity
        $humidity = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'humidity', FILTER_SANITIZE_STRING));

        //Other
        $battery = (string)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'battery', FILTER_SANITIZE_STRING));
        $rssi = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rssi', FILTER_SANITIZE_STRING));

        // Enter 5N1x38 readings into DB
        $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
            INSERT INTO `temperature` (`tempF`, `timestamp`, `device`, `source`) VALUES ('$tempF', '$timestamp', '$device', '$source');
            INSERT INTO `humidity` (`relH`, `timestamp`, `device`, `source`) VALUES ('$humidity', '$timestamp', '$device', '$source');
            INSERT INTO `pressure` (`inhg`, `timestamp`, `device`, `source`) VALUES ('$baromin', '$timestamp', '$device', '$source');
            UPDATE `iris_status` SET `battery`='$battery', `rssi`='$rssi', `last_update`='$timestamp' WHERE `device`='hub';";
        $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(HUB){IRIS}[SQL ERROR]:" . mysqli_error($conn));
        while (mysqli_next_result($conn)) {
            null;
        }

        // Log it
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(HUB): Pressure = $baromin");
            syslog(LOG_DEBUG,
                "(HUB){IRIS}: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH");
            syslog(LOG_DEBUG, "(HUB){IRIS}: Battery: $battery | Signal: $rssi");
        }

        // Update the time the data was received
        last_updated_at();
    } // Done 5N1x38
} // Done Iris

// Process Tower Sensors
elseif ($config->station->towers === true && ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn' || $_GET['mt'] === 'light')) {

    // Tower ID
    $towerID = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'sensor', FILTER_SANITIZE_NUMBER_INT));

// Check if this tower exists
    $sql = "SELECT * FROM `towers` WHERE `sensor` = '$towerID';";
    $count = mysqli_num_rows(mysqli_query($conn, $sql)) or syslog(LOG_ERR,
        "(HUB){TOWER}[SQL ERROR]:" . mysqli_error($conn));
    if ($count === 1) {
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql)) or syslog(LOG_ERR,
            "(HUB){TOWER}[SQL ERROR]:" . mysqli_error($conn));
        $towerName = $result['name'];

        // ProIn Specific Variables
        if ($_GET['mt'] === 'ProIn') {
            $tempF = (float)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'indoortempf', FILTER_SANITIZE_STRING));
            $humidity = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'indoorhumidity', FILTER_SANITIZE_STRING));
        } else {
            // Temperature
            $tempF = (float)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'tempf', FILTER_SANITIZE_STRING));

            // Humidity
            $humidity = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'humidity', FILTER_SANITIZE_STRING));
        }

        //Other
        $battery = (string)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'battery', FILTER_SANITIZE_STRING));
        $rssi = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rssi', FILTER_SANITIZE_STRING));

        // Check if this is the upload tower and save the baro. reading
        // If there is no primary sensor, this will be the only baro. reading
        if ($config->upload->sensor->id === $towerID) {
            //Barometer
            $baromin = (float)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'baromin', FILTER_SANITIZE_STRING));
            if ($config->station->baro_offset !== 0) {
                $source = 'T';
                $baromin = $baromin + $config->station->baro_offset;
            }

            // Insert pressure reading into DB
            $sql = "INSERT INTO `pressure` (`inhg`, `timestamp`, `device`, `source`) VALUES ('$baromin', '$timestamp', '$device', '$source');";
            $result = mysqli_query($conn, $sql) or syslog(LOG_ERR,
                "(HUB){TOWER}[SQL ERROR]:" . mysqli_error($conn));

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG, "(HUB): Pressure = $baromin");
            }
        }

        // Insert Tower data into DB
        $sql = "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`, `battery`, `rssi`, `timestamp`, `device`) VALUES ('$tempF', '$humidity', '$towerID', '$battery', '$rssi', '$timestamp', '$device');";
        $result = mysqli_query($conn, $sql) or syslog(LOG_ERR, "(HUB){TOWER}[SQL ERROR]:" . mysqli_error($conn));

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(HUB){TOWER}<$towerName>: tempF = $tempF | relH = $humidity");
            syslog(LOG_DEBUG, "(HUB){TOWER}<$towerName>: Battery = $battery | Signal = $rssi");
        }

        // Update the time the data was received
        last_updated_at();
    } // This tower has not been added
    else {
        syslog(LOG_ERR, "(HUB){TOWER}[ERROR]: Unknown ID $towerID. Raw: $myacuriteQuery");
        exit();
    }
} // Done Tower Sensors

// This sensor is not added
else {
    $sensor = $_GET['sensor'];
    if ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn' || $_GET['mt'] === 'light') {
        syslog(LOG_ERR,
            "(HUB){TOWER}[ERROR]: Towers not enabled - Tower ID $sensor. Raw = $myacuriteQuery");
    } elseif ($_GET['mt'] === '5N1') {
        syslog(LOG_ERR, "(HUB){IRIS}[ERROR]: Unknown Sensor ID $sensor. Raw = $myacuriteQuery");
    } else {
        syslog(LOG_ERR, "(HUB)[ERROR]: Unknown Sensor ID $sensor. Raw = $myacuriteQuery");
    }
    exit();
}

// Finish Update

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents('http://' . $config->debug->server->url . '/weatherstation/updateweatherstation?' . $myacuriteQuery);
}

$responseTimestamp = date('H:i:s');
$hubResponse = json_encode(["localtime" => "$responseTimestamp"]);

// Log the raw data
if ($config->debug->logging === true) {
    syslog(LOG_DEBUG, "(HUB){Update}: Query = $myacuriteQuery | Response = $hubResponse");
}

// Output the expected response to the smartHUB

header_remove();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo $hubResponse;
