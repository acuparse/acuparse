<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2018 Maxwell Power
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
 * File: src/fcn/weather/getCurrentHTML.php
 * Get current weather conditions HTML
 */

function getCurrentHTML()
{
    // Get the loader
    require(dirname(dirname(__DIR__)) . '/inc/loader.php');

    // Load weather Data:
    require('GetCurrentWeatherData.php');
    $GetData = new GetCurrentWeatherData();
    $wx = $GetData->getConditions();

    // Get Moon Data:
    require(APP_BASE_PATH . '/pub/lib/mit/moon/moonphase.php');
    $moon = new MoonPhase();
    $moon_age = round($moon->age(), 1);
    $moon_stage = $moon->phase_name();
    $next_new_moon = date('j M @ H:i', $moon->next_new_moon());
    $next_full_moon = date('j M @ H:i', $moon->next_full_moon());
    $last_new_moon = date('j M @ H:i', $moon->new_moon());
    $last_full_moon = date('j M @ H:i', $moon->full_moon());
    function percent($number)
    {
        return $number * 100 . '%';
    }

    $moon_illumination = percent(round($moon->illumination(), 2));

    // Get icons:
    require('weatherIcons.php');

    // Moon rise/set
    $moon_time_enabled = false;
    if (file_exists(APP_BASE_PATH . '/pub/lib/gpl/moon/moontime.php')) {
        $moon_time_enabled = true;
        require(APP_BASE_PATH . '/pub/lib/gpl/moon/moontime.php');
        $moon_time = Moon::calculateMoonTimes($config->site->lat, $config->site->long);
        $moon_rise = gmdate('j M @ H:i', $moon_time->moonrise);
        $moon_set = gmdate('j M @ H:i', $moon_time->moonset);
    }
    // Get Sun Data
    $zenith = 90 + (50 / 60);
    $offset = date('Z') / 3600;
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_STRING, $config->site->lat, $config->site->long, $zenith, $offset);
    $sunset = date_sunset(time(), SUNFUNCS_RET_STRING, $config->site->lat, $config->site->long, $zenith, $offset);

    ?>
    <section id="weather_data">
        <div class="row row_weather_data">

            <!-- Left Column -->
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                <!-- Temperature Data -->
                <div class="row row_temperature_data">
                    <h2><i class="fas <?= tempIcon($wx->tempC); ?>" aria-hidden="true"></i> Temperature:</h2>
                    <h4><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457; ($wx->tempC&#8451;)" : "$wx->tempC&#8451; ($wx->tempF&#8457;)";
                        } else {
                            $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457;" : "$wx->tempC&#8451;";
                        }
                        echo $temp . trendIcon($wx->tempF_trend) ?></h4>

                    <!-- Feels Like -->
                    <ul class="list-unstyled">
                        <?php if ($wx->feelsF != 0) {
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457; ($wx->feelsC&#8451;)" : "$wx->feelsC&#8451; ($wx->feelsF&#8457;)";
                            } else {
                                $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457;" : "$wx->feelsC&#8451;";
                            }
                            echo "<li><strong>Feels Like:</strong> " . $feels . "</li>";
                        } ?>

                        <!-- Daily Low -->
                        <li><strong>Low:</strong>
                            <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457; ($wx->tempC_low&#8451;)" : "$wx->tempC_low&#8451; ($wx->tempF_low&#8457;)";
                            } else {
                                $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457;" : "$wx->tempC_low&#8451;";
                            }
                            echo $temp_low . " @ $wx->low_temp_recorded"; ?></li>

                        <!-- Daily High -->
                        <li><strong>High:</strong>
                            <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457; ($wx->tempC_high&#8451;)" : "$wx->tempC_high&#8451; ($wx->tempF_high&#8457;)";
                            } else {
                                $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457;" : "$wx->tempC_high&#8451;";
                            }
                            echo $temp_high . " @ $wx->high_temp_recorded"; ?></li>

                        <!-- Average -->
                        <li><strong>Average:</strong> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457; ($wx->tempC_avg&#8451;)" : "$wx->tempC_avg&#8451; ($wx->tempF_avg&#8457;)";
                            } else {
                                $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457;" : "$wx->tempC_avg&#8451;";
                            }
                            echo $temp_avg; ?></li>

                        <!-- Dew Point -->
                        <li><strong>Dew Point:</strong> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457; ($wx->dewptC&#8451;)" : "$wx->dewptC&#8451; ($wx->dewptF&#8457;)";
                            } else {
                                $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457;" : "$wx->dewptC&#8451;";
                            }
                            echo $dewpt; ?></li>
                    </ul>
                </div>

                <!-- Wind Data -->
                <div class="row row_wind_data">
                    <h2><?php if ($wx->windSkmh >= 25) {
                            echo ' <i class="wi wi-strong-wind" aria-hidden="true"></i>';
                        } elseif ($wx->windSkmh < 25) {
                            if ($wx->windSkmh >= 10) {
                                echo ' <i class="wi wi-windy" aria-hidden="true"></i> ';
                            }
                        }
                        echo '<i class="wi wi-wind wi-from-', strtolower($wx->windDIR), '" aria-hidden="true"></i>'; ?>
                        Wind:</h2>
                    <h4>from <?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $wind = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSmph mph ($wx->windSkmh km/h)" : "$wx->windDIR @ $wx->windSkmh km/h ($wx->windSmph mph)";
                        } else {
                            $wind = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSmph mph" : "$wx->windDIR @ $wx->windSkmh km/h";
                        }
                        echo $wind; ?></h4>
                    <ul class="list-unstyled">
                        <li><strong>Average:</strong> from <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $wind_avg = ($config->site->imperial === true) ? "$wx->windDIR_avg2 @ $wx->windSmph_avg2 mph ($wx->windSkmh_avg2 km/h)" : "$wx->windDIR_avg2 @ $wx->windSkmh_avg2 km/h ($wx->windSmph_avg2 mph)";
                            } else {
                                $wind_avg = ($config->site->imperial === true) ? "$wx->windDIR_avg2 @ $wx->windSmph_avg2 mph" : "$wx->windDIR_avg2 @ $wx->windSkmh_avg2 km/h";
                            }
                            echo $wind_avg; ?></li>
                        <li><strong>Peak:</strong>
                            <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $wind_peak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSmph_peak mph ($wx->windSkmh_peak km/h)" : "$wx->windDIR_peak @ $wx->windSkmh_peak km/h ($wx->windSmph_peak mph)";
                            } else {
                                $wind_peak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSmph_peak mph" : "$wx->windDIR_peak @ $wx->windSkmh_peak km/h";
                            }
                            echo $wind_peak . ' @ ' . $wx->wind_recorded_peak; ?></li>
                    </ul>
                </div>
            </div> <!-- END Left Column -->

            <!-- Middle Column -->
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                <!-- Humidity -->
                <div class="row row_humidity_data">
                    <h2><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h2>
                    <h4><?= $wx->relH, '%', trendIcon($wx->relH_trend); ?></h4>
                </div>

                <!-- Pressure -->
                <div class="row row_pressure_data">
                    <h2><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h2>
                    <h4><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $pressure = ($config->site->imperial === true) ? "$wx->pressure_inHg inHg ($wx->pressure_kPa kPa)" : "$wx->pressure_kPa kPa ($wx->pressure_inHg inHg)";
                        } else {
                            $pressure = ($config->site->imperial === true) ? "$wx->pressure_inHg inHg" : "$wx->pressure_kPa kPa";
                        }
                        echo $pressure . trendIcon($wx->inHg_trend); ?></h4>
                </div>

                <!-- Rain -->
                <div class="row row_rain_data">
                    <h2><i class="wi wi-raindrops" aria-hidden="true"></i> Rain:</h2>
                    <ul class="list-unstyled">
                        <li><strong>Fall Rate:</strong> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $rain = ($config->site->imperial === true) ? "$wx->rainIN in/hr ($wx->rainMM mm/hr)" : "$wx->rainMM mm/hr ($wx->rainIN in/hr)";
                            } else {
                                $rain = ($config->site->imperial === true) ? "$wx->rainIN in/hr" : "$wx->rainMM mm/hr";
                            }
                            echo $rain; ?></li>
                        <li><strong>Daily Total:</strong> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $rain_today = ($config->site->imperial === true) ? "$wx->rainTotalIN_today in ($wx->rainTotalMM_today mm)" : "$wx->rainTotalMM_today mm ($wx->rainTotalIN_today in)";
                            } else {
                                $rain_today = ($config->site->imperial === true) ? "$wx->rainTotalIN_today in" : "$wx->rainTotalMM_today mm";
                            }
                            echo $rain_today; ?></li>
                    </ul>
                </div>
            </div> <!-- END Middle Column -->
            <?php
            // Done with Weather data

            // Show environment details
            ?>

            <!-- Right Column -->
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                <!-- Sun -->
                <div class="row row_sun_data">
                    <h2><i class="wi wi-day-sunny" aria-hidden="true"></i> Sun:</h2>
                    <ul class="list-unstyled">
                        <li><i class="wi wi-sunrise" aria-hidden="true"></i>
                            <strong>Sunrise:</strong> <?= $sunrise; ?></li>
                        <li><i class="wi wi-sunset" aria-hidden="true"></i>
                            <strong>Sunset:</strong> <?= $sunset; ?></li>
                    </ul>
                </div> <!-- END Sun -->

                <!-- Moon -->
                <div class="row row_moon_data">
                    <h2><i class="wi <?= moonIcon($moon_stage); ?>" aria-hidden="true"></i> Moon:</h2>
                    <h4><?= "$moon_stage"; ?></h4>
                    <p><?= "$moon_age days old, $moon_illumination visible"; ?></p>
                    <ul class="list-unstyled">
                        <?php if ($moon_time_enabled === true) { ?>
                            <li><i class="wi wi-moonrise" aria-hidden="true"></i>
                                <strong>Moonrise:</strong> <?= $moon_rise; ?></li>
                            <li><i class="wi wi-moonset" aria-hidden="true"></i>
                                <strong>Moonset:</strong> <?= $moon_set; ?></li>
                        <?php } ?>
                        <li><strong>Current New:</strong> <?= $last_new_moon; ?></li>
                        <li><strong>Current Full:</strong> <?= $last_full_moon; ?></li>
                        <li><strong>Upcoming New:</strong> <?= $next_new_moon; ?></li>
                        <li><strong>Upcoming Full:</strong> <?= $next_full_moon; ?></li>
                    </ul>
                </div> <!-- END Moon -->
            </div> <!-- END Right Column -->
        </div>
    </section>

    <?php

    // If tower sensors are active show the tower data
    if ($config->station->towers === true) {

        // Can we display private data?
        if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true) {
            $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange`");
        } else {
            $result = mysqli_query($conn, "SELECT * FROM `towers` WHERE `private` = 0 ORDER BY `arrange`");
        }

        // Is there data to show? If yes, show it.
        if (mysqli_num_rows($result) >= 1) { ?>
            <hr class="hr-dashed">
            <section id="tower_data">
                <div class="row row_tower_data">
                    <?php
                    $counter = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sensor = $row['sensor'];
                        $result2 = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT * FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
                        $tempF = round($result2['tempF'], 1);
                        $tempC = round(($result2['tempF'] - 32) * 5 / 9, 1);
                        $relH = $result2['relH'];

                        // Temp Trending
                        $tempF_trend = trendIcon($GetData->calculateTrend('tempF', 'tower_data', $sensor));

                        // Humidity Trending
                        $relH_trend = trendIcon($GetData->calculateTrend('relH', 'tower_data', $sensor));

                        ?>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12"> <!-- 1/4th Column -->
                            <h2 class="panel-heading"><?= $row['name']; ?>:</h2>
                            <h3><i class="fas <?= tempIcon($tempC); ?>" aria-hidden="true"></i> Temperature:</h3>
                            <h4><?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                    $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; ($tempC&#8451;) $tempF_trend" : "$tempC&#8451; ($tempF&#8457;) $tempF_trend";
                                } else {
                                    $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; $tempF_trend" : "$tempC&#8451; $tempF_trend";
                                }
                                echo $tower_temp ?></h4>
                            <h3><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h3>
                            <h4><?= "$relH% $relH_trend"; ?></h4>
                        </div> <!-- END 1/4th Column -->
                        <?php

                        // Apply clearfixes to keep columns in place
                        $counter++;
                        if ($counter % 2 === 0) {
                            echo '<div class="clearfix visible-sm-block"></div>';
                        }
                        if ($counter % 4 === 0) {
                            echo '<div class="clearfix visible-md-block visible-lg-block"></div>';
                        }
                    }
                    ?>
                </div>
            </section>
            <?php
        }
    }
}
