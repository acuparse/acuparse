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

namespace atlas;

class getCurrentLightningData
{
    // Set variables
    private $dailystrikes;
    private $currentstrikes;
    private $interference;
    private $last_strike_ts;
    private $last_strike_display;
    private $last_strike_distance_KM;
    private $last_strike_distance_M;
    private $source;

    function __construct($source = null)
    {
        $todaysDate = date('Y-m-d');
        // Get the loader
        require(dirname(dirname(__DIR__)) . '/inc/loader.php');
        /** @var mysqli $conn Global MYSQL Connection */
        /**
         * @return array
         * @var object $config Global Config
         */
        // Check for recent readings
        $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_update` FROM `atlasLightning`"));
        if (!isset($lastUpdate)) {
            exit();
        }

        $this->source = $source;
        //Process Strike Count
        $currentData = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `dailystrikes`, `currentstrikes` FROM `atlasLightning` WHERE `date`='$todaysDate'"));
        $atlasData = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_strike_ts`, `interference`, `last_strike_distance` FROM `lightningData` WHERE `source` = 'A'"));
        $this->dailystrikes = (float)$currentData['dailystrikes'];
        $this->currentstrikes = (float)$currentData['currentstrikes'];
        $this->interference = (float)$atlasData['interference'];
        $this->last_strike_ts = $atlasData['last_strike_ts'];
        $this->last_strike_display = date($config->site->dashboard_display_date, strtotime($this->last_strike_ts));
        $this->last_strike_distance_KM = (float)round($atlasData['last_strike_distance'] * 1.609, 1);
        $this->last_strike_distance_M = (float)round($atlasData['last_strike_distance'], 1);
    }

    // Calculate Interference
    private function interferenceText($interference)
    {
        return ($interference === true) ? "Yes" : "No";
    }

    // Calculate Last Update
    private function lastUpdate($last_strike_ts, $last_strike_display, $source)
    {

        function between($number, $from, $to)
        {
            return $number > $from && $number < $to;
        }

        if ($source === 'json') {
            $output = $last_strike_display;
        } elseif (between(strtotime($last_strike_ts), strtotime(date('Y-m-d H:i:s')) - 1800,
            strtotime(date('Y-m-d H:i:s')) + 1800)) {
            $output = '<i title="Reported within last 30 Minutes" class="fas fa-exclamation-triangle lightningDanger"></i><strong> ' . $last_strike_display . '</strong>';
        } elseif (between(strtotime($last_strike_ts), strtotime(date('Y-m-d H:i:s')) - 7200,
            strtotime(date('Y-m-d H:i:s')) + 7200)) {
            $output = '<i title="Reported within last 2 Hours" class="fas fa-exclamation-circle lightningWarning"></i> ' . $last_strike_display;
        } else {
            $output = $last_strike_display;
        }

        return $output;
    }

    // Get Data
    public function getData()
    {
        return (object)array(
            'dailystrikes' => $this->dailystrikes,
            'currentstrikes' => $this->currentstrikes,
            'interference' => $this->interferenceText($this->interference),
            'last_strike_ts' => $this->last_strike_ts,
            'last_update' => $this->lastUpdate($this->last_strike_ts, $this->last_strike_display, $this->source),
            'last_strike_distance_KM' => $this->last_strike_distance_KM,
            'last_strike_distance_M' => $this->last_strike_distance_M
        );
    }
}
