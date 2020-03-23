<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2020 Maxwell Power
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
$device = 'H';

// Process UTC timestamp
$timestamp = date("Y-m-d H:i:s");

// Process 5n1 Update
if ($_GET['sensor'] === $config->station->sensor_5n1) {

    // Process Hub Pressure, Wind Speed, Wind Direction, and Rainfall
    if ($_GET['mt'] === '5N1x31') {
        $source = '5';
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
        $rainDate = date('Y-m-d');
        $rainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rainin', FILTER_SANITIZE_STRING));
        $dailyRainIN = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'dailyrainin', FILTER_SANITIZE_STRING));

        //Other
        $battery = (string)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'battery', FILTER_SANITIZE_STRING));
        $rssi = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rssi', FILTER_SANITIZE_STRING));

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.
            mysqli_query($conn,
                "INSERT INTO `pressure` (`inhg`, `timestamp`, `device`, `source`) VALUES ('$baromin', '$timestamp', '$device', '$source');");

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(HUB)[SYS]: Pressure = $baromin");
            }
        }

        // Enter 5N1x31 readings into DB
        $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
            INSERT INTO `winddirection` (`degrees`, `timestamp`, `device`, `source`) VALUES ('$windDirection', '$timestamp', '$device', '$source');
            UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';
            INSERT INTO `dailyrain` (`dailyrainin`, `date`, `last_update`, `device`, `source`) VALUES ('$dailyRainIN', '$rainDate', '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';
            UPDATE `5n1_status` SET `battery`='$battery', `rssi`='$rssi', `last_update`='$timestamp' WHERE `device`='hub';";
        $result = mysqli_multi_query($conn, $sql);
        while (mysqli_next_result($conn)) {
            null;
        }

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG,
                "(HUB)[5N1]: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN");
            syslog(LOG_DEBUG, "(HUB)[5N1]: Battery: $battery | Signal: $rssi");
        }

        // Update the time the data was received
        last_updated_at();

    } // Done 5N1x31

    // Process Wind Speed, Temperature, Humidity
    elseif ($_GET['mt'] === '5N1x38') {
        $source = '5';

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

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.

            $sql = "INSERT INTO `pressure` (`inhg`, `timestamp`, `device`, `source`) VALUES ('$baromin', '$timestamp', '$device', '$source');";
            $result = mysqli_query($conn, $sql);

            // Log it
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG,
                    "(HUB)[SYS]: Pressure = $baromin");
            }
        }

        // Enter 5N1x38 readings into DB
        $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
            INSERT INTO `temperature` (`tempF`, `timestamp`, `device`, `source`) VALUES ('$tempF', '$timestamp', '$device', '$source');
            INSERT INTO `humidity` (`relH`, `timestamp`, `device`, `source`) VALUES ('$humidity', '$timestamp', '$device', '$source');
            UPDATE `5n1_status` SET `battery`='$battery', `rssi`='$rssi', `last_update`='$timestamp' WHERE `device`='hub';";
        $result = mysqli_multi_query($conn, $sql);
        while (mysqli_next_result($conn)) {
            null;
        }

        // Log it
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG,
                "(HUB)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH");
            syslog(LOG_DEBUG, "(HUB)[5N1]: Battery: $battery | Signal: $rssi");
        }

        // Update the time the data was received
        last_updated_at();
    } // Done 5N1x38
} // Done 5N1

// Process Tower Sensors
elseif ($config->station->towers === true && ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn' || $_GET['mt'] === 'light')) {

    // Tower ID
    $towerID = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'sensor', FILTER_SANITIZE_NUMBER_INT));

// Check if this tower exists
    $sql = "SELECT * FROM `towers` WHERE `sensor` = '$towerID';";
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
        if ($config->station->baro_source !== 2) {
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
                $result = mysqli_query($conn, $sql);

                // Log it
                if ($config->debug->logging === true) {
                    syslog(LOG_DEBUG,
                        "(HUB)[SYS]: Pressure = $baromin");
                }
            }
        }

        // Insert Tower data into DB
        $sql = "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`, `battery`, `rssi`, `timestamp`, `device`) VALUES ('$tempF', '$humidity', '$towerID', '$battery', '$rssi', '$timestamp', '$device');";
        $result = mysqli_query($conn, $sql);

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG,
                "(HUB)[TOWER][$towerName]: tempF = $tempF | relH = $humidity");
            syslog(LOG_DEBUG, "(HUB)[TOWER][$towerName]: Battery = $battery | Signal = $rssi");
        }
    } // This tower has not been added
    else {
        syslog(LOG_ERR, "(HUB)[TOWER][ERROR]: Unknown ID $towerID. Raw: $myacuriteQuery");
        die();
    }
} // Done Tower Sensors

// This sensor is not added
else {
    $sensor = $_GET['sensor'];
    if ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn' || $_GET['mt'] === 'light') {
        syslog(LOG_ERR,
            "(HUB)[TOWER][ERROR]: Towers not enabled - Tower ID $sensor. Raw = $myacuriteQuery");
    } elseif ($_GET['mt'] === '5N1') {
        syslog(LOG_ERR, "(HUB)[5N1][ERROR]: Unknown Sensor ID $sensor. Raw = $myacuriteQuery");
    } else {
        syslog(LOG_ERR, "(HUB)[ERROR]: Unknown Sensor $sensor. Raw = $myacuriteQuery");
    }
    die();
}

// Finish Update

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents('http://' . $config->debug->server->url . '/weatherstation/updateweatherstation?' . $myacuriteQuery);
}

$hubResponse = '{"localtime":"' . date('H:i:s') . '"}';

// Log the raw data
if ($config->debug->logging === true) {
    syslog(LOG_DEBUG, "(HUB)[Update]: Query = $myacuriteQuery | Response = $hubResponse");
}

// Output the expected response to the smartHUB
echo $hubResponse;
