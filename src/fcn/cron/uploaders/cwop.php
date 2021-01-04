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
 * File: src/fcn/cron/cwop.php
 * CWOP Updater
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/**
 * @return array
 * @return array
 * @return array
 * @var object $atlas Atlas Data
 * @var object $data Weather Data
 * @var object $appInfo Global Application Info
 */

$sql = "SELECT `timestamp` FROM `cwop_updates` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
$count = mysqli_num_rows(mysqli_query($conn, $sql));

// Make sure update interval has passed since last update
if ((strtotime($result['timestamp']) < strtotime("-" . $config->upload->cwop->interval)) or ($count == 0)) {
    // Process and send update
    $cwopDate = gmdate("dHi", time());

    $relH = $data->relH;
    if ($relH == 100) {
        $relH = '00';
    }

    if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
        $cwop_windGustMPH = $atlas->windGustMPH;
    } elseif ($config->station->device === 1 && $config->station->primary_sensor === 1) {
        // Process Average Wind Speed over the last 5 minutes
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT MAX(speedMPH) AS `max_speedMPH` FROM `windspeed` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)"));
        $cwop_windGustMPH = (int)round($result['max_speedMPH']); // Miles per hour
    } else {
        $cwop_windGustMPH = '...';
    }

    $cwopQuery = $config->upload->cwop->id . '>APRS,TCPIP*:@' . $cwopDate . 'z' . $config->upload->cwop->location . '_';
    $cwopQuery = $cwopQuery . sprintf('%03d/%03dg%03dt%03dr%03dP%03dh%02db%05d', $data->windDEG,
            $data->windSpeedMPH, $cwop_windGustMPH, $data->tempF, $data->rainIN * 100,
            $data->rainTotalIN_today * 100, $relH, $data->pressure_kPa * 100);
    $cwopSocket = fsockopen($config->upload->cwop->url, 14580, $cwopSocket_errno, $cwopSocket_errstr, 30);
    if (!$cwopSocket) {
        // Log it
        syslog(LOG_ERR, "(EXTERNAL){CWOP}[ERROR]: $cwopSocket_errno ($cwopSocket_errstr)");

    } else {
        $cwop_out = 'user ' . $config->upload->cwop->id . ' pass -1 vers ' . $appInfo->name . "\r" . $cwopQuery . '.' . ucfirst($appInfo->name) . "\r";
        fwrite($cwopSocket, $cwop_out);
        fclose($cwopSocket);
    }

    // Save to DB
    mysqli_query($conn, "INSERT INTO `cwop_updates` (`query`) VALUES ('$cwopQuery')");
    // Log
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "(EXTERNAL){CWOP}: Query = $cwopQuery");
    }
} // No new update to send
else {
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "(EXTERNAL){CWOP}: Update not sent. Not enough time has passed");
    }
}
