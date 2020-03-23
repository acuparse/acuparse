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
 * File: src/pub/weatherstation/updateweatherstation.php
 * Commandeers data from the Access/smartHUB
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

function last_updated_at() {
    global $conn;
    $lastUpdate = date("Y-m-d H:i:s");
    mysqli_query($conn, "UPDATE `last_update` SET `timestamp` = '$lastUpdate';");
}

// Process Access Update
if (($_SERVER['REQUEST_METHOD'] === 'POST') && $_GET['id'] === $config->station->access_mac) {
    $myacuriteQuery = str_replace('/weatherstation/updateweatherstation?&', '', $_SERVER['REQUEST_URI']);
    require(dirname(dirname(__DIR__)) . '/fcn/weatherstation/access.php');
} // Process smartHUB Update
elseif (($_SERVER['REQUEST_METHOD'] === 'GET') && $_GET['id'] === $config->station->hub_mac) {
    $myacuriteQuery = str_replace('/weatherstation/updateweatherstation?', '', $_SERVER['REQUEST_URI']);
    require(dirname(dirname(__DIR__)) . '/fcn/weatherstation/hub.php');
} // This MAC is not configured
else {
    $mac = $_GET['id'];
    // Log it
    syslog(LOG_ERR, "(SYSTEM)[ERROR]: MAC $mac is not configured.");
}
