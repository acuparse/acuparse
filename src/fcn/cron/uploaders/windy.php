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
 * File: src/fcn/cron/windy.php
 * Windy Updater
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/**
 * @return array
 * @return array
 * @var object $data Weather Data
 * @var object $atlas Atlas Data
 */

$sql = "SELECT `timestamp` FROM `windy_updates` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
$count = mysqli_num_rows(mysqli_query($conn, $sql));

// Make sure update interval has passed since last update
if ((strtotime($result['timestamp']) < strtotime('-5 minutes')) or ($count == 0)) {
    $windyQueryUrl = $config->upload->windy->url . '/' . $config->upload->windy->key;
    $windyQuery = '?station=' . $config->upload->windy->station . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSpeedMPH . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN;
    if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
        $windyQuery = $windyQuery . '&uv=' . $atlas->uvIndex;
    }
    $windyQueryResult = file_get_contents($windyQueryUrl . $windyQuery);
// Save to DB
    mysqli_query($conn,
        "INSERT INTO `windy_updates` (`query`,`result`) VALUES ('$windyQuery', '$windyQueryResult')");
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "(EXTERNAL){Windy}: Query = $windyQuery | Response = $windyQueryResult");
    }
} // No new update to send
else {
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "(EXTERNAL){Windy}: Update not sent. Not enough time has passed");
    }
}
