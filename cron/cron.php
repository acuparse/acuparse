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
 * File: cron/cron.php
 * Called by system to send data to PWS services and archive to database
 */

// Get the loader
require(dirname(__DIR__) . '/src/inc/loader.php');

// Check the event scheduler
if ($config->mysql->trim !== 0) {
    $result = mysqli_fetch_array(mysqli_query($conn, "SHOW VARIABLES WHERE VARIABLE_NAME = 'event_scheduler'"));
    $scheduler = $result['Value'];
    if ($scheduler === 'OFF') {
        if ($config->mysql->trim === 1) {
            $schema = dirname(__DIR__) . '/sql/trim/enable.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "Event Scheduler Reset");
        } elseif ($config->mysql->trim === 2) {
            // Load the database with the trim schema
            $schema = dirname(__DIR__) . '/sql/trim/enable_xtower.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "Event Scheduler Reset");
        }
    }
}

// Load weather Data:
require(APP_BASE_PATH . '/fcn/weather/GetCurrentWeatherData.php');
$get_data = new GetCurrentWeatherData();
$data = $get_data->getConditions();

// Send data to wunderground and PWS

// Set the UTC date for the update
$utc_date = gmdate("Y-m-d+H:i:s");

// Make sure new data is being sent
$result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `archive` ORDER BY `reported` DESC LIMIT 1"));
if (($result['tempF'] != $data->tempF) || ($result['windSmph'] != $data->windSmph) || ($result['windDEG'] != $data->windDEG) || ($result['relH'] != $data->relH) || ($result['pressureinHg'] != $data->pressure_inHg)) {
    // New Data, proceed

    // Save to DB
    mysqli_query($conn,
        "INSERT INTO `archive` (`tempF`, `feelsF`, `windSmph`, `windSmph_avg2m`, `windDEG`, `windDEG_avg2m`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`) VALUES ('$data->tempF', '$data->feelsF', '$data->windSmph', '$data->windSmph_avg2', '$data->windDEG', '$data->windDEG_avg2', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today')");
    if ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "Processed Archive Update");
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
            syslog(LOG_INFO, "Station Online. Email sent to admin.");

            // Update the time the email was sent
            $last_sent = date("Y-m-d H:i:s");
            mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$last_sent', `status` = '1'");

        } else {
            // Log it
            syslog(LOG_INFO, "ONLINE: Receiving updates from Access/smartHUB. Email notifications not enabled.");
            // Update the status
            mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '1'");
        }
    }

    // Build PWS Update
    if ($config->upload->pws->enabled === true) {
        $pws_query_url = $config->upload->pws->url . '?ID=' . $config->upload->pws->id . '&PASSWORD=' . $config->upload->pws->password;
        $pws_query = '&dateutc=' . $utc_date . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&windspeedmph=' . $data->windSmph . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
        $pws_query_static = '&softwaretype=other&action=updateraw';
        $pws_query_result = file_get_contents($pws_query_url . $pws_query . $pws_query_static);
        // Save to DB
        mysqli_query($conn, "INSERT INTO `pws_updates` (`query`,`result`) VALUES ('$pws_query', '$pws_query_result')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "PWS Query: $pws_query | Result: $pws_query_result");
        }
    }

    // Build Weather Underground Update
    if ($config->upload->wu->enabled === true) {
        $wu_query_url = $config->upload->wu->url . '?ID=' . $config->upload->wu->id . '&PASSWORD=' . $config->upload->wu->password;
        $wu_query = '&dateutc=' . $utc_date . '&tempf=' . $data->tempF . '&winddir=' . $data->windDEG . '&winddir_avg2m=' . $data->windDEG_avg2 . '&windspeedmph=' . $data->windSmph . '&windspdmph_avg2m=' . $data->windSmph_avg2 . '&baromin=' . $data->pressure_inHg . '&humidity=' . $data->relH . '&dewptf=' . $data->dewptF . '&rainin=' . $data->rainIN . '&dailyrainin=' . $data->rainTotalIN_today;
        $wu_query_static = '&softwaretype=other&action=updateraw';
        $wu_query_result = file_get_contents($wu_query_url . $wu_query . $wu_query_static);
        // Save to DB
        mysqli_query($conn, "INSERT INTO `wu_updates` (`query`,`result`) VALUES ('$wu_query', '$wu_query_result')");
        if ($config->debug->logging === true) {
            // Log it
            syslog(LOG_DEBUG, "WU Query: $wu_query | Result: $wu_query_result");
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
            $cwop_date = gmdate("dHi", time());
            $relH = $data->relH;
            if ($relH == 100) {
                $relH = '00';
            }
            $cwop_query = $config->upload->cwop->id . '>APRS,TCPIP*:@' . $cwop_date . 'z' . $config->upload->cwop->location . '_';
            $cwop_query = $cwop_query . sprintf('%03d/%03dg%03dt%03dr%03dP%03dh%02db%05d', $data->windDEG,
                    $data->windSmph, $data->windSmph_max5, $data->tempF, $data->rainIN * 100,
                    $data->rainTotalIN_today * 100, $relH, $data->pressure_kPa * 100);
            $cwop_socket = fsockopen($config->upload->cwop->url, 14580, $cwop_socket_errno, $cwop_socket_errstr, 30);
            if (!$cwop_socket) {
                if ($config->debug->logging === true) {
                    // Log it
                    syslog(LOG_DEBUG, "CWOP Query: $cwop_socket_errno ($cwop_socket_errstr)");
                }
            } else {
                $cwop_out = 'user ' . $config->upload->cwop->id . ' pass -1 vers ' . $app_info->name . "\r" . $cwop_query . '.' . $config->site->hostname . "\r";
                fwrite($cwop_socket, $cwop_out);
                fclose($cwop_socket);
            }

            // Save to DB
            mysqli_query($conn, "INSERT INTO `cwop_updates` (`query`) VALUES ('$cwop_query')");
            // Log
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG, "CWOP Query: $cwop_query");
            }
        } // No new update to send
        else {
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_DEBUG, "CWOP update not sent. Not enough time has passed");
            }
        }
    }
} // Nothing has changed
else {
    $last_update = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `timestamp` FROM `last_update`"));
    // Check to see if the station is down
    if ((strtotime($last_update['timestamp']) < strtotime("-" . $config->outage_alert->offline_for))) {
        $outage_alert = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `last_sent`, `status` FROM `outage_alert`"));

        // Should a notification be sent?
        if (strtotime($outage_alert['last_sent']) < strtotime("-" . $config->outage_alert->interval)) {

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
                syslog(LOG_ERR, "OFFLINE: not receiving data from the Access/smartHUB. Email sent to admin.");
                // Update the time the email was sent
                $last_sent = date("Y-m-d H:i:s");
                mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$last_sent', `status` = '0'");

            } else {
                // Log it
                syslog(LOG_ERR, "OFFLINE: not receiving data from the Access/smartHUB.");
                // Update the status
                mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '0'");
            }
        } else {
            // Log it
            syslog(LOG_ERR, "OFFLINE: Too soon to send another notification.");
        }
    } // Not offline long enough,
    elseif ($config->debug->logging === true) {
        // Log it
        syslog(LOG_DEBUG, "No update to send. There is no new data to send or station is offline.");
    }
}
