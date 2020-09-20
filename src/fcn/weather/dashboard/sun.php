<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2020 Maxwell Power
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
 * File: src/fcn/weather/dashboard/sun.php
 * Get the sun HTML
 */

/**
 * @return array
 * @var object $config Global Config
 * @return array
 * @var object $wx Weather Values
 * @return array
 * @var object $atlas Atlas Values
 */
?>
<div class="row">
    <div class="col">
        <h1><i class="wi wi-day-sunny" aria-hidden="true"></i> Sun</h1>
        <ul class="list-unstyled">
            <li><i class="wi wi-sunrise" aria-hidden="true"></i>
                <h3>Sunrise:</h3> <?= $wx->sunrise; ?></li>
            <li><i class="wi wi-sunset" aria-hidden="true"></i>
                <h3>Sunset:</h3> <?= $wx->sunset; ?></li>
            <?php
            if ($config->station->primary_sensor === 0) {
                ?>
                <li><i class="wi wi-hot" aria-hidden="true"></i>
                    <h3>UV Index:</h3> <?= $atlas->uvIndex; ?> (<?= $atlas->uvIndex_text; ?>)
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
</div>
