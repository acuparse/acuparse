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
 * File: src/pub/api/v1/text/realtime/index.php
 * Builds a Cumulus compatible realtime file
 * https://cumuluswiki.org/a/Realtime.txt
 */

/**
 * @param $unit
 * @param $table
 * @return string
 */

// Calculate the trending value
function calculateTrend($unit, $table): string
{
    require(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/inc/loader.php');
    /** @var mysqli $conn Global MYSQL Connection */

    $result = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT AVG(`$unit`) AS `trend1` FROM `$table` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR)"));
    $trend_1 = (float)$result['trend1'];

    $result = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT AVG(`$unit`) AS `trend2` FROM `$table` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR)"));
    $trend_2 = (float)$result['trend2'];

    return round($trend_1 - $trend_2, 1);
}

function realtime(): string
{
    // Get the loader
    require(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/inc/loader.php');

    /** @var mysqli $conn Global MYSQL Connection */
    /**
     * @return array
     * @var object $config Global Config
     */

    $pressureTrend = calculateTrend('inhg', 'pressure');
    $tempTrend = calculateTrend('tempF', 'temperature');

    $rainfall_IN_total_yesterday = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT SUM(`dailyrainin`) AS `rainfall_yesterday_total` FROM `dailyrain` WHERE DATE(`date`) = SUBDATE(CURDATE(),1)"));
    $rainfall_IN_total_yesterday = (float)$rainfall_IN_total_yesterday['rainfall_yesterday_total'];
    $rainfall_IN_total_month = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT SUM(`dailyrainin`) AS `rainfall_month_total` FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())"));
    $rainfall_IN_total_month = (float)$rainfall_IN_total_month['rainfall_month_total'];
    $rainfall_IN_total_year = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT SUM(`dailyrainin`) AS `rainfall_year_total` FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE())"));
    $rainfall_IN_total_year = (float)$rainfall_IN_total_year['rainfall_year_total'];

    // Load weather Data:
    if (!class_exists('getCurrentWeatherData')) {
        require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
        $getData = new getCurrentWeatherData();
        $wx = $getData->getConditions();
        if ($config->station->primary_sensor === 0) {
            if (!class_exists('getCurrentAtlasData')) {
                require(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
                $getAtlasData = new getCurrentAtlasData();
                $atlas = $getAtlasData->getData();
            }
        }
    }
    if ($config->station->device === 0 && $config->station->primary_sensor === 0) {
        $windSpeedAverage = $atlas->windAvgMPH;
        $windGustMPHPeak = $atlas->windGustPeakMPH;
        $windGustPeakRecorded = $atlas->windGustPeakRecorded;
        $uvIndex = $atlas->uvIndex;
        $lightHours = $atlas->lightHours;
        if ($atlas->lightIntensity >= 21521) {
            $sunShining = '1';
        } else {
            $sunShining = '0';
        }
        if ($atlas->lightIntensity >= 5380) {
            $sunDaylight = '1';
        } else {
            $sunDaylight = '0';
        }
    } else {
        $windSpeedAverage = 'NULL';
        $windGustMPHPeak = 'NULL';
        $windGustPeakRecorded = 'NULL';
        $uvIndex = 'NULL';
        $lightHours = 'NULL';
        $sunShining = 'NULL';
        $sunDaylight = 'NULL';
    }
    // High Pressure
    $result = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE DATE(`reported`) = CURDATE()) AND DATE(`reported`) = CURDATE() ORDER BY `reported` DESC LIMIT 1"));
    $pressure_inHg_high_recorded = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
    $pressure_inHg_high = (float)$result['pressureinHg']; // Inches of Mercury
    // Low Pressure
    $result = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE DATE(`reported`) = CURDATE()) AND DATE(`reported`) = CURDATE() ORDER BY `reported` DESC LIMIT 1"));
    $pressure_inHg_low_recorded = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
    $pressure_inHg_low = (float)$result['pressureinHg']; // Inches of Mercury

    $date = date('d/m/y H:m:s');

    return "$date $wx->tempF $wx->relH $wx->dewptF $windSpeedAverage $wx->windSpeedMPH $wx->windDEG $wx->rainIN $wx->rainTotalIN_today $wx->pressure_inHg $wx->windDIR $wx->windBeaufort mps F in in NULL $pressureTrend $rainfall_IN_total_month $rainfall_IN_total_year $rainfall_IN_total_yesterday NULL NULL NULL $tempTrend $wx->tempF_high $wx->high_temp_recorded $wx->tempF_low $wx->low_temp_recorded $wx->windSpeedMPH_peak $wx->windSpeed_peak_recorded $windGustMPHPeak $windGustPeakRecorded $pressure_inHg_high $pressure_inHg_high_recorded $pressure_inHg_low $pressure_inHg_low_recorded 1.8.7 819 NULL NULL NULL $uvIndex NULL NULL NULL NULL NULL $sunDaylight NULL NULL NULL NULL NULL $lightHours NULL $sunShining $wx->feelsF";

}

header('Content-Type: text/plain; charset=UTF-8');

echo realtime();
