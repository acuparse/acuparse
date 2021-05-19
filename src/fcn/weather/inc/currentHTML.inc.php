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
 * File: src/fcn/weather/inc/currentHTML.inc.php
 * Processes Main HTML Data
 */
// Get the loader
require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

// Warn if offline
$systemStatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `status` FROM `outage_alert`"));
if ($systemStatus['status'] === '0') {
    $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `timestamp` FROM `last_update`"));
    ?>
    <!-- Offline -->
    <section id="offline" class="row live-weather-offline">
        <div class="col-md-8 col-12 mx-auto text-center">
            <p class="alert alert-warning">Live data is temporarily unavailable!<br>Last
                update: <?= $lastUpdate['timestamp']; ?></p>
        </div>
    </section>
    <?php
}

// Load weather Data
if (!class_exists('getCurrentWeatherData')) {
    require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
    $getData = new getCurrentWeatherData();
}

$wx = $getData->getConditions();

// Load Lightning Data
if ($config->station->device === 0) {
    if ($config->station->primary_sensor === 0) {
        if ($config->station->lightning_source === 1 || $config->station->lightning_source === 3) {
            if (!class_exists('atlas\getCurrentLightningData')) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                $getLightningData = new atlas\getCurrentLightningData;
                $lightning = $getLightningData->getData();
            }
        }
    }
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

// Load Atlas Data
if ($config->station->device === 0) {
    if ($config->station->primary_sensor === 0) {
        // Load weather Data:
        if (!class_exists('getCurrentAtlasData')) {
            require(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
            $getAtlasData = new getCurrentAtlasData();
            $atlas = $getAtlasData->getData();
        }
    }
}

// Get icons
require(APP_BASE_PATH . '/fcn/weather/weatherIcons.php');

// Render the Dashboard
require(APP_BASE_PATH . '/fcn/weather/templates/dashboard.php');
