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
 * File: src/fcn/weather/getCurrentAtlasData.php
 * Gets the requested lightning data from the database
 */
class getCurrentAtlasData
{
    // Set variables
    private $uvIndex;
    private $lightIntensity;
    private $lightSeconds;
    private $lastUpdated;
    private $lastUpdated_json;

    function __construct($cron = false)
    {
        // Get the loader
        require(dirname(__DIR__, 2) . '/inc/loader.php');
        /**
         * @var mysqli $conn Global MYSQL Connection
         * @var object $config Global Config
         */
        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update` FROM `atlas_status`"));
        if (!isset($lastUpdate)) {
            echo '<div class="col text-center alert alert-danger"><strong>No Atlas Data Reported!</strong><br>Check your <a href="https://docs.acuparse.com/TROUBLESHOOTING/#logs">logs</a> for more details.</div>';
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

        if ($cron === false) {

            // Get last Update
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `last_update` FROM `atlas_status` LIMIT 1"));
            $this->lastUpdated = $result['last_update'];
            $this->lastUpdated_json = date($config->site->date_api_json, strtotime($result['last_update']));
        }
    }

    // Calculate the UV Index Text
    private function calculateUV($index): string
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
    private function lightText($index): string
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
    private function calculateLightHours($seconds): float
    {
        return round($seconds / 3600, 2);
    }

    // Get Dashboard Data
    public function getData(): object
    {
        return (object)array(
            'lightIntensity' => $this->lightIntensity,
            'lightIntensity_text' => $this->lightText($this->lightIntensity),
            'lightSeconds' => $this->lightSeconds,
            'lightHours' => $this->calculateLightHours($this->lightSeconds),
            'uvIndex' => $this->uvIndex,
            'uvIndex_text' => $this->calculateUV($this->uvIndex),
            'lastUpdated' => $this->lastUpdated,
        );
    }

    // Get JSON Data
    public function getJSONData(): object
    {
        return (object)array(
            'lightIntensity' => $this->lightIntensity,
            'lightIntensity_text' => $this->lightText($this->lightIntensity),
            'lightSeconds' => $this->lightSeconds,
            'lightHours' => $this->calculateLightHours($this->lightSeconds),
            'uvIndex' => $this->uvIndex,
            'uvIndex_text' => $this->calculateUV($this->uvIndex),
            'lastUpdated' => $this->lastUpdated_json,
        );
    }

    public function getCRONData(): object
    {
        return (object)array(
            'lightIntensity' => $this->lightIntensity,
            'lightSeconds' => $this->lightSeconds,
            'uvIndex' => $this->uvIndex,
        );
    }
}
