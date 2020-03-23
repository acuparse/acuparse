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
 * File: src/fcn/weather/getCurrentHTML.php
 * Get current weather conditions HTML
 */

function getCurrentHTML()
{
    // Get the loader
    require(dirname(dirname(__DIR__)) . '/inc/loader.php');

    // Load weather Data:
    require('getCurrentWeatherData.php');
    $getData = new getCurrentWeatherData();
    $wx = $getData->getConditions();

    // Load Lightning Data:
    if ($config->station->lightning_source === 1 || $config->station->lightning_source === 2) {
        require('getCurrentLightningData.php');
        $getLightningData = new getCurrentLightningData();
        $lightning = $getLightningData->getData();
    }

    // Load Atlas Data:
    if ($config->station->primary_sensor === 0) {
        // Load weather Data:
        require('getCurrentAtlasData.php');
        $getAtlasData = new getCurrentAtlasData();
        $atlas = $getAtlasData->getData();
    }

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

    // Warn if offline
    $systemStatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `status` FROM `outage_alert`"));
    if ($systemStatus['status'] === '0') {
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp` FROM `last_update`"));
        ?>
        <!-- Offline -->
        <section id="offline" class="row live-weather-offline">
            <div class="col-md-8 col-12 mx-auto text-center">
                <p class="alert alert-warning">Live data is temporarily unavailable!<br>Last
                    update: <?= $lastUpdate['timestamp']; ?></p>
            </div>
        </section>
        <?php
    }
    ?>
    <!-- Live Weather Data -->
    <section id="live-weather-data" class="row live-weather-data">

        <!-- Left Column -->
        <div class="col-md-4 col-sm-6 col-12">

            <!-- Temperature Data -->
            <div class="row">
                <div class="col">
                    <h1><i class="fas <?= tempIcon($wx->tempC); ?>" aria-hidden="true"></i> Temperature:</h1>
                    <h2><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457; ($wx->tempC&#8451;)" : "$wx->tempC&#8451; ($wx->tempF&#8457;)";
                        } else {
                            $temp = ($config->site->imperial === true) ? "$wx->tempF&#8457;" : "$wx->tempC&#8451;";
                        }
                        echo $temp . trendIcon($wx->tempF_trend) ?></h2>
                    <!-- Feels Like -->
                    <?php if ($wx->feelsF != 0) {
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457; ($wx->feelsC&#8451;)" : "$wx->feelsC&#8451; ($wx->feelsF&#8457;)";
                        } else {
                            $feels = ($config->site->imperial === true) ? "$wx->feelsF&#8457;" : "$wx->feelsC&#8451;";
                        }
                        echo '<h3>Feels Like:</h3> ' . $feels . '<br>';
                    } ?>

                    <!-- Daily Low -->
                    <h3>Low:</h3>
                    <p>
                        <?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457; ($wx->tempC_low&#8451;)" : "$wx->tempC_low&#8451; ($wx->tempF_low&#8457;)";
                        } else {
                            $temp_low = ($config->site->imperial === true) ? "$wx->tempF_low&#8457;" : "$wx->tempC_low&#8451;";
                        }
                        echo $temp_low . " @ $wx->low_temp_recorded"; ?></p>

                    <ul class="list-unstyled">

                        <!-- Daily High -->
                        <li><h3>High:</h3>
                            <p>
                                <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                    $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457; ($wx->tempC_high&#8451;)" : "$wx->tempC_high&#8451; ($wx->tempF_high&#8457;)";
                                } else {
                                    $temp_high = ($config->site->imperial === true) ? "$wx->tempF_high&#8457;" : "$wx->tempC_high&#8451;";
                                }
                                echo $temp_high . " @ $wx->high_temp_recorded"; ?></p></li>

                        <!-- Average -->
                        <li><h3>Average:</h3>
                            <p>
                                <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                    $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457; ($wx->tempC_avg&#8451;)" : "$wx->tempC_avg&#8451; ($wx->tempF_avg&#8457;)";
                                } else {
                                    $temp_avg = ($config->site->imperial === true) ? "$wx->tempF_avg&#8457;" : "$wx->tempC_avg&#8451;";
                                }
                                echo $temp_avg; ?></p></li>

                        <!-- Dew Point -->
                        <li><h3>Dew Point:</h3>
                            <p>
                                <?php
                                if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                    $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457; ($wx->dewptC&#8451;)" : "$wx->dewptC&#8451; ($wx->dewptF&#8457;)";
                                } else {
                                    $dewpt = ($config->site->imperial === true) ? "$wx->dewptF&#8457;" : "$wx->dewptC&#8451;";
                                }
                                echo $dewpt; ?></p></li>
                    </ul>
                </div>
            </div>
            <!-- END: Temperature Data -->

            <!-- Wind Data -->
            <div class="row">
                <div class="col">
                    <h1><?php if ($wx->windSkmh >= 25) {
                            echo ' <i class="wi wi-strong-wind" aria-hidden="true"></i>';
                        } elseif ($wx->windSkmh < 25) {
                            if ($wx->windSkmh >= 10) {
                                echo ' <i class="wi wi-windy" aria-hidden="true"></i> ';
                            }
                        }
                        echo '<i class="wi wi-wind wi-from-', strtolower($wx->windDIR), '" aria-hidden="true"></i>'; ?>
                        Wind:</h1>
                    <h2>from <?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $wind = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSmph mph ($wx->windSkmh km/h)" : "$wx->windDIR @ $wx->windSkmh km/h ($wx->windSmph mph)";
                        } else {
                            $wind = ($config->site->imperial === true) ? "$wx->windDIR @ $wx->windSmph mph" : "$wx->windDIR @ $wx->windSkmh km/h";
                        }
                        echo $wind; ?></h2>
                    <ul class="list-unstyled">
                        <li><h3>Average:</h3> from <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $wind_avg = ($config->site->imperial === true) ? "$wx->windDIR_avg2 @ $wx->windSmph_avg2 mph ($wx->windSkmh_avg2 km/h)" : "$wx->windDIR_avg2 @ $wx->windSkmh_avg2 km/h ($wx->windSmph_avg2 mph)";
                            } else {
                                $wind_avg = ($config->site->imperial === true) ? "$wx->windDIR_avg2 @ $wx->windSmph_avg2 mph" : "$wx->windDIR_avg2 @ $wx->windSkmh_avg2 km/h";
                            }
                            echo $wind_avg; ?></li>
                        <li><h3>Peak:</h3>
                            <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $wind_peak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSmph_peak mph ($wx->windSkmh_peak km/h)" : "$wx->windDIR_peak @ $wx->windSkmh_peak km/h ($wx->windSmph_peak mph)";
                            } else {
                                $wind_peak = ($config->site->imperial === true) ? "$wx->windDIR_peak @ $wx->windSmph_peak mph" : "$wx->windDIR_peak @ $wx->windSkmh_peak km/h";
                            }
                            echo $wind_peak . ' @ ' . $wx->wind_recorded_peak; ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END: Wind Data -->

        <!-- END: Left Column -->

        <!-- Middle Column -->
        <div class="col-md-4 col-sm-6 col-12">

            <!-- Humidity -->
            <div class="row">
                <div class="col">
                    <h1><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h1>
                    <h2><?= $wx->relH, '%', trendIcon($wx->relH_trend); ?></h2>
                </div>
            </div>

            <!-- Pressure -->
            <div class="row">
                <div class="col">
                    <h1><i class="wi wi-barometer" aria-hidden="true"></i> Pressure:</h1>
                    <h2><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $pressure = ($config->site->imperial === true) ? "$wx->pressure_inHg inHg ($wx->pressure_kPa kPa)" : "$wx->pressure_kPa kPa ($wx->pressure_inHg inHg)";
                        } else {
                            $pressure = ($config->site->imperial === true) ? "$wx->pressure_inHg inHg" : "$wx->pressure_kPa kPa";
                        }
                        echo $pressure . trendIcon($wx->inHg_trend); ?></h2>
                </div>
            </div>

            <!-- Rain -->
            <div class="row">
                <div class="col">
                    <h1><i class="wi wi-raindrops" aria-hidden="true"></i> Rain:</h1>
                    <ul class="list-unstyled">
                        <li><h3>Fall Rate:</h3> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $rain = ($config->site->imperial === true) ? "$wx->rainIN in/hr ($wx->rainMM mm/hr)" : "$wx->rainMM mm/hr ($wx->rainIN in/hr)";
                            } else {
                                $rain = ($config->site->imperial === true) ? "$wx->rainIN in/hr" : "$wx->rainMM mm/hr";
                            }
                            echo $rain; ?></li>
                        <li><h3>Daily Total:</h3> <?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $rain_today = ($config->site->imperial === true) ? "$wx->rainTotalIN_today in ($wx->rainTotalMM_today mm)" : "$wx->rainTotalMM_today mm ($wx->rainTotalIN_today in)";
                            } else {
                                $rain_today = ($config->site->imperial === true) ? "$wx->rainTotalIN_today in" : "$wx->rainTotalMM_today mm";
                            }
                            echo $rain_today; ?></li>
                    </ul>
                </div>
            </div>
            <?php
            if ($config->station->primary_sensor === 0) {
                ?>
                <!-- Atlas Data -->
                <div class="row">
                    <div class="col">
                        <h1><i class="fas fa-lightbulb" aria-hidden="true"></i> Light:</h1>
                        <ul class="list-unstyled">
                            <li><h3>Intensity:</h3> <?php
                                echo $atlas->lightintensity_text; ?></li>
                            <li><h3>Measured:</h3> <?php
                                echo $atlas->measured_light_seconds; ?> Seconds</li>
                        </ul>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <!-- END: Middle Column -->

        <!-- Right Column -->
        <div class="col-md-4 col-sm-12">

            <!-- Sun -->
            <div class="row">
                <div class="col">
                    <h1><i class="wi wi-day-sunny" aria-hidden="true"></i> Sun:</h1>
                    <ul class="list-unstyled">
                        <li><i class="wi wi-sunrise" aria-hidden="true"></i>
                            <h3>Sunrise:</h3> <?= $sunrise; ?></li>
                        <li><i class="wi wi-sunset" aria-hidden="true"></i>
                            <h3>Sunset:</h3> <?= $sunset; ?></li>
                        <?php
                        if ($config->station->primary_sensor === 0) {
                            ?>
                            <li><i class="wi wi-hot" aria-hidden="true"></i>
                                <h3>UV Index:</h3> <?= $atlas->uvindex_text; ?></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <!-- END: Sun -->

            <!-- Moon -->
            <div class="row">
                <div class="col">
                    <h1><i class="wi <?= moonIcon($moon_stage); ?>" aria-hidden="true"></i> Moon:</h1>
                    <h2><?= "$moon_stage"; ?></h2>
                    <p><?= "$moon_age days old, $moon_illumination visible"; ?></p>
                    <ul class="list-unstyled">
                        <?php if ($moon_time_enabled === true) { ?>
                            <li><i class="wi wi-moonrise" aria-hidden="true"></i>
                                <h3>Moonrise:</h3> <?= $moon_rise; ?></li>
                            <li><i class="wi wi-moonset" aria-hidden="true"></i>
                                <h3>Moonset:</h3> <?= $moon_set; ?></li>
                        <?php } ?>
                        <li><i class="wi wi-moon-new" aria-hidden="true"></i>
                            <h3>Latest
                                New:</h3> <?= $last_new_moon; ?></li>
                        <li><i class="wi wi-moon-full" aria-hidden="true"></i>
                            <h3>Latest
                                Full:</h3> <?= $last_full_moon; ?></li>
                        <li><i class="wi wi-moon-new" aria-hidden="true"></i>
                            <h3>Upcoming
                                New:</h3> <?= $next_new_moon; ?></li>
                        <li><i class="wi wi-moon-full" aria-hidden="true"></i>
                            <h3>Upcoming
                                Full:</h3> <?= $next_full_moon; ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END: Right Column -->
    </section>
    <!-- END: Live Weather Data -->
    <?php
    if ($config->station->lightning_source === 1 || $config->station->lightning_source === 2) {
        $interference = ($lightning->interference === true) ? "Yes" : "No";
        ?>
        <!-- Lightning Data -->
        <section id="live-lightning-data" class="row live-weather-data">
            <div class="col-12">
                <h1><i class="fas fa-bolt" aria-hidden="true"></i> Lightning</h1>
                <ul class="list-unstyled">
                    <li><h3>Interference:</h3> <?= $interference ?> |
                        <h3>Strikes:</h3> <?= $lightning->strikecount; ?> |
                        <h3>Last:</h3> <?= $lightning->last_strike_ts; ?> |
                        <h3>Distance:</h3><?php
                        if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                            $last_strike_distance = ($config->site->imperial === true) ? "$lightning->last_strike_ts_M Miles ($lightning->last_strike_ts_KM KM)" : "$lightning->last_strike_ts_KM KM ($lightning->last_strike_ts_M Miles)";
                        } else {
                            $last_strike_distance = ($config->site->imperial === true) ? "$lightning->last_strike_ts_M Miles" : "$lightning->last_strike_ts_KM KM";
                        }
                        echo $last_strike_distance; ?></li>
                </ul>
            </div>
        </section>
        <?php
    }
    // If tower sensors are active show the tower data
    if ($config->station->towers === true) {

        // Can we display private data?
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange`");
        } else {
            $result = mysqli_query($conn, "SELECT * FROM `towers` WHERE `private` = 0 ORDER BY `arrange`");
        }

        // Is there data to show? If yes, show it.
        if (mysqli_num_rows($result) >= 1) { ?>
            <hr class="hr-dotted">

            <!-- Tower Sensors -->
            <section id="live-tower-data" class="row live-tower-data">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $sensor = $row['sensor'];
                    $result2 = mysqli_fetch_assoc(mysqli_query($conn,
                        "SELECT * FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
                    $tempF = round($result2['tempF'], 1);
                    $tempC = round(($result2['tempF'] - 32) * 5 / 9, 1);
                    $relH = $result2['relH'];

                    // Temp Trending
                    $tempF_trend = trendIcon($getData->calculateTrend('tempF', 'tower_data', $sensor));

                    // Humidity Trending
                    $relH_trend = trendIcon($getData->calculateTrend('relH', 'tower_data', $sensor));

                    ?>
                    <div class="col">
                        <h1><?= $row['name']; ?>:</h1>
                        <h2><i class="fas <?= tempIcon($tempC); ?>" aria-hidden="true"></i> Temperature:</h2>
                        <h3><?php
                            if ($config->site->hide_alternate === 'false' || $config->site->hide_alternate === 'archive') {
                                $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; ($tempC&#8451;) $tempF_trend" : "$tempC&#8451; ($tempF&#8457;) $tempF_trend";
                            } else {
                                $tower_temp = ($config->site->imperial === true) ? "$tempF&#8457; $tempF_trend" : "$tempC&#8451; $tempF_trend";
                            }
                            echo $tower_temp ?></h3>
                        <h2><i class="wi wi-humidity" aria-hidden="true"></i> Humidity:</h2>
                        <h3><?= "$relH% $relH_trend"; ?></h3>
                    </div>
                    <?php
                }
                ?>
            </section>
            <!-- END: Tower Sensors -->
            <?php
        }
    }
}
