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
 * File: src/fcn/updater/2_x/2_3.php
 * 2.3 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {

    // Update from 2.2.3
    case '2.2.3':
        $config->version->app = '2.3.0';
        $config->version->schema = '2.3';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '2.3' WHERE `system`.`name` = 'schema';"); //Update Schema Version
        $config->upload->sensor->external = 'default';
        $config->upload->sensor->id = null;
        $config->upload->sensor->archive = false;
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Added ability to choose master sensor for external upload.</li>';

    // Update from 2.3.0
    case '2.3.0':
        $config->version->app = '2.3.1';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor changes to CWOP packet and Findu link.</li>';

    // Update from 2.3.1
    case '2.3.1':
        $config->version->app = '2.3.2-beta';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Security and 3rd party script updates.</li>';
}
