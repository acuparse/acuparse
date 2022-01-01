<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/fcn/weather/getArchiveAtlasWeatherData.php
 * Gets the Atlas archive weather data from the database
 */
class getArchiveAtlasWeatherData
{
    // Set variables

    // Yesterday
    private $uvindex_high_yesterday;
    private $uvindex_high_recorded_yesterday;
    private $uvindex_high_recorded_yesterday_JSON;
    private $light_high_yesterday;
    private $light_high_recorded_yesterday;
    private $light_high_recorded_yesterday_JSON;
    private $lightSeconds_high_yesterday;
    private $lightSeconds_high_recorded_yesterday;
    private $lightSeconds_high_recorded_yesterday_JSON;
    private $lightning_yesterday;
    private $lightning_recorded_yesterday;
    private $lightning_recorded_yesterday_JSON;

    // This Week
    private $uvindex_high_week;
    private $uvindex_high_recorded_week;
    private $uvindex_high_recorded_week_JSON;
    private $light_high_week;
    private $light_high_recorded_week;
    private $light_high_recorded_week_JSON;
    private $lightSeconds_high_week;
    private $lightSeconds_high_recorded_week;
    private $lightSeconds_high_recorded_week_JSON;
    private $lightning_week;
    private $lightning_recorded_week;
    private $lightning_recorded_week_JSON;

    // This Month
    private $uvindex_high_month;
    private $uvindex_high_recorded_month;
    private $uvindex_high_recorded_month_JSON;
    private $light_high_month;
    private $light_high_recorded_month;
    private $light_high_recorded_month_JSON;
    private $lightSeconds_high_month;
    private $lightSeconds_high_recorded_month;
    private $lightSeconds_high_recorded_month_JSON;
    private $lightning_month;
    private $lightning_recorded_month;
    private $lightning_recorded_month_JSON;

    // Last Month
    private $uvindex_high_last_month;
    private $uvindex_high_recorded_last_month;
    private $uvindex_high_recorded_last_month_JSON;
    private $light_high_last_month;
    private $light_high_recorded_last_month;
    private $light_high_recorded_last_month_JSON;
    private $lightSeconds_high_last_month;
    private $lightSeconds_high_recorded_last_month;
    private $lightSeconds_high_recorded_last_month_JSON;
    private $lightning_last_month;
    private $lightning_recorded_last_month;
    private $lightning_recorded_last_month_JSON;

    // This Year
    private $uvindex_high_year;
    private $uvindex_high_recorded_year;
    private $uvindex_high_recorded_year_JSON;
    private $light_high_year;
    private $light_high_recorded_year;
    private $light_high_recorded_year_JSON;
    private $lightSeconds_high_year;
    private $lightSeconds_high_recorded_year;
    private $lightSeconds_high_recorded_year_JSON;
    private $lightning_year;
    private $lightning_recorded_year;
    private $lightning_recorded_year_JSON;

    // All Time
    private $uvindex_high_ever;
    private $uvindex_high_recorded_ever;
    private $uvindex_high_recorded_ever_JSON;
    private $light_high_ever;
    private $light_high_recorded_ever;
    private $light_high_recorded_ever_JSON;
    private $lightSeconds_high_ever;
    private $lightSeconds_high_recorded_ever;
    private $lightSeconds_high_recorded_ever_JSON;
    private $lightning_ever;
    private $lightning_recorded_ever;
    private $lightning_recorded_ever_JSON;

    function __construct($json = null)
    {
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
        if (!isset($lastUpdate)) {
            if ($json === true) {
                $json_output = ['Status' => 'error', 'message' => 'No Atlas Archive Data Reported'];
                echo json_encode($json_output);
            } else {
                echo '<div class="col text-center alert alert-danger"><p><strong>No Atlas Archive Data Reported!</strong><br>Check that your Cron tasks are running! See your <a href="https://docs.acuparse.com/TROUBLESHOOTING/#logs">logs</a> for more details.</p></div>';
            }
            exit();
        }

        // Process UV:
        // Yesterday High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_yesterday = (int)$result['uvindex']; // Percent
        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_week = (int)$result['uvindex']; // Percent
        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_month = (int)$result['uvindex']; // Percent
        // Last Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_last_month = (int)$result['uvindex']; // Percent
        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_year = (int)$result['uvindex']; // Percent
        // All Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `uvindex` FROM `archive` WHERE `uvindex` = (SELECT MAX(`uvindex`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->uvindex_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->uvindex_high_ever = (int)$result['uvindex']; // Percent

        // Process Light Illuminance:
        // Yesterday High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_yesterday = (int)$result['light']; // Percent
        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_week = (int)$result['light']; // Percent
        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_month = (int)$result['light']; // Percent
        // Last Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_last_month = (int)$result['light']; // Percent
        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_year = (int)$result['light']; // Percent
        // All Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `light` FROM `archive` WHERE `light` = (SELECT MAX(`light`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->light_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->light_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->light_high_ever = (int)$result['light']; // Percent

        // Process Light Measured:
        // Yesterday High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_yesterday = (int)$result['lightSeconds']; // Seconds
        // This Week High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_week = (int)$result['lightSeconds']; // Seconds
        // This Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_month = (int)$result['lightSeconds']; // Seconds
        // Last Month High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_last_month = (int)$result['lightSeconds']; // Seconds
        // This Year High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_year = (int)$result['lightSeconds']; // Seconds
        // All Time High
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightSeconds` FROM `archive` WHERE `lightSeconds` = (SELECT MAX(`lightSeconds`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightSeconds_high_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightSeconds_high_ever = (int)$result['lightSeconds']; // Seconds

        // Process Lightning:
        // Yesterday
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive` WHERE DATE(`reported`) = SUBDATE(CURDATE(),1)) AND DATE(`reported`) = SUBDATE(CURDATE(),1) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_yesterday = date($config->site->dashboard_display_time, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_yesterday_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_yesterday = (int)$result['lightning']; // Percent
        // This Week
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive` WHERE YEARWEEK(`reported`) = YEARWEEK(CURDATE())) AND YEARWEEK(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_week = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_week_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_week = (int)$result['lightning']; // Percent
        // This Month
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) AND MONTH(`reported`) = MONTH(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_month = (int)$result['lightning']; // Percent
        // Last Month
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH)) AND YEAR(`reported`) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(`reported`) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_last_month = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_last_month_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_last_month = (int)$result['lightning']; // Percent
        // This Year
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive` WHERE YEAR(`reported`) = YEAR(CURDATE())) AND YEAR(`reported`) = YEAR(CURDATE()) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_year = date($config->site->dashboard_display_date, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_year_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_year = (int)$result['lightning']; // Percent
        // All Time
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `lightning` FROM `archive` WHERE `lightning` = (SELECT MAX(`lightning`) FROM `archive`) ORDER BY `reported` DESC LIMIT 1"));
        $this->lightning_recorded_ever = date($config->site->dashboard_display_date_full, strtotime($result['reported'])); // Recorded at
        $this->lightning_recorded_ever_JSON = date($config->site->date_api_json, strtotime($result['reported'])); // Recorded at
        $this->lightning_ever = (int)$result['lightning']; // Percent
    }
    //Private Functions

    // Calculate Light Hours
    private function calculateLightHours($seconds): float
    {
        return round($seconds / 3600, 2);
    }

    // Public Functions

    // Yesterdays Archive Data
    public function getYesterday(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_yesterday,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_yesterday,
            'light_high' => $this->light_high_yesterday,
            'light_high_recorded' => $this->light_high_recorded_yesterday,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_yesterday),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_yesterday,
            'lightning' => $this->lightning_yesterday,
            'lightning_recorded' => $this->lightning_recorded_yesterday
        );
    }

    // Yesterdays JSON Archive Data
    public function getJSONYesterday(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_yesterday,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_yesterday_JSON,
            'light_high' => $this->light_high_yesterday,
            'light_high_recorded' => $this->light_high_recorded_yesterday_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_yesterday),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_yesterday_JSON,
            'lightning' => $this->lightning_yesterday,
            'lightning_recorded' => $this->lightning_recorded_yesterday_JSON
        );
    }

    // This Weeks Archive Data
    public function getWeek(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_week,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_week,
            'light_high' => $this->light_high_week,
            'light_high_recorded' => $this->light_high_recorded_week,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_week),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_week,
            'lightning' => $this->lightning_week,
            'lightning_recorded' => $this->lightning_recorded_week
        );
    }

    // This Weeks JSON Archive Data
    public function getJSONWeek(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_week,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_week_JSON,
            'light_high' => $this->light_high_week,
            'light_high_recorded' => $this->light_high_recorded_week_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_week),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_week_JSON,
            'lightning' => $this->lightning_week,
            'lightning_recorded' => $this->lightning_recorded_week_JSON
        );
    }

    // This Months Archive Data
    public function getMonth(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_month,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_month,
            'light_high' => $this->light_high_month,
            'light_high_recorded' => $this->light_high_recorded_month,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_month),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_month,
            'lightning' => $this->lightning_month,
            'lightning_recorded' => $this->lightning_recorded_month
        );
    }

    // This Months JSON Archive Data
    public function getJSONMonth(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_month,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_month_JSON,
            'light_high' => $this->light_high_month,
            'light_high_recorded' => $this->light_high_recorded_month_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_month),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_month_JSON,
            'lightning' => $this->lightning_month,
            'lightning_recorded' => $this->lightning_recorded_month_JSON
        );
    }

    // Last Months Archive Data
    public function getLastMonth(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_last_month,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_last_month,
            'light_high' => $this->light_high_last_month,
            'light_high_recorded' => $this->light_high_recorded_last_month,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_last_month),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_last_month,
            'lightning' => $this->lightning_last_month,
            'lightning_recorded' => $this->lightning_recorded_last_month,
        );
    }

    // Last Months JSON Archive Data
    public function getJSONLastMonth(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_last_month,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_last_month_JSON,
            'light_high' => $this->light_high_last_month,
            'light_high_recorded' => $this->light_high_recorded_last_month_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_last_month),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_last_month_JSON,
            'lightning' => $this->lightning_last_month,
            'lightning_recorded' => $this->lightning_recorded_last_month_JSON,
        );
    }

    // This Year
    public function getYear(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_year,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_year,
            'light_high' => $this->light_high_year,
            'light_high_recorded' => $this->light_high_recorded_year,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_year),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_year,
            'lightning' => $this->lightning_year,
            'lightning_recorded' => $this->lightning_recorded_year,
        );
    }

    // This JSON Year
    public function getJSONYear(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_year,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_year_JSON,
            'light_high' => $this->light_high_year,
            'light_high_recorded' => $this->light_high_recorded_year_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_year),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_year_JSON,
            'lightning' => $this->lightning_year,
            'lightning_recorded' => $this->lightning_recorded_year_JSON,
        );
    }

    // All Time Records
    public function getAllTime(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_ever,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_ever,
            'light_high' => $this->light_high_ever,
            'light_high_recorded' => $this->light_high_recorded_ever,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_ever),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_ever,
            'lightning' => $this->lightning_ever,
            'lightning_recorded' => $this->lightning_recorded_ever,
        );
    }

    // All Time Records
    public function getJSONAllTime(): object
    {
        return (object)array(
            'uvindex_high' => $this->uvindex_high_ever,
            'uvindex_high_recorded' => $this->uvindex_high_recorded_ever_JSON,
            'light_high' => $this->light_high_ever,
            'light_high_recorded' => $this->light_high_recorded_ever_JSON,
            'lightHours_high' => $this->calculateLightHours($this->lightSeconds_high_ever),
            'lightHours_high_recorded' => $this->lightSeconds_high_recorded_ever_JSON,
            'lightning' => $this->lightning_ever,
            'lightning_recorded' => $this->lightning_recorded_ever_JSON,
        );
    }
}
