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
 * File: src/fcn/updater/2_x/2_2.php
 * 2.2 Site Update Tasks
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $notes
 */

switch ($config->version->app) {

    // Update from 2.1.9
    case '2.1.9':
        $config->version->app = '2.2.0';
        $config->version->schema = '2.2';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '2.2' WHERE `system`.`name` = 'schema';"); //Update Schema Version
        @$config->station->access_mac = '000000000000'; // Add Access MAC

        // Fix MyAcuRite upload variables
        @$config->upload->myacurite->enabled === true ? $config->upload->myacurite->hub_enabled = true : $config->upload->myacurite->hub_enabled = false;
        @$config->upload->myacurite->enabled === true ? $config->upload->myacurite->access_enabled = true : $config->upload->myacurite->access_enabled = false;
        @$config->upload->myacurite->url === 'http://hubapi.myacurite.com' ? $config->upload->myacurite->hub_url = 'http://hubapi.myacurite.com' : $config->upload->myacurite->hub_url = 'http://hubapi.acuparse.com';
        @$config->upload->myacurite->url === 'http://hubapi.myacurite.com' ? $config->upload->myacurite->access_url = 'https://atlasapi.myacurite.com' : $config->upload->myacurite->hub_url = 'https://atlasapi.acuparse.com';
        unset($config->upload->myacurite->enabled);
        unset($config->upload->myacurite->url);

        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Support for the AcuRite Access.<br> NOTICE: Apache rebuild required. See <a href="https://docs.acuparse.com/updates/from2_1">docs/updates/from2_1.md</a></li>';

    // Update from 2.2.0
    case '2.2.0':
        $config->version->app = '2.2.1';
        @$config->upload->myacurite->access_url = 'https://atlasapi.myacurite.com';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Blocks AcuRite response from affecting the HUB.<br>Resolves broken Access updates to MyAcuRite.</li>';

    // Update from 2.2.1
    case '2.2.1':
        $config->version->app = '2.2.2';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Access bug fixes.<br>New script to change Access Upload server.</li>';

    // Update from 2.2.2
    case '2.2.2':
        $config->version->app = '2.2.3';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Tower sensors now support the indoor/outdoor monitors for temp/humidity readings</li>';
}
