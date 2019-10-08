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
 * File: src/fcn/weather/getCurrentTowerData.php
 * Gets the requested tower data from the database
 */
class getCurrentTowerData
{
    // Set variables
    private $tempF;
    private $tempC;
    private $relH;

    function __construct($sensor)
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

        //Process Temp
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `tempF` FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
        $this->tempF = (float)round($result['tempF'], 1); // Fahrenheit
        $this->tempC = (float)round(($result['tempF'] - 32) * 5 / 9, 1); // Convert to Celsius
        //Process Humidity:
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `relH` FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
        $this->relH = (int)$result['relH']; // Percentage
    }
    // Get current conditions
    public function getConditions()
    {
        return (object)array(
            'tempF' => $this->tempF,
            'tempC' => $this->tempC,
            'relH' => $this->relH
        );
    }
}
