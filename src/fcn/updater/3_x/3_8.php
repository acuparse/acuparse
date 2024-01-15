<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2024 Maxwell Power
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
 * File: src/fcn/updater/3_x/3_8.php
 * 3.8 Site Update Tasks
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $notes
 */

switch ($config->version->app) {

    // Update from 3.7.1 to 3.8.0
    case '3.7.1':
        $config->version->app = '3.8.0';
        @$config->upload->mqtt = (object)array();
        @$config->upload->mqtt->enabled = false;
        @$config->upload->mqtt->client = "acuparse";
        @$config->upload->mqtt->server = null;
        @$config->upload->mqtt->port = 1883;
        @$config->upload->mqtt->topic = "acuparse";
        @$config->upload->mqtt->username = null;
        @$config->upload->mqtt->password = null;

        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Add MQTT Publishing. Minor Fixes.';

    // Update from 3.8.0 to 3.8.1
    case '3.8.0':
        $config->version->app = '3.8.1';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor Lightning fixes.';
}
