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
 * File: src/fcn/weather/getArchiveWeatherData.php
 * Gets the archive weather data from the database
 */
class getArchiveWeatherData
{
    // Set variables

    // Yesterday
    private $tempF_high_yesterday;
    private $tempF_low_yesterday;
    private $tempC_high_yesterday;
    private $tempC_low_yesterday;
    private $tempF_high_recorded_yesterday;
    private $tempF_low_recorded_yesterday;
    private $tempF_high_recorded_yesterday_JSON;
    private $tempF_low_recorded_yesterday_JSON;
    private $windS_mph_high_yesterday;
    private $windS_kmh_high_yesterday;
    private $windS_mph_high_recorded_yesterday;
    private $windS_mph_high_recorded_yesterday_JSON;
    private $windDIR_yesterday;
    private $pressure_inHg_high_yesterday;
    private $pressure_kPa_high_yesterday;
    private $pressure_inHg_low_yesterday;
    private $pressure_kPa_low_yesterday;
    private $pressure_inHg_high_recorded_yesterday;
    private $pressure_inHg_low_recorded_yesterday;
    private $pressure_inHg_high_recorded_yesterday_JSON;
    private $pressure_inHg_low_recorded_yesterday_JSON;
    private $relH_high_yesterday;
    private $relH_low_yesterday;
    private $relH_high_recorded_yesterday;
    private $relH_low_recorded_yesterday;
    private $relH_high_recorded_yesterday_JSON;
    private $relH_low_recorded_yesterday_JSON;
    private $rainfall_IN_total_yesterday;
    private $rainfall_MM_total_yesterday;

    // This Week
    private $tempF_high_week;
    private $tempF_low_week;
    private $tempC_high_week;
    private $tempC_low_week;
    private $tempF_high_recorded_week;
    private $tempF_low_recorded_week;
    private $tempF_high_recorded_week_JSON;
    private $tempF_low_recorded_week_JSON;
    private $windS_mph_high_week;
    private $windS_kmh_high_week;
    private $windS_mph_high_recorded_week;
    private $windS_mph_high_recorded_week_JSON;
    private $windDIR_week;
    private $pressure_inHg_high_week;
    private $pressure_kPa_high_week;
    private $pressure_inHg_low_week;
    private $pressure_kPa_low_week;
    private $pressure_inHg_high_recorded_week;
    private $pressure_inHg_low_recorded_week;
    private $pressure_inHg_high_recorded_week_JSON;
    private $pressure_inHg_low_recorded_week_JSON;
    private $relH_high_week;
    private $relH_low_week;
    private $relH_high_recorded_week;
    private $relH_low_recorded_week;
    private $relH_high_recorded_week_JSON;
    private $relH_low_recorded_week_JSON;
    private $rainfall_IN_most_week;
    private $rainfall_MM_most_week;
    private $rainfall_IN_most_recorded_week;
    private $rainfall_IN_most_recorded_week_JSON;
    private $rainfall_IN_total_week;
    private $rainfall_MM_total_week;

    // This Month
    private $tempF_high_month;
    private $tempF_low_month;
    private $tempC_high_month;
    private $tempC_low_month;
    private $tempF_high_recorded_month;
    private $tempF_low_recorded_month;
    private $tempF_high_recorded_month_JSON;
    private $tempF_low_recorded_month_JSON;
    private $windS_mph_high_month;
    private $windS_kmh_high_month;
    private $windS_mph_high_recorded_month;
    private $windS_mph_high_recorded_month_JSON;
    private $windDIR_month;
    private $pressure_inHg_high_month;
    private $pressure_kPa_high_month;
    private $pressure_inHg_low_month;
    private $pressure_kPa_low_month;
    private $pressure_inHg_high_recorded_month;
    private $pressure_inHg_low_recorded_month;
    private $pressure_inHg_high_recorded_month_JSON;
    private $pressure_inHg_low_recorded_month_JSON;
    private $relH_high_month;
    private $relH_low_month;
    private $relH_high_recorded_month;
    private $relH_low_recorded_month;
    private $relH_high_recorded_month_JSON;
    private $relH_low_recorded_month_JSON;
    private $rainfall_IN_most_month;
    private $rainfall_MM_most_month;
    private $rainfall_IN_most_recorded_month;
    private $rainfall_IN_most_recorded_month_JSON;
    private $rainfall_IN_total_month;
    private $rainfall_MM_total_month;

    // Last Month
    private $tempF_high_last_month;
    private $tempF_low_last_month;
    private $tempC_high_last_month;
    private $tempC_low_last_month;
    private $tempF_high_recorded_last_month;
    private $tempF_low_recorded_last_month;
    private $tempF_high_recorded_last_month_JSON;
    private $tempF_low_recorded_last_month_JSON;
    private $windS_mph_high_last_month;
    private $windS_kmh_high_last_month;
    private $windS_mph_high_recorded_last_month;
    private $windS_mph_high_recorded_last_month_JSON;
    private $windDIR_last_month;
    private $pressure_inHg_high_last_month;
    private $pressure_kPa_high_last_month;
    private $pressure_inHg_low_last_month;
    private $pressure_kPa_low_last_month;
    private $pressure_inHg_high_recorded_last_month;
    private $pressure_inHg_low_recorded_last_month;
    private $pressure_inHg_high_recorded_last_month_JSON;
    private $pressure_inHg_low_recorded_last_month_JSON;
    private $relH_high_last_month;
    private $relH_low_last_month;
    private $relH_high_recorded_last_month;
    private $relH_low_recorded_last_month;
    private $relH_high_recorded_last_month_JSON;
    private $relH_low_recorded_last_month_JSON;
    private $rainfall_IN_most_last_month;
    private $rainfall_MM_most_last_month;
    private $rainfall_IN_most_recorded_last_month;
    private $rainfall_IN_most_recorded_last_month_JSON;
    private $rainfall_IN_total_last_month;
    private $rainfall_MM_total_last_month;

    // This Year
    private $tempF_high_year;
    private $tempF_low_year;
    private $tempC_high_year;
    private $tempC_low_year;
    private $tempF_high_recorded_year;
    private $tempF_low_recorded_year;
    private $tempF_high_recorded_year_JSON;
    private $tempF_low_recorded_year_JSON;
    private $windS_mph_high_year;
    private $windS_kmh_high_year;
    private $windS_mph_high_recorded_year;
    private $windS_mph_high_recorded_year_JSON;
    private $windDIR_year;
    private $pressure_inHg_high_year;
    private $pressure_kPa_high_year;
    private $pressure_inHg_low_year;
    private $pressure_kPa_low_year;
    private $pressure_inHg_high_recorded_year;
    private $pressure_inHg_low_recorded_year;
    private $pressure_inHg_high_recorded_year_JSON;
    private $pressure_inHg_low_recorded_year_JSON;
    private $relH_high_year;
    private $relH_low_year;
    private $relH_high_recorded_year;
    private $relH_low_recorded_year;
    private $relH_high_recorded_year_JSON;
    private $relH_low_recorded_year_JSON;
    private $rainfall_IN_most_year;
    private $rainfall_MM_most_year;
    private $rainfall_IN_most_recorded_year;
    private $rainfall_IN_most_recorded_year_JSON;
    private $rainfall_IN_total_year;
    private $rainfall_MM_total_year;

    // All Time
    private $tempF_high_ever;
    private $tempF_low_ever;
    private $tempC_high_ever;
    private $tempC_low_ever;
    private $tempF_high_recorded_ever;
    private $tempF_low_recorded_ever;
    private $tempF_high_recorded_ever_JSON;
    private $tempF_low_recorded_ever_JSON;
    private $windS_mph_high_ever;
    private $windS_kmh_high_ever;
    private $windS_mph_high_recorded_ever;
    private $windS_mph_high_recorded_ever_JSON;
    private $windDIR_ever;
    private $pressure_inHg_high_ever;
    private $pressure_kPa_high_ever;
    private $pressure_inHg_low_ever;
    private $pressure_kPa_low_ever;
    private $pressure_inHg_high_recorded_ever;
    private $pressure_inHg_low_recorded_ever;
    private $pressure_inHg_high_recorded_ever_JSON;
    private $pressure_inHg_low_recorded_ever_JSON;
    private $relH_high_ever;
    private $relH_low_ever;
    private $relH_high_recorded_ever;
    private $relH_low_recorded_ever;
    private $relH_high_recorded_ever_JSON;
    private $relH_low_recorded_ever_JSON;
    private $rainfall_IN_most_ever;
    private $rainfall_MM_most_ever;
    private $rainfall_IN_most_recorded_ever;
    private $rainfall_IN_most_recorded_ever_JSON;
    private $rainfall_IN_total_ever;
    private $rainfall_MM_total_ever;
    private $rainfall_IN_total_ever_since;
    private $rainfall_IN_total_ever_since_JSON;

    function __construct($json = false)
    {
        set_time_limit(0);

        // Get the loader
        require(dirname(__DIR__, 2) . '/inc/loader.php');
        /** @var mysqli $conn Global MYSQL Connection */
        /**
         * @return array
         * @var object $config Global Config
         */

        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported` FROM `archive` ORDER BY `reported` DESC LIMIT 1"));

        if (empty($lastUpdate)) {
            if ($json === true) {
                $json_output = ['Status' => 'error', 'message' => 'No Archive Data Reported'];
                echo json_encode($json_output);
            } else {
                echo '<div class="col text-center alert alert-danger"><p><strong>No Archive Data Reported!</strong><br>Check that your Cron tasks are running! See your <a href="https://docs.acuparse.com/TROUBLESHOOTING/#logs">logs</a> for more details.</p></div>';
            }
            exit();
        }

        // Check for Yesterday Readings
        $archiveYesterday = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported` FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));

        // Check for Last Month Readings
        $archiveLastMonth = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported` FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));


        // Process Wind Speed:

        // Yesterday
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->windS_mph_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->windS_mph_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->windDIR_yesterday = $this->windDirection($result['windDEG']); // Wind from
            $this->windS_mph_high_yesterday = (int)round($result['windSpeedMPH']); // Miles per hour
            $this->windS_kmh_high_yesterday = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour
        } else {
            $this->windS_mph_high_recorded_yesterday = null;
            $this->windS_mph_high_recorded_yesterday_JSON = null;
            $this->windDIR_yesterday = null;
            $this->windS_mph_high_yesterday = null;
            $this->windS_kmh_high_yesterday = null;
        }

        // This Week
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->windS_mph_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->windS_mph_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->windDIR_week = $this->windDirection($result['windDEG']); // Wind from
        $this->windS_mph_high_week = (int)round($result['windSpeedMPH']); // Miles per hour
        $this->windS_kmh_high_week = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour

        // This Month
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->windS_mph_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->windS_mph_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->windDIR_month = $result['windDEG']; // Wind from
        $this->windS_mph_high_month = (int)round($result['windSpeedMPH']); // Miles per hour
        $this->windS_kmh_high_month = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour

        // Last Month
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->windS_mph_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->windS_mph_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->windDIR_last_month = $result['windDEG']; // Wind from
            $this->windS_mph_high_last_month = (int)round($result['windSpeedMPH']); // Miles per hour
            $this->windS_kmh_high_last_month = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour
        } else {
            $this->windS_mph_high_recorded_last_month = null;
            $this->windS_mph_high_recorded_last_month_JSON = null;
            $this->windDIR_last_month = null;
            $this->windS_mph_high_last_month = null;
            $this->windS_kmh_high_last_month = null;
        }

        // This Year
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->windS_mph_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->windS_mph_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->windDIR_year = $result['windDEG']; // Wind from
        $this->windS_mph_high_year = (int)round($result['windSpeedMPH']); // Miles per hour
        $this->windS_kmh_high_year = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour

        // All Time
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->windS_mph_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->windS_mph_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->windDIR_ever = $result['windDEG']; // Wind from
        $this->windS_mph_high_ever = (int)round($result['windSpeedMPH']); // Miles per hour
        $this->windS_kmh_high_ever = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour

        // Process Temp:

        // Yesterday High
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->tempF_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->tempF_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->tempF_high_yesterday = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_high_yesterday = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        } else {
            $this->tempF_high_recorded_yesterday = null;
            $this->tempF_high_recorded_yesterday_JSON = null;
            $this->tempF_high_yesterday = null;
            $this->tempC_high_yesterday = null;
        }

        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_week = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_high_week = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_month = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_high_month = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Last Month High
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->tempF_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->tempF_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->tempF_high_last_month = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_high_last_month = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        } else {
            $this->tempF_high_recorded_last_month = null;
            $this->tempF_high_recorded_last_month_JSON = null;
            $this->tempF_high_last_month = null;
            $this->tempC_high_last_month = null;
        }

        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_year = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_high_year = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // All-Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MAX(`tempF`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_high_ever = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_high_ever = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Yesterday Low
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->tempF_low_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->tempF_low_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->tempF_low_yesterday = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_low_yesterday = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        } else {
            $this->tempF_low_recorded_yesterday = null;
            $this->tempF_low_recorded_yesterday_JSON = null;
            $this->tempF_low_yesterday = null;
            $this->tempC_low_yesterday = null;
        }

        // This Week Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_low_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_week = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_low_week = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // This Month Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_low_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_month = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_low_month = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Last Month Low
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->tempF_low_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->tempF_low_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->tempF_low_last_month = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_low_last_month = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        } else {
            $this->tempF_low_recorded_last_month = null;
            $this->tempF_low_recorded_last_month_JSON = null;
            $this->tempF_low_last_month = null;
            $this->tempC_low_last_month = null;
        }

        // This Year Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_low_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_year = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_low_year = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // All-Time Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `tempF` FROM `archive` WHERE `tempF` = (SELECT MIN(`tempF`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->tempF_low_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->tempF_low_ever = round($result['tempF'], 1); // Fahrenheit
        $this->tempC_low_ever = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Process Pressure:

        // Yesterday High
        if (isset($archiveYesterday)) {

            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->pressure_inHg_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_high_yesterday = (float)$result['pressureinHg']; // Inches of Mercury
            $this->pressure_kPa_high_yesterday = round($result['pressureinHg'] * 3.38638866667,
                2); // Convert to Kilopascals
        } else {
            $this->pressure_inHg_high_recorded_yesterday = null;
            $this->pressure_inHg_high_recorded_yesterday_JSON = null;
            $this->pressure_inHg_high_yesterday = null;
            $this->pressure_kPa_high_yesterday = null;
        }

        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_week = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_high_week = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_month = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_high_month = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // Last Month High
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->pressure_inHg_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_high_last_month = (float)$result['pressureinHg']; // Inches of Mercury
            $this->pressure_kPa_high_last_month = round($result['pressureinHg'] * 3.38638866667,
                2); // Convert to Kilopascals
        } else {
            $this->pressure_inHg_high_recorded_last_month = null;
            $this->pressure_inHg_high_recorded_last_month_JSON = null;
            $this->pressure_inHg_high_last_month = null;
            $this->pressure_kPa_high_last_month = null;
        }

        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_year = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_high_year = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // All-Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MAX(`pressureinHg`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_high_ever = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_high_ever = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // Yesterday Low
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->pressure_inHg_low_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_low_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_low_yesterday = (float)$result['pressureinHg']; // Inches of Mercury
            $this->pressure_kPa_low_yesterday = round($result['pressureinHg'] * 3.38638866667,
                2); // Convert to Kilopascals
        } else {
            $this->pressure_inHg_low_recorded_yesterday = null;
            $this->pressure_inHg_low_recorded_yesterday_JSON = null;
            $this->pressure_inHg_low_yesterday = null;
            $this->pressure_kPa_low_yesterday = null;
        }

        // This Week Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_low_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_week = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_low_week = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // This Month Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_low_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_month = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_low_month = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // Last Month Low
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->pressure_inHg_low_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_low_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->pressure_inHg_low_last_month = (float)$result['pressureinHg']; // Inches of Mercury
            $this->pressure_kPa_low_last_month = round($result['pressureinHg'] * 3.38638866667,
                2); // Convert to Kilopascals
        } else {
            $this->pressure_inHg_low_recorded_last_month = null;
            $this->pressure_inHg_low_recorded_last_month_JSON = null;
            $this->pressure_inHg_low_last_month = null;
            $this->pressure_kPa_low_last_month = null;
        }

        // This Year Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_low_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_year = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_low_year = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // All-Time Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `pressureinHg` FROM `archive` WHERE `pressureinHg` = (SELECT MIN(`pressureinHg`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->pressure_inHg_low_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->pressure_inHg_low_ever = (float)$result['pressureinHg']; // Inches of Mercury
        $this->pressure_kPa_low_ever = round($result['pressureinHg'] * 3.38638866667,
            2); // Convert to Kilopascals

        // Process Humidity:

        // Yesterday High
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->relH_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->relH_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->relH_high_yesterday = (int)$result['relH']; // Percent
        } else {
            $this->relH_high_recorded_yesterday = null;
            $this->relH_high_recorded_yesterday_JSON = null;
            $this->relH_high_yesterday = null;
        }

        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_high_week = (int)$result['relH']; // Percent

        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_high_month = (int)$result['relH']; // Percent

        // Last Month High
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->relH_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->relH_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->relH_high_last_month = (int)$result['relH']; // Percent
        } else {
            $this->relH_high_recorded_last_month = null;
            $this->relH_high_recorded_last_month_JSON = null;
            $this->relH_high_last_month = null;
        }

        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_high_year = (int)$result['relH']; // Percent

        // All-Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MAX(`relH`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->relH_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_high_ever = (int)$result['relH']; // Percent

        // Yesterday Low
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
            $this->relH_low_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
            $this->relH_low_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->relH_low_yesterday = (int)$result['relH']; // Percent
        } else {
            $this->relH_low_recorded_yesterday = null;
            $this->relH_low_recorded_yesterday_JSON = null;
            $this->relH_low_yesterday = null;
        }

        // This Week Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(`reported`) = YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_low_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_low_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_low_week = (int)$result['relH']; // Percent

        // This Month Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_low_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_low_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_low_month = (int)$result['relH']; // Percent

        // Last Month Low
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
            $this->relH_low_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
            $this->relH_low_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
            $this->relH_low_last_month = (int)$result['relH']; // Percent
        } else {
            $this->relH_low_recorded_last_month = null;
            $this->relH_low_recorded_last_month_JSON = null;
            $this->relH_low_last_month = null;
        }

        // This Year Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_low_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->relH_low_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_low_year = (int)$result['relH']; // Percent

        // All-Time Low
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `relH` FROM `archive` WHERE `relH` = (SELECT MIN(`relH`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->relH_low_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->relH_low_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->relH_low_ever = (int)$result['relH']; // Percent

        // Process Rainfall:

        // Yesterday Rainfall:
        if (isset($archiveYesterday)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT SUM(`dailyrainin`) AS `rainfall_yesterday_total` FROM `dailyrain` WHERE DATE(`date`) = SUBDATE(CURDATE(),1)"));
            $this->rainfall_IN_total_yesterday = (float)$result['rainfall_yesterday_total']; // Inches
            $this->rainfall_MM_total_yesterday = round($result['rainfall_yesterday_total'] * 25.4, 2); // Millimeters
        } else {
            $this->rainfall_IN_total_yesterday = null;
            $this->rainfall_MM_total_yesterday = null;
        }

        // Weekly Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT SUM(`dailyrainin`) AS `rainfall_week_total` FROM `dailyrain` WHERE YEARWEEK(`date`) = YEARWEEK(NOW())"));
        $this->rainfall_IN_total_week = (float)$result['rainfall_week_total']; // Inches
        $this->rainfall_MM_total_week = round($result['rainfall_week_total'] * 25.4, 2); // Millimeters
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update`, `dailyrainin` FROM `dailyrain` WHERE `dailyrainin` = (SELECT MAX(`dailyrainin`) FROM `dailyrain` WHERE YEARWEEK(`date`) = YEARWEEK(NOW())) AND YEARWEEK(`date`) = YEARWEEK(NOW()) ORDER BY `date` DESC LIMIT 1"));
        $this->rainfall_IN_most_recorded_week = date($config->site->dashboard_display_date, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_week = (float)$result['dailyrainin']; // Inches
        $this->rainfall_MM_most_week = round($result['dailyrainin'] * 25.4, 2); // Millimeters

        // Monthly Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT SUM(`dailyrainin`) AS `rainfall_month_total` FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())"));
        $this->rainfall_IN_total_month = (float)$result['rainfall_month_total']; // Inches
        $this->rainfall_MM_total_month = round($result['rainfall_month_total'] * 25.4, 2); // Millimeters
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update`, `dailyrainin` FROM `dailyrain` WHERE `dailyrainin` = (SELECT MAX(`dailyrainin`) FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())) AND YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE()) ORDER BY `date` DESC LIMIT 1"));
        $this->rainfall_IN_most_recorded_month = date($config->site->dashboard_display_date, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_month = (float)$result['dailyrainin']; // Inches
        $this->rainfall_MM_most_month = round($result['dailyrainin'] * 25.4, 2); // Millimeters

        // Last Month Rainfall:
        if (isset($archiveLastMonth)) {
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT SUM(`dailyrainin`) AS `rainfall_last_month_total` FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`date`) = MONTH(CURDATE() - INTERVAL 1 MONTH)"));
            $this->rainfall_IN_total_last_month = (float)$result['rainfall_last_month_total']; // Inches
            $this->rainfall_MM_total_last_month = round($result['rainfall_last_month_total'] * 25.4,
                2); // Millimeters
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `last_update`, `dailyrainin` FROM `dailyrain` WHERE `dailyrainin` = (SELECT MAX(`dailyrainin`) FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`date`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`date`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`date`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `date` DESC LIMIT 1"));
            $this->rainfall_IN_most_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['last_update'])); // Recorded at
            $this->rainfall_IN_most_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['last_update'])); // Recorded at
            $this->rainfall_IN_most_last_month = (float)$result['dailyrainin']; // Inches
            $this->rainfall_MM_most_last_month = round($result['dailyrainin'] * 25.4, 2); // Millimeters
        } else {
            $this->rainfall_IN_total_last_month = null;
            $this->rainfall_MM_total_last_month = null;
            $this->rainfall_IN_most_recorded_last_month = null;
            $this->rainfall_IN_most_recorded_last_month_JSON = null;
            $this->rainfall_IN_most_last_month = null;
            $this->rainfall_MM_most_last_month = null;
        }

        // Yearly Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT SUM(`dailyrainin`) AS `rainfall_year_total` FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE())"));
        $this->rainfall_IN_total_year = (float)$result['rainfall_year_total']; // Inches
        $this->rainfall_MM_total_year = round($result['rainfall_year_total'] * 25.4, 2); // Millimeters
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update`, `dailyrainin` FROM `dailyrain` WHERE `dailyrainin` = (SELECT MAX(`dailyrainin`) FROM `dailyrain` WHERE YEAR(`date`) = YEAR(CURDATE())) AND YEAR(`date`) = YEAR(CURDATE()) ORDER BY `date` DESC LIMIT 1"));
        $this->rainfall_IN_most_recorded_year = date($config->site->dashboard_display_date, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_year = (float)$result['dailyrainin']; // Inches
        $this->rainfall_MM_most_year = round($result['dailyrainin'] * 25.4, 2); // Millimeters

        // All Time Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT SUM(`dailyrainin`) AS `rainfall_all_time_total` FROM `dailyrain`"));
        $since = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `date` FROM `dailyrain` ORDER BY `date` LIMIT 1"));
        $this->rainfall_IN_total_ever_since = date('j M Y', strtotime($since['date'])); // Results since
        $this->rainfall_IN_total_ever_since_JSON = date($config->site->date_api_json, strtotime($since['date'])); // Results since
        $this->rainfall_IN_total_ever = (float)$result['rainfall_all_time_total']; // Inches
        $this->rainfall_MM_total_ever = round($result['rainfall_all_time_total'] * 25.4, 2); // Millimeters
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update`, `dailyrainin` FROM `dailyrain` WHERE `dailyrainin` = (SELECT MAX(`dailyrainin`) FROM `dailyrain`) ORDER BY `last_update` DESC LIMIT 1"));
        $this->rainfall_IN_most_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['last_update'])); // Recorded at
        $this->rainfall_IN_most_ever = (float)$result['dailyrainin']; // Inches
        $this->rainfall_MM_most_ever = round($result['dailyrainin'] * 25.4, 2); // Millimeters
    }

    // Private Functions

    // Calculate human-readable wind direction from a range of values:
    private
    function windDirection($windDEG): string
    {
        if ($windDEG === 0) {
            $windDEG = 0.1;
        }
        switch ($windDEG) {
            case ($windDEG >= 11.25 && $windDEG < 33.75):
                $windDIR = 'NNE';
                break;
            case ($windDEG >= 33.75 && $windDEG < 56.25):
                $windDIR = 'NE';
                break;
            case ($windDEG >= 56.25 && $windDEG < 78.75):
                $windDIR = 'ENE';
                break;
            case ($windDEG >= 78.75 && $windDEG < 101.25):
                $windDIR = 'E';
                break;
            case ($windDEG >= 101.25 && $windDEG < 123.75):
                $windDIR = 'ESE';
                break;
            case ($windDEG >= 123.75 && $windDEG < 146.25):
                $windDIR = 'SE';
                break;
            case ($windDEG >= 146.25 && $windDEG < 168.75):
                $windDIR = 'SSE';
                break;
            case ($windDEG >= 168.75 && $windDEG < 191.25):
                $windDIR = 'S';
                break;
            case ($windDEG >= 191.25 && $windDEG < 213.75):
                $windDIR = 'SSW';
                break;
            case ($windDEG >= 213.75 && $windDEG < 236.25):
                $windDIR = 'SW';
                break;
            case ($windDEG >= 236.25 && $windDEG < 258.75):
                $windDIR = 'WSW';
                break;
            case ($windDEG >= 258.75 && $windDEG < 281.25):
                $windDIR = 'W';
                break;
            case ($windDEG >= 281.25 && $windDEG < 303.75):
                $windDIR = 'WNW';
                break;
            case ($windDEG >= 303.75 && $windDEG < 326.25):
                $windDIR = 'NW';
                break;
            case ($windDEG >= 326.25 && $windDEG < 348.75):
                $windDIR = 'NNW';
                break;
            default:
                $windDIR = 'N';
                break;
        }
        return $windDIR;

    }

    // Public Functions

    // Yesterdays Archive Data
    public
    function getYesterday(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_yesterday,
            'tempF_low' => $this->tempF_low_yesterday,
            'tempC_high' => $this->tempC_high_yesterday,
            'tempC_low' => $this->tempC_low_yesterday,
            'tempF_high_recorded' => $this->tempF_high_recorded_yesterday,
            'tempF_low_recorded' => $this->tempF_low_recorded_yesterday,
            'windS_mph_high' => $this->windS_mph_high_yesterday,
            'windS_kmh_high' => $this->windS_kmh_high_yesterday,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_yesterday,
            'windDIR' => $this->windDirection($this->windDIR_yesterday),
            'pressure_inHg_high' => $this->pressure_inHg_high_yesterday,
            'pressure_kPa_high' => $this->pressure_kPa_high_yesterday,
            'pressure_inHg_low' => $this->pressure_inHg_low_yesterday,
            'pressure_kPa_low' => $this->pressure_kPa_low_yesterday,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_yesterday,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_yesterday,
            'relH_high' => $this->relH_high_yesterday,
            'relH_low' => $this->relH_low_yesterday,
            'relH_high_recorded' => $this->relH_high_recorded_yesterday,
            'relH_low_recorded' => $this->relH_low_recorded_yesterday,
            'rainfall_IN_total' => $this->rainfall_IN_total_yesterday,
            'rainfall_MM_total' => $this->rainfall_MM_total_yesterday
        );
    }

    // Yesterdays JSON Archive Data
    public
    function getJSONYesterday(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_yesterday,
            'tempF_low' => $this->tempF_low_yesterday,
            'tempC_high' => $this->tempC_high_yesterday,
            'tempC_low' => $this->tempC_low_yesterday,
            'tempF_high_recorded' => $this->tempF_high_recorded_yesterday_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_yesterday_JSON,
            'windS_mph_high' => $this->windS_mph_high_yesterday,
            'windS_kmh_high' => $this->windS_kmh_high_yesterday,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_yesterday_JSON,
            'windDIR' => $this->windDirection($this->windDIR_yesterday),
            'pressure_inHg_high' => $this->pressure_inHg_high_yesterday,
            'pressure_kPa_high' => $this->pressure_kPa_high_yesterday,
            'pressure_inHg_low' => $this->pressure_inHg_low_yesterday,
            'pressure_kPa_low' => $this->pressure_kPa_low_yesterday,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_yesterday_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_yesterday_JSON,
            'relH_high' => $this->relH_high_yesterday,
            'relH_low' => $this->relH_low_yesterday,
            'relH_high_recorded' => $this->relH_high_recorded_yesterday_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_yesterday_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_yesterday,
            'rainfall_MM_total' => $this->rainfall_MM_total_yesterday
        );
    }

    // This Week's Archive Data
    public
    function getWeek(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_week,
            'tempF_low' => $this->tempF_low_week,
            'tempC_high' => $this->tempC_high_week,
            'tempC_low' => $this->tempC_low_week,
            'tempF_high_recorded' => $this->tempF_high_recorded_week,
            'tempF_low_recorded' => $this->tempF_low_recorded_week,
            'windS_mph_high' => $this->windS_mph_high_week,
            'windS_kmh_high' => $this->windS_kmh_high_week,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_week,
            'windDIR' => $this->windDirection($this->windDIR_week),
            'pressure_inHg_high' => $this->pressure_inHg_high_week,
            'pressure_kPa_high' => $this->pressure_kPa_high_week,
            'pressure_inHg_low' => $this->pressure_inHg_low_week,
            'pressure_kPa_low' => $this->pressure_kPa_low_week,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_week,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_week,
            'relH_high' => $this->relH_high_week,
            'relH_low' => $this->relH_low_week,
            'relH_high_recorded' => $this->relH_high_recorded_week,
            'relH_low_recorded' => $this->relH_low_recorded_week,
            'rainfall_IN_most' => $this->rainfall_IN_most_week,
            'rainfall_MM_most' => $this->rainfall_MM_most_week,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_week,
            'rainfall_IN_total' => $this->rainfall_IN_total_week,
            'rainfall_MM_total' => $this->rainfall_MM_total_week
        );
    }

    // This Week's JSON Archive Data
    public
    function getJSONWeek(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_week,
            'tempF_low' => $this->tempF_low_week,
            'tempC_high' => $this->tempC_high_week,
            'tempC_low' => $this->tempC_low_week,
            'tempF_high_recorded' => $this->tempF_high_recorded_week_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_week_JSON,
            'windS_mph_high' => $this->windS_mph_high_week,
            'windS_kmh_high' => $this->windS_kmh_high_week,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_week_JSON,
            'windDIR' => $this->windDirection($this->windDIR_week),
            'pressure_inHg_high' => $this->pressure_inHg_high_week,
            'pressure_kPa_high' => $this->pressure_kPa_high_week,
            'pressure_inHg_low' => $this->pressure_inHg_low_week,
            'pressure_kPa_low' => $this->pressure_kPa_low_week,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_week_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_week_JSON,
            'relH_high' => $this->relH_high_week,
            'relH_low' => $this->relH_low_week,
            'relH_high_recorded' => $this->relH_high_recorded_week_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_week_JSON,
            'rainfall_IN_most' => $this->rainfall_IN_most_week,
            'rainfall_MM_most' => $this->rainfall_MM_most_week,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_week_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_week,
            'rainfall_MM_total' => $this->rainfall_MM_total_week
        );
    }

    // This Month's Archive Data
    public
    function getMonth(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_month,
            'tempF_low' => $this->tempF_low_month,
            'tempC_high' => $this->tempC_high_month,
            'tempC_low' => $this->tempC_low_month,
            'tempF_high_recorded' => $this->tempF_high_recorded_month,
            'tempF_low_recorded' => $this->tempF_low_recorded_month,
            'windS_mph_high' => $this->windS_mph_high_month,
            'windS_kmh_high' => $this->windS_kmh_high_month,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_month,
            'windDIR' => $this->windDirection($this->windDIR_month),
            'pressure_inHg_high' => $this->pressure_inHg_high_month,
            'pressure_kPa_high' => $this->pressure_kPa_high_month,
            'pressure_inHg_low' => $this->pressure_inHg_low_month,
            'pressure_kPa_low' => $this->pressure_kPa_low_month,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_month,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_month,
            'relH_high' => $this->relH_high_month,
            'relH_low' => $this->relH_low_month,
            'relH_high_recorded' => $this->relH_high_recorded_month,
            'relH_low_recorded' => $this->relH_low_recorded_month,
            'rainfall_IN_most' => $this->rainfall_IN_most_month,
            'rainfall_MM_most' => $this->rainfall_MM_most_month,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_month,
            'rainfall_IN_total' => $this->rainfall_IN_total_month,
            'rainfall_MM_total' => $this->rainfall_MM_total_month
        );
    }

    // This Month's JSON Archive Data
    public
    function getJSONMonth(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_month,
            'tempF_low' => $this->tempF_low_month,
            'tempC_high' => $this->tempC_high_month,
            'tempC_low' => $this->tempC_low_month,
            'tempF_high_recorded' => $this->tempF_high_recorded_month_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_month_JSON,
            'windS_mph_high' => $this->windS_mph_high_month,
            'windS_kmh_high' => $this->windS_kmh_high_month,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_month_JSON,
            'windDIR' => $this->windDirection($this->windDIR_month),
            'pressure_inHg_high' => $this->pressure_inHg_high_month,
            'pressure_kPa_high' => $this->pressure_kPa_high_month,
            'pressure_inHg_low' => $this->pressure_inHg_low_month,
            'pressure_kPa_low' => $this->pressure_kPa_low_month,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_month_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_month_JSON,
            'relH_high' => $this->relH_high_month,
            'relH_low' => $this->relH_low_month,
            'relH_high_recorded' => $this->relH_high_recorded_month_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_month_JSON,
            'rainfall_IN_most' => $this->rainfall_IN_most_month,
            'rainfall_MM_most' => $this->rainfall_MM_most_month,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_month_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_month,
            'rainfall_MM_total' => $this->rainfall_MM_total_month
        );
    }

    // Last Months Archive Data
    public
    function getLastMonth(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_last_month,
            'tempF_low' => $this->tempF_low_last_month,
            'tempC_high' => $this->tempC_high_last_month,
            'tempC_low' => $this->tempC_low_last_month,
            'tempF_high_recorded' => $this->tempF_high_recorded_last_month,
            'tempF_low_recorded' => $this->tempF_low_recorded_last_month,
            'windS_mph_high' => $this->windS_mph_high_last_month,
            'windS_kmh_high' => $this->windS_kmh_high_last_month,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_last_month,
            'windDIR' => $this->windDirection($this->windDIR_last_month),
            'pressure_inHg_high' => $this->pressure_inHg_high_last_month,
            'pressure_kPa_high' => $this->pressure_kPa_high_last_month,
            'pressure_inHg_low' => $this->pressure_inHg_low_last_month,
            'pressure_kPa_low' => $this->pressure_kPa_low_last_month,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_last_month,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_last_month,
            'relH_high' => $this->relH_high_last_month,
            'relH_low' => $this->relH_low_last_month,
            'relH_high_recorded' => $this->relH_high_recorded_last_month,
            'relH_low_recorded' => $this->relH_low_recorded_last_month,
            'rainfall_IN_most' => $this->rainfall_IN_most_last_month,
            'rainfall_MM_most' => $this->rainfall_MM_most_last_month,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_last_month,
            'rainfall_IN_total' => $this->rainfall_IN_total_last_month,
            'rainfall_MM_total' => $this->rainfall_MM_total_last_month
        );
    }

    // Last Months JSON Archive Data
    public
    function getJSONLastMonth(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_last_month,
            'tempF_low' => $this->tempF_low_last_month,
            'tempC_high' => $this->tempC_high_last_month,
            'tempC_low' => $this->tempC_low_last_month,
            'tempF_high_recorded' => $this->tempF_high_recorded_last_month_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_last_month_JSON,
            'windS_mph_high' => $this->windS_mph_high_last_month,
            'windS_kmh_high' => $this->windS_kmh_high_last_month,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_last_month_JSON,
            'windDIR' => $this->windDirection($this->windDIR_last_month),
            'pressure_inHg_high' => $this->pressure_inHg_high_last_month,
            'pressure_kPa_high' => $this->pressure_kPa_high_last_month,
            'pressure_inHg_low' => $this->pressure_inHg_low_last_month,
            'pressure_kPa_low' => $this->pressure_kPa_low_last_month,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_last_month_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_last_month_JSON,
            'relH_high' => $this->relH_high_last_month,
            'relH_low' => $this->relH_low_last_month,
            'relH_high_recorded' => $this->relH_high_recorded_last_month_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_last_month_JSON,
            'rainfall_IN_most' => $this->rainfall_IN_most_last_month,
            'rainfall_MM_most' => $this->rainfall_MM_most_last_month,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_last_month_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_last_month,
            'rainfall_MM_total' => $this->rainfall_MM_total_last_month
        );
    }

    // This Year
    public
    function getYear(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_year,
            'tempF_low' => $this->tempF_low_year,
            'tempC_high' => $this->tempC_high_year,
            'tempC_low' => $this->tempC_low_year,
            'tempF_high_recorded' => $this->tempF_high_recorded_year,
            'tempF_low_recorded' => $this->tempF_low_recorded_year,
            'windS_mph_high' => $this->windS_mph_high_year,
            'windS_kmh_high' => $this->windS_kmh_high_year,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_year,
            'windDIR' => $this->windDirection($this->windDIR_year),
            'pressure_inHg_high' => $this->pressure_inHg_high_year,
            'pressure_kPa_high' => $this->pressure_kPa_high_year,
            'pressure_inHg_low' => $this->pressure_inHg_low_year,
            'pressure_kPa_low' => $this->pressure_kPa_low_year,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_year,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_year,
            'relH_high' => $this->relH_high_year,
            'relH_low' => $this->relH_low_year,
            'relH_high_recorded' => $this->relH_high_recorded_year,
            'relH_low_recorded' => $this->relH_low_recorded_year,
            'rainfall_IN_most' => $this->rainfall_IN_most_year,
            'rainfall_MM_most' => $this->rainfall_MM_most_year,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_year,
            'rainfall_IN_total' => $this->rainfall_IN_total_year,
            'rainfall_MM_total' => $this->rainfall_MM_total_year
        );
    }

    // This JSON Year
    public
    function getJSONYear(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_year,
            'tempF_low' => $this->tempF_low_year,
            'tempC_high' => $this->tempC_high_year,
            'tempC_low' => $this->tempC_low_year,
            'tempF_high_recorded' => $this->tempF_high_recorded_year_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_year_JSON,
            'windS_mph_high' => $this->windS_mph_high_year,
            'windS_kmh_high' => $this->windS_kmh_high_year,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_year_JSON,
            'windDIR' => $this->windDirection($this->windDIR_year),
            'pressure_inHg_high' => $this->pressure_inHg_high_year,
            'pressure_kPa_high' => $this->pressure_kPa_high_year,
            'pressure_inHg_low' => $this->pressure_inHg_low_year,
            'pressure_kPa_low' => $this->pressure_kPa_low_year,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_year_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_year_JSON,
            'relH_high' => $this->relH_high_year,
            'relH_low' => $this->relH_low_year,
            'relH_high_recorded' => $this->relH_high_recorded_year_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_year_JSON,
            'rainfall_IN_most' => $this->rainfall_IN_most_year,
            'rainfall_MM_most' => $this->rainfall_MM_most_year,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_year_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_year,
            'rainfall_MM_total' => $this->rainfall_MM_total_year
        );
    }

    // All-Time Records
    public
    function getAllTime(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_ever,
            'tempF_low' => $this->tempF_low_ever,
            'tempC_high' => $this->tempC_high_ever,
            'tempC_low' => $this->tempC_low_ever,
            'tempF_high_recorded' => $this->tempF_high_recorded_ever,
            'tempF_low_recorded' => $this->tempF_low_recorded_ever,
            'windS_mph_high' => $this->windS_mph_high_ever,
            'windS_kmh_high' => $this->windS_kmh_high_ever,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_ever,
            'windDIR' => $this->windDirection($this->windDIR_ever),
            'pressure_inHg_high' => $this->pressure_inHg_high_ever,
            'pressure_kPa_high' => $this->pressure_kPa_high_ever,
            'pressure_inHg_low' => $this->pressure_inHg_low_ever,
            'pressure_kPa_low' => $this->pressure_kPa_low_ever,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_ever,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_ever,
            'relH_high' => $this->relH_high_ever,
            'relH_low' => $this->relH_low_ever,
            'relH_high_recorded' => $this->relH_high_recorded_ever,
            'relH_low_recorded' => $this->relH_low_recorded_ever,
            'rainfall_IN_most' => $this->rainfall_IN_most_ever,
            'rainfall_MM_most' => $this->rainfall_MM_most_ever,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_ever,
            'rainfall_IN_total' => $this->rainfall_IN_total_ever,
            'rainfall_MM_total' => $this->rainfall_MM_total_ever,
            'rainfall_IN_total_since' => $this->rainfall_IN_total_ever_since
        );
    }

    // All Time JSON Records
    public
    function getJSONAllTime(): object
    {
        return (object)array(
            'tempF_high' => $this->tempF_high_ever,
            'tempF_low' => $this->tempF_low_ever,
            'tempC_high' => $this->tempC_high_ever,
            'tempC_low' => $this->tempC_low_ever,
            'tempF_high_recorded' => $this->tempF_high_recorded_ever_JSON,
            'tempF_low_recorded' => $this->tempF_low_recorded_ever_JSON,
            'windS_mph_high' => $this->windS_mph_high_ever,
            'windS_kmh_high' => $this->windS_kmh_high_ever,
            'windS_mph_high_recorded' => $this->windS_mph_high_recorded_ever_JSON,
            'windDIR' => $this->windDirection($this->windDIR_ever),
            'pressure_inHg_high' => $this->pressure_inHg_high_ever,
            'pressure_kPa_high' => $this->pressure_kPa_high_ever,
            'pressure_inHg_low' => $this->pressure_inHg_low_ever,
            'pressure_kPa_low' => $this->pressure_kPa_low_ever,
            'pressure_inHg_high_recorded' => $this->pressure_inHg_high_recorded_ever_JSON,
            'pressure_inHg_low_recorded' => $this->pressure_inHg_low_recorded_ever_JSON,
            'relH_high' => $this->relH_high_ever,
            'relH_low' => $this->relH_low_ever,
            'relH_high_recorded' => $this->relH_high_recorded_ever_JSON,
            'relH_low_recorded' => $this->relH_low_recorded_ever_JSON,
            'rainfall_IN_most' => $this->rainfall_IN_most_ever,
            'rainfall_MM_most' => $this->rainfall_MM_most_ever,
            'rainfall_IN_most_recorded' => $this->rainfall_IN_most_recorded_ever_JSON,
            'rainfall_IN_total' => $this->rainfall_IN_total_ever,
            'rainfall_MM_total' => $this->rainfall_MM_total_ever,
            'rainfall_IN_total_since' => $this->rainfall_IN_total_ever_since_JSON
        );
    }
}
