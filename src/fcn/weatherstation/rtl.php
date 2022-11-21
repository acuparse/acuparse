<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/pub/weatherstation/rtl.php
 * Accepts data from the Acuparse RTL Relay
 */

// Get the loader
require(dirname(__DIR__, 2) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $relayQuery The incoming query string
 */

// Retrieve the JSON data
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json);

// Build update data
$postData = http_build_query($data);
$opts = array(
    'http' =>
        array(
            'method' => 'POST',
            'header' => 'User-Agent: Acuparse',
            'content' => $json
        ),
    'ssl' =>
        array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        )
);
$context = stream_context_create($opts);

// Process UTC timestamp
$updateTimestamp = $data->time;
$updateTimestamp = strtotime($updateTimestamp . ' UTC');
$date = date('Y-m-d', $updateTimestamp);
$timestamp = date("Y-m-d H:i:s", $updateTimestamp);

$device = 'R';

// Atlas Update
if ($data->model === 'Acurite-Atlas') {
    $source = 'A';
    if ($data->sequence_num === 0) {
        $atlasID = sprintf('%08d', $data->id);
        if ($atlasID === $config->station->sensor_atlas && $config->station->primary_sensor === 0) {
            if ($data->battery_ok === 1) {
                $battery = "normal";
            } else {
                $battery = "low";
            }
            mysqli_query($conn, "UPDATE `atlas_status` SET `battery`='$battery',`last_update`='$timestamp'");

            // Message 37
            if ($data->message_type === 37) {
                $windSpeedMPH = (int)mysqli_real_escape_string($conn, $data->wind_avg_mi_h);
                $tempF = (float)mysqli_real_escape_string($conn, $data->temperature_F);
                $humidity = (int)mysqli_real_escape_string($conn, $data->humidity);

                // Enter readings into DB
                $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
                        INSERT INTO `temperature` (`tempF`, `timestamp`, `device`, `source`) VALUES ('$tempF', '$timestamp', '$device', '$source');
                        INSERT INTO `humidity` (`relH`, `timestamp`, `device`, `source`) VALUES ('$humidity', '$timestamp', '$device', '$source');";
                $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(RTL){ATLAS}[SQL ERROR]:" . mysqli_error($conn));
                while (mysqli_next_result($conn)) {
                    NULL;
                }

                // Log it
                syslog(LOG_INFO,
                    "(RTL){ATLAS}: TempF = $tempF | relH = $humidity | Windspeed = $windSpeedMPH");
                syslog(LOG_INFO, "(RTL){ATLAS}: Battery: $battery");

            } // Message 38
            elseif ($data->message_type === 38) {
                $windSpeedMPH = mysqli_real_escape_string($conn, $data->wind_avg_mi_h);

                // Wind Direction & check for reversal)
                if ($config->station->reverse_wind === true) {
                    require(APP_BASE_PATH . '/fcn/weather/inc/reverseWindDirection.php');
                    $windDirection = (int)mysqli_real_escape_string($conn,
                        reverseWindDirection($data->wind_dir_deg));
                } else {
                    $windDirection = (int)mysqli_real_escape_string($conn, $data->wind_dir_deg);
                }

                // Rain
                $rainIN = (float)mysqli_real_escape_string($conn, $data->rain_in);

                // Enter readings into DB
                $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
                        INSERT INTO `winddirection` (`degrees`, `timestamp`, `device`, `source`) VALUES ('$windDirection', '$timestamp', '$device', '$source');";

                /*
                Disabling Rainfall update as RTL seems to send inconsistent data
                UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';
                */

                $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(RTL){ATLAS}[SQL ERROR]:" . mysqli_error($conn));
                while (mysqli_next_result($conn)) {
                    NULL;
                }

                // Log it
                syslog(LOG_INFO,
                    "(RTL){ATLAS}: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN (Disabled)");
                syslog(LOG_INFO, "(RTL){ATLAS}: Battery: $battery");

            } // Message 39
            elseif ($data->message_type === 39) {
                $windSpeedMPH = (int)mysqli_real_escape_string($conn, $data->wind_avg_mi_h);
                $uvindex = (int)mysqli_real_escape_string($conn, $data->uv);

                // Get Light Data
                $lightIntensity = (int)mysqli_real_escape_string($conn, $data->lux);

                $result = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT `lightintensity`, `measured_light_seconds`, `timestamp` FROM `light` ORDER BY `timestamp` DESC LIMIT 1"));
                $lastLightIntensity = (int)$result['lightintensity'];
                $lastMeasuredLightSeconds = (int)$result['measured_light_seconds'];
                $lastLightTimestamp = (float)$result['timestamp'];

                $lastLightDay = date('d', strtotime($lastLightTimestamp));
                $currentLightDay = date('d', strtotime($timestamp));

                if ($lastLightDay === $currentLightDay) {
                    if ($lastLightIntensity !== 0) {
                        $lightSecondsDifference = date('s', (strtotime($timestamp) - strtotime($lastMeasuredLightSeconds)));
                        $lightSeconds = $lastMeasuredLightSeconds + $lightSecondsDifference;
                    } else {
                        $lightSeconds = $lastMeasuredLightSeconds;
                    }
                } else {
                    $lightSeconds = 0;
                }

                // Enter readings into DB
                $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `speedMPH`='$windSpeedMPH', `timestamp`='$timestamp', `device`='$device', `source`='$source';
                        INSERT INTO `light` (`lightintensity`, `measured_light_seconds`, `timestamp`) VALUES ('$lightIntensity', '$lightSeconds', '$timestamp') ON DUPLICATE KEY UPDATE `lightintensity`='$lightIntensity', `measured_light_seconds`='$lightSeconds', `timestamp`='$timestamp';
                        INSERT INTO `uvindex` (`uvindex`, `timestamp`) VALUES ('$uvindex', '$timestamp') ON DUPLICATE KEY UPDATE `uvindex`='$uvindex', `timestamp`='$timestamp';";

                $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(RTL){ATLAS}[SQL ERROR]:" . mysqli_error($conn));
                while (mysqli_next_result($conn)) {
                    null;
                }

                // Log it
                syslog(LOG_INFO,
                    "(RTL){ATLAS}: Windspeed = $windSpeedMPH | UV = $uvindex | Light = $lightIntensity / $lightSeconds");
                syslog(LOG_INFO, "(RTL){ATLAS}: Battery: $battery");

            }
            // Respond to the Relay service
            echo json_encode(array('success' => array('message' => 'Accepted', 'details' => "ATLAS ($data->id)")));
        } else {
            syslog(LOG_WARNING, "(RTL){ATLAS}[WARNING]: Unknown Sensor $data->id");
            echo json_encode(array('error' => array('message' => 'Unknown Sensor', 'details' => "ATLAS ($data->id)")));
        }
    } else {
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(RTL){ATLAS}: Duplicate reading");
        }
        echo json_encode(array('skipped' => array('message' => 'Skipping Duplicate', 'details' => "ATLAS ($data->id)")));
    }
} // Iris/5N1
elseif ($data->model === 'Acurite-5n1') {
    $source = 'I';
    if ($data->sequence_num === 0) {
        $irisID = sprintf('%08d', $data->id);
        if ($irisID === $config->station->sensor_iris && $config->station->primary_sensor === 1) {
            if ($data->battery_ok === 1) {
                $battery = "normal";
            } else {
                $battery = "low";
            }
            if ($config->station->device === 0) {
                mysqli_query($conn, "UPDATE `iris_status` SET `battery`='$battery',`last_update`='$timestamp' WHERE `device`='access';");
            } else {
                mysqli_query($conn, "UPDATE `iris_status` SET `battery`='$battery',`last_update`='$timestamp' WHERE `device`='hub';");
            }
            // Message 49
            if ($data->message_type === 49) {
                // Wind Speed
                $windSpeedKMH = (int)mysqli_real_escape_string($conn, $data->wind_avg_km_h);
                $windSpeedMPH = round($windSpeedKMH * 0.6213712, 1);

                // Wind Direction
                // Check for reversal
                if ($config->station->reverse_wind === true) {
                    require(APP_BASE_PATH . '/fcn/weather/inc/reverseWindDirection.php');
                    $windDirection = (int)mysqli_real_escape_string($conn,
                        reverseWindDirection($data->wind_dir_deg));
                } else {
                    $windDirection = (int)mysqli_real_escape_string($conn, $data->wind_dir_deg);
                }

                // Rain
                $rainIN = (float)mysqli_real_escape_string($conn, $data->rain_in);

                // Enter readings into DB
                $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source');
            INSERT INTO `winddirection` (`degrees`, `timestamp`, `device`, `source`) VALUES ('$windDirection', '$timestamp', '$device', '$source');";

                // UPDATE `rainfall` SET `rainin`='$rainIN', `last_update`='$timestamp', `device`='$device', `source`='$source';

                $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(RTL){IRIS}[SQL ERROR]:" . mysqli_error($conn));
                while (mysqli_next_result($conn)) {
                    NULL;
                }

                // Log it
                syslog(LOG_INFO,
                    "(RTL){IRIS}: Wind = $windDirection @ $windSpeedMPH | Rain = $rainIN");
                syslog(LOG_INFO, "(RTL){IRIS}: Battery: $battery");

            } // Message 56
            elseif ($data->message_type === 56) {
                // Wind Speed
                $windSpeedKMH = (int)mysqli_real_escape_string($conn, $data->wind_avg_km_h);
                $windSpeedMPH = round($windSpeedKMH * 0.6213712, 1);
                $tempF = $data->temperature_F;
                $humidity = $data->humidity;

                // Enter readings into DB
                $sql = "INSERT INTO `windspeed` (`speedMPH`, `timestamp`, `device`, `source`) VALUES ('$windSpeedMPH' , '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `speedMPH`='$windSpeedMPH', `timestamp`='$timestamp', `device`='$device', `source`='$source';
            INSERT INTO `temperature` (`tempF`, `timestamp`, `device`, `source`) VALUES ('$tempF', '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `tempF`='$tempF', `timestamp`='$timestamp', `device`='$device', `source`='$source';
            INSERT INTO `humidity` (`relH`, `timestamp`, `device`, `source`) VALUES ('$humidity', '$timestamp', '$device', '$source') ON DUPLICATE KEY UPDATE `relH`='$humidity', `timestamp`='$timestamp', `device`='$device', `source`='$source';";

                $result = mysqli_multi_query($conn, $sql) or syslog(LOG_ERR, "(RTL){IRIS}[SQL ERROR]:" . mysqli_error($conn));
                while (mysqli_next_result($conn)) {
                    NULL;
                }

                // Log it
                syslog(LOG_INFO,
                    "(RTL){IRIS}: Windspeed = $windSpeedMPH | Temp = $tempF | Humidity = $humidity");
                syslog(LOG_INFO, "(RTL){IRIS}: Battery: $battery");
            }

            echo json_encode(array('success' => array('message' => 'Accepted', 'details' => 'IRIS (' . $data->id . ')'),));

        } else {
            syslog(LOG_WARNING, "(RTL){IRIS}[WARNING]: Unknown Sensor $data->id");
            echo json_encode(array('error' => array('message' => 'Unknown Iris', 'details' => 'IRIS (' . $data->id . ')'),));
        }
    } else {
        if ($config->debug->logging === true) {
            syslog(LOG_DEBUG, "(RTL){IRIS}: Duplicate reading");
        }
        echo json_encode(array('skipped' => array('message' => 'Skipping Duplicate', 'details' => 'IRIS (' . $data->id . ')'),));
    }
} // Towers
elseif ($data->model === 'Acurite-Tower') {
    $source = 'T';
    if ($config->station->towers === true) {
        // Tower ID
        $towerID = sprintf('%08d', mysqli_real_escape_string($conn, $data->id));

        // Check if this tower exists
        $sql = "SELECT * FROM `towers` WHERE `sensor` = '$towerID';";
        $count = mysqli_num_rows(mysqli_query($conn, $sql));

        if ($count === 1) {
            $result = mysqli_fetch_assoc(mysqli_query($conn, $sql)) or syslog(LOG_ERR,
                "(RTL){TOWER}[SQL ERROR]:" . mysqli_error($conn));
            $towerName = $result['name'];

            // Temperature
            $tempC = (float)mysqli_real_escape_string($conn, $data->temperature_C);
            $tempF = $tempC * 1.8 + 32;

            // Humidity
            $humidity = (int)mysqli_real_escape_string($conn, $data->humidity);

            //Other
            if ($data->battery_ok === 1) {
                $battery = "normal";
            } else {
                $battery = "low";
            }

            // Check for duplicate tower reading

            // Make sure new data is being sent
            $timestampCount = mysqli_num_rows(mysqli_query($conn, "SELECT * from `tower_data` WHERE `sensor` = '$towerID' AND `timestamp` = '$timestamp' LIMIT 1;"));
            if ($timestampCount === 0) {

                // Insert Tower data into DB
                $sql = "INSERT INTO `tower_data` (`tempF`, `relH`, `sensor`, `battery`, `rssi`, `timestamp`, `device`) VALUES ('$tempF', '$humidity', '$towerID', '$battery', '9', '$timestamp', '$device') ON DUPLICATE KEY UPDATE `tempF`='$tempF', `relH`='$humidity', `sensor`='$towerID', `battery`='$battery', `rssi`='9', `timestamp`='$timestamp', `device`='$device';";
                $result = mysqli_query($conn, $sql) or syslog(LOG_ERR, "(RTL){TOWER}[SQL ERROR]:" . mysqli_error($conn));

                // Log it
                syslog(LOG_INFO, "(RTL){TOWER}<$towerName>: tempF = $tempF | relH = $humidity");
                syslog(LOG_INFO, "(RTL){TOWER}<$towerName>: Battery = $battery");

                // Respond to the Relay service
                echo json_encode(array('success' => array('message' => 'Accepted', 'details' => "TOWER ($towerName)")));
            } else {
                if ($config->debug->logging === true) {
                    syslog(LOG_DEBUG, "(RTL){TOWER}<$towerName>: Duplicate reading");
                }
                echo json_encode(array('skipped' => array('message' => 'Duplicate Update')));
            }
        } else {
            // Log it
            syslog(LOG_WARNING, "(RTL){TOWER}[WARNING]: Unknown Tower $data->id");
            echo json_encode(array('error' => array('message' => 'Unknown Tower', 'details' => "TOWER ($data->id)")));
        }
    } else {
        // Log it
        syslog(LOG_WARNING, "(RTL){TOWER}[WARNING]: Tower Sensors not Enabled!");
        echo json_encode(array('error' => array('message' => 'Tower Sensors Disabled', 'details' => "TOWER ($data->id)")));
    }
} // Sensor not found
else {
    // Log it
    syslog(LOG_WARNING, "(RTL)[WARNING]: Unknown Sensor $data->id");
    echo json_encode(array('error' => array('message' => 'Unknown Sensor', 'details' => "SENSOR ($data->id)")));
}

if ($config->debug->logging === true) {
    syslog(LOG_DEBUG, "(RTL){JSON}: $json");
}

// Send data to debug server
if ($config->debug->server->enabled === true) {
    file_get_contents('https://' . $config->debug->server->url . '/weatherstation/updateweatherstation?' . $relayQuery,
        false, $context);
    if ($config->debug->logging === true) {
        syslog(LOG_DEBUG, '(ACCESS){DEBUG_SERVER}: Data sent to debug server ' . $config->debug->server->url);
    }
}
