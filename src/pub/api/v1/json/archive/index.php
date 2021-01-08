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
 * File: src/pub/api/v1/json/archive/index.php
 * Get archive weather JSON data
 */

// Get the loader
require(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

function getArchiveWeatherData()
{
    global $config;

// Load Archive Weather Data:
    require(APP_BASE_PATH . '/fcn/weather/getArchiveWeatherData.php');
    $getData = new getArchiveWeatherData(true);
    $yesterday = $getData->getJSONYesterday();
    $week = $getData->getJSONWeek();
    $month = $getData->getJSONMonth();
    $last_month = $getData->getJSONLastMonth();
    $year = $getData->getJSONYear();
    $ever = $getData->getJSONAllTime();

    $jsonExportArchive = array(
        "main" => array(
            "yesterday" => $yesterday,
            "week" => $week,
            "month" => $month,
            "lastMonth" => $last_month,
            "year" => $year,
            "ever" => $ever
        )
    );

// Load Atlas Data:
    if ($config->station->device === 0) {
        if ($config->station->primary_sensor === 0) {
            // Load weather Data:
            require(APP_BASE_PATH . '/fcn/weather/getArchiveAtlasWeatherData.php');
            $getAtlasData = new getArchiveAtlasWeatherData();
            $atlasYesterday = $getAtlasData->getJSONYesterday();
            $atlasWeek = $getAtlasData->getJSONWeek();
            $atlasMonth = $getAtlasData->getJSONMonth();
            $atlasLastMonth = $getAtlasData->getJSONLastMonth();
            $atlasYear = $getAtlasData->getJSONYear();
            $atlasEver = $getAtlasData->getJSONAllTime();

            $jsonExportAtlasArchive = array(
                "atlas" => array(
                    "yesterday" => $atlasYesterday,
                    "week" => $atlasWeek,
                    "month" => $atlasMonth,
                    "lastMonth" => $atlasLastMonth,
                    "year" => $atlasYear,
                    "ever" => $atlasEver
                )
            );

            $result = array_merge($jsonExportArchive, $jsonExportAtlasArchive);
        }
    } else {
        $result = $jsonExportArchive;
    }

    if (empty($result)) {
        return json_encode(['Error' => "Atlas Data Unavailable"]);
    } else {
        return json_encode($result);
    }
}

// Get Dashboard JSON

$archiveData = getArchiveWeatherData();
if (empty($archiveData)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo json_encode(['Error' => "Archive Data Unavailable"]);
} else {
    echo $archiveData;
}
