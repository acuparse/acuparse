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
 * File: src/fcn/weather/inc/currentTowerHTML.inc.php
 * Processes Tower HTML Data
 */
// Get the loader
require(dirname(__DIR__, 3) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

if (!function_exists('trendIcon')) {
    require(APP_BASE_PATH . '/fcn/weather/weatherIcons.php');
}

// Load Tower Lightning Data
if ($config->station->device === 0) {
    if ($config->station->primary_sensor === 0) {
        if ($config->station->lightning_source === 2 || $config->station->lightning_source === 3) {
            if (!class_exists('tower\getCurrentLightningData')) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData;
                $towerLightning = $getTowerLightningData->getData();
            }
        }
    } else if ($config->station->primary_sensor === 1) {
        if ($config->station->lightning_source === 2) {
            if (!class_exists('tower\getCurrentLightningData')) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData;
                $towerLightning = $getTowerLightningData->getData();
            }
        }
    }
}

// Can we display private data?
if (isset($sensor)) {
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        $result = mysqli_query($conn, "SELECT `sensor` FROM `towers` WHERE `sensor` = '$sensor' ORDER BY `arrange`");
    } else {
        $result = mysqli_query($conn, "SELECT `sensor` FROM `towers` WHERE `private` = 0 AND `sensor` = '$sensor' ORDER BY `arrange`");
    }
} else {
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        $result = mysqli_query($conn, "SELECT `sensor` FROM `towers` ORDER BY `arrange`");
    } else {
        $result = mysqli_query($conn, "SELECT `sensor` FROM `towers` WHERE `private` = 0 ORDER BY `arrange`");
    }
}

require(APP_BASE_PATH . '/fcn/weather/templates/towers.php');
