<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/pub/api/v1/system/status.php
 * Get JSON status data
 */

// Get the loader
require(dirname(__DIR__, 3) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

$sensors = ['sensors' => []];

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {
    // Access Data
    if ($config->station->access_mac != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery`, `last_update` FROM `access_status` ORDER BY `last_update` DESC LIMIT 1"));
        $battery_access = $result['battery'];
        $lastUpdate_access = date($config->site->date_api_json, strtotime($result['last_update']));
        $access = array_push($sensors['sensors'], ['access' => ['mac' => $config->station->access_mac, 'battery' => $battery_access, 'last_update' => $lastUpdate_access]]);
    }
    // Atlas Data
    if ($config->station->sensor_atlas != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery`, `rssi`, `last_update` FROM `atlas_status` ORDER BY `last_update` DESC LIMIT 1"));
        $rssi_atlas = $result['rssi'];
        $battery_atlas = $result['battery'];
        $lastUpdate_atlas = date($config->site->date_api_json, strtotime($result['last_update']));
        $atlas = array_push($sensors['sensors'], ['atlas' => ['sensor' => $config->station->sensor_atlas, 'battery' => $battery_atlas, 'rssi' => $rssi_atlas, 'last_update' => $lastUpdate_atlas]]);
    }
    // Iris Data
    if ($config->station->sensor_iris != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery`, `rssi`, `last_update` FROM `iris_status` ORDER BY `last_update` DESC LIMIT 1"));
        $rssi_iris = $result['rssi'];
        $battery_iris = $result['battery'];
        $lastUpdate_iris = date($config->site->date_api_json, strtotime($result['last_update']));
        $iris = array_push($sensors['sensors'], ['iris' => ['sensor' => $config->station->sensor_iris, 'battery' => $battery_iris, 'rssi' => $rssi_iris, 'last_update' => $lastUpdate_iris]]);
    }
    // Tower Data
    if ($config->station->towers === true) {
        $result = mysqli_query($conn, "SELECT `name`,`sensor` FROM `towers` ORDER BY `arrange`");
        $towers = ['towers' => []];
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $sensor = $row['sensor'];
            $result2 = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `battery`, `rssi`, `timestamp` FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
            $rssi = $result2['rssi'];
            $battery = $result2['battery'];
            $last_update = date($config->site->date_api_json, strtotime($result2['timestamp']));
            $towers_output = array_push($towers['towers'], ['sensor' => $sensor, 'name' => $name, 'battery' => $battery, 'rssi' => $rssi, 'last_update' => $last_update]);
        }
        $towers_export = array_push($sensors['sensors'], $towers);
    }
    echo json_encode($sensors, true);

    // End Token
    include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');

} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo '{"error":"Unauthorized"}';
}
