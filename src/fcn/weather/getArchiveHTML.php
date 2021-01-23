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
 * File: src/fcn/weather/getArchiveHTML.php
 * Get the archive data HTML
 */

function getArchiveHTML()
{
    require(dirname(dirname(__DIR__)) . '/inc/loader.php');
    require(APP_BASE_PATH . '/fcn/weather/getArchiveWeatherData.php');

    /**
     * @return array
     * @var object $config Global Config
     */

// Load Archive Weather Data:
    $getData = new getArchiveWeatherData();
    $yesterday = $getData->getYesterday();
    $week = $getData->getWeek();
    $month = $getData->getMonth();
    $last_month = $getData->getLastMonth();
    $year = $getData->getYear();
    $ever = $getData->getAllTime();

    // Load Atlas Data:
    if ($config->station->device === 0) {
        if ($config->station->primary_sensor === 0) {
            // Load weather Data:
            require(APP_BASE_PATH . '/fcn/weather/getArchiveAtlasWeatherData.php');
            $getAtlasData = new getArchiveAtlasWeatherData();
            $atlasYesterday = $getAtlasData->getYesterday();
            $atlasWeek = $getAtlasData->getWeek();
            $atlasMonth = $getAtlasData->getMonth();
            $atlasLastMonth = $getAtlasData->getLastMonth();
            $atlasYear = $getAtlasData->getYear();
            $atlasEver = $getAtlasData->getAllTime();
        }
    }
    ?>

    <div id="archive-weather-data" class="row archive-weather-data">
        <div class="col">
            <div class="row">
                <!-- Yesterday -->
                <section id="archive-weather-data-yesterday" class="col-md-4 col-12">
                    <h2>Yesterday</h2>
                    <?php if (isset($yesterday->tempF_high)) { ?>
                        <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $temp_high_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_high&#8457; ($yesterday->tempC_high&#8451;)" : "$yesterday->tempC_high&#8451; ($yesterday->tempF_high&#8457;)";
                                } else {
                                    $temp_high_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_high&#8457;" : "$yesterday->tempC_high&#8451;";
                                }
                                echo $temp_high_yesterday . ' @ ' . $yesterday->tempF_high_recorded; ?></li>
                            <li><h4>Low:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $temp_low_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_low&#8457; ($yesterday->tempC_low&#8451;)" : "$yesterday->tempC_low&#8451; ($yesterday->tempF_low&#8457;)";
                                } else {
                                    $temp_low_yesterday = ($config->site->imperial === true) ? "$yesterday->tempF_low&#8457;" : "$yesterday->tempC_low&#8451;";
                                }
                                echo $temp_low_yesterday . ' @ ' . $yesterday->tempF_low_recorded; ?></li>
                        </ul>
                        <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                        <p><?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $wind_high_yesterday = ($config->site->imperial === true) ? "$yesterday->windS_mph_high mph ($yesterday->windS_kmh_high km/h)" : "$yesterday->windS_kmh_high km/h ($yesterday->windS_mph_high mph)";
                            } else {
                                $wind_high_yesterday = ($config->site->imperial === true) ? "$yesterday->windS_mph_high mph" : "$yesterday->windS_kmh_high km/h";
                            }
                            echo 'From ' . $yesterday->windDIR . ' at ' . $wind_high_yesterday . ' @ ' . $yesterday->windS_mph_high_recorded; ?></p>
                        <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $pressure_high_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_high inHg ($yesterday->pressure_kPa_high kPa)" : "$yesterday->pressure_kPa_high kPa ($yesterday->pressure_inHg_high inHg)";
                                } else {
                                    $pressure_high_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_high inHg" : "$yesterday->pressure_kPa_high kPa";
                                }
                                echo $pressure_high_yesterday . ' @ ' . $yesterday->pressure_inHg_high_recorded; ?></li>
                            <li><h4>Low:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $pressure_low_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_low inHg ($yesterday->pressure_kPa_low kPa)" : "$yesterday->pressure_kPa_low kPa ($yesterday->pressure_inHg_low inHg)";
                                } else {
                                    $pressure_low_yesterday = ($config->site->imperial === true) ? "$yesterday->pressure_inHg_low inHg" : "$yesterday->pressure_kPa_low kPa";

                                }
                                echo $pressure_low_yesterday . ' @ ' . $yesterday->pressure_inHg_low_recorded; ?></li>
                        </ul>
                        <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?= "$yesterday->relH_high% @ " . $yesterday->relH_high_recorded; ?></li>
                            <li><h4>Low:</h4> <?= "$yesterday->relH_low% @ " . $yesterday->relH_low_recorded; ?></li>
                        </ul>
                        <?php if ($yesterday->rainfall_IN_total !== 0.) { ?>
                            <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                            <ul class="list-unstyled">
                                <li><h4>Total Rain:</h4> <?php
                                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                        $rain_total_yesterday = ($config->site->imperial === true) ? "$yesterday->rainfall_IN_total in ($yesterday->rainfall_MM_total mm)" : "$yesterday->rainfall_MM_total mm ($yesterday->rainfall_IN_total in)";
                                    } else {
                                        $rain_total_yesterday = ($config->site->imperial === true) ? "$yesterday->rainfall_IN_total in" : "$yesterday->rainfall_MM_total mm";
                                    }
                                    echo $rain_total_yesterday; ?></li>
                            </ul>
                        <?php }
                        if ($config->station->primary_sensor === 0) { ?>
                            <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        High:</h4> <?= "$atlasYesterday->uvindex_high @ " . $atlasYesterday->uvindex_high_recorded; ?>
                                </li>
                            </ul>

                            <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                            <ul class="list-unstyled">
                                <li><h4>Illuminance
                                        High:</h4> <?= "$atlasYesterday->light_high @ " . $atlasYesterday->light_high_recorded; ?>
                                </li>
                                <li><h4>Measured
                                        High:</h4> <?= "$atlasYesterday->lightHours_high hours @ " . $atlasYesterday->lightHours_high_recorded; ?>
                                </li>
                            </ul>
                            <?php if ($atlasYesterday->lightning !== 0) { ?>
                                <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                                <ul class="list-unstyled">
                                    <li><h4>
                                            Strikes:</h4> <?= "$atlasYesterday->lightning @ " . $atlasYesterday->lightning_recorded; ?>
                                    </li>
                                </ul>
                            <?php }
                        }
                    } else {
                        echo '<h3>No Data</h3>';
                    } ?>
                </section>

                <!-- This Week -->
                <section id="archive-weather-data-week" class="col-md-4 col-12">
                    <h2>This Week</h2>
                    <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_high_week = ($config->site->imperial === true) ? "$week->tempF_high&#8457; ($week->tempC_high&#8451;)" : "$week->tempC_high&#8451; ($week->tempF_high&#8457;)";
                            } else {
                                $temp_high_week = ($config->site->imperial === true) ? "$week->tempF_high&#8457;" : "$week->tempC_high&#8451;";
                            }
                            echo $temp_high_week . ' on ' . $week->tempF_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_low_week = ($config->site->imperial === true) ? "$week->tempF_low&#8457; ($week->tempC_low&#8451;)" : "$week->tempC_low&#8451; ($week->tempF_low&#8457;)";
                            } else {
                                $temp_low_week = ($config->site->imperial === true) ? "$week->tempF_low&#8457;" : "$week->tempC_low&#8451;";
                            }
                            echo $temp_low_week . ' on ' . $week->tempF_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                    <p><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                            $wind_high_week = ($config->site->imperial === true) ? "$week->windS_mph_high mph ($week->windS_kmh_high km/h)" : "$week->windS_kmh_high km/h ($week->windS_mph_high mph)";
                        } else {
                            $wind_high_week = ($config->site->imperial === true) ? "$week->windS_mph_high mph" : "$week->windS_kmh_high km/h";
                        }
                        echo 'From ' . $week->windDIR . ' at ' . $wind_high_week . ' on ' . $week->windS_mph_high_recorded; ?></p>
                    <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_high_week = ($config->site->imperial === true) ? "$week->pressure_inHg_high inHg ($week->pressure_kPa_high kPa)" : "$week->pressure_kPa_high kPa ($week->pressure_inHg_high inHg)";
                            } else {
                                $pressure_high_week = ($config->site->imperial === true) ? "$week->pressure_inHg_high inHg" : "$week->pressure_kPa_high kPa";
                            }
                            echo $pressure_high_week . ' on ' . $week->pressure_inHg_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_low_week = ($config->site->imperial === true) ? "$week->pressure_inHg_low inHg ($week->pressure_kPa_low kPa)" : "$week->pressure_kPa_low kPa ($week->pressure_inHg_low inHg)";
                            } else {
                                $pressure_low_week = ($config->site->imperial === true) ? "$week->pressure_inHg_low inHg" : "$week->pressure_kPa_low kPa";
                            }
                            echo $pressure_low_week . ' on ' . $week->pressure_inHg_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?= "$week->relH_high% on " . $week->relH_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?= "$week->relH_low% on " . $week->relH_low_recorded; ?></li>
                    </ul>
                    <?php if ($week->rainfall_IN_total !== 0.) { ?>
                        <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                        <ul class="list-unstyled">
                            <li><h4>Most Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $most_rain_week = ($config->site->imperial === true) ? "$week->rainfall_IN_most in ($week->rainfall_MM_most mm)" : "$week->rainfall_MM_most mm ($week->rainfall_IN_most in)";
                                } else {
                                    $most_rain_week = ($config->site->imperial === true) ? "$week->rainfall_IN_most in" : "$week->rainfall_MM_most mm";
                                }
                                echo $most_rain_week . ' on ' . $month->rainfall_IN_most_recorded; ?></li>
                            <li><h4>Total Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $rain_total_week = ($config->site->imperial === true) ? "$week->rainfall_IN_total in ($week->rainfall_MM_total mm)" : "$week->rainfall_MM_total mm ($week->rainfall_IN_total in)";
                                } else {
                                    $rain_total_week = ($config->site->imperial === true) ? "$week->rainfall_IN_total in" : "$week->rainfall_MM_total mm";
                                }
                                echo $rain_total_week; ?></li>
                        </ul>
                    <?php }
                    if ($config->station->primary_sensor === 0) { ?>
                        <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                        <ul class="list-unstyled">
                            <li><h4>
                                    High:</h4> <?= "$atlasWeek->uvindex_high on " . $atlasWeek->uvindex_high_recorded; ?>
                            </li>
                        </ul>

                        <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                        <ul class="list-unstyled">
                            <li><h4>Illuminance
                                    High:</h4> <?= "$atlasWeek->light_high on " . $atlasWeek->light_high_recorded; ?>
                            </li>
                            <li><h4>Measured
                                    High:</h4> <?= "$atlasWeek->lightHours_high hours on " . $atlasWeek->lightHours_high_recorded; ?>
                            </li>
                        </ul>
                        <?php if ($atlasWeek->lightning !== 0) { ?>
                            <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        Strikes:</h4> <?= "$atlasWeek->lightning on " . $atlasWeek->lightning_recorded; ?>
                                </li>
                            </ul>
                        <?php }
                    } ?>
                </section>

                <!-- This Month -->
                <section id="archive-weather-data-month" class="col-md-4 col-12">
                    <h2>This Month</h2>
                    <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_high_month = ($config->site->imperial === true) ? "$month->tempF_high&#8457; ($month->tempC_high&#8451;)" : "$month->tempC_high&#8451; ($month->tempF_high&#8457;)";
                            } else {
                                $temp_high_month = ($config->site->imperial === true) ? "$month->tempF_high&#8457;" : "$month->tempC_high&#8451;";
                            }
                            echo $temp_high_month . ' on ' . $month->tempF_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_low_month = ($config->site->imperial === true) ? "$month->tempF_low&#8457; ($month->tempC_low&#8451;)" : "$month->tempC_low&#8451; ($month->tempF_low&#8457;)";
                            } else {
                                $temp_low_month = ($config->site->imperial === true) ? "$month->tempF_low&#8457;" : "$month->tempC_low&#8451;";
                            }
                            echo $temp_low_month . ' on ' . $month->tempF_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                    <p><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                            $wind_high_month = ($config->site->imperial === true) ? "$month->windS_mph_high mph ($month->windS_kmh_high km/h)" : "$month->windS_kmh_high km/h ($month->windS_mph_high mph)";
                        } else {
                            $wind_high_month = ($config->site->imperial === true) ? "$month->windS_mph_high mph" : "$month->windS_kmh_high km/h";
                        }
                        echo 'From ' . $month->windDIR . ' at ' . $wind_high_month . ' on ' . $month->windS_mph_high_recorded; ?></p>
                    <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_high_month = ($config->site->imperial === true) ? "$month->pressure_inHg_high inHg ($month->pressure_kPa_high kPa)" : "$month->pressure_kPa_high kPa ($month->pressure_inHg_high inHg)";
                            } else {
                                $pressure_high_month = ($config->site->imperial === true) ? "$month->pressure_inHg_high inHg" : "$month->pressure_kPa_high kPa";
                            }
                            echo $pressure_high_month . ' on ' . $month->pressure_inHg_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_low_month = ($config->site->imperial === true) ? "$month->pressure_inHg_low inHg ($month->pressure_kPa_low kPa)" : "$month->pressure_kPa_low kPa ($month->pressure_inHg_low inHg)";
                            } else {
                                $pressure_low_month = ($config->site->imperial === true) ? "$month->pressure_inHg_low inHg" : "$month->pressure_kPa_low kPa";
                            }
                            echo $pressure_low_month . ' on ' . $month->pressure_inHg_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?= "$month->relH_high% on " . $month->relH_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?= "$month->relH_low% on " . $month->relH_low_recorded; ?></li>
                    </ul>
                    <?php if ($month->rainfall_IN_total !== 0.) { ?>
                        <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                        <ul class="list-unstyled">
                            <li><h4>Most Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $most_rain_month = ($config->site->imperial === true) ? "$month->rainfall_IN_most in ($month->rainfall_MM_most mm)" : "$month->rainfall_MM_most mm ($month->rainfall_IN_most in)";
                                } else {
                                    $most_rain_month = ($config->site->imperial === true) ? "$month->rainfall_IN_most in" : "$month->rainfall_MM_most mm";
                                }
                                echo $most_rain_month . ' on ' . $month->rainfall_IN_most_recorded; ?></li>
                            <li><h4>Total Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $rain_total_month = ($config->site->imperial === true) ? "$month->rainfall_IN_total in ($month->rainfall_MM_total mm)" : "$month->rainfall_MM_total mm ($month->rainfall_IN_total in)";
                                } else {
                                    $rain_total_month = ($config->site->imperial === true) ? "$month->rainfall_IN_total in" : "$month->rainfall_MM_total mm";
                                }
                                echo $rain_total_month; ?></li>
                        </ul>
                    <?php }
                    if ($config->station->primary_sensor === 0) { ?>
                        <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                        <ul class="list-unstyled">
                            <li><h4>
                                    High:</h4> <?= "$atlasMonth->uvindex_high on " . $atlasMonth->uvindex_high_recorded; ?>
                            </li>
                        </ul>

                        <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                        <ul class="list-unstyled">
                            <li><h4>Illuminance
                                    High:</h4> <?= "$atlasMonth->light_high on " . $atlasWeek->light_high_recorded; ?>
                            </li>
                            <li><h4>Measured
                                    High:</h4> <?= "$atlasMonth->lightHours_high hours on " . $atlasWeek->lightHours_high_recorded; ?>
                            </li>
                        </ul>
                        <?php if ($atlasMonth->lightning !== 0) { ?>
                            <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        Strikes:</h4> <?= "$atlasMonth->lightning on " . $atlasMonth->lightning_recorded; ?>
                                </li>
                            </ul>
                        <?php }
                    } ?>
                </section>
            </div>

            <hr class="hr-dashed">

            <div class="row">
                <!-- Last Month -->
                <section id="archive-weather-data-last-month" class="col-md-4 col-12">
                    <h2>Last Month</h2>
                    <?php if (isset($last_month->tempF_high)) { ?>

                        <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $temp_high_last_month = ($config->site->imperial === true) ? "$last_month->tempF_high&#8457; ($last_month->tempC_high&#8451;)" : "$last_month->tempC_high&#8451; ($last_month->tempF_high&#8457;)";
                                } else {
                                    $temp_high_last_month = ($config->site->imperial === true) ? "$last_month->tempF_high&#8457;" : "$last_month->tempC_high&#8451;";
                                }
                                echo $temp_high_last_month . ' on ' . $last_month->tempF_high_recorded; ?></li>
                            <li><h4>Low:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $temp_low_last_month = ($config->site->imperial === true) ? "$last_month->tempF_low&#8457; ($last_month->tempC_low&#8451;)" : "$last_month->tempC_low&#8451; ($last_month->tempF_low&#8457;)";
                                } else {
                                    $temp_low_last_month = ($config->site->imperial === true) ? "$last_month->tempF_low&#8457;" : "$last_month->tempC_low&#8451;";
                                }
                                echo $temp_low_last_month . ' on ' . $last_month->tempF_low_recorded; ?></li>
                        </ul>
                        <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                        <p><?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $wind_high_last_month = ($config->site->imperial === true) ? "$last_month->windS_mph_high mph ($last_month->windS_kmh_high km/h)" : "$last_month->windS_kmh_high km/h ($last_month->windS_mph_high mph)";
                            } else {
                                $wind_high_last_month = ($config->site->imperial === true) ? "$last_month->windS_mph_high mph" : "$last_month->windS_kmh_high km/h";
                            }
                            echo 'From ' . $last_month->windDIR . ' at ' . $wind_high_last_month . ' on ' . $last_month->windS_mph_high_recorded; ?></p>
                        <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $pressure_high_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_high inHg ($last_month->pressure_kPa_high kPa)" : "$last_month->pressure_kPa_high kPa ($last_month->pressure_inHg_high inHg)";
                                } else {
                                    $pressure_high_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_high inHg" : "$last_month->pressure_kPa_high kPa";
                                }
                                echo $pressure_high_last_month . ' on ' . $last_month->pressure_inHg_high_recorded; ?>
                            </li>
                            <li><h4>Low:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $pressure_low_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_low inHg ($last_month->pressure_kPa_low kPa)" : "$last_month->pressure_kPa_low kPa ($last_month->pressure_inHg_low inHg)";
                                } else {
                                    $pressure_low_last_month = ($config->site->imperial === true) ? "$last_month->pressure_inHg_low inHg" : "$last_month->pressure_kPa_low kPa";
                                }
                                echo $pressure_low_last_month . ' on ' . $last_month->pressure_inHg_low_recorded; ?>
                            </li>
                        </ul>
                        <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                        <ul class="list-unstyled">
                            <li><h4>High:</h4> <?= "$last_month->relH_high% on " . $last_month->relH_high_recorded; ?>
                            </li>
                            <li><h4>Low:</h4> <?= "$last_month->relH_low% on " . $last_month->relH_low_recorded; ?></li>
                        </ul>
                        <?php if ($last_month->rainfall_IN_total !== 0.) { ?>
                            <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                            <ul class="list-unstyled">
                                <li><h4>Most Rain:</h4> <?php
                                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                        $most_rain_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_most in ($last_month->rainfall_MM_most mm)" : "$last_month->rainfall_MM_most mm ($last_month->rainfall_IN_most in)";
                                    } else {
                                        $most_rain_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_most in" : "$last_month->rainfall_MM_most mm";
                                    }
                                    echo $most_rain_last_month . ' on ' . $last_month->rainfall_IN_most_recorded; ?>
                                </li>
                                <li><h4>Total Rain:</h4> <?php
                                    if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                        $rain_total_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_total in ($last_month->rainfall_MM_total mm)" : "$last_month->rainfall_MM_total mm ($last_month->rainfall_IN_total in)";
                                    } else {
                                        $rain_total_last_month = ($config->site->imperial === true) ? "$last_month->rainfall_IN_total in" : "$last_month->rainfall_MM_total mm";
                                    }
                                    echo $rain_total_last_month; ?></li>
                            </ul>
                        <?php }
                        if ($config->station->primary_sensor === 0) { ?>
                            <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        High:</h4> <?= "$atlasLastMonth->uvindex_high on " . $atlasLastMonth->uvindex_high_recorded; ?>
                                </li>
                            </ul>

                            <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                            <ul class="list-unstyled">
                                <li><h4>Illuminance
                                        High:</h4> <?= "$atlasLastMonth->light_high on " . $atlasWeek->light_high_recorded; ?>
                                </li>
                                <li><h4>Measured
                                        High:</h4> <?= "$atlasLastMonth->lightHours_high hours on " . $atlasWeek->lightHours_high_recorded; ?>
                                </li>
                            </ul>
                            <?php if ($atlasLastMonth->lightning !== 0) { ?>
                                <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                                <ul class="list-unstyled">
                                    <li><h4>
                                            Strikes:</h4> <?= "$atlasLastMonth->lightning on " . $atlasLastMonth->lightning_recorded; ?>
                                    </li>
                                </ul>
                            <?php }
                        }
                    } else {
                        echo '<h3>No Data</h3>';
                    } ?>
                </section>

                <!-- This Year -->
                <section id="archive-weather-data-year" class="col-md-4 col-12">
                    <h2>This Year</h2>
                    <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_high_year = ($config->site->imperial === true) ? "$year->tempF_high&#8457; ($year->tempC_high&#8451;)" : "$year->tempC_high&#8451; ($year->tempF_high&#8457;)";
                            } else {
                                $temp_high_year = ($config->site->imperial === true) ? "$year->tempF_high&#8457;" : "$year->tempC_high&#8451;";
                            }
                            echo $temp_high_year . ' on ' . $year->tempF_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_low_year = ($config->site->imperial === true) ? "$year->tempF_low&#8457; ($year->tempC_low&#8451;)" : "$year->tempC_low&#8451; ($year->tempF_low&#8457;)";
                            } else {
                                $temp_low_year = ($config->site->imperial === true) ? "$year->tempF_low&#8457;" : "$year->tempC_low&#8451;";
                            }
                            echo $temp_low_year . ' on ' . $year->tempF_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                    <p><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                            $wind_high_year = ($config->site->imperial === true) ? "$year->windS_mph_high mph ($year->windS_kmh_high km/h)" : "$year->windS_kmh_high km/h ($year->windS_mph_high mph)";
                        } else {
                            $wind_high_year = ($config->site->imperial === true) ? "$year->windS_mph_high mph" : "$year->windS_kmh_high km/h";
                        }
                        echo 'From ' . $year->windDIR . ' at ' . $wind_high_year . ' on ' . $year->windS_mph_high_recorded; ?></p>
                    <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_high_year = ($config->site->imperial === true) ? "$year->pressure_inHg_high inHg ($year->pressure_kPa_high kPa)" : "$year->pressure_kPa_high kPa ($year->pressure_inHg_high inHg)";
                            } else {
                                $pressure_high_year = ($config->site->imperial === true) ? "$year->pressure_inHg_high inHg" : "$year->pressure_kPa_high kPa";
                            }
                            echo $pressure_high_year . ' on ' . $year->pressure_inHg_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_low_year = ($config->site->imperial === true) ? "$year->pressure_inHg_low inHg ($year->pressure_kPa_low kPa)" : "$year->pressure_kPa_low kPa ($year->pressure_inHg_low inHg)";
                            } else {
                                $pressure_low_year = ($config->site->imperial === true) ? "$year->pressure_inHg_low inHg" : "$year->pressure_kPa_low kPa";
                            }
                            echo $pressure_low_year . ' on ' . $year->pressure_inHg_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?= "$year->relH_high% on " . $year->relH_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?= "$year->relH_low% on " . $year->relH_low_recorded; ?></li>
                    </ul>
                    <?php if ($year->rainfall_IN_total !== 0.) { ?>
                        <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                        <ul class="list-unstyled">
                            <li><h4>Most Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $most_rain_year = ($config->site->imperial === true) ? "$year->rainfall_IN_most in ($year->rainfall_MM_most mm)" : "$year->rainfall_MM_most mm ($year->rainfall_IN_most in)";
                                } else {
                                    $most_rain_year = ($config->site->imperial === true) ? "$year->rainfall_IN_most in" : "$year->rainfall_MM_most mm";
                                }
                                echo $most_rain_year . ' on ' . $year->rainfall_IN_most_recorded; ?></li>
                            <li><h4>Total Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $rain_total_year = ($config->site->imperial === true) ? "$year->rainfall_IN_total in ($year->rainfall_MM_total mm)" : "$year->rainfall_MM_total mm ($year->rainfall_IN_total in)";
                                } else {
                                    $rain_total_year = ($config->site->imperial === true) ? "$year->rainfall_IN_total in" : "$year->rainfall_MM_total mm";
                                }
                                echo $rain_total_year; ?></li>
                        </ul>
                    <?php }
                    if ($config->station->primary_sensor === 0) { ?>
                        <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                        <ul class="list-unstyled">
                            <li><h4>
                                    High:</h4> <?= "$atlasYear->uvindex_high on " . $atlasYear->uvindex_high_recorded; ?>
                            </li>
                        </ul>

                        <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                        <ul class="list-unstyled">
                            <li><h4>Illuminance
                                    High:</h4> <?= "$atlasYear->light_high on " . $atlasWeek->light_high_recorded; ?>
                            </li>
                            <li><h4>Measured
                                    High:</h4> <?= "$atlasYear->lightHours_high hours on " . $atlasWeek->lightHours_high_recorded; ?>
                            </li>
                        </ul>
                        <?php if ($atlasYear->lightning !== 0) { ?>
                            <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        Strikes:</h4> <?= "$atlasYear->lightning on " . $atlasYear->lightning_recorded; ?>
                                </li>
                            </ul>
                        <?php }
                    } ?>
                </section>

                <!-- All Time -->
                <section id="archive-weather-data-all" class="col-md-4 col-12">
                    <h2 class="panel-heading">All Time</h2>
                    <h3><i class="fas fa-thermometer-half" aria-hidden="true"></i> Temperature</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_high_ever = ($config->site->imperial === true) ? "$ever->tempF_high&#8457; ($ever->tempC_high&#8451;)" : "$ever->tempC_high&#8451; ($ever->tempF_high&#8457;)";
                            } else {
                                $temp_high_ever = ($config->site->imperial === true) ? "$ever->tempF_high&#8457;" : "$ever->tempC_high&#8451;";
                            }
                            echo $temp_high_ever . ' on ' . $ever->tempF_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $temp_low_ever = ($config->site->imperial === true) ? "$ever->tempF_low&#8457; ($ever->tempC_low&#8451;)" : "$ever->tempC_low&#8451; ($ever->tempF_low&#8457;)";
                            } else {
                                $temp_low_ever = ($config->site->imperial === true) ? "$ever->tempF_low&#8457;" : "$ever->tempC_low&#8451;";
                            }
                            echo $temp_low_ever . ' on ' . $ever->tempF_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-windy" aria-hidden="true"></i> Wind</h3>
                    <p><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                            $wind_high_ever = ($config->site->imperial === true) ? "$ever->windS_mph_high mph ($ever->windS_kmh_high km/h)" : "$ever->windS_kmh_high km/h ($ever->windS_mph_high mph)";
                        } else {
                            $wind_high_ever = ($config->site->imperial === true) ? "$ever->windS_mph_high mph" : "$ever->windS_kmh_high km/h";
                        }
                        echo 'From ' . $ever->windDIR . ' at ' . $wind_high_ever . ' on ' . $ever->windS_mph_high_recorded; ?></p>
                    <h3 class="margin-top-05"><i class="wi wi-barometer" aria-hidden="true"></i> Pressure</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_high_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_high inHg ($ever->pressure_kPa_high kPa)" : "$ever->pressure_kPa_high kPa ($ever->pressure_inHg_high inHg)";
                            } else {
                                $pressure_high_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_high inHg" : "$ever->pressure_kPa_high kPa";
                            }
                            echo $pressure_high_ever . ' on ' . $ever->pressure_inHg_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                $pressure_low_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_low inHg ($ever->pressure_kPa_low kPa)" : "$ever->pressure_kPa_low kPa ($ever->pressure_inHg_low inHg)";
                            } else {
                                $pressure_low_ever = ($config->site->imperial === true) ? "$ever->pressure_inHg_low inHg" : "$ever->pressure_kPa_low kPa";
                            }
                            echo $pressure_low_ever . ' on ' . $ever->pressure_inHg_low_recorded; ?></li>
                    </ul>
                    <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity</h3>
                    <ul class="list-unstyled">
                        <li><h4>High:</h4> <?= "$ever->relH_high% on " . $ever->relH_high_recorded; ?></li>
                        <li><h4>Low:</h4> <?= "$ever->relH_low% on " . $ever->relH_low_recorded; ?></li>
                    </ul>
                    <?php if ($ever->rainfall_IN_total !== 0.) { ?>
                        <h3><i class="wi wi-raindrops" aria-hidden="true"></i> Rainfall</h3>
                        <ul class="list-unstyled">
                            <li><h4>Most Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $most_rain_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_most in ($ever->rainfall_MM_most mm)" : "$ever->rainfall_MM_most mm ($ever->rainfall_IN_most in)";
                                } else {
                                    $most_rain_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_most in" : "$ever->rainfall_MM_most mm";
                                }
                                echo $most_rain_ever . ' on ' . $ever->rainfall_IN_most_recorded; ?></li>
                            <li><h4>Total Rain:</h4> <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'live') {
                                    $rain_total_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_total in ($ever->rainfall_MM_total mm)" : "$ever->rainfall_MM_total mm ($ever->rainfall_IN_total in)";
                                } else {
                                    $rain_total_ever = ($config->site->imperial === true) ? "$ever->rainfall_IN_total in" : "$ever->rainfall_MM_total mm";
                                }
                                echo $rain_total_ever; ?></li>
                        </ul>
                    <?php }
                    if ($config->station->primary_sensor === 0) { ?>
                        <h3><i class="wi wi-hot" aria-hidden="true"></i> UV Index</h3>
                        <ul class="list-unstyled">
                            <li><h4>
                                    High:</h4> <?= "$atlasEver->uvindex_high on " . $atlasEver->uvindex_high_recorded; ?>
                            </li>
                        </ul>

                        <h3><i class="fas fa-lightbulb" aria-hidden="true"></i> Light</h3>
                        <ul class="list-unstyled">
                            <li><h4>Illuminance
                                    High:</h4> <?= "$atlasEver->light_high on " . $atlasEver->light_high_recorded; ?>
                            </li>
                            <li><h4>Measured
                                    High:</h4> <?= "$atlasEver->lightHours_high hours on " . $atlasEver->lightHours_high_recorded; ?>
                            </li>
                        </ul>
                        <?php if ($atlasEver->lightning !== 0) { ?>
                            <h3><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h3>
                            <ul class="list-unstyled">
                                <li><h4>
                                        Strikes:</h4> <?= "$atlasEver->lightning on " . $atlasEver->lightning_recorded; ?>
                                </li>
                            </ul>
                        <?php }
                    } ?>
                </section>
            </div>
        </div>
    </div>
    <?php
}
