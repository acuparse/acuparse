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
 * File: src/api/system/health.php
 * System Health API
 */

// Get the loader
require(dirname(__DIR__, 3) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

if (isset($installed) && $installed === true) {
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    if (($conn) && (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) >= 1)) {
        $sqlStats = $conn->stat();
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp` FROM `last_update`"));
        $lastUpdate = $lastUpdate['timestamp'];
        $status = [
            "status" => "OK",
            "installed" => "true",
            "updated" => "$lastUpdate",
            "stats" => "$sqlStats"
        ];
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        $status = [
            "status" => "Error",
            "installed" => "Partial"
        ];
    }
    echo json_encode($status);
} elseif (isset($installed) && $installed === false) {
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    $status = [
        "status" => "OK",
        "installed" => "false"
    ];
    echo json_encode($status);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable");
    echo '{"error":"Unavailable"}';
}
