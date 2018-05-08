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
        $windspeedmph = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'windspeedmph', FILTER_SANITIZE_STRING));

        // Wind Direction
        $wind_direction = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'winddir', FILTER_SANITIZE_STRING));

        // Rainfall
        $rain_date = date('Y-m-d');
        $rainin = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'rainin', FILTER_SANITIZE_STRING));
        $dailyrainin = (float)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'dailyrainin', FILTER_SANITIZE_STRING));

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.
            mysqli_multi_query($conn,
                "INSERT INTO `pressure` (`inhg`) VALUES ('$baromin');
                    INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windspeedmph');
                    INSERT INTO `winddirection` (`degrees`) VALUES ('$wind_direction');
                    UPDATE `rainfall` SET `rainin`='$rainin';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`) VALUES ('$dailyrainin', '$rain_date') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyrainin'");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: Wind = $wind_direction @ $windspeedmph | Rain = $rainin | DailyRain = $dailyrainin | Pressure = $baromin");
            }

        } else { // Baro. readings disabled
            mysqli_multi_query($conn,
                "INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windspeedmph');
                    INSERT INTO `winddirection` (`degrees`) VALUES ('$wind_direction');
                    UPDATE `rainfall` SET `rainin`='$rainin';
                    INSERT INTO `dailyrain` (`dailyrainin`, `date`) VALUES ('$dailyrainin', '$rain_date') ON DUPLICATE KEY UPDATE `dailyrainin`='$dailyrainin'");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: Wind = $wind_direction @ $windspeedmph | Rain = $rainin | DailyRain = $dailyrainin | Pressure (DISABLED) = $baromin");
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
        $windspeedmph = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'windspeedmph', FILTER_SANITIZE_STRING));

        // Humidity
        $humidity = (int)mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'humidity', FILTER_SANITIZE_STRING));

        // Add readings to database

        // Check if Baro. readings are enabled or not
        if ($config->station->baro_source !== 2) { // Baro. readings not disabled.
            mysqli_multi_query($conn,
                "INSERT INTO `pressure` (`inhg`) VALUES ('$baromin');
                    INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windspeedmph');
                    INSERT INTO `temperature` (`tempF`) VALUES ('$tempF');
                    INSERT INTO `humidity` (`relH`) VALUES ('$humidity')");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windspeedmph | Pressure = $baromin");
            }
        } else { // Baro. readings disabled
            mysqli_multi_query($conn,
                "INSERT INTO `windspeed` (`speedMPH`) VALUES ('$windspeedmph');
                    INSERT INTO `temperature` (`tempF`) VALUES ('$tempF');
                    INSERT INTO `humidity` (`relH`) VALUES ('$humidity')");
            while (mysqli_next_result($conn)) {
                ;
            };

            // Log it
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG,
                    "(HUB)[5N1]: TempF = $tempF | relH = $humidity | Windspeed = $windspeedmph | Pressure (DISABLED) = $baromin");
            }
        }
    }
} // Process Tower Sensors
elseif ($config->station->towers === true && ($_GET['mt'] === 'tower' || $_GET['mt'] === 'ProOut' || $_GET['mt'] === 'ProIn')) {

    // Tower ID
    $tower_id = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'sensor', FILTER_SANITIZE_NUMBER_INT));

    // Check if this tower exists
    $sql = "SELECT * FROM `towers` WHERE `sensor` = '$tower_id'";
    $count = mysqli_num_rows(mysqli_query($conn, $sql));
    if ($count === 1) {
        $result = mysqli_fetch_array(mysqli_query($conn, $sql));
        $tower_name = $result['name'];

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
            "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`) VALUES ('$tempF', '$humidity', '$tower_id')");

        // Log it
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(HUB)[TOWER][$tower_name]: tempF = $tempF | relH = $humidity");
        }
    } // This tower has not been added
    else {
        syslog(LOG_ERR, "(HUB)[TOWER][ERROR]: Unknown ID $tower_id. Raw: $myacurite_query");
        die();
    }
} // This sensor is not added
else {
    $sensor = $_GET['sensor'];
    if ($_GET['mt'] === 'tower') {
        syslog(LOG_ERR, "(HUB)[TOWER][ERROR]: Towers not enabled. Raw = $myacurite_query");
    } elseif ($_GET['mt'] === '5N1x31' || $_GET['mt'] === '5N1x38') {
        syslog(LOG_ERR, "(HUB)[5N1][ERROR]: Unknown ID $sensor. Raw = $myacurite_query");
    } else {
        syslog(LOG_ERR, "(HUB)[ERROR]: Unknown Sensor $sensor. Raw = $myacurite_query");
    }
    die();
}

// Update the time the data was received
$last_update = date("Y-m-d H:i:s");
mysqli_query($conn, "UPDATE `last_update` SET `timestamp` = '$last_update'");

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents($config->debug->server->url . '/weatherstation/updateweatherstation?' . $myacurite_query);
}

// Forward the raw data to MyAcurite
if ($config->upload->myacurite->hub_enabled === true) {

    // Don't send updates after EoL
    if ((strtotime('2019-03-01') > strtotime(date('Y-m-d')))) {
        $myacurite = file_get_contents($config->upload->myacurite->hub_url . '/weatherstation/updateweatherstation?' . $myacurite_query);

        // Create the response to the HUB. Since Acurite is ending support, we don't want firmware updates
        $hub_response = '{"localtime":"' . date('H:i:s') . '"}';

        // Log the raw data
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG,
                "(HUB)[MyAcuRite]: Query = $myacurite_query | Response = $myacurite | Hub Response = $hub_response");
        }
        // Output the expected response to the smartHUB
        echo $hub_response;
    } else {
        goto disabled;
    }
} else {
    disabled:
    $myacurite = '{"localtime":"' . date('H:i:s') . '"}';

// Log the raw data
    if ($config->debug->logging === true) {
        syslog(LOG_DEBUG, "(HUB)[MyAcuRite]: Query = $myacurite_query | Response = $myacurite");
    }

// Output the expected response to the smartHUB
    echo $myacurite;
}
