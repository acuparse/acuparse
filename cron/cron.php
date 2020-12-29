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
 * File: cron/cron.php
 * Called by system to send data to external weather services, archive to database, and check for updates
 */

// Get the loader
require(dirname(__DIR__) . '/src/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 * @var mysqli $conn Global MYSQL Connection
 * @var string $installed
 */

syslog(LOG_DEBUG, "(SYSTEM){CRON}: Running System Tasks");

if ($installed === true) {
    // Not Configured
    if (empty($config->station->access_mac) && empty($config->station->hub_mac)) {
        syslog(LOG_ERR, "(SYSTEM){CRON}[ERROR]: No Device MAC Configured");
    } // Run Tasks
    else {
        // Load weather Data
        if (!class_exists('getCurrentWeatherData')) {
            require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
            $get_data = new getCurrentWeatherData();
            $data = $get_data->getConditions();
        }
        /* @var object $data */

        // Load Atlas Data
        if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
            if (!class_exists('getCurrentAtlasData')) {
                require(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
                $getAtlasData = new getCurrentAtlasData();
                $atlas = $getAtlasData->getData();
            }

            if ($config->station->lightning_source === 1 || $config->station->lightning_source === 3) {
                // Load Lightning Data
                if (!class_exists('atlas\getCurrentLightningData')) {
                    require(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                    $getLightningData = new atlas\getCurrentLightningData;
                    $lightning = $getLightningData->getData();
                }
            }
        }

        /* @var object $atlas */
        /* @var object $lightning */

        // If using tower data for archiving, set it now
        if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === true) {
            // Load Tower Lightning
            if (($config->station->device === 0 && ($config->station->primary_sensor === 0 || $config->station->primary_sensor === 1)) && ($config->station->lightning_source === 2 || $config->station->lightning_source === 3)) {
                if (!class_exists('tower\getCurrentLightningData')) {
                    require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                    $getLightningData = new tower\getCurrentLightningData;
                    $lightning = $getLightningData->getData();
                }
            }
            // Load Tower Data
            require(APP_BASE_PATH . '/fcn/cron/towerData.php');
            /* @var string $sensor */
            syslog(LOG_DEBUG, "(SYSTEM){CRON}: Using Tower $sensor for Archive & Uploads");
        }

        // Set the UTC date for the update
        $utcDate = gmdate("Y-m-d+H:i:s");

        // Make sure new data is being sent
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `archive` ORDER BY `reported` DESC LIMIT 1"));
        if (($result['tempF'] != $data->tempF) || ($result['windSpeedMPH'] != $data->windSpeedMPH) || ($result['windDEG'] != $data->windDEG) || ($result['relH'] != $data->relH) || ($result['pressureinHg'] != $data->pressure_inHg) && (($data->pressure_inHg != 0) && ($data->tempF != 0))) {
            // New Data, proceed

            if ($config->station->device === 0 && $config->station->primary_sensor === 0 && $config->station->lightning_source !== 0) {
                $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`, `uvindex`, `light`, `lightSeconds`, `lightning`) VALUES ('$data->tempF', '$data->feelsF', '$data->windSpeedMPH', '$atlas->windAvgMPH', '$atlas->windGustMPH', '$data->windDEG', '$atlas->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today', '$atlas->uvIndex', '$atlas->lightIntensity', '$atlas->lightSeconds', '$lightning->dailystrikes')";
            } elseif ($config->station->device === 0 && $config->station->primary_sensor === 0 && $config->station->lightning_source === 0) {
                $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windSpeedMPH_avg`, `windGustMPH`, `windDEG`, `windGustDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`, `uvindex`, `light`, `lightSeconds`) VALUES ('$data->tempF', '$data->feelsF', '$data->windSpeedMPH', '$atlas->windAvgMPH', '$atlas->windGustMPH', '$data->windDEG', '$atlas->windGustDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today', '$atlas->uvIndex', '$atlas->lightIntensity', '$atlas->lightSeconds')";
            } else {
                $archiveQuery = "INSERT INTO `archive` (`tempF`, `feelsF`, `windSpeedMPH`, `windDEG`, `relH`, `pressureinHg`, `dewptF`, `rainin`,`total_rainin`) VALUES ('$data->tempF', '$data->feelsF', '$data->windSpeedMPH','$data->windDEG', '$data->relH', '$data->pressure_inHg', '$data->dewptF', '$data->rainIN', '$data->rainTotalIN_today')";
            }

            // Save to DB
            mysqli_query($conn, $archiveQuery) or syslog(LOG_ERR, "(SYSTEM){CRON}[ERROR]: Failed to Update Archive (" . mysqli_error($conn) . ")");
            if ($config->debug->logging === true) {
                // Log it
                syslog(LOG_INFO, "(SYSTEM){CRON}: Archive Updated");
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
                    foreach ($admin_email as $to) {
                        mailer($to, $subject, $message);
                    }
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM){CRON}: *ONLINE* Now Receiving Updates (Notification Sent))");

                    // Update the time the email was sent
                    $lastSent = date("Y-m-d H:i:s");
                    mysqli_query($conn, "UPDATE `outage_alert` SET `last_sent` = '$lastSent', `status` = '1'");

                } else {
                    // Log it
                    syslog(LOG_INFO,
                        "(SYSTEM){CRON}: *ONLINE* Now Receiving Updates (Notifications Disabled)");
                    // Update the status
                    mysqli_query($conn, "UPDATE `outage_alert` SET `status` = '1'");
                }
            }

            // Using Tower Data
            if ($config->upload->sensor->external === 'tower' && $config->upload->sensor->archive === false) {
                require(APP_BASE_PATH . '/fcn/cron/towerData.php');
                /* @var string $sensor */
                syslog(LOG_DEBUG, "(SYSTEM){CRON}: Using Tower $sensor for Uploads");
            }

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

        } // Nothing has changed
        else {
            require(APP_BASE_PATH . '/fcn/cron/noChange.php');
        }

    }
} else {
    exit(syslog(LOG_WARNING, "(SYSTEM){CRON}[WARNING]: Not Installed. Run installer"));
}

// Check the event scheduler
require(APP_BASE_PATH . '/fcn/trim.php');

// Check for updates
if ($config->site->updates === true) {
    require(APP_BASE_PATH . '/fcn/cron/checkUpdates.php');
}

syslog(LOG_DEBUG, "(SYSTEM){CRON}: DONE Running System Tasks");
