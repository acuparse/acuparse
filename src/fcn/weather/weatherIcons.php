<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
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
 * File: src/fcn/weather/weatherIcons.php
 * Builds the right moon icon for display
 */

// Find the right moon icon to show:
function moonIcon($moon_stage)
{
    switch ($moon_stage) {
        case 'New Moon':
            $moon_icon = 'wi-moon-new';
            break;
        case 'Waxing Crescent':
            $moon_icon = 'wi-moon-waxing-crescent-6';
            break;
        case 'First Quarter':
            $moon_icon = 'wi-moon-first-quarter';
            break;
        case 'Waxing Gibbous':
            $moon_icon = 'wi-moon-waxing-gibbous-6';
            break;
        case 'Full Moon':
            $moon_icon = 'wi-moon-full';
            break;
        case 'Waning Gibbous':
            $moon_icon = 'wi-moon-waning-gibbous-6';
            break;
        case 'Third Quarter':
            $moon_icon = 'wi-moon-third-quarter';
            break;
        case 'Waning Crescent':
            $moon_icon = 'wi-moon-waning-crescent-1';
            break;
    }
    return $moon_icon;
}

// Find the right temp icon to show:
function tempIcon($tempC)
{
    switch ($tempC) {
        case ($tempC < -10):
            $temp_icon = 'fa-thermometer-empty';
            break;
        case ($tempC >= -10 && $tempC <= 0):
            $temp_icon = 'fa-thermometer-quarter';
            break;
        case ($tempC > 0 && $tempC < 15):
            $temp_icon = 'fa-thermometer-half';
            break;
        case ($tempC >= 15 && $tempC <= 30):
            $temp_icon = 'fa-thermometer-three-quarters';
            break;
        case ($tempC > 30):
            $temp_icon = 'fa-thermometer-full';
            break;
    }
    return $temp_icon;
}

// Convert Trend to icon
function trendIcon($trend)
{
    switch ($trend) {
        case 'Rising':
            $icon = ' <i class="wi wi-direction-up"></i>';
            break;
        case 'Falling':
            $icon = ' <i class="wi wi-direction-down"></i>';
            break;
        case 'Steady':
            $icon = ' <i class="wi wi-direction-right"></i>';
            break;
    }
    return $icon;
}