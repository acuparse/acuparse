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
 * File: src/fcn/weather/dashboard/temp.php
 * Get the dashboard temperature HTML
 */

/**
 * @var object $config Global Config
 * @var object $wx Weather Values
 */
?>
<section class="row">
    <div id="live-temp" class="col">
        <!-- BEGIN: Current -->
        <h1><i class="fas <?= tempIcon($wx->tempC); ?>" aria-hidden="true"></i> Temperature</h1>
        <h2><?php
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457; ($wx->tempC&#8451;)" : "$wx->tempC&#8451; ($wx->tempF&#8457;)";
            } else {
                $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457;" : "$wx->tempC&#8451;";
            }
            echo $temp . trendIcon($wx->tempF_trend) ?></h2>
        <!-- END: Current -->

        <!-- BEGIN: Feels Like -->
        <?php if ($wx->feelsF !== NULL) {
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457; ($wx->feelsC&#8451;)" : "$wx->feelsC&#8451; ($wx->feelsF&#8457;)";
            } else {
                $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457;" : "$wx->feelsC&#8451;";
            }
            echo '<h3>Feels Like:</h3> ' . $feels . '<br>';
        } ?>
        <!-- END: Feels Like -->

        <!-- BEGIN: Daily Low -->
        <h3>Low:</h3>
        <p>
            <?php
            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457; ($wx->tempC_low&#8451;)" : "$wx->tempC_low&#8451; ($wx->tempF_low&#8457;)";
            } else {
                $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457;" : "$wx->tempC_low&#8451;";
            }
            echo $temp_low . " @ $wx->low_temp_recorded"; ?></p>
        <!-- END: Daily Low -->

        <ul class="list-unstyled">

            <!-- BEGIN: Daily High -->
            <li><h3>High:</h3>
                <p>
                    <?php
                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                        $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457; ($wx->tempC_high&#8451;)" : "$wx->tempC_high&#8451; ($wx->tempF_high&#8457;)";
                    } else {
                        $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457;" : "$wx->tempC_high&#8451;";
                    }
                    echo $temp_high . " @ $wx->high_temp_recorded"; ?></p></li>
            <!-- END: Daily High -->

            <!-- BEGIN: Average -->
            <li><h3>Average:</h3>
                <p>
                    <?php
                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                        $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457; ($wx->tempC_avg&#8451;)" : "$wx->tempC_avg&#8451; ($wx->tempF_avg&#8457;)";
                    } else {
                        $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457;" : "$wx->tempC_avg&#8451;";
                    }
                    echo $temp_avg; ?></p></li>
            <!-- END: Average -->

            <!-- BEGIN: Dew Point -->
            <li><h3>Dew Point:</h3>
                <p>
                    <?php
                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                        $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457; ($wx->dewptC&#8451;)" : "$wx->dewptC&#8451; ($wx->dewptF&#8457;)";
                    } else {
                        $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457;" : "$wx->dewptC&#8451;";
                    }
                    echo $dewpt; ?></p></li>
            <!-- END: Dew Point -->
        </ul>
    </div>
</section>
