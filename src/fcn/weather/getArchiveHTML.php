<?php
/**
 * Acuparse - AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2017 Maxwell Power
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
 * File: src/getArchiveHTML.php
 * Get the archive data HTML
 */
function getArchiveHTML()
{
    require(dirname(dirname(__DIR__)) . '/inc/loader.php');
    require(APP_BASE_PATH . '/fcn/weather/GetArchiveWeatherData.php');

// Load Archive Weather Data:
    $GetData = new GetArchiveWeatherData();
    $yesterday = $GetData->getYesterday();
    $week = $GetData->getWeek();
    $month = $GetData->getMonth();
    $last_month = $GetData->getLastMonth();
    $year = $GetData->getYear();
    $ever = $GetData->getAllTime();
    ?>
    <section id="archive_data" class="weather_archive_display">
        <div class="row">
            <!-- Yesterday -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">Yesterday:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_high&#8457; ($yesterday->tempC_high&#8451;)" : "$yesterday->tempC_high&#8451; ($yesterday->tempF_high&#8457;)";
                    echo $temp_high_yesterday . ' @ ' . $yesterday->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_low&#8457; ($yesterday->tempC_low&#8451;)" : "$yesterday->tempC_low&#8451; ($yesterday->tempF_low&#8457;)";
                    echo $temp_low_yesterday . ' @ ' . $yesterday->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_yesterday = ($config->site->imperial === true) ? "$yesterday->windS_mph_high mph ($yesterday->windS_kmh_high km/h)" : "$yesterday->windS_kmh_high km/h ($yesterday->windS_mph_high mph)";
                    echo 'From ' . $yesterday->windDIR . ' @ ' . $wind_high_yesterday . ' @ ' . $yesterday->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_high inHg ($yesterday->pressure_kPa_high kPa)" : "$yesterday->pressure_kPa_high kPa ($yesterday->pressure_inHg_high inHg)";
                    echo $pressure_high_yesterday . ' @ ' . $yesterday->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_low inHg ($yesterday->pressure_kPa_low kPa)" : "$yesterday->pressure_kPa_low kPa ($yesterday->pressure_inHg_low inHg)";
                    echo $pressure_low_yesterday . ' @ ' . $yesterday->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$yesterday->relH_high% @ " . $yesterday->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$yesterday->relH_low% @ " . $yesterday->relH_low_recorded; ?></h4>
                <?php if ($yesterday->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_yesterday = ($config->site->imperial === true) ? "$yesterday->rainfall_IN_total in ($yesterday->rainfall_MM_total mm)" : "$yesterday->rainfall_MM_total mm ($yesterday->rainfall_IN_total in)";
                        echo $rain_total_yesterday; ?></h4>
                <?php } ?>
            </div>

            <!-- This Week -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">This Week:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_week = ($config->site->imperial === true) ? "$week->tempF_high&#8457; ($week->tempC_high&#8451;)" : "$week->tempC_high&#8451; ($week->tempF_high&#8457;)";
                    echo $temp_high_week . ' @ ' . $week->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_week = ($config->site->imperial === true) ? "$week->tempF_low&#8457; ($week->tempC_low&#8451;)" : "$week->tempC_low&#8451; ($week->tempF_low&#8457;)";
                    echo $temp_low_week . ' @ ' . $week->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_week = ($config->site->imperial === true) ? "$week->windS_mph_high mph ($week->windS_kmh_high km/h)" : "$week->windS_kmh_high km/h ($week->windS_mph_high mph)";
                    echo 'From ' . $week->windDIR . ' @ ' . $wind_high_week . ' @ ' . $week->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_week = ($config->site->imperial === true) ? "$week->pressure_inHg_high inHg ($week->pressure_kPa_high kPa)" : "$week->pressure_kPa_high kPa ($week->pressure_inHg_high inHg)";
                    echo $pressure_high_week . ' @ ' . $week->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_week = ($config->site->imperial === true) ? "$week->pressure_inHg_low inHg ($week->pressure_kPa_low kPa)" : "$week->pressure_kPa_low kPa ($week->pressure_inHg_low inHg)";
                    echo $pressure_low_week . ' @ ' . $week->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$week->relH_high% @ " . $week->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$week->relH_low% @ " . $week->relH_low_recorded; ?></h4>
                <?php if ($week->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Most Rain:</strong> <?php
                        $most_rain_week = ($config->site->imperial === true) ? "$week->rainfall_IN_most in ($week->rainfall_MM_most mm)" : "$week->rainfall_MM_most mm ($week->rainfall_IN_most in)";
                        echo $most_rain_week . ' on ' . $week->rainfall_IN_most_recorded; ?></h4>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_week = ($config->site->imperial === true) ? "$week->rainfall_IN_total in ($week->rainfall_MM_total mm)" : "$week->rainfall_MM_total mm ($week->rainfall_IN_total in)";
                        echo $rain_total_week; ?></h4>
                <?php } ?>
            </div>
            <div class="clearfix visible-sm-block"></div>

            <!-- This Month -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">This Month:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_month = ($config->site->imperial === true) ? "$month->tempF_high&#8457; ($month->tempC_high&#8451;)" : "$month->tempC_high&#8451; ($month->tempF_high&#8457;)";
                    echo $temp_high_month . ' @ ' . $month->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_month = ($config->site->imperial === true) ? "$month->tempF_low&#8457; ($month->tempC_low&#8451;)" : "$month->tempC_low&#8451; ($month->tempF_low&#8457;)";
                    echo $temp_low_month . ' @ ' . $month->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_month = ($config->site->imperial === true) ? "$month->windS_mph_high mph ($month->windS_kmh_high km/h)" : "$month->windS_kmh_high km/h ($month->windS_mph_high mph)";
                    echo 'From ' . $month->windDIR . ' @ ' . $wind_high_month . ' @ ' . $month->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_month = ($config->site->imperial === true) ? "$month->pressure_inHg_high inHg ($month->pressure_kPa_high kPa)" : "$month->pressure_kPa_high kPa ($month->pressure_inHg_high inHg)";
                    echo $pressure_high_month . ' @ ' . $month->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_month = ($config->site->imperial === true) ? "$month->pressure_inHg_low inHg ($month->pressure_kPa_low kPa)" : "$month->pressure_kPa_low kPa ($month->pressure_inHg_low inHg)";
                    echo $pressure_low_month . ' @ ' . $month->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$month->relH_high% @ " . $month->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$month->relH_low% @ " . $month->relH_low_recorded; ?></h4>
                <?php if ($month->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Most Rain:</strong> <?php
                        $most_rain_month = ($config->site->imperial === true) ? "$month->rainfall_IN_most in ($month->rainfall_MM_most mm)" : "$month->rainfall_MM_most mm ($month->rainfall_IN_most in)";
                        echo $most_rain_month . ' on ' . $month->rainfall_IN_most_recorded; ?></h4>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_month = ($config->site->imperial === true) ? "$month->rainfall_IN_total in ($month->rainfall_MM_total mm)" : "$month->rainfall_MM_total mm ($month->rainfall_IN_total in)";
                        echo $rain_total_month; ?></h4>
                <?php } ?>
            </div>
            <div class="clearfix visible-md-block visible-lg-block"></div>

            <!-- Last Month -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">Last Month:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_last_month = ($config->site->imperial === true) ? "$last_month->tempF_high&#8457; ($last_month->tempC_high&#8451;)" : "$last_month->tempC_high&#8451; ($last_month->tempF_high&#8457;)";
                    echo $temp_high_last_month . ' @ ' . $last_month->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_last_month = ($config->site->imperial === true) ? "$last_month->tempF_low&#8457; ($last_month->tempC_low&#8451;)" : "$last_month->tempC_low&#8451; ($last_month->tempF_low&#8457;)";
                    echo $temp_low_last_month . ' @ ' . $last_month->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_last_month = ($config->site->imperial === true) ? "$last_month->windS_mph_high mph ($last_month->windS_kmh_high km/h)" : "$last_month->windS_kmh_high km/h ($last_month->windS_mph_high mph)";
                    echo 'From ' . $last_month->windDIR . ' @ ' . $wind_high_last_month . ' @ ' . $last_month->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_high inHg ($last_month->pressure_kPa_high kPa)" : "$last_month->pressure_kPa_high kPa ($last_month->pressure_inHg_high inHg)";
                    echo $pressure_high_last_month . ' @ ' . $last_month->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_low inHg ($last_month->pressure_kPa_low kPa)" : "$last_month->pressure_kPa_low kPa ($last_month->pressure_inHg_low inHg)";
                    echo $pressure_low_last_month . ' @ ' . $last_month->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$last_month->relH_high% @ " . $last_month->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$last_month->relH_low% @ " . $last_month->relH_low_recorded; ?></h4>
                <?php if ($last_month->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Most Rain:</strong> <?php
                        $most_rain_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_most in ($last_month->rainfall_MM_most mm)" : "$last_month->rainfall_MM_most mm ($last_month->rainfall_IN_most in)";
                        echo $most_rain_last_month . ' on ' . $last_month->rainfall_IN_most_recorded; ?></h4>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_total in ($last_month->rainfall_MM_total mm)" : "$last_month->rainfall_MM_total mm ($last_month->rainfall_IN_total in)";
                        echo $rain_total_last_month; ?></h4>
                <?php } ?>
            </div>
            <div class="clearfix visible-sm-block"></div>

            <!-- This Year -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">This Year:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_year = ($config->site->imperial === true) ? "$year->tempF_high&#8457; ($year->tempC_high&#8451;)" : "$year->tempC_high&#8451; ($year->tempF_high&#8457;)";
                    echo $temp_high_year . ' @ ' . $year->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_year = ($config->site->imperial === true) ? "$year->tempF_low&#8457; ($year->tempC_low&#8451;)" : "$year->tempC_low&#8451; ($year->tempF_low&#8457;)";
                    echo $temp_low_year . ' @ ' . $year->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_year = ($config->site->imperial === true) ? "$year->windS_mph_high mph ($year->windS_kmh_high km/h)" : "$year->windS_kmh_high km/h ($year->windS_mph_high mph)";
                    echo 'From ' . $year->windDIR . ' @ ' . $wind_high_year . ' @ ' . $year->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_year = ($config->site->imperial === true) ? "$year->pressure_inHg_high inHg ($year->pressure_kPa_high kPa)" : "$year->pressure_kPa_high kPa ($year->pressure_inHg_high inHg)";
                    echo $pressure_high_year . ' @ ' . $year->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_year = ($config->site->imperial === true) ? "$year->pressure_inHg_low inHg ($year->pressure_kPa_low kPa)" : "$year->pressure_kPa_low kPa ($year->pressure_inHg_low inHg)";
                    echo $pressure_low_year . ' @ ' . $year->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$year->relH_high% @ " . $year->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$year->relH_low% @ " . $year->relH_low_recorded; ?></h4>
                <?php if ($year->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Most Rain:</strong> <?php
                        $most_rain_year = ($config->site->imperial === true) ? "$year->rainfall_IN_most in ($year->rainfall_MM_most mm)" : "$year->rainfall_MM_most mm ($year->rainfall_IN_most in)";
                        echo $most_rain_year . ' on ' . $year->rainfall_IN_most_recorded; ?></h4>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_year = ($config->site->imperial === true) ? "$year->rainfall_IN_total in ($year->rainfall_MM_total mm)" : "$year->rainfall_MM_total mm ($year->rainfall_IN_total in)";
                        echo $rain_total_year; ?></h4>
                <?php } ?>
            </div>

            <!-- All Time -->
            <div class="col-lg-4 col-md-4 col-sm-6">
                <h2 class="panel-heading">All Time:</h2>
                <h3><i class="fa fa-thermometer-full" aria-hidden="true"></i> Temperature:</h3>
                <h4><strong>High:</strong> <?php
                    $temp_high_ever = ($config->site->imperial === true) ? "$ever->tempF_high&#8457; ($ever->tempC_high&#8451;)" : "$ever->tempC_high&#8451; ($ever->tempF_high&#8457;)";
                    echo $temp_high_ever . ' @ ' . $ever->tempF_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $temp_low_ever = ($config->site->imperial === true) ? "$ever->tempF_low&#8457; ($ever->tempC_low&#8451;)" : "$ever->tempC_low&#8451; ($ever->tempF_low&#8457;)";
                    echo $temp_low_ever . ' @ ' . $ever->tempF_low_recorded; ?></h4>
                <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                <h4><?php
                    $wind_high_ever = ($config->site->imperial === true) ? "$ever->windS_mph_high mph ($ever->windS_kmh_high km/h)" : "$ever->windS_kmh_high km/h ($ever->windS_mph_high mph)";
                    echo 'From ' . $ever->windDIR . ' @ ' . $wind_high_ever . ' @ ' . $ever->windS_mph_high_recorded; ?></h4>
                <h3><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h3>
                <h4><strong>High:</strong> <?php
                    $pressure_high_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_high inHg ($ever->pressure_kPa_high kPa)" : "$ever->pressure_kPa_high kPa ($ever->pressure_inHg_high inHg)";
                    echo $pressure_high_ever . ' @ ' . $ever->pressure_inHg_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?php
                    $pressure_low_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_low inHg ($ever->pressure_kPa_low kPa)" : "$ever->pressure_kPa_low kPa ($ever->pressure_inHg_low inHg)";
                    echo $pressure_low_ever . ' @ ' . $ever->pressure_inHg_low_recorded; ?></h4>
                <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                <h4><strong>High:</strong> <?= "$ever->relH_high% @ " . $ever->relH_high_recorded; ?></h4>
                <h4><strong>Low:</strong> <?= "$ever->relH_low% @ " . $ever->relH_low_recorded; ?></h4>
                <?php if ($ever->rainfall_IN_total !== 0.) { ?>
                    <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall:</h3>
                    <h4><strong>Most Rain:</strong> <?php
                        $most_rain_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_most in ($ever->rainfall_MM_most mm)" : "$ever->rainfall_MM_most mm ($ever->rainfall_IN_most in)";
                        echo $most_rain_ever . ' on ' . $ever->rainfall_IN_most_recorded; ?></h4>
                    <h4><strong>Total Rain:</strong> <?php
                        $rain_total_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_total in ($ever->rainfall_MM_total mm)" : "$ever->rainfall_MM_total mm ($ever->rainfall_IN_total in)";
                        echo $rain_total_ever . ' since ' . $ever->rainfall_IN_total_since; ?></h4>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php
}
