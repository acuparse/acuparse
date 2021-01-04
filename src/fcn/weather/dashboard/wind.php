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
 * File: src/fcn/weather/dashboard/wind.php
 * Get the wind HTML
 */

/**
 * @return array
 * @return array
 * @return array
 * @var object $wx Weather Values
 * @var object $config Global Config
 * @var object $atlas Atlas Values
 */
?>
<div class="row">
    <div class="col">
        <h1>
            <i class="wi wi-wind-beaufort-<?= $wx->windBeaufort; ?>" aria-hidden="true"
               title="Beauford <?= $wx->windBeaufort; ?>"></i> Wind</h1>
        <?php
        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
            $windSpeed = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSpeedMPH mph ($wx->windSpeedKMH km/h)" :
                "$wx->windDIR @ $wx->windSpeedKMH km/h ($wx->windSpeedMPH mph)";
        } else {
            $windSpeed = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSpeedMPH mph" : "$wx->windDIR @
            $wx->windSpeedKMH km/h";
        }
        echo '<h2>from <i class="wi wi-wind wi-from-' . strtolower($wx->windDIR) . '" aria-hidden="true"></i> ' . $windSpeed . '</h2>'; ?>
        <ul class="list-unstyled">
            <?php if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
                if ($atlas->windGustMPH !== $wx->windSpeedMPH) {
                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate ===
                        'archive') {
                        $windGust = ($config->site->imperial === true) ? "$atlas->windGustDIR @ $atlas->windGustMPH mph
                ($atlas->windGustKMH km/h)" : "$atlas->windGustDIR @ $atlas->windGustKMH km/h ($atlas->windGustMPH mph)";
                    } else {
                        $windGust = ($config->site->imperial === true) ? "$atlas->windGustDIR @ $atlas->windGustMPH mph" :
                            "$atlas->windGustDIR @ $atlas->windGustKMH km/h";
                    }
                    echo "<li><h3>Gust:</h3> $windGust</li>";
                }
                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate ===
                    'archive') {
                    $windAvg = ($config->site->imperial === true) ? "$atlas->windAvgMPH mph
                ($atlas->windAvgKMH km/h)" : "$atlas->windAvgKMH km/h ($atlas->windAvgMPH mph)";
                } else {
                    $windAvg = ($config->site->imperial === true) ? "$atlas->windAvgMPH mph" :
                        "$atlas->windAvgKMH km/h";
                }
                echo "<li><h3>Average:</h3> $windAvg</li>";
            }
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate ===
                'archive') {
                $windPeak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSpeedMPH_peak mph
                ($wx->windSpeedKMH_peak km/h)" : "$wx->windDIR_peak @ $wx->windSpeedKMH_peak km/h ($wx->windSpeedMPH_peak mph)";
            } else {
                $windPeak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSpeedMPH_peak mph" :
                    "$wx->windDIR_peak @ $wx->windSpeedKMH_peak km/h";
            }
            echo "<li><h3>Peak:</h3> $windPeak @ $wx->windSpeed_peak_recorded</li>"; ?>
        </ul>
    </div>
</div>
