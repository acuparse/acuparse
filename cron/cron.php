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
 * File: cron/cron.php
 * Called by system to send data to external weather services, archive to database, and check for updates
 */

// Get the loader
require(dirname(__DIR__) . '/src/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 * @var mysqli $conn Global MYSQL Connection
 * @var boolean $installed
 */

syslog(LOG_NOTICE, "(SYSTEM){CRON}: Running System Tasks ...");

if ($installed === true) {
    // Not Configured
    if (empty($config->station->access_mac) && empty($config->station->hub_mac)) {
        exit(syslog(LOG_EMERG, "(SYSTEM){CRON}[ERROR]: No System MAC Address Configured!"));
    } // Run Tasks
    else {
        // Load weather Data
        if (!class_exists('getCurrentWeatherData')) {
            require_once(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
            $get_data = new getCurrentWeatherData(true);
            $data = $get_data->getCRONConditions();
        }
        /* @var object $data */

        // Load Atlas/Lightning Data
        if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
            if (!class_exists('getCurrentAtlasData')) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
                $getAtlasData = new getCurrentAtlasData(true);
                $atlas = $getAtlasData->getCRONData();
            }

            if ($config->station->lightning_source === 1 || $config->station->lightning_source === 3) {
                // Load Lightning Data
                if (!class_exists('atlas\getCurrentLightningData')) {
                    require_once(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                    $getLightningData = new atlas\getCurrentLightningData(NULL, true);
                    $lightning = $getLightningData->getCRONData();
                }
            }
        } else if ($config->station->device === 0 && $config->station->primary_sensor === 1) {
            // Load Tower Lightning
            if ($config->station->lightning_source === 2) {
                if (!class_exists('tower\getCurrentLightningData')) {
                    require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                    $getLightningData = new tower\getCurrentLightningData(NULL, true);
                    $lightning = $getLightningData->getCRONData();
                }
            }
        }

        /* @var object $atlas */
        /* @var object $lightning */

        // If using tower data for archiving, set it now
        if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === true) {
            // Load Tower Data
            require(APP_BASE_PATH . '/fcn/cron/towerData.php');
            /* @var string $sensor */
            syslog(LOG_INFO, "(SYSTEM){CRON}: Using Tower $sensor for Archive & Uploads");
            $towerDataLoaded = true;
        }

        // Set the UTC date for the update
        $utcDate = gmdate("Y-m-d+H:i:s");

        syslog(LOG_NOTICE, "(SYSTEM){CRON}: Processing Archive ...");

        if ($config->station->realtime === true) {
            $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `timestamp` FROM `last_update`"));
            $lastUpdate = $lastUpdate['timestamp'];
            if ((strtotime($lastUpdate) < strtotime('-12 minutes'))) {
                syslog(LOG_WARNING,
                    "(SYSTEM){CRON}[WARNING]: Primary device has not updated in over 12 minutes");
                goto CHECK_FOR_OUTAGE;
            }
        }

        // Make sure new data is being sent
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `archive` ORDER BY `reported` DESC LIMIT 1"));
        if ((($result['tempF'] != $data->tempF) || ($result['windSpeedMPH'] != $data->windSpeedMPH) || ($result['windDEG'] != $data->windDEG) || ($result['relH'] != $data->relH) || ($result['pressureinHg'] != $data->pressure_inHg) && (($data->pressure_inHg != 0) && ($data->tempF != 0))) || empty($result)) {
            // New Data, proceed

            if ($data->tempF !== 0 && $data->relH !== 0) {
                syslog(LOG_INFO, "(SYSTEM){CRON}: Updating Archive ...");

                // Access Update
                if ($config->station->device === 0) {
                    if ($config->station->primary_sensor === 0 && $config->station->lightning_source !== 0) {
                        $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`, `uvindex`, `light`, `lightSeconds`, `lightning`) VALUES ('$data->tempF', NULLIF('$data->feelsF', ''), '$data->windSpeedMPH', '$data->windAvgMPH', '$data->windGustMPH', '$data->windDEG', '$data->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today', '$atlas->uvIndex', '$atlas->lightIntensity', '$atlas->lightSeconds', '$lightning->dailystrikes')";
                    } elseif ($config->station->primary_sensor === 0) {
                        $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`, `uvindex`, `light`, `lightSeconds`) VALUES ('$data->tempF', NULLIF('$data->feelsF', ''), '$data->windSpeedMPH', '$data->windAvgMPH', '$data->windGustMPH', '$data->windDEG', '$data->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today', '$atlas->uvIndex', '$atlas->lightIntensity', '$atlas->lightSeconds')";
                    } elseif ($config->station->primary_sensor === 1 && $config->station->lightning_source === 2) {
                        $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`, `lightning`) VALUES ('$data->tempF', NULLIF('$data->feelsF', ''), '$data->windSpeedMPH', '$data->windAvgMPH', '$data->windGustMPH', '$data->windDEG', '$data->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today', '$lightning->dailystrikes')";
                    } else {
                        $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`) VALUES ('$data->tempF', NULLIF('$data->feelsF', ''), '$data->windSpeedMPH', '$data->windAvgMPH', '$data->windGustMPH', '$data->windDEG', '$data->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today')";
                    }
                } //Hub Update
                else {
                    $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`) VALUES ('$data->tempF', NULLIF('$data->feelsF', ''), '$data->windSpeedMPH','$data->windAvgMPH','$data->windDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today')";
                }

                // Save to DB
                $result = mysqli_query($conn, $archiveQuery);
                if (mysqli_affected_rows($conn) === 1) {
                    syslog(LOG_INFO, "(SYSTEM){CRON}: Archive Updated Successfully");
                } else {
                    syslog(LOG_CRIT, "(SYSTEM){CRON}[ERROR]: Failed to Update Archive (" . mysqli_error($conn) . ") - " . $archiveQuery);
                }
            } else {
                syslog(LOG_WARNING, "(SYSTEM){CRON}[WARNING]: Data may not be valid, skipping archive update");
            }

            // Using Tower Data
            if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === false) {
                if (!isset($towerDataLoaded)) {
                    require(APP_BASE_PATH . '/fcn/cron/towerData.php');
                    /* @var string $sensor */
                    syslog(LOG_INFO, "(SYSTEM){CRON}: Using Tower $sensor for External Uploads");
                }
            }

            // Check the readings to ensure we are not uploading 0's.
            if ($data->tempF !== 0 && $data->relH !== 0) {

                syslog(LOG_NOTICE, "(SYSTEM){CRON}: Updating External Providers ...");

                ini_set('default_socket_timeout', 1);

                // Build PWS Update
                if ($config->upload->pws->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/pwsweather.php');
                }

                // Build Weather Underground Update
                if ($config->upload->wu->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/weatherunderground.php');
                }

                // Build CWOP Update
                if ($config->upload->cwop->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/cwop.php');
                }

                // Build Weathercloud Update
                if ($config->upload->wc->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/weathercloud.php');
                }

                // Build Windy Update
                if ($config->upload->windy->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/windy.php');
                }

                // Build Windguru Update
                if ($config->upload->windguru->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/windguru.php');
                }

                // Build OpenWeather Update
                if ($config->upload->openweather->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/openweather.php');
                }

                // Build Generic WU Based Update
                if ($config->upload->generic->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/generic.php');
                }

                // Build MQTT Update
                if ($config->upload->mqtt->enabled === true) {
                    require(APP_BASE_PATH . '/fcn/cron/uploaders/mqtt.php');
                }

                syslog(LOG_NOTICE, "(SYSTEM){CRON}: DONE Updating External Providers");
            } else {
                syslog(LOG_NOTICE, "(SYSTEM){CRON}: Skipping External Providers - No Readings Detected");
            }

            // Check if this is the first update after an outage
            $status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `status` FROM `outage_alert`"));
            if ($status['status'] === '0') {
                require(APP_BASE_PATH . '/fcn/mailer.php');
                $subject = $config->site->hostname . ' ONLINE';
                $message = '<p><strong>' . $config->site->hostname . ' is receiving weather updates.</strong></p>';
                $sql = mysqli_query($conn, "SELECT `email` FROM `users` WHERE `admin` = '1'");
                while ($row = mysqli_fetch_assoc($sql)) {
                    $admin_email[] = $row['email'];
                }
                if ($config->outage_alert->enabled === true) {
                    // Mail it
                    /**
                     * @var array $admin_email Array of Admin Emails
                     */
                    foreach ($admin_email as $to) {
                        mailer($to, $subject, $message);
                    }
                    // Log it
                    syslog(LOG_ALERT, "(SYSTEM){CRON}: *ONLINE* Now Receiving Updates (Notification Sent))");

                    // Update the time the email was sent
                    $lastSent = date("Y-m-d H:i:s");
                    mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$lastSent', `status` = '1'");

                } else {
                    // Log it
                    syslog(LOG_ALERT,
                        "(SYSTEM){CRON}: *ONLINE* Now Receiving Updates (Notifications Disabled)");
                    // Update the status
                    mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '1'");
                }
            }
        } // Nothing has changed
        else {
            // Log it
            syslog(LOG_INFO, "(SYSTEM){CRON}: No Change, Skip Archiving");
            CHECK_FOR_OUTAGE:
            require(APP_BASE_PATH . '/fcn/cron/noChange.php');
        }
    }
} else {
    exit(syslog(LOG_EMERG, "(SYSTEM){CRON}[ERROR]: Not Installed. Run installer"));
}

// Check the event scheduler
require(APP_BASE_PATH . '/fcn/trim.php');

// Check for updates
if ($config->site->updates === true) {
    require(APP_BASE_PATH . '/fcn/cron/checkUpdates.php');
}

syslog(LOG_NOTICE, "(SYSTEM){CRON}: DONE Running System Tasks");
