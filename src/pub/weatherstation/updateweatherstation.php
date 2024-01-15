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
 * File: src/pub/weatherstation/updateweatherstation.php
 * Commandeers data from the Access/smartHUB
 */

// Get the loader
require(dirname(__DIR__, 2) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

function last_updated_at()
{
    global $conn;
    $lastUpdate = date("Y-m-d H:i:s");
    mysqli_query($conn, "UPDATE `last_update` SET `timestamp` = '$lastUpdate';");
}

ini_set('default_socket_timeout', 1);

if ((isset($_GET['id']))) {

    $mac = filter_var($_GET['id'], FILTER_SANITIZE_STRING);

    if (empty($config->station->access_mac) && empty($config->station->hub_mac)) {
        exit(syslog(LOG_EMERG, "(SYSTEM)[ERROR]: Device $mac is not configured"));
    }

    if ($mac === $config->station->access_mac || $mac === $config->station->hub_mac) {
        // Process Access Update
        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
            if (isset($_GET['softwaretype']) && $_GET['softwaretype'] === 'rtl_433') {
                if ($config->station->realtime !== true) {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'Realtime is not enabled'));
                    exit(syslog(LOG_ALERT, "(SYSTEM)[ERROR]: Realtime is not enabled"));
                } else {
                    $relayQuery = str_replace('/weatherstation/updateweatherstation?', '', $_SERVER['REQUEST_URI']);
                    require(dirname(__DIR__, 2) . '/fcn/weatherstation/rtl.php');
                }
            } else {
                $myacuriteQuery = str_replace('/weatherstation/updateweatherstation?&', '', $_SERVER['REQUEST_URI']);
                require(dirname(__DIR__, 2) . '/fcn/weatherstation/access.php');
            }
        } // Process smartHUB Update
        elseif (($_SERVER['REQUEST_METHOD'] === 'GET')) {
            $myacuriteQuery = str_replace('/weatherstation/updateweatherstation?', '', $_SERVER['REQUEST_URI']);
            require(dirname(__DIR__, 2) . '/fcn/weatherstation/hub.php');
        } else {
            header_remove();
            header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
            echo json_encode(array('status' => 'error', 'message' => "Device $mac is not configured"));
            exit(syslog(LOG_EMERG, "(SYSTEM)[ERROR]: Device $mac is not configured"));
        }
    } else {
        header_remove();
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
        echo json_encode(array('status' => 'error', 'message' => "Device $mac is not configured"));
        if ($_GET['softwaretype'] === 'rtl_433') {
            exit(syslog(LOG_ALERT, "(SYSTEM){RTL}[ERROR]: Invalid PRIMARY_MAC_ADDRESS $mac"));
        } else {
            exit(syslog(LOG_EMERG, "(SYSTEM)[ERROR]: Invalid Device $mac"));
        }
    }
} else {
    header_remove();
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
    echo json_encode(array('status' => 'error', 'message' => 'No MAC Address Provided'));
    exit(syslog(LOG_EMERG, "(SYSTEM)[ERROR]: No MAC Address Provided"));
}