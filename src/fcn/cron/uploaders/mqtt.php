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
 * File: src/fcn/cron/uploaders/mqtt.php
 * MQTT Updater
 */

/**
 * @var object $config Global Config
 * @var mysqli $conn Global MYSQL Connection
 */

syslog(LOG_NOTICE, "(EXTERNAL){MQTT}: Starting Update ...");

require(APP_BASE_PATH . '/fcn/lib/phpmqtt/phpMQTT.php');

require(APP_BASE_PATH . '/fcn/weather/getCurrentJSONData.php');

$MQTT_JSON = getJSONWeatherData(true);
$MQTT_data = json_decode($MQTT_JSON);

$mqtt = new Bluerhinos\phpMQTT($config->upload->mqtt->server, $config->upload->mqtt->port, $config->upload->mqtt->client);
$mqtt_connect = $mqtt->connect(true, null, $config->upload->mqtt->username, $config->upload->mqtt->password);

if ($mqtt_connect) {
// Send JSON readings
    $MQTT_topic = $config->upload->mqtt->topic;

    $mqtt->publish($MQTT_topic, $MQTT_JSON);

// Send Main Readings
    $MQTT_topic = $config->upload->mqtt->topic . "/main/";
    foreach ($MQTT_data->main as $topic => $value) {
        $mqtt->publish($MQTT_topic . $topic, $value, 0, 1);
    }

    if ($config->station->device === 0) {
        // Atlas Readings
        if ($config->station->primary_sensor === 0) {
            $MQTT_topic = $config->upload->mqtt->topic . "/atlas/";
            foreach ($MQTT_data->atlas as $topic => $value) {
                $mqtt->publish($MQTT_topic . $topic, $value, 0, 1);
            }
        }
        // Lightning Readings
        if ($config->station->lightning_source === 1 || $config->station->lightning_source === 3) {
            $MQTT_topic = $config->upload->mqtt->topic . "/lightning/";

            foreach ($MQTT_data->lightning as $topic => $value) {
                $mqtt->publish($MQTT_topic . $topic, $value, 0, 1);
            }
        }
    }

    // Tower Readings
    if ($config->station->towers === true) {
        $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange`");
        $towerCount = mysqli_num_rows($result);

        if ($towerCount >= 1) {
            require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerData.php');

            while ($row = mysqli_fetch_assoc($result)) {
                $MQTT_topic = $config->upload->mqtt->topic . "/towers/" . $row['sensor'] . "/";
                $getTowerData = new getCurrentTowerData($row['sensor']);
                $MQTT_Tower_JSON = $getTowerData->getJSONConditions();
                $mqtt->publish($MQTT_topic, json_encode($MQTT_Tower_JSON));
                foreach ($MQTT_Tower_JSON as $topic => $value) {
                    $mqtt->publish($MQTT_topic . $topic, $value, 0, 1);
                }
            }

            // Get Lightning Data
            if ($config->station->lightning_source === 2 || $config->station->lightning_source === 3) {
                $MQTT_topic = $config->upload->mqtt->topic . "towerLightning/";
                foreach ($MQTT_data->towerLightning as $topic => $value) {
                    $mqtt->publish($MQTT_topic . $topic, $value, 0, 1);
                }
            }
        }
    }

    $mqtt->disconnect();
    syslog(LOG_NOTICE, "(EXTERNAL){MQTT}: Published to Broker");
} else {
    syslog(LOG_ERR, "(EXTERNAL){MQTT}: Connection Failed");
}
