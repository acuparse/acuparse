<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: src/fcn/cron/uploaders/weathercloud.php
 * Weathercloud Updater
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $data Weather Data
 * @var object $atlas Atlas Data
 * @var object $appInfo Global Application Info
 */

syslog(LOG_NOTICE, "(EXTERNAL){WeatherCloud}: Starting Update ...");

$sql = "SELECT `timestamp` FROM `wc_updates` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
$count = mysqli_num_rows(mysqli_query($conn, $sql));

// Make sure update interval has passed since last update
if ((strtotime($result['timestamp']) < strtotime('-10 minutes')) or ($count == 0)) {
    // Build and send update
    $wcQueryUrl = $config->upload->wc->url . '?wid=' . $config->upload->wc->id . '&key=' . $config->upload->wc->key;
    $wcQuery = '&temp=' . ($data->tempC * 10) . '&wdir=' . $data->windDEG . '&wspd=' . (($data->windSpeedKMH * 0.277778) * 10) . '&bar=' . ($data->pressure_kPa * 100) . '&hum=' . $data->relH . '&dew=' . ($data->dewptC * 10) . '&rainrate=' . ($data->rainMM * 10) . '&rain=' . ($data->rainTotalMM_today * 10);
    if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
        $wcQuery = $wcQuery . '&uvi=' . ($atlas->uvIndex * 10);
    }
    $wcQueryStatic = '&softwareid=555e1df0d6eb' . '&software=' . $appInfo->name . '_v' . $config->version->app;
    $wcQueryResult = file_get_contents($wcQueryUrl . $wcQuery . $wcQueryStatic);
    // Save to DB
    mysqli_query($conn, "INSERT INTO `wc_updates` (`query`,`result`) VALUES ('$wcQuery', '$wcQueryResult')");
    // Log it
    syslog(LOG_NOTICE, "(EXTERNAL){WeatherCloud}: Query = $wcQuery | Response = $wcQueryResult");
} // No new update to send
else {
    // Log it
    syslog(LOG_NOTICE, "(EXTERNAL){WeatherCloud}: Update not sent. Not enough time has passed");
}
