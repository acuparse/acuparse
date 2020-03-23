<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2020 Maxwell Power
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
 * File: cron/cron.php
 * Called by system to send data to PWS services and archive to database
 */

// Get the loader
require(dirname(__DIR__) . '/src/inc/loader.php');

// Load weather Data:
require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
$get_data = new getCurrentWeatherData();
$data = $get_data->getConditions();

// Load Atlas Data:
if ($config->station->primary_sensor === 0) {
    // Load weather Data:
    require('getCurrentAtlasData.php');
    $getAtlasData = new getCurrentAtlasData();
    $atlas = $getAtlasData->getData();
}

// If using tower data for archiving, set it now
if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === true) {
    $sensor = $config->upload->sensor->id;
    $result = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
    $data->tempF = round($result['tempF'], 1);
    $data->tempC = round(($result['tempF'] - 32) * 5 / 9, 1);
    $data->relH = $result['relH'];
    $data->dewptC = ((pow(($data->relH / 100), 0.125)) * (112 + 0.9 * $data->tempC) + (0.1 * $data->tempC) - 112);
    $data->dewptF = ($data->dewptC * 9 / 5) + 32;
}

// Set the UTC date for the update
$utcDate = gmdate("Y-m-d+H:i:s");

// Make sure new data is being sent
$result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `archive` ORDER BY `reported` DESC LIMIT 1"));
if (($result['tempF'] != $data->tempF) || ($result['windSmph'] != $data->windSmph) || ($result['windDEG'] != $data->windDEG) || ($result['relH'] != $data->relH) || ($result['pressureinHg'] != $data->pressure_inHg)) {
    // New Data, proceed

    // Save to DB
    mysqli_query($conn,
        "INSERT INTO `archive` (`tempF`, `feelsF`, `windSmph`, `windSmph_avg2m`, `windDEG`, `windDEG_avg2m`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`) VALUES ('$data->tempF', '$data->feelsF', '$data->windSmph', '$data->windSmph_avg2', '$data->windDEG', '$data->windDEG_avg2', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today')");
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Processed Archive Update");
    }

    // Check if this is the first update after an outage
    $status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `status` FROM `outage_alert`"));
    if ($status['status'] === '0') {

        require(APP_BASE_PATH . '/fcn/mailer.php');
        $subject = 'Access/smartHUB ONLINE';
        $message = '<p><strong>Acuparse is receiving and processing updates from your Access/smartHUB.</strong>';

        $sql = mysqli_query($conn, "SELECT `email` FROM `users` WHERE `admin` = '1'");
        while ($row = mysqli_fetch_array($sql)) {
            $admin_email[] = $row['email'];
        }
        if ($config->outage_alert->enabled === true) {
            // Mail it
            foreach ($admin_email as $to) {
                mailer($to, $subject, $message);
            }
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Station Online. Email sent to admin.");

            // Update the time the email was sent
            $lastSent = date("Y-m-d H:i:s");
            mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$lastSent', `status` = '1'");

        } else {
            // Log it
            syslog(LOG_INFO,
                "(SYSTEM)[INFO]: ONLINE: Receiving updates from Access/smartHUB. Email notifications not enabled.");
            // Update the status
            mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '1'");
        }
    }

    // Using Tower Data
    if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === false) {
        $sensor = $config->upload->sensor->id;
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
        $data->tempF = round($result['tempF'], 1);
        $data->tempC = round(($result['tempF'] - 32) * 5 / 9, 1);
        $data->relH = $result['relH'];
        $data->dewptC = ((pow(($data->relH / 100), 0.125)) * (112 + 0.9 * $data->tempC) + (0.1 * $data->tempC) - 112);
        $data->dewptF = ($data->dewptC * 9 / 5) + 32;
    }

    // Build PWS Update
    if ($config->upload->pws->enabled === true) {
        $pwsQueryUrl = $config->upload->pws->url . '?ID=' . $config->upload->pws->id . '&PASSWORD=' . $config->upload->pws->password;
        if ($config->station->primary_sensor === 0) {
            $pwsQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSmph . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today . '&uv=' . $atlas->uvindex;
        } else {
            $pwsQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSmph . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
        }
        $pwsQueryStatic = '&softwaretype=' . ucfirst($appInfo->name) . '&action=updateraw';
        $pwsQueryResult = file_get_contents($pwsQueryUrl . $pwsQuery . $pwsQueryStatic);
        // Save to DB
        mysqli_query($conn, "INSERT INTO `pws_updates` (`query`,`result`) VALUES ('$pwsQuery', '$pwsQueryResult')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(EXTERNAL)[PWS]: Query = $pwsQuery | Result = $pwsQueryResult");
        }
    }

    // Build Weather Underground Update
    if ($config->upload->wu->enabled === true) {
        $wuQueryUrl = $config->upload->wu->url . '?ID=' . $config->upload->wu->id . '&PASSWORD=' . $config->upload->wu->password;
        if ($config->station->primary_sensor === 0) {
            $wuQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&winddir_avg2m=' . $data->windDEG_avg2 . '&windspeedmph=' . $data->windSmph . '&windspdmph_avg2m=' . $data->windSmph_avg2 . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today . '&UV=' . $atlas->uvindex;
        } else {
            $wuQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&winddir_avg2m=' . $data->windDEG_avg2 . '&windspeedmph=' . $data->windSmph . '&windspdmph_avg2m=' . $data->windSmph_avg2 . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
        }
        $wuQueryStatic = '&softwaretype=' . ucfirst($appInfo->name) . '&action=updateraw';
        $wuQueryResult = file_get_contents(htmlentities($wuQueryUrl . $wuQuery . $wuQueryStatic));
        // Save to DB
        mysqli_query($conn, "INSERT INTO `wu_updates` (`query`,`result`) VALUES ('$wuQuery', '$wuQueryResult')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(EXTERNAL)[WU]: Query = $wuQuery | Result = $wuQueryResult");
        }
    }

    // Build CWOP Update
    if ($config->upload->cwop->enabled === true) {
        $sql = "SELECT `timestamp` FROM `cwop_updates` ORDER BY `timestamp` DESC LIMIT 1";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        $count = mysqli_num_rows(mysqli_query($conn, $sql));

        // Make sure update interval has passed since last update
        if ((strtotime($result['timestamp']) < strtotime("-" . $config->upload->cwop->interval)) OR ($count == 0)) {
            // Process and send update
            $cwopDate = gmdate("dHi", time());
            $relH = $data->relH;
            if ($relH == 100) {
                $relH = '00';
            }
            $cwopQuery = $config->upload->cwop->id . '>APRS,TCPIP*:@' . $cwopDate . 'z' . $config->upload->cwop->location . '_';
            $cwopQuery = $cwopQuery . sprintf('%03d/%03dg%03dt%03dr%03dP%03dh%02db%05d', $data->windDEG,
                    $data->windSmph, $data->windSmph_max5, $data->tempF, $data->rainIN * 100,
                    $data->rainTotalIN_today * 100, $relH, $data->pressure_kPa * 100);
            $cwopSocket = fsockopen($config->upload->cwop->url, 14580, $cwopSocket_errno, $cwopSocket_errstr, 30);
            if (!$cwopSocket) {
                if ($config->debug->logging === true) {
                    // Log it
                    syslog(LOG_DEBUG, "(EXTERNAL)[CWOP] Socket Error: $cwopSocket_errno ($cwopSocket_errstr)");
                }
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
                syslog(LOG_DEBUG, "(EXTERNAL)[CWOP]: Query = $cwopQuery");
            }
        } // No new update to send
        else {
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG, "(EXTERNAL)[CWOP]: Update not sent. Not enough time has passed");
            }
        }
    }

    // Build Weathercloud Update
    if ($config->upload->wc->enabled === true) {
        $sql = "SELECT `timestamp` FROM `wc_updates` ORDER BY `timestamp` DESC LIMIT 1";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        $count = mysqli_num_rows(mysqli_query($conn, $sql));

        // Make sure update interval has passed since last update
        if ((strtotime($result['timestamp']) < strtotime('-10 minutes')) OR ($count == 0)) {
            $wcQueryUrl = $config->upload->wc->url . '?wid=' . $config->upload->wc->id . '&key=' . $config->upload->wc->key;
            if ($config->station->primary_sensor === 0) {
                $wcQuery = '&temp=' . ($data->tempC * 10) . '&wdir=' . $data->windDEG . '&wdiravg=' . $data->windDEG_avg10 . '&wspd=' . (($data->windSkmh * 0.277778) * 10) . '&wspdavg=' . (($data->windSmph_avg10 * 0.44704) * 10) . '&bar=' . ($data->pressure_kPa * 100) . '&hum=' . $data->relH . '&dew=' . ($data->dewptC * 10) . '&rainrate=' . ($data->rainMM * 10) . '&rain=' . ($data->rainTotalMM_today * 10) . '&uvi=' . $atlas->uvindex;
            } else {
                $wcQuery = '&temp=' . ($data->tempC * 10) . '&wdir=' . $data->windDEG . '&wdiravg=' . $data->windDEG_avg10 . '&wspd=' . (($data->windSkmh * 0.277778) * 10) . '&wspdavg=' . (($data->windSmph_avg10 * 0.44704) * 10) . '&bar=' . ($data->pressure_kPa * 100) . '&hum=' . $data->relH . '&dew=' . ($data->dewptC * 10) . '&rainrate=' . ($data->rainMM * 10) . '&rain=' . ($data->rainTotalMM_today * 10);
            }
            $wcQueryStatic = '&type=555e1df0d6eb' . '&version=' . $config->version->app;
            $wcQueryResult = file_get_contents($wcQueryUrl . $wcQuery . $wcQueryStatic);
            // Save to DB
            mysqli_query($conn, "INSERT INTO `wc_updates` (`query`,`result`) VALUES ('$wcQuery', '$wcQueryResult')");
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG, "(EXTERNAL)[WC]: Query = $wcQuery | Result = $wcQueryResult");
            }
        } // No new update to send
        else {
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG, "(EXTERNAL)[WC]: Update not sent. Not enough time has passed");
            }
        }
    }

// Build Windy Update
    if ($config->upload->windy->enabled === true) {
        $windyQueryUrl = $config->upload->windy->url . '/' . $config->upload->windy->key;
        if ($config->station->primary_sensor === 0) {
            $windyQuery = '?tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSmph . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&uv=' . $atlas->uvindex;
        } else {
            $windyQuery = '?tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSmph . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN;
        }
        $windyQueryResult = file_get_contents($windyQueryUrl . $windyQuery);
        // Save to DB
        mysqli_query($conn,
            "INSERT INTO `windy_updates` (`query`,`result`) VALUES ('$windyQuery', '$windyQueryResult')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(EXTERNAL)[Windy]: Query = $windyQuery | Result = $windyQueryResult");
        }
    }

// Build Generic WU Based Update
    if ($config->upload->generic->enabled === true) {
        $genericQueryUrl = $config->upload->generic->url . '?ID=' . $config->upload->generic->id . '&PASSWORD=' . $config->upload->generic->password;
        if ($config->station->primary_sensor === 0) {
            $genericQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&winddir_avg2m=' . $data->windDEG_avg2 . '&windspeedmph=' . $data->windSmph . '&windspdmph_avg2m=' . $data->windSmph_avg2 . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today . '&uv=' . $atlas->uvindex;
        } else {
            $genericQuery = '&dateutc=' . $utcDate . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&winddir_avg2m=' . $data->windDEG_avg2 . '&windspeedmph=' . $data->windSmph . '&windspdmph_avg2m=' . $data->windSmph_avg2 . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
        }
        $genericQueryStatic = '&softwaretype=' . ucfirst($appInfo->name) . '&action=updateraw';
        $genericQueryResult = file_get_contents(htmlentities($genericQueryUrl . $genericQuery . $genericQueryStatic));
        // Save to DB
        mysqli_query($conn,
            "INSERT INTO `generic_updates` (`query`,`result`) VALUES ('$genericQuery', '$genericQueryResult')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "(EXTERNAL)[GENERIC]: Query = $genericQuery | Result = $genericQueryResult");
        }
    }
} // Nothing has changed
else {
    $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `timestamp` FROM `last_update`"));
    // Check to see if the station is down
    if ((strtotime($lastUpdate['timestamp']) < strtotime("-" . $config->outage_alert->offline_for))) {
        $outageAlert = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `last_sent`, `status` FROM `outage_alert`"));

        // Should a notification be sent?
        if (strtotime($outageAlert['last_sent']) < strtotime("-" . $config->outage_alert->interval)) {

            if ($config->outage_alert->enabled === true) {
                require(APP_BASE_PATH . '/fcn/mailer.php');
                $subject = 'Access/smartHUB offline! No Updates received.';
                $message = '<p><strong>Acuparse is not receiving updates from your Access/smartHUB.</strong><p>Check your internet connection.</p>';

                $sql = mysqli_query($conn, "SELECT `email` FROM `users` WHERE `admin` = '1'");
                while ($row = mysqli_fetch_array($sql)) {
                    $admin_email[] = $row['email'];
                }

                // Mail it
                foreach ($admin_email as $to) {
                    mailer($to, $subject, $message);
                }
                // Log it
                syslog(LOG_ERR,
                    "(SYSTEM)[ERROR]: OFFLINE: not receiving data from the Access/smartHUB. Email sent to admin.");
                // Update the time the email was sent
                $lastSent = date("Y-m-d H:i:s");
                mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$lastSent', `status` = '0'");

            } else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: OFFLINE: not receiving data from the Access/smartHUB.");
                // Update the status
                mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '0'");
            }
        } else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: OFFLINE: Too soon to send another notification.");
        }
    } // Not offline long enough,
    else {
        // Log it
        syslog(LOG_INFO, "(SYSTEM)[INFO]: No update to send. There is no new data to send or station is offline.");
    }
}

// Check the event scheduler
if ($config->mysql->trim !== 0) {
    $result = mysqli_fetch_array(mysqli_query($conn, "SHOW VARIABLES WHERE VARIABLE_NAME = 'event_scheduler'"));
    $scheduler = $result['Value'];
    if ($scheduler === 'OFF') {
        if ($config->mysql->trim === 1) {
            $schema = dirname(__DIR__) . '/sql/trim/enable.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Event Scheduler Reset");
        } elseif ($config->mysql->trim === 2) {
            // Load the database with the trim schema
            $schema = dirname(__DIR__) . '/sql/trim/enable_xtower.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Event Scheduler Reset");
        }
    }
}
