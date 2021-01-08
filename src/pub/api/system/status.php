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
 * File: src/pub/api/v1/system/status.php
 * Get JSON status data
 */

// Get the loader
require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

$sensors = ['sensors' => []];

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {
    if ($config->station->access_mac != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery` FROM `access_status` ORDER BY `last_update` DESC LIMIT 1"));
        $battery_access = ucfirst($result['battery']);
        $access = array_push($sensors['sensors'], ['access' => ['battery' => $battery_access]]);
    }
    if ($config->station->sensor_atlas != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery`, `rssi` FROM `atlas_status` ORDER BY `last_update` DESC LIMIT 1"));
        $rssi_atlas = $result['rssi'];
        $battery_atlas = $result['battery'];
        $atlas = array_push($sensors['sensors'], ['atlas' => ['battery' => $battery_atlas, 'rssi' => $rssi_atlas]]);
    }
    if ($config->station->sensor_iris != 0) {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `battery`, `rssi` FROM `iris_status` ORDER BY `last_update` DESC LIMIT 1"));
        $rssi_iris = $result['rssi'];
        $battery_iris = $result['battery'];
        $iris = array_push($sensors['sensors'], ['atlas' => ['battery' => $battery_iris, 'rssi' => $rssi_iris]]);
    }
    if ($config->station->towers === true) {
        $result = mysqli_query($conn, "SELECT `name`,`sensor` FROM `towers` ORDER BY `arrange` ASC");
        $towers = ['towers' => []];
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $sensor = $row['sensor'];
            $result2 = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `battery`, `rssi` FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
            $rssi = $result2['rssi'];
            $battery = $result2['battery'];
            $towers_output = array_push($towers['towers'], ['name' => $name, 'sensor' => $sensor, 'battery' => $battery, 'rssi' => $rssi]);
        }
        $towers_export = array_push($sensors['sensors'], $towers);
    }
    echo json_encode($sensors, true);

    // End Token
    include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');

} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Unauthorized";
}