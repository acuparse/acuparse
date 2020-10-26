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
 * File: src/fcn/weather/dashboard/moon.php
 * Get the moon HTML
 */

/**
 * @return array
 * @var object $wx Weather Values
 */
?>
<div class="row">
    <div class="col">
        <h1><i class="wi <?= moonIcon($wx->moon_stage); ?>" aria-hidden="true"></i> Moon</h1>
        <h2><?= "$wx->moon_stage"; ?></h2>
        <p><?= "$wx->moon_age days old, $wx->moon_illumination visible"; ?></p>
        <ul class="list-unstyled">
            <?php if ($wx->moonrise !== null) { ?>
                <li><i class="wi wi-moonrise" aria-hidden="true"></i>
                    <h3>Moonrise:</h3> <?= $wx->moonrise; ?></li>
                <li><i class="wi wi-moonset" aria-hidden="true"></i>
                    <h3>Moonset:</h3> <?= $wx->moonset; ?></li>
            <?php } ?>
            <li><i class="wi wi-moon-new" aria-hidden="true"></i>
                <h3>Latest
                    New:</h3> <?= $wx->moon_lastNew; ?></li>
            <li><i class="wi wi-moon-full" aria-hidden="true"></i>
                <h3>Latest
                    Full:</h3> <?= $wx->moon_lastFull; ?></li>
            <li><i class="wi wi-moon-new" aria-hidden="true"></i>
                <h3>Upcoming
                    New:</h3> <?= $wx->moon_nextNew; ?></li>
            <li><i class="wi wi-moon-full" aria-hidden="true"></i>
                <h3>Upcoming
                    Full:</h3> <?= $wx->moon_nextFull; ?></li>
        </ul>
    </div>
</div>
