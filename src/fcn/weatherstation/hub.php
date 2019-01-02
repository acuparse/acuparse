<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2019 Maxwell Power
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

// Process 5n1 Update
if ($_GET['sensor'] === $config->station->sensor_5n1) {

    // Process Hub Pressure, Wind Speed, Wind Direction, and Rainfall
    if ($_GET['mt'] === '5N1x31') {
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

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.
            mysqli_multi_query($conn,
                "INSERT INTO `pressure` (`inhg`) VALUES ('$baromin');
                    INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windSpeedMPH');
                    INSERT INTO `winddirection` (`degrees`) VALUES ('$windDirection');
                    UPDATE `rainfall` SET `rainin`='$rainIN';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`) VALUES ('$dailyRainIN', '$rainDate') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN'");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN | Pressure = $baromin");
            }

        } else { // Baro. readings disabled
            mysqli_multi_query($conn,
                "INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windSpeedMPH');
                    INSERT INTO `winddirection` (`degrees`) VALUES ('$windDirection');
                    UPDATE `rainfall` SET `rainin`='$rainIN';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`) VALUES ('$dailyRainIN', '$rainDate') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyRainIN'");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN | DailyRain = $dailyRainIN | Pressure (DISABLED) = $baromin");
            }
        }
    } // Process Wind Speed, Temperature, Humidity
    elseif ($_GET['mt'] === '5N1x38') {

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

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.
            mysqli_multi_query($conn,
                "INSERT INTO `pressure` (`inhg`) VALUES ('$baromin');
                    INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windSpeedMPH');
                    INSERT INTO `temperature` (`tempF`) VALUES ('$tempF');
                    INSERT INTO `humidity` (`relH`) VALUES ('$humidity')");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH | Pressure = $baromin");
            }
        } else { // Baro. readings disabled
            mysqli_multi_query($conn,
                "INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windSpeedMPH');
                    INSERT INTO `temperature` (`tempF`) VALUES ('$tempF');
                    INSERT INTO `humidity` (`relH`) VALUES ('$humidity')");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH | Pressure (DISABLED) = $baromin");
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
            "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`) VALUES ('$tempF', '$humidity', '$towerID')");

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(HUB)[TOWER][$towerName]: tempF = $tempF | relH = $humidity");
        }
    } // This tower has not been added
    else {
        syslog(LOG_ERR, "(HUB)[TOWER][ERROR]: Unknown ID $towerID. Raw: $myacuriteQuery");
        die();
    }
} // This sensor is not added
else {
    $sensor = $_GET['sensor'];
    if ($_GET['mt'] === 'tower') {
        syslog(LOG_ERR, "(HUB)[TOWER][ERROR]: Towers not enabled. Raw = $myacuriteQuery");
    } elseif ($_GET['mt'] === '5N1x31' || $_GET['mt'] === '5N1x38') {
        syslog(LOG_ERR, "(HUB)[5N1][ERROR]: Unknown ID $sensor. Raw = $myacuriteQuery");
    } else {
        syslog(LOG_ERR, "(HUB)[ERROR]: Unknown Sensor $sensor. Raw = $myacuriteQuery");
    }
    die();
}

// Update the time the data was received
$lastUpdate = date("Y-m-d H:i:s");
mysqli_query($conn, "UPDATE `last_update` SET `timestamp` = '$lastUpdate'");

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents('http://' . $config->debug->server->url . '/weatherstation/updateweatherstation?' . $myacuriteQuery);
}

// Forward the raw data to MyAcurite
if ($config->upload->myacurite->hub_enabled === true) {

    // Don't send updates after EoL
    if ((strtotime('2019-03-01') > strtotime(date('Y-m-d')))) {
        $myacurite = file_get_contents($config->upload->myacurite->hub_url . '/weatherstation/updateweatherstation?' . $myacuriteQuery);

        // Create the response to the HUB. Since Acurite is ending support, we don't want firmware updates
        $hubResponse = '{"localtime":"' . date('H:i:s') . '"}';

        // Log the raw data
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG,
                "(HUB)[MyAcuRite]: Query = $myacuriteQuery | Response = $myacurite | Hub Response = $hubResponse");
        }
        // Output the expected response to the smartHUB
        echo $hubResponse;
    } else {
        goto disabled;
    }
} else {
    disabled:
    $myacurite = '{"localtime":"' . date('H:i:s') . '"}';

// Log the raw data
    if ($config->debug->logging === true) {
        syslog(LOG_DEBUG, "(HUB)[MyAcuRite]: Query = $myacuriteQuery | Response = $myacurite");
    }

// Output the expected response to the smartHUB
    echo $myacurite;
}
