<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
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
 * File: src/fcn/weather/getCurrentAtlasData.php
 * Gets the requested lightning data from the database
 */
class getCurrentAtlasData
{
    // Set variables
    private $uvIndex;
    private $lightIntensity;
    private $lightSeconds;
    private $windGustMPH;
    private $windGustKMH;
    private $windGustDEG;
    private $windSpeedMPH_avg;
    private $windSpeedKMH_avg;
    private $windGust_peak_recorded;
    private $windGustMPH_peak;
    private $windGustKMH_peak;
    private $windGustDEG_peak;
    private $battery;
    private $rssi;
    private $lastUpdate;

    function __construct()
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');
        /** @var mysqli $conn Global MYSQL Connection */

        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update` FROM `atlas_status`"));
        if (!isset($lastUpdate)) {
            exit();
        }

        // Get UV Index
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `uvindex` FROM `uvindex` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->uvIndex = (int)$result['uvindex'];

        // Get Light Data
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `lightintensity`, `measured_light_seconds` FROM `light` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->lightIntensity = (int)$result['lightintensity'];
        $this->lightSeconds = (int)$result['measured_light_seconds'];

        // Get Wind Data
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `gust` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windGustDEG = (int)$result['gust'];

        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `gustMPH` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windGustMPH = (int)$result['gustMPH'];
        $this->windGustKMH = (int)round($result['gustMPH'] * 1.60934);

        // 2 Min Average Windspeed:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `averageMPH` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->windSpeedMPH_avg = (int)$result['averageMPH']; // Miles per hour
        $this->windSpeedKMH_avg = (int)round($result['averageMPH'] * 1.60934); // Convert to Kilometers per hour

        // Today's Peak Gust:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `reported`, `windGustMPH`, `windGustDEG` FROM `archive` WHERE `windGustMPH` = (SELECT MAX(`windGustMPH`) FROM `archive` WHERE DATE(`reported`) = CURDATE()) AND DATE(`reported`) = CURDATE() ORDER BY `reported` DESC LIMIT 1"));
        $this->windGust_peak_recorded = date('H:i', strtotime($result['reported'])); // Recorded at
        $this->windGustMPH_peak = (int)round($result['windGustMPH']); // Miles per hour
        $this->windGustKMH_peak = (int)round($result['windGustMPH'] * 1.60934); // Convert to Kilometers per hour
        $this->windGustDEG_peak = (int)$result['windGustDEG']; // Degrees

        // Get Status
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM `atlas_status` LIMIT 1"));
        $this->battery = $result['battery'];
        $this->rssi = $result['rssi'];
        $this->lastUpdate = $result['last_update'];
    }

    // Calculate the UV Index Text
    private function calculateUV($index)
    {
        if ($index <= 2) {
            $result = 'Low';
        } elseif (($index >= 3) && ($index <= 5)) {
            $result = 'Moderate';
        } elseif (($index == 6) || ($index == 7)) {
            $result = 'High';
        } elseif (($index >= 8) && ($index <= 10)) {
            $result = 'Very High';
        } elseif ($index >= 11) {
            $result = 'Extreme';
        } else {
            $result = 'Out of Range';
        }

        return $result;
    }

    // Calculate the Light Intensity Text
    private function lightText($index)
    {
        if (($index >= 0) && ($index <= 500)) {
            $result = 'Dark/Night';
        } elseif (($index >= 501) && ($index <= 5380)) {
            $result = 'Low Light';
        } elseif (($index >= 5381) && ($index <= 21520)) {
            $result = 'Overcast/Shade';
        } elseif (($index >= 21521) && ($index <= 43050)) {
            $result = 'Daylight';
        } elseif (($index > 43051)) {
            $result = 'Direct Sun';
        } else {
            $result = 'Out of Range';
        }

        return $result;
    }

    // Calculate Light Hours
    private function calculateLightHours($seconds)
    {
        return round($seconds / 3600, 2);
    }

    // Calculate human readable wind direction from a range of values:
    private function windGustDirection($windDEG)
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

    // Get Data
    public function getData()
    {
        return (object)array(
            'lightIntensity' => $this->lightIntensity,
            'lightIntensity_text' => $this->lightText($this->lightIntensity),
            'lightSeconds' => $this->lightSeconds,
            'lightHours' => $this->calculateLightHours($this->lightSeconds),
            'uvIndex' => $this->uvIndex,
            'uvIndex_text' => $this->calculateUV($this->uvIndex),
            'windGustDEG' => $this->windGustDEG,
            'windGustDIR' => $this->windGustDirection($this->windGustDEG),
            'windGustMPH' => $this->windGustMPH,
            'windGustKMH' => $this->windGustKMH,
            'windGustPeakMPH' => $this->windGustMPH_peak,
            'windGustPeakKMH' => $this->windGustKMH_peak,
            'windGustDEGPeak' => $this->windGustDEG_peak,
            'windGustDIRPeak' => $this->windGustDirection($this->windGustDEG_peak),
            'windGustPeakRecorded' => $this->windGust_peak_recorded,
            'windAvgMPH' => $this->windSpeedMPH_avg,
            'windAvgKMH' => $this->windSpeedKMH_avg,
            'battery' => $this->battery,
            'signal' => $this->rssi,
            'lastUpdate' => $this->lastUpdate,
        );
    }
}
