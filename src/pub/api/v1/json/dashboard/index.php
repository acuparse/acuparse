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
 * File: src/pub/api/v1/json/dashboard/index.php
 * Get weather JSON data
 */

// Get the loader
require(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/inc/loader.php');

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

header('Content-Type: application/json; charset=UTF-8'); // Set the header for JSON output

function getMainWeatherData()
{
    global $config;
    require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
    $getData = new getCurrentWeatherData(false, true);
    $jsonExportMain = array("main" => $getData->getJSONConditions());

    if ($config->station->device === 0) {
        if ($config->station->primary_sensor === 0) {
            require(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
            $getAtlasData = new getCurrentAtlasData();
            $jsonExportAtlas = array("atlas" => $getAtlasData->getJSONData());

            // Load Lightning Data:
            if ($config->station->lightning_source === 1 || $config->station->lightning_source === 3) {
                require(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                $getLightningData = new atlas\getCurrentLightningData('json');
                $jsonExportLightning = array("lightning" => $getLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportAtlas, $jsonExportLightning);
            } // Load Tower Lightning Data:
            elseif ($config->station->lightning_source === 2 || $config->station->lightning_source === 3) {
                require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData('json');
                $jsonExportTowerLightning = array("towerLightning" => $getTowerLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportAtlas, $jsonExportTowerLightning);
            } else {
                $result = array_merge($jsonExportMain, $jsonExportAtlas);
            }
        } else if ($config->station->primary_sensor === 1) {
            if ($config->station->lightning_source === 2) {
                require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData('json');
                $jsonExportTowerLightning = array("towerLightning" => $getTowerLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportTowerLightning);
            } else {
                $result = $jsonExportMain;
            }
        }
    } else {
        $result = $jsonExportMain;
    }
    if (empty($result)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        echo json_encode(['Error' => "Weather Data Unavailable"]);
        exit();
    } else {
        return json_encode($result);
    }
}

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

// Get main JSON
if (isset($_GET['main'])) {
    $mainData = getMainWeatherData();
    echo $mainData;
    exit();
} // Get Dashboard JSON
else {
    $mainData = getMainWeatherData();
    echo "[$mainData";
    if ($config->station->towers === true) {
        // Can we display private data?
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange`");
        } else {
            $result = mysqli_query($conn, "SELECT * FROM `towers` WHERE `private` = 0 ORDER BY `arrange`");
        }
        $towerCount = mysqli_num_rows($result);
        if ($towerCount >= 1) {
            $i = 1;
            $initString = ',{"towers":{';
            include(APP_BASE_PATH . '/fcn/api/json/getTowers.php');
            echo "}}";
        }
    }
    echo "]";
}
// End Token
include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');

exit();
