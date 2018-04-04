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
 * File: src/pub/admin/install/scripts/2_2.php
 * 2.2 Site Update Tasks
 */

switch ($config->version->app) {
    // Update from 2.2.0
    case '2.2.0':
        $config->version->app = '2.2.1';
        $config->upload->myacurite->access_url = 'https://atlasapi.myacurite.com';
        $notes .= '<li>' . $config->version->app . ' - ' . 'Blocks Acurite response from affecting the HUB.<br>Resolves broken Access updates to MyAcuRite.</li>';

    // Update from 2.2.1
    case '2.2.1':
        $config->version->app = '2.2.2';
        $notes .= '<li>' . $config->version->app . ' - ' . 'Access bug fixes.<br>New script to change Access Upload server.</li>';

    // Update from 2.2.2
    case '2.2.1':
        $config->version->app = '2.2.3';
        $notes .= '<li>' . $config->version->app . ' - ' . 'Tower sensors now support the indoor/outdoor monitors for temp/humidity readings</li>';
}
