<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2020 Maxwell Power
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
 * File: src/pub/api/v1/text/watermark/index.php
 * Builds the watermark for the camera image
 */

function camWmark()
{
    // Get the loader
    require(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/inc/loader.php');

    /**
 * @return array
 * @var object $config Global Config
 */

    // Load weather Data:
    if (!class_exists('getCurrentWeatherData')) {
        require(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
        $getData = new getCurrentWeatherData();
        $wx = $getData->getConditions();
        $output = ($config->site->imperial === true) ? 'Baro: ' . $wx->pressure_inHg . ' inHg | RelH: ' . $wx->relH . '% | Temp: ' . $wx->tempF . '°F | Wind: ' . $wx->windDIR . ' @ ' . $wx->windSpeedMPH . ' mph | Accum: ' . $wx->rainTotalIN_today . ' in' : 'Baro: ' . $wx->pressure_kPa . ' kPa | RelH: ' . $wx->relH . '% | Temp: ' . $wx->tempC . '°C | Wind: ' . $wx->windDIR . ' @ ' . $wx->windSpeedKMH . ' km/h | Accum: ' . $wx->rainTotalMM_today . ' mm';
    }
    return $output;
}

header('Content-Type: text/plain; charset=UTF-8');

echo camWmark();
