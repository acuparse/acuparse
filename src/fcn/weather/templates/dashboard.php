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
 * File: src/fcn/weather/templates/dashboard.php
 * Dashboard Display Source
 */

/**
 * @return array
 * @var object $config Global Config
 */
?>
<!-- Live Weather Data -->
<section id="live-weather-data" class="row live-weather-data">

    <!-- Left Column -->
    <div class="col-md-4 col-sm-6 col-12">
        <!-- BEGIN: Temperature -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/temp.php'); ?>
        <!-- END: Temperature -->

        <!-- BEGIN: Wind -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/wind.php'); ?>
        <!-- END: Wind -->
        <?php
        if ($config->station->primary_sensor === 0) {
            ?>
            <!-- BEGIN: Humidity -->
            <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/humidity.php'); ?>
            <!-- END: Humidity -->
        <?php } ?>
    </div>
    <!-- END: Left Column -->

    <!-- Middle Column -->
    <div class="col-md-4 col-sm-6 col-12">
        <?php
        if ($config->station->primary_sensor === 1) {
            ?>
            <!-- BEGIN: Humidity -->
            <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/humidity.php'); ?>
            <!-- END: Humidity -->
        <?php } ?>

        <!-- BEGIN: Pressure -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/pressure.php'); ?>
        <!-- END: Pressure -->

        <!-- BEGIN: Rain -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/rain.php'); ?>
        <!-- END: Rain -->

        <?php
        if ($config->station->device === 0 && $config->station->access_mac != null) {
            if ($config->station->primary_sensor === 0) {
                ?>
                <!-- BEGIN: Light -->
                <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/light.php'); ?>
                <!-- END: Light -->
            <?php }
            if ($config->station->primary_sensor === 0 && ($config->station->lightning_source === 1 || $config->station->lightning_source === 3)) { ?>
                <!-- BEGIN: Lightning -->
                <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/lightning.php'); ?>
                <!-- END: Lightning -->
            <?php } elseif (($config->station->primary_sensor === 0 || $config->station->primary_sensor === 1) && ($config->station->lightning_source === 2 || $config->station->lightning_source === 3)) { ?>
                <!-- BEGIN: Lightning Tower -->
                <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/towerLightning.php'); ?>
                <!-- END: Lightning Tower-->
            <?php }
        } ?>
    </div>

    <!-- END: Middle Column -->

    <!-- Right Column -->
    <div class="col-md-4 col-sm-6 col-12">

        <!-- BEGIN: Sun -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/sun.php'); ?>
        <!-- END: Sun -->

        <!-- BEGIN: Moon -->
        <?php require_once(APP_BASE_PATH . '/fcn/weather/dashboard/moon.php'); ?>
        <!-- END: Moon -->
    </div>
    <!-- END: Right Column -->
</section>
<!-- END: Live Weather Data -->
