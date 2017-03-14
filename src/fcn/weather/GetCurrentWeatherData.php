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
 * File: src/fcn/weather/GetCurrentWeatherData.php
 * Gets the current weather data from the database
 */
class GetCurrentWeatherData
{
    // Set variables
    private $windSmph;
    private $windSkmh;
    private $windDEG;
    private $windDEG_avg2;
    private $windDEG_peak;
    private $wind_recorded_peak;
    private $windSmph_peak;
    private $windSkmh_peak;
    private $windSmph_avg2;
    private $windSkmh_avg2;
    private $windSmph_max5;
    private $windSkmh_max5;
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

    function __construct()
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

        // Process Wind Speed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `speedMPH` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windSmph = (int)round($result['speedMPH']); // Miles per hour
        $this->windSkmh = (int)round($result['speedMPH'] * 1.60934); // Convert to Kilometers per hour

        // Process Wind Direction:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `degrees` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windDEG = (int)$result['degrees']; // Degrees

        // Process Average Wind Direction over the last 2 minutes:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(degrees) AS `avg_degrees` FROM `winddirection` WHERE `timestamp` <= DATE_SUB(NOW(), INTERVAL 2 MINUTE)"));
        $this->windDEG_avg2 = (int)round($result['avg_degrees']); // Degrees

        // Today's Peak Windspeed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windSmph`, `windDEG` FROM `archive` WHERE `windSmph` = (SELECT MAX(`windSmph`) FROM `archive` WHERE DATE(`reported`) = CURDATE()) AND DATE(`reported`) = CURDATE() ORDER BY `reported` DESC LIMIT 1"));
        $this->wind_recorded_peak = date('H:i', strtotime($result['reported'])); // Recorded at
        $this->windSmph_peak = (int)round($result['windSmph']); // Miles per hour
        $this->windSkmh_peak = (int)round($result['windSmph'] * 1.60934); // Convert to Kilometers per hour

        // Process Peak Wind Direction:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `degrees` FROM `winddirection` WHERE `timestamp` = (SELECT `timestamp` FROM `windspeed` WHERE (SELECT MAX(speedMPH) FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()
              AND DATE(`timestamp`) = CURDATE() ORDER BY `timestamp` DESC LIMIT 1) ORDER BY `timestamp` DESC LIMIT 1) ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windDEG_peak = (int)$result['degrees']; // Degrees

        // 2 Min Average Windspeed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(speedMPH) AS `avg_speedMPH` FROM `windspeed` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)"));
        $this->windSmph_avg2 = (int)round($result['avg_speedMPH']); // Miles per hour
        $this->windSkmh_avg2 = (int)round($result['avg_speedMPH'] * 1.60934); // Convert to Kilometers per hour

        // Process Average Wind Speed over the last 5 minutes
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT MAX(speedMPH) AS `max_speedMPH` FROM `windspeed` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)"));
        $this->windSmph_max5 = (int)round($result['max_speedMPH']); // Miles per hour
        $this->windSkmh_max5 = (int)round($result['max_speedMPH'] * 1.60934); // Convert to Kilometers per hour

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
    }

    //Private Functions

    // Calculate human readable wind direction:
    private function windDirection($windDEG)
    {
        switch ($windDEG) {
            case '0':
                $windDIR = 'N';
                break;
            case '23':
                $windDIR = 'NNE';
                break;
            case '45':
                $windDIR = 'NE';
                break;
            case '68':
                $windDIR = 'ENE';
                break;
            case '90':
                $windDIR = 'E';
                break;
            case '113':
                $windDIR = 'ESE';
                break;
            case '135':
                $windDIR = 'SE';
                break;
            case '158':
                $windDIR = 'SSE';
                break;
            case '180':
                $windDIR = 'S';
                break;
            case '203':
                $windDIR = 'SSW';
                break;
            case '225':
                $windDIR = 'SW';
                break;
            case '248':
                $windDIR = 'WSW';
                break;
            case '270':
                $windDIR = 'W';
                break;
            case '293':
                $windDIR = 'WNW';
                break;
            case '315':
                $windDIR = 'NW';
                break;
            case '338':
                $windDIR = 'NNW';
                break;
        }
        if (isset($windDIR)) {
            return (string)$windDIR;
        } else {
            return null;
        }
    }

    // Calculate human readable wind direction from a range of values:
    private function windDirection_range($windDEG)
    {
        switch ($windDEG) {
            case ($windDEG === false):
                $windDIR = 'N';
                break;
            case ($windDEG >= '1' && $windDEG < '23'):
                $windDIR = 'N';
                break;
            case ($windDEG >= '23' && $windDEG < '45'):
                $windDIR = 'NNE';
                break;
            case ($windDEG >= '45' && $windDEG < '68'):
                $windDIR = 'NE';
                break;
            case ($windDEG >= '68' && $windDEG < '90'):
                $windDIR = 'ENE';
                break;
            case ($windDEG >= '90' && $windDEG < '113'):
                $windDIR = 'E';
                break;
            case ($windDEG >= '113' && $windDEG < '135'):
                $windDIR = 'ESE';
                break;
            case ($windDEG >= '135' && $windDEG < '158'):
                $windDIR = 'SE';
                break;
            case ($windDEG >= '158' && $windDEG < '180'):
                $windDIR = 'SSE';
                break;
            case ($windDEG >= '180' && $windDEG < '203'):
                $windDIR = 'S';
                break;
            case ($windDEG >= '203' && $windDEG < '225'):
                $windDIR = 'SSW';
                break;
            case ($windDEG >= '225' && $windDEG < '248'):
                $windDIR = 'SW';
                break;
            case ($windDEG >= '248' && $windDEG < '270'):
                $windDIR = 'WSW';
                break;
            case ($windDEG >= '270' && $windDEG < '293'):
                $windDIR = 'W';
                break;
            case ($windDEG >= '293' && $windDEG < '315'):
                $windDIR = 'WNW';
                break;
            case ($windDEG >= '315' && $windDEG < '338'):
                $windDIR = 'NW';
                break;
            case ($windDEG >= '338'):
                $windDIR = 'NNW';
                break;
        }

        if (isset($windDIR)) {
            return (string)$windDIR;
        } else {
            return null;
        }
    }

    // Calculate feels like temp
    private function feelsLike()
    {
        $feelsF = 0;
        $feelsC = 0;

        // Wind Chill:
        if ($this->tempC <= 5 && $this->windSkmh >= 3) {
            $feelsC = 13.12 + (0.6215 * (($this->tempF - 32) * 5 / 9)) - (11.37 * (($this->windSmph * 1.60934) ** 0.16)) + ((0.3965 * (($this->tempF - 32) * 5 / 9)) * (($this->windSmph * 1.60934) ** 0.16));
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

    // Public Functions

    public function calculateTrend($unit, $table, $sensor = null)
    {
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

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
            'pressure_inHg' => $this->pressure_inHg,
            'pressure_kPa' => $this->pressure_kPa,
            'inHg_trend' => $this->calculateTrend('inhg', 'pressure'),
            'windSmph' => $this->windSmph,
            'windSkmh' => $this->windSkmh,
            'windDEG' => $this->windDEG,
            'windDIR' => $this->windDirection($this->windDEG),
            'windDEG_avg2' => $this->windDEG_avg2,
            'windDIR_avg2' => $this->windDirection_range($this->windDEG_avg2),
            'windDEG_peak' => $this->windDEG_peak,
            'windDIR_peak' => $this->windDirection_range($this->windDEG_peak),
            'wind_recorded_peak' => $this->wind_recorded_peak,
            'windSmph_peak' => $this->windSmph_peak,
            'windSkmh_peak' => $this->windSkmh_peak,
            'windSmph_avg2' => $this->windSmph_avg2,
            'windSkmh_avg2' => $this->windSkmh_avg2,
            'windSmph_max5' => $this->windSmph_max5,
            'windSkmh_max5' => $this->windSkmh_max5,
            'rainIN' => $this->rainIN,
            'rainMM' => $this->rainMM,
            'rainTotalIN_today' => $this->rainTotalIN_today,
            'rainTotalMM_today' => $this->rainTotalMM_today
        );
    }
}
