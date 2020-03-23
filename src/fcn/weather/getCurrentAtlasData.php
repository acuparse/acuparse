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
    private $uvindex;
    private $lightintensity;
    private $measured_light_seconds;

    function __construct()
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

        //Process Strike Count
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `uvindex` FROM `uvindex` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->uvindex = $result['uvindex'];
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `lightintensity`, `measured_light_seconds` FROM `light` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->lightintensity = $result['lightintensity'];
        $this->measured_light_seconds = $result['measured_light_seconds'];

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
    private function calculateLight($index)
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

    // Get Data
    public function getData()
    {
        return (object)array(
            'uvindex' => $this->uvindex,
            'uvindex_text' => $this->calculateUV($this->uvindex),
            'lightintensity' => $this->lightintensity,
            'lightintensity_text' => $this->calculateLight($this->lightintensity),
            'measured_light_seconds' => $this->measured_light_seconds
        );
    }
}
