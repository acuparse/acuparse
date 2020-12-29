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
 * File: src/fcn/weather/dashboard/tower.php
 * Get the dashboard tower HTML
 */

/**
 * @return array
 * @var object $config Global Config
 */

/**
 * @var string $sensor
 * @var string $sensorName
 * @var string $tempC
 * @var string $tempF
 * @var string $tempF_trend
 * @var string $tempF_low
 * @var string $tempC_low
 * @var string $tempC
 * @var string $tempF
 * @var string $temp_low_recorded
 * @var string $tempF_trend
 * @var string $tempF_high
 * @var string $tempC_high
 * @var string $temp_high_recorded
 * @var string $relH
 * @var string $relH_trend
 */
?>
<!-- BEGIN: Tower <?= ltrim($sensor, '0'); ?> -->
<div class="col">
    <h1><?= $sensorName; ?></h1>
    <h2><i class="fas <?= tempIcon($tempC); ?>" aria-hidden="true"></i> Temperature</h2>
    <h3><?php
        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
            $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; ($tempC&#8451;) $tempF_trend" : "$tempC&#8451; ($tempF&#8457;) $tempF_trend";
        } else {
            $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; $tempF_trend" : "$tempC&#8451; $tempF_trend";
        }
        echo $tower_temp ?></h3>

    <?php if ($config->station->towers_additional === true) { ?>
        <h4><strong>Low</strong>: <?php
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                $tower_temp = ($config->site->imperial === true) ? "$tempF_low&#8457; ($tempC_low&#8451;) @ $temp_low_recorded" : "$tempC_low&#8451; ($tempF_low&#8457;) @ $temp_low_recorded";
            } else {
                $tower_temp = ($config->site->imperial === true) ? "$tempF_low&#8457; @ $temp_low_recorded" : "$tempC_low&#8451; @ $temp_low_recorded";
            }
            echo $tower_temp ?></h4>
        <h4><strong>High</strong>: <?php
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                $tower_temp = ($config->site->imperial === true) ? "$tempF_high&#8457; ($tempC_high&#8451;) @ $temp_high_recorded" : "$tempC_high&#8451; ($tempF_high&#8457;) @ $temp_high_recorded";
            } else {
                $tower_temp = ($config->site->imperial === true) ? "$tempF_high&#8457; @ $temp_high_recorded" : "$tempC_high&#8451; @ $temp_high_recorded";
            }
            echo $tower_temp ?></h4>
    <?php } ?>
    <h2><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h2>
    <h3><?= "$relH% $relH_trend"; ?></h3>
</div>
<!-- END: Tower <?= ltrim($sensor, '0'); ?> -->
