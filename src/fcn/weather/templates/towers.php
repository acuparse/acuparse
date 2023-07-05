<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: src/fcn/weather/templates/towers.php
 * Tower Display Source
 */

/**
 * @return array
 * @var object $config Global Config
 */
/** @var mysqli_result $result */

// Is there data to show? If yes, show it.
if (mysqli_num_rows($result) >= 1) {
    // Load weather Data:
    if (!class_exists('getCurrentWeatherData')) {
        require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
        $getData = new getCurrentWeatherData();
    }

    if (!class_exists('getCurrentTowerData')) {
        require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerData.php');
    }
    ?>
    <!-- Tower Sensors -->
    <section id="live-tower-data" class="row live-tower-data">
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $sensor = $row['sensor'];

            $getTowerData = new getCurrentTowerData($sensor);
            $towerData = $getTowerData->getConditions();

            $sensorName = $towerData->name;

            $tempF = $towerData->tempF;
            $tempF_low = $towerData->tempF_low;
            $tempF_high = $towerData->tempF_high;

            $tempC = $towerData->tempC;
            $tempC_low = $towerData->tempC_low;
            $tempC_high = $towerData->tempC_high;

            $temp_high_recorded = $towerData->high_temp_recorded;
            $temp_low_recorded = $towerData->low_temp_recorded;

            $relH = $towerData->relH;

            // Temp Trending
            $tempF_trend = trendIcon($getData->calculateTrend('tempF', 'tower_data', $sensor));

            // Humidity Trending
            $relH_trend = trendIcon($getData->calculateTrend('relH', 'tower_data', $sensor));

            require(APP_BASE_PATH . '/fcn/weather/dashboard/tower.php');
        }

        ?>
    </section>
    <!-- END: Tower Sensors -->

    <?php
    if (($config->station->primary_sensor === 0 || $config->station->primary_sensor === 1) && $config->station->lightning_source === 3) { ?>
        <hr class="hr-dotted hr-half">
        <!-- BEGIN: Tower Lightning -->
        <?php require(APP_BASE_PATH . '/fcn/weather/dashboard/towerLightning.php'); ?>
        <!-- END: Tower Lightning -->
        <?php
    }
}