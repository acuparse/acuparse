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
 * File: src/fcn/weather/getCurrentLightningData.php
 * Gets the requested lightning data from the database
 */
class getCurrentLightningData
{
    // Set variables
    private $strikecount;
    private $interference;
    private $last_strike_ts;
    private $last_strike_distance_KM;
    private $last_strike_distance_M;

    function __construct()
    {
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');

        //Process Strike Count
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `strikecount`, `interference`, `last_strike_ts`, `last_strike_distance` FROM `lightning` ORDER BY `timestamp` DESC LIMIT 1"));
        $this->strikecount = (int)$result['strikecount'];
        $this->interference = (bool)$result['interference'];
        $this->last_strike_ts = $result['last_strike_ts'];
        $this->last_strike_distance_KM = (float)round($result['last_strike_distance'],1);
        $this->last_strike_distance_M = (float)round($result['last_strike_distance'] / 1.609,1);
    }

    // Calculate Light Hours
    private function interferenceText($interference)
    {
        return ($interference === true) ? "Yes" : "No";
    }

    // Get Data
    public function getData()
    {
        return (object)array(
            'strikecount' => $this->strikecount,
            'interference' => $this->interferenceText($this->interference),
            'last_strike_ts' => $this->last_strike_ts,
            'last_strike_distance_KM' => $this->last_strike_distance_KM,
            'last_strike_distance_M' => $this->last_strike_distance_M
        );
    }
}
