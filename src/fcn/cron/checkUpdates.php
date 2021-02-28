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
 * File: src/fcn/cron/checkUpdates.php
 * System Update Checker
 */

require(dirname(dirname(__DIR__)) . '/inc/loader.php');

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/**
 * @return array
 * @var object $appInfo Global Application Info
 */

function getTelemetry(): array
{
    if (shell_exec('hostnamectl')) {
        @$input = shell_exec('hostnamectl');
        preg_match_all('/[^\s].*:\s.*/', $input, $input);
        $input = str_replace(': ', '=', $input[0]);
        $output = array();
        for ($i = 0; $i < count($input); $i++) {
            $value = explode('=', $input [$i]);
            $value[0] = strtolower($value[0]);
            $value[0] = str_replace(' ', '_', $value[0]);
            $value[1] = htmlentities($value[1]);
            $output[$value[0]] = $value[1];
        }
        unset($output['static_hostname'], $output['icon_name'], $output['machine_id'], $output['boot_id']);
    } else {
        $releaseInput = shell_exec('cat /etc/os-release');
        preg_match_all('/.*=.*/', $releaseInput, $releaseInput);
        $releaseInput = str_replace('"', '', $releaseInput[0]);
        $releaseOutput = array();
        for ($i = 0; $i < count($releaseInput); $i++) {
            $value = explode('=', $releaseInput[$i]);
            $value[1] = htmlentities($value[1]);
            $releaseOutput[$value[0]] = $value[1];
        }
        $kernel = shell_exec('uname -r');
        $kernel = str_replace(' ', '', $kernel);
        $kernel = str_replace("\n", '', $kernel);
        $arch = shell_exec('uname -m');
        $arch = str_replace('_', '-', $arch);
        $arch = str_replace(' ', '', $arch);
        $arch = str_replace("\n", '', $arch);
        $output = array('os' => $releaseOutput["PRETTY_NAME"]);
        $output['kernel'] = $kernel;
        $output['architecture'] = $arch;
        if (isset($releaseOutput["DOCKER"])) {
            $output['chassis'] = 'container';
            $output['virtualization'] = 'docker';
        }
    }
    return $output;
}

$result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name` = 'lastUpdateCheck'"));
if ($result) {
    syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: Checking for updates ...");
    // Make sure update interval has passed since last update
    if ((strtotime($result['value']) < strtotime("-" . '23 hours 20 min')) || isset($updateComplete)) {
        $telemetry = getTelemetry();
        $telemetry['clientID'] = $config->version->installHash;
        $telemetry['version'] = $config->version->app;
        if ($config->station->access_mac != null) {
            $telemetry['mac'] = $config->station->access_mac;
        } elseif ($config->station->hub_mac != null) {
            $telemetry['mac'] = $config->station->hub_mac;
        } else {
            $telemetry['mac'] = 'none';
        }
        if ($telemetry['mac'] !== 'none') {
            $telemetry = json_encode($telemetry);
            syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: Telemetry = $telemetry");

            $ch = curl_init($appInfo->release_server . '/current');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $telemetry);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $checkLatestVersion = curl_exec($ch);
            curl_close($ch);
            syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: Response = $checkLatestVersion");

            if ($checkLatestVersion) {
                $result = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT `value` FROM `system` WHERE `name`='latestRelease'"));
                $lastLatestRelease = $result['value'];
                $checkLatestVersion = json_decode($checkLatestVersion);
                $latestRelease = $checkLatestVersion->latestRelease;
                $lastCheckedOn = date("Y-m-d H:i:s");
                mysqli_query($conn, "UPDATE `system` SET `value` = '$lastCheckedOn' WHERE `name` = 'lastUpdateCheck'");
                if ($lastLatestRelease != $latestRelease) {
                    mysqli_query($conn, "UPDATE `system` SET `value` = '$latestRelease' WHERE `name` = 'latestRelease'");
                    syslog(LOG_INFO, "(SYSTEM){UPDATER}: New Version $latestRelease now available");
                } else if ($config->version->app != $latestRelease) {
                    syslog(LOG_INFO, "(SYSTEM){UPDATER}: Version $latestRelease available");
                } else {
                    syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: No new version available");
                }
            } else {
                syslog(LOG_ERR, "(SYSTEM){UPDATER}: Error checking for update");
            }
        } else {
            syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: No Device MAC Found");
        }
    } else {
        syslog(LOG_DEBUG, "(SYSTEM){UPDATER}: Too soon to check for updates");
    }
}
