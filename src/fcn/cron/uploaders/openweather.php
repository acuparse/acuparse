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
 * File: src/fcn/cron/uploaders/openweather.php
 * Open Weather Updater
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $data Weather Data
 * @var object $atlas Atlas Data
 */

syslog(LOG_NOTICE, "(EXTERNAL){OpenWeather}: Starting Update ...");

// Build and send update
if ($config->station->device === 0) {
    $data = array("station_id" => $config->upload->openweather->id, "dt" => time(), "temperature" => $data->tempC, "wind_speed" => round($data->windSpeedKMH / 3.6, 1), "wind_gust" => round($atlas->windGustKMH / 3.6, 1), "wind_deg" => $data->windDEG, "pressure" => round($data->pressure_kPa * 10, 1), "humidity" => $data->relH, "rain_1h" => $data->rainMM, "rain_24h" => $data->rainTotalMM_today);
} else {
    $data = array("station_id" => $config->upload->openweather->id, "dt" => time(), "temperature" => $data->tempC, "wind_speed" => round($data->windSpeedKMH / 3.6, 1), "wind_deg" => $data->windDEG, "pressure" => round($data->pressure_kPa * 10, 1), "humidity" => $data->relH, "rain_1h" => $data->rainMM, "rain_24h" => $data->rainTotalMM_today);
}
$openweatherQuery = json_encode(array($data));

$ch = curl_init($config->upload->openweather->url . '?appid=' . $config->upload->openweather->key);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $openweatherQuery);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$openweatherQueryResult = curl_exec($ch);
$openweatherQueryResponseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
$openweatherQueryResponseProtocol = curl_getinfo($ch, CURLINFO_PROTOCOL);
curl_close($ch);

$openweatherQueryResponse = (empty($openweatherQueryResult)) ? '{"SUCCESS": "HTTP ' . $openweatherQueryResponseCode . '"}' : '[{"FAIL": ' . $openweatherQueryResult . '}]';

// Save to DB
mysqli_query($conn,
    "INSERT INTO `openweather_updates` (`query`,`result`) VALUES ('$openweatherQuery', '$openweatherQueryResponse')");

// Log it
syslog(LOG_NOTICE, "(EXTERNAL){OpenWeather}: Query = $openweatherQuery | Response = $openweatherQueryResponse");
