<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2024 Maxwell Power
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
 * File: src/fcn/cron/uploaders/pwsweather.php
 * PWS Weather Updater
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $atlas Atlas Data
 * @var object $data Weather Data
 * @var object $appInfo Global Application Info
 * @var string $utcDate Date
 */

syslog(LOG_NOTICE, "(EXTERNAL){PWS}: Starting Update ...");

// Build and send update
$pwsQueryUrl = $config->upload->pws->url . '?ID=' . $config->upload->pws->id . '&PASSWORD=' . $config->upload->pws->key;
$pwsQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSpeedMPH . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
if ($config->station->device === 0) {
    $pwsQuery = $pwsQuery . '&windgustmph=' . $data->windGustMPH;
    if ($config->station->primary_sensor === 0) {
        $pwsQuery = $pwsQuery . '&UV=' . $atlas->uvIndex;
    }
}
$pwsQueryStatic = '&softwaretype=' . $appInfo->name . '_v' . $config->version->app . '&action=updateraw';
$pwsQueryResult = file_get_contents($pwsQueryUrl . $pwsQuery . $pwsQueryStatic);
// Save to DB
mysqli_query($conn, "INSERT INTO `pws_updates` (`query`,`result`) VALUES ('$pwsQuery', '$pwsQueryResult')");

// Log it
syslog(LOG_NOTICE, "(EXTERNAL){PWS}: Query = $pwsQuery | Response = $pwsQueryResult");
