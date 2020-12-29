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
 * File: src/fcn/weather/dashboard/Lightning.php
 * Get the lightning HTML
 */

/**
 * @return array
 * @return array
 * @var object $config Global Config
 * @var object $lightning Lightning Values
 */
?>
<div class="row">
    <div class="col">
        <h1><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h1>
        <ul class="list-unstyled">
            <?php
            if ($lightning->dailystrikes != 0) {
                ?>
                <li><h3>Latest Strikes:</h3> <?= $lightning->currentstrikes; ?> | <h3>
                        Total Today:</h3> <?= $lightning->dailystrikes; ?></li>
                <li><h3>Distance:</h3> <?php
                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                        $last_strike_distance = ($config->site->imperial === true) ? "$lightning->last_strike_distance_M Miles ($lightning->last_strike_distance_KM KM)" : "$lightning->last_strike_distance_KM KM ($lightning->last_strike_distance_M Miles)";
                    } else {
                        $last_strike_distance = ($config->site->imperial === true) ? "$lightning->last_strike_distance_M Miles" : "$lightning->last_strike_distance_KM KM";
                    }
                    echo $last_strike_distance; ?></li>
                <li><h3>Interference:</h3> <?= $lightning->interference; ?> |
                    <h3>Last:</h3> <?= $lightning->last_update; ?></li>
                <?php
            } else {
                ?>
                <li><h3>No Lightning Detected</h3></li>
                <li><h3>Interference:</h3> <?= $lightning->interference; ?></li>
                <?php
            }
            ?>
        </ul>
    </div>
</div>
