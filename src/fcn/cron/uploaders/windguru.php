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
 * File: src/fcn/cron/uploaders/windguru.php
 * Wind Guru Updater
 */

/**
 * @var object $config Global Config
 * @var mysqli $conn Global MYSQL Connection
 * @var object $data Weather Data
 * @var object $atlas Atlas Data
 */

if ($config->debug->logging === true) {
    syslog(LOG_DEBUG, "(EXTERNAL){CWOP}: Starting Update ...");
}

// Build and send update
$windguruSalt = date('YmdHis');
$windguruHash = md5($windguruSalt . $config->upload->windguru->uid . $config->upload->windguru->password);
$windguruQueryUrl = $config->upload->windguru->url . '?uid=' . $config->upload->windguru->uid . '&salt=' . $windguruSalt . '&hash=' . $windguruHash;
$windguruQuery = '&temperature=' . $data->tempC . '&wind_direction=' . $data->windDEG . '&wind_avg=' . round($data->windSpeedMPH / 1.15078, 1) . '&windspdmph_avg2m=' . $data->windAvgMPH / 1.15078 . '&mslp=' . round($data->pressure_kPa * 10, 1) . '&rh=' . $data->relH . '&precip=' . $data->rainMM;
if ($config->station->device === 0) {
    $windguruQuery = $windguruQuery . '&wind_max=' . $data->windGustMPH / 1.15078;
}
$windguruQueryResult = file_get_contents($windguruQueryUrl . $windguruQuery);
// Save to DB
mysqli_query($conn,
    "INSERT INTO `windguru_updates` (`query`,`result`) VALUES ('$windguruQuery', '$windguruQueryResult')");
if ($config->debug->logging === true) {
    // Log it
    syslog(LOG_INFO, "(EXTERNAL){Windguru}: Query = $windguruQuery | Response = $windguruQueryResult");
}
