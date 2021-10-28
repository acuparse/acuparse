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
 * File: src/pub/api/v1/json/archive/search/index.php
 * JSON Search Archive Table
 */

// Get the loader
require(dirname(__DIR__, 6) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

if (isset($_GET['start'])) {
    $startDate = strtotime(mysqli_real_escape_string($conn,
        filter_input(INPUT_GET, 'start', FILTER_SANITIZE_STRING)));
    $startDate = date('Y-m-d H:i:s', $startDate);
} else {
    echo "Missing start date";
    exit ();
}

if (isset($_GET['end'])) {
    $endDate = strtotime(mysqli_real_escape_string($conn,
        filter_input(INPUT_GET, 'end', FILTER_SANITIZE_STRING)));
    $endDate = date('Y-m-d H:i:s', $endDate);
} else {
    $endDate = date('Y-m-d H:i:s', strtotime('now'));
}

if (isset($_GET['sort'])) {
    $sort = mysqli_real_escape_string($conn,
        strtoupper(filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)));
    $sort = ' ORDER BY `reported` ' . $sort;
} else {
    $sort = ' ORDER BY `reported` ASC';
}

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

// Apply Limit
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    if (isset($_GET['limit'])) {
        $limit = mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT));
        $limit = ' LIMIT ' . $limit;
    } else {
        $limit = '';
    }
} else {
    if (isset($_GET['limit'])) {
        $limit = mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT));
        $limit = ' LIMIT ' . $limit;
        if ($limit < 100) {
            $limit = ' LIMIT 100';
        }
    } else {
        $limit = ' LIMIT 100';
    }
}

// Get Readings
if (isset($_GET['query'])) {
    // Wind
    if ($_GET['query'] === 'wind') {
        // Atlas Readings
        if ($config->station->primary_sensor === 0) {
            $result = mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windSpeedMPH_avg`, `windDEG`, `windGustMPH`, `windGustDEG` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
        } //Iris
        else {
            $result = mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
        }
    } // Temp
    else if ($_GET['query'] === 'temp') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `tempF`, `feelsF`, `dewptF` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // Rain
    else if ($_GET['query'] === 'rain') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `rainin`, `total_rainin` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // Humidity
    else if ($_GET['query'] === 'relh') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // Pressure
    else if ($_GET['query'] === 'pressure') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // Lightning
    else if ($_GET['query'] === 'lightning') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // Light
    else if ($_GET['query'] === 'light') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `light`, `lightSeconds` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // UV
    else if ($_GET['query'] === 'uv') {
        $result = mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
    } // All
    else if ($_GET['query'] === 'all') {
        // Atlas Readings
        if ($config->station->primary_sensor === 0) {
            $result = mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windSpeedMPH_avg`, `windDEG`, `windGustMPH`, `windGustDEG`, `tempF`, `feelsF`, `dewptF`, `rainin`, `total_rainin`, `relH`, `pressureinHg`, `lightning`, `light`, `lightSeconds`, `uvindex`  FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
        } //Iris
        else {
            $result = mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windDEG`, `tempF`, `feelsF`, `dewptF`, `rainin`, `total_rainin`, `relH`, `pressureinHg`, `lightning`, `light`, `lightSeconds`, `uvindex`  FROM `archive` WHERE `reported` BETWEEN '$startDate' AND '$endDate'" . $sort . $limit);
        }
    }
} else {
    echo 'Missing Query. Use query=wind/rain/temp/relh/pressure/lightning/uv/light';
    echo 'See API Guide for more details.';
    exit();
}

/** @var $result */
$count = mysqli_num_rows($result);
$i = 1;

// Output Readings

if (empty($result)) {
    return json_encode(['Error' => "Data Unavailable"]);
} else {
    echo '[';
    while ($row = mysqli_fetch_assoc($result)) {
        $row['reported'] = date($config->site->date_api_json, strtotime($row['reported']));
        echo json_encode($row);
        if ($i < $count) {
            echo ",";
        }
        $i++;
    }
    echo ']';
}

// End Token
include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');
