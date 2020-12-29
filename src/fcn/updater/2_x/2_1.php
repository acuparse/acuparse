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
 * File: src/fcn/updater/2_x/2_1.php
 * 2.1 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {
    // Update from 2.1.0
    case '2.1.0':

        // Update from 2.1.1
    case '2.1.1':

        // Update from 2.1.2
    case '2.1.2':

        // Update from 2.1.3
    case '2.1.3':

        // Update from 2.1.4
    case '2.1.4':

        // Update from 2.1.5
    case '2.1.5':
        $config->version->app = '2.1.6';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor changes and bug fixes. See CHANGELOG for details.</li>';

    // Update from 2.1.6
    case '2.1.6':

        // Update from 2.1.7
    case '2.1.7':

        // Update from 2.1.8
    case '2.1.8':
        $config->version->app = '2.1.9';
        $config->site->hide_alternate = 'false';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'New option to hide alternate measurements</li>';
}
