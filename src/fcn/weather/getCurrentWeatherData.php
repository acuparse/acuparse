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
 * File: src/fcn/weather/getCurrentWeatherData.php
 * Gets the current weather data from the database
 */
class getCurrentWeatherData
{
    // Set variables
    private $windSpeedMPH;
    private $windSkmh;
    private $windDEG;
    private $windDEG_peak;
    private $wind_recorded_peak;
    private $windSpeedMPH_peak;
    private $windSkmh_peak;
    private $pressure_inHg;
    private $pressure_kPa;
    private $tempF;
    private $tempC;
    private $high_temp_recorded;
    private $tempC_high;
    private $tempF_high;
    private $low_temp_recorded;
    private $tempC_low;
    private $tempF_low;
    private $tempC_avg;
    private $tempF_avg;
    private $feelsF;
    private $feelsC;
    private $dewptF;
    private $dewptC;
    private $relH;
    private $rainIN;
    private $rainMM;
    private $rainTotalIN_today;
    private $rainTotalMM_today;
    private $sunrise;
    private $sunset;
    private $moonrise;
    private $moonset;
    private $moon_age;
    private $moon_stage;
    private $next_new_moon;
    private $next_full_moon;
    private $last_new_moon;
    private $last_full_moon;
    private $moon_illumination;
    private $lastUpdate;

    function __construct()
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

        /** @var mysqli $conn Global MYSQL Connection */
        /**
         * @return array
         * @var object $config Global Config
         */

        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp` FROM `pressure`"));
        if (!isset($lastUpdate)) {
            exit();
        }

        // Get Moon Data:
        require(APP_BASE_PATH . '/pub/lib/mit/moon/moonPhase.php');
        $moon = new Solaris\MoonPhase();
        $this->moon_age = round($moon->get('age'));
        $this->moon_stage = $moon->phase_name();
        $this->next_new_moon = date($config->site->dashboard_display_date, $moon->get_phase('next_new_moon'));
        $this->next_full_moon = date($config->site->dashboard_display_date, $moon->get_phase('next_full_moon'));
        $this->last_new_moon = date($config->site->dashboard_display_date, $moon->get_phase('new_moon'));
        $this->last_full_moon = date($config->site->dashboard_display_date, $moon->get_phase('full_moon'));
        $this->moon_illumination = round($moon->get('illumination'), 1) * 100 . '%';

        // Moon rise/set
        if (file_exists(APP_BASE_PATH . '/pub/lib/gpl/moon/moontime.php')) {
            require(APP_BASE_PATH . '/pub/lib/gpl/moon/moontime.php');
            $moon_time = Moon::calculateMoonTimes($config->site->lat, $config->site->long);
            $this->moonrise = gmdate($config->site->dashboard_display_date, $moon_time->moonrise);
            $this->moonset = gmdate($config->site->dashboard_display_date, $moon_time->moonset);
        }

        // Get Sun Data
        $zenith = 90 + (50 / 60);
        $offset = date('Z') / 3600;
        $this->sunrise = date_sunrise(time(), SUNFUNCS_RET_STRING, $config->site->lat, $config->site->long, $zenith, $offset);
        $this->sunset = date_sunset(time(), SUNFUNCS_RET_STRING, $config->site->lat, $config->site->long, $zenith, $offset);

        // Process Wind Speed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `speedMPH` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windSpeedMPH = (int)round($result['speedMPH']); // Miles per hour
        $this->windSkmh = (int)round($result['speedMPH'] * 1.60934); // Convert to Kilometers per hour

        // Process Wind Direction:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `degrees` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windDEG = (int)$result['degrees']; // Degrees

        // Today's Peak Windspeed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSpeedMPH`, `windDEG` FROM `archive` WHERE `windSpeedMPH` = (SELECT MAX(`windSpeedMPH`) FROM `archive` WHERE DATE(`reported`) = CURDATE()) AND DATE(`reported`) = CURDATE() ORDER BY `reported` DESC LIMIT 1"));
        $this->wind_recorded_peak = date('H:i', strtotime($result['reported'])); // Recorded at
        $this->windSpeedMPH_peak = (int)round($result['windSpeedMPH']); // Miles per hour
        $this->windSkmh_peak = (int)round($result['windSpeedMPH'] * 1.60934); // Convert to Kilometers per hour
        $this->windDEG_peak = (int)$result['windDEG']; // Degrees

        // Process Pressure:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `inhg` FROM `pressure` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->pressure_inHg = (float)$result['inhg']; // Inches of Mercury
        $this->pressure_kPa = (float)round($result['inhg'] * 3.38638866667, 2); // Convert to Kilopascals

        // Process Temp:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `tempF` FROM `temperature` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->tempF = (float)round($result['tempF'], 1); // Fahrenheit
        $this->tempC = (float)round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // High Temp:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp`, `tempF` FROM `temperature` WHERE `tempF` = (SELECT MAX(tempF) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE())
              AND DATE(`timestamp`) = CURDATE()"));
        $this->high_temp_recorded = date('H:i', strtotime($result['timestamp'])); // Recorded at
        $this->tempF_high = (float)round($result['tempF'], 1); // Fahrenheit
        $this->tempC_high = (float)round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Low Temp:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp`, `tempF` FROM `temperature` WHERE `tempF` = (SELECT MIN(tempF) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE())
              AND DATE(`timestamp`) = CURDATE()"));
        $this->low_temp_recorded = date('H:i', strtotime($result['timestamp'])); // Recorded at
        $this->tempF_low = (float)round($result['tempF'], 1); // Fahrenheit
        $this->tempC_low = (float)round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Average Temp:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(tempF) AS `avg_tempF` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()"));
        $this->tempF_avg = (float)round($result['avg_tempF'], 1); // Fahrenheit
        $this->tempC_avg = (float)round(($result['avg_tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

        // Process Humidity:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `relH` FROM `humidity` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->relH = (int)$result['relH']; // Percentage

        // Process Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `rainin` FROM `rainfall`"));
        $this->rainIN = (float)$result['rainin']; // Inches
        $this->rainMM = (float)round($result['rainin'] * 25.4, 2); // Millimeters

        // Today's Rainfall:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `dailyrainin` FROM `dailyrain` WHERE DATE(`date`) = CURDATE()"));
        $this->rainTotalIN_today = (float)$result['dailyrainin']; // Inches
        $this->rainTotalMM_today = (float)round($result['dailyrainin'] * 25.4, 2); // Millimeters

        // Last Update
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `timestamp` FROM `last_update`"));
        $this->lastUpdate = $result['timestamp'];
    }

    //Private Functions

    // Calculate human readable wind direction from a range of values:
    private function windDirection($windDEG)
    {
        switch ($windDEG) {
            case (is_null($windDEG)):
                $windDIR = 'ERROR';
                break;
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
        return (string)$windDIR;
    }

    // Calculate feels like temp
    private function feelsLike()
    {
        $feelsF = 0;
        $feelsC = 0;

        // Wind Chill:
        if ($this->tempC <= 5 && $this->windSkmh >= 3) {
            $feelsC = 13.12 + (0.6215 * (($this->tempF - 32) * 5 / 9)) - (11.37 * (($this->windSpeedMPH * 1.60934) ** 0.16)) + ((0.3965 * (($this->tempF - 32) * 5 / 9)) * (($this->windSpeedMPH * 1.60934) ** 0.16));
            $feelsF = $feelsC * 9 / 5 + 32;
        } // Heat Index:
        elseif ($this->tempF >= 80 && $this->relH >= 40) {
            $feelsF = -42.379 + (2.04901523 * $this->tempF) + (10.14333127 * $this->relH) - (0.22475541 * $this->tempF * $this->relH) - (6.83783 * (10 ** -3) * ($this->tempF ** 2)) - (5.481717 * (10 ** -2) * ($this->relH ** 2)) + (1.22874 * (10 ** -3) * ($this->tempF ** 2) * $this->relH) + (8.5282 * (10 ** -4) * $this->tempF * ($this->relH ** 2)) - (1.99 * (10 ** -6) * ($this->tempF ** 2) * ($this->relH ** 2));
            $feelsC = ($feelsF - 32) / 1.8;
        }

        return (object)array(
            'feelsF' => (float)round($feelsF, 1),
            'feelsC' => (float)round($feelsC, 1)
        );
    }

    // Calculate dew point
    private function dewPoint()
    {
        $dewptC = ((pow(($this->relH / 100), 0.125)) * (112 + 0.9 * $this->tempC) + (0.1 * $this->tempC) - 112);
        $dewptF = ($dewptC * 9 / 5) + 32;

        return (object)array(
            'dewptF' => (float)round($dewptF, 1),
            'dewptC' => (float)round($dewptC, 1)
        );
    }

    private function windBeaufort($windSpeedMPH)
    {
        $windSpeed = $windSpeedMPH;

        switch ($windSpeed) {
            case ($windSpeed >= 1 && $windSpeed <= 3):
                $beaufort = 1;
                break;
            case ($windSpeed >= 4 && $windSpeed <= 7):
                $beaufort = 2;
                break;
            case ($windSpeed >= 8 && $windSpeed <= 12):
                $beaufort = 3;
                break;
            case ($windSpeed >= 13 && $windSpeed <= 18):
                $beaufort = 4;
                break;
            case ($windSpeed >= 19 && $windSpeed <= 24):
                $beaufort = 5;
                break;
            case ($windSpeed >= 25 && $windSpeed <= 31):
                $beaufort = 6;
                break;
            case ($windSpeed >= 32 && $windSpeed <= 38):
                $beaufort = 7;
                break;
            case ($windSpeed >= 39 && $windSpeed <= 46):
                $beaufort = 8;
                break;
            case ($windSpeed >= 47 && $windSpeed <= 54):
                $beaufort = 9;
                break;
            case ($windSpeed >= 55 && $windSpeed <= 63):
                $beaufort = 10;
                break;
            case ($windSpeed >= 64 && $windSpeed <= 72):
                $beaufort = 11;
                break;
            case ($windSpeed >= 72):
                $beaufort = 12;
                break;
            default:
                $beaufort = 0;
                break;
        }
        return (int)$beaufort;
    }

    // Public Functions

    // Calculate the trending value
    public function calculateTrend($unit, $table, $sensor = null)
    {
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');
        /** @var mysqli $conn Global MYSQL Connection */

        if ($sensor !== null) {
            $sensor = "AND `sensor` = '$sensor'";
        }

        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(`$unit`) AS `trend1` FROM `$table` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR) $sensor"));
        $trend_1 = (float)$result['trend1'];

        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(`$unit`) AS `trend2` FROM `$table` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR) $sensor"));
        $trend_2 = (float)$result['trend2'];

        $trend = $trend_1 - $trend_2;

        if ($trend >= 1) {
            $trend = 'Rising';
        } elseif ($trend <= -1) {
            $trend = 'Falling';
        } else {
            $trend = 'Steady';
        }

        return $trend;
    }

    // Get current conditions
    public function getConditions()
    {
        return (object)array(
            'tempF' => $this->tempF,
            'tempC' => $this->tempC,
            'tempF_trend' => $this->calculateTrend('tempF', 'temperature'),
            'feelsF' => $this->feelsLike()->feelsF,
            'feelsC' => $this->feelsLike()->feelsC,
            'dewptF' => $this->dewPoint()->dewptF,
            'dewptC' => $this->dewPoint()->dewptC,
            'tempC_high' => $this->tempC_high,
            'tempF_high' => $this->tempF_high,
            'high_temp_recorded' => $this->high_temp_recorded,
            'tempC_low' => $this->tempC_low,
            'tempF_low' => $this->tempF_low,
            'low_temp_recorded' => $this->low_temp_recorded,
            'tempC_avg' => $this->tempC_avg,
            'tempF_avg' => $this->tempF_avg,
            'relH' => $this->relH,
            'relH_trend' => $this->calculateTrend('inhg', 'pressure'),
            'windSpeedMPH' => $this->windSpeedMPH,
            'windSpeedKMH' => $this->windSkmh,
            'windDEG' => $this->windDEG,
            'windDIR' => $this->windDirection($this->windDEG),
            'windDEG_peak' => $this->windDEG_peak,
            'windDIR_peak' => $this->windDirection($this->windDEG_peak),
            'windSpeedMPH_peak' => $this->windSpeedMPH_peak,
            'windSpeedKMH_peak' => $this->windSkmh_peak,
            'windSpeed_peak_recorded' => $this->wind_recorded_peak,
            'windBeaufort' => $this->windBeaufort($this->windSpeedMPH),
            'rainIN' => $this->rainIN,
            'rainMM' => $this->rainMM,
            'rainTotalIN_today' => $this->rainTotalIN_today,
            'rainTotalMM_today' => $this->rainTotalMM_today,
            'pressure_inHg' => $this->pressure_inHg,
            'pressure_kPa' => $this->pressure_kPa,
            'pressure_trend' => $this->calculateTrend('inhg', 'pressure'),
            'sunrise' => $this->sunrise,
            'sunset' => $this->sunset,
            'moonrise' => $this->moonrise,
            'moonset' => $this->moonset,
            'moon_age' => $this->moon_age,
            'moon_stage' => $this->moon_stage,
            'moon_illumination' => $this->moon_illumination,
            'moon_nextNew' => $this->next_new_moon,
            'moon_nextFull' => $this->next_full_moon,
            'moon_lastNew' => $this->last_new_moon,
            'moon_lastFull' => $this->last_full_moon,
            'lastUpdated' => $this->lastUpdate
        );
    }
}
