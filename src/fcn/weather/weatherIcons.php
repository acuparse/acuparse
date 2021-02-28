<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2021 Maxwell Power
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
 * File: src/fcn/weather/weatherIcons.php
 * Builds the right moon icon for display
 */

/**
 * @param $moonStage
 * @return string
 */

// Find the right moon icon to show:
function moonIcon($moonStage): string
{
    switch ($moonStage) {
        case 'New Moon':
            $moonIcon = 'wi-moon-new';
            break;
        case 'Waxing Crescent':
            $moonIcon = 'wi-moon-waxing-crescent-6';
            break;
        case 'First Quarter':
            $moonIcon = 'wi-moon-first-quarter';
            break;
        case 'Waxing Gibbous':
            $moonIcon = 'wi-moon-waxing-gibbous-6';
            break;
        case 'Full Moon':
            $moonIcon = 'wi-moon-full';
            break;
        case 'Waning Gibbous':
            $moonIcon = 'wi-moon-waning-gibbous-6';
            break;
        case 'Third Quarter':
            $moonIcon = 'wi-moon-third-quarter';
            break;
        case 'Waning Crescent':
            $moonIcon = 'wi-moon-waning-crescent-1';
            break;
        default:
            $moonIcon = 'Error';
    }
    return $moonIcon;
}

// Show the right icon based on current temp
function tempIcon($tempC): string
{
    if ($tempC < -30) {
        $tempIcon = 'fa-thermometer-empty';
    } else if ($tempC >= -30 && $tempC < 0) {
        $tempIcon = 'fa-thermometer-quarter';
    } else if ($tempC >= 0 && $tempC < 15) {
        $tempIcon = 'fa-thermometer-half';
    } else if ($tempC >= 15 && $tempC <= 30) {
        $tempIcon = 'fa-thermometer-three-quarters';
    } else if ($tempC > 30) {
        $tempIcon = 'fa-thermometer-full';
    } else {
        $tempIcon = 'fa-thermometer';
    }
    return $tempIcon;
}

// Convert Trend to icon
function trendIcon($trend): string
{
    switch ($trend) {
        case 'Rising':
            $trendIcon = ' <i class="fas fa-level-up-alt"></i>';
            break;
        case 'Falling':
            $trendIcon = ' <i class="fas fa-level-down-alt"></i>';
            break;
        case 'Steady':
            $trendIcon = ' <i class="fas fa-long-arrow-alt-right"></i>';
            break;
        default:
            $trendIcon = 'Error';
    }
    return $trendIcon;
}
