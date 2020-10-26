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
 * File: src/fcn/updater/2_x/2_6.php
 * 2.6 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {
    // Update from 2.5.2-release
    case '2.5.2-release':
        $config->station->primary_sensor = 1;
        $config->station->sensor_atlas = '';
        $config->version->app = '2.6.0-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Implement basic Atlas Support.</li>';

    // Update from 2.6.0-release
    case '2.6.0-release':
        $config->version->app = '2.6.1-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor bug fixes.</li>';
}
