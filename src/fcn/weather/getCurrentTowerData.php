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
 * File: src/fcn/weather/getCurrentTowerData.php
 * Gets the requested tower data from the database
 */
class getCurrentTowerData
{
    // Set variables
    private $name;
    private $tempF;
    private $tempF_high;
    private $tempF_low;
    private $high_temp_recorded;
    private $high_temp_recorded_json;
    private $low_temp_recorded;
    private $low_temp_recorded_json;
    private $tempC;
    private $tempC_high;
    private $tempC_low;
    private $relH;
    private $lastUpdate;
    private $lastUpdate_json;
    private $id;

    function __construct($sensor)
    {
        // Get the loader
        require(dirname(__DIR__, 2) . '/inc/loader.php');
        /**
         * @var mysqli $conn Global MYSQL Connection
         * @var object $config Global Config
         */

        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `timestamp` FROM `tower_data`"));
        if (!isset($lastUpdate)) {
            echo '<div class="col text-center alert alert-danger"><strong>No Tower Data Reported!</strong><br>Check your <a href="https://docs.acuparse.com/TROUBLESHOOTING/#logs">logs</a> for more details.</div>';
            exit();
        }

        $this->id = $sensor;

        //Process Temp
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM `tower_data` WHERE `sensor` = '$this->id' ORDER BY `timestamp` DESC LIMIT 1"));

        // Additional Data
        $sensorName = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `name` FROM `towers` WHERE `sensor` = '$sensor'"));
        $this->name = $sensorName['name'];

        if (empty($result['tempF'])) {
            $this->tempF = NULL;
            $this->lastUpdate = 'OFFLINE';
            $this->lastUpdate_json = $this->lastUpdate;
        } else {
            $this->tempF = round($result['tempF'], 1); // Fahrenheit
            $this->tempC = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

            // Process Humidity
            $this->relH = $result['relH']; // Percentage

            // Process Timestamps
            $this->lastUpdate = $result['timestamp'];
            $this->lastUpdate_json = date($config->site->date_api_json, strtotime($result['timestamp']));

            // High Temp:
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `timestamp`, `tempF` FROM `tower_data` WHERE `sensor` = '$sensor' AND `tempF` = (SELECT MAX(tempF) FROM `tower_data` WHERE `sensor` = '$sensor' AND DATE(`timestamp`) = CURDATE())
              AND DATE(`timestamp`) = CURDATE()"));
            $this->high_temp_recorded = date($config->site->dashboard_display_time, strtotime($result['timestamp'])); // Recorded at
            $this->high_temp_recorded_json = date($config->site->date_api_json, strtotime($result['timestamp'])); // Recorded at
            $this->tempF_high = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_high = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius

            // Low Temp:
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `timestamp`, `tempF` FROM `tower_data` WHERE `sensor` = '$sensor' AND `tempF` = (SELECT MIN(tempF) FROM `tower_data` WHERE `sensor` = '$sensor' AND DATE(`timestamp`) = CURDATE())
              AND DATE(`timestamp`) = CURDATE()"));
            $this->low_temp_recorded = date($config->site->dashboard_display_time, strtotime($result['timestamp'])); // Recorded at
            $this->low_temp_recorded_json = date($config->site->date_api_json, strtotime($result['timestamp'])); // Recorded at
            $this->tempF_low = round($result['tempF'], 1); // Fahrenheit
            $this->tempC_low = round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        }
    }

    // Calculate the trending value
    private function calculateTrend($unit, $sensor = null): string
    {
        require(dirname(__DIR__, 2) . '/inc/loader.php');
        /** @var mysqli $conn Global MYSQL Connection */

        if ($sensor !== null) {
            $sensor = "AND `sensor` = '$sensor'";
        }

        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(`$unit`) AS `trend1` FROM `tower_data` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR) $sensor"));
        $trend_1 = (float)$result['trend1'];

        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(`$unit`) AS `trend2` FROM `tower_data` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR) $sensor"));
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
    public function getConditions(): object
    {
        return (object)array(
            'name' => $this->name,
            'tempF' => $this->tempF,
            'tempF_high' => $this->tempF_high,
            'tempF_low' => $this->tempF_low,
            'tempF_trend' => $this->calculateTrend('tempF', $this->id),
            'tempC' => $this->tempC,
            'tempC_high' => $this->tempC_high,
            'tempC_low' => $this->tempC_low,
            'high_temp_recorded' => $this->high_temp_recorded,
            'low_temp_recorded' => $this->low_temp_recorded,
            'relH' => $this->relH,
            'relH_trend' => $this->calculateTrend('RelH', $this->id),
            'lastUpdated' => $this->lastUpdate,
        );
    }

    // Get current JSON conditions
    public function getJSONConditions(): object
    {
        return (object)array(
            'name' => $this->name,
            'tempF' => $this->tempF,
            'tempF_high' => $this->tempF_high,
            'tempF_low' => $this->tempF_low,
            'tempF_trend' => lcfirst($this->calculateTrend('tempF', $this->id)),
            'tempC' => $this->tempC,
            'tempC_high' => $this->tempC_high,
            'tempC_low' => $this->tempC_low,
            'high_temp_recorded' => $this->high_temp_recorded_json,
            'low_temp_recorded' => $this->low_temp_recorded_json,
            'relH' => $this->relH,
            'relH_trend' => lcfirst($this->calculateTrend('RelH', $this->id)),
            'lastUpdated' => $this->lastUpdate_json,
        );
    }
}
