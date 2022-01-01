<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/fcn/updater/3_x/3_4.php
 * 3.4 Site Update Tasks
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $notes
 */

switch ($config->version->app) {

    // Update from 3.3.1 to 3.4.0
    case '3.3.1':
        $config->version->app = '3.4.0';
        @$config->upload->wu->url = 'https://weatherstation.wunderground.com/weatherstation/updateweatherstation.php';
        @$config->upload->pws->key = $config->upload->pws->password;
        unset ($config->upload->pws->password);
        @$config->upload->pws->url = 'https://pwsupdate.pwsweather.com/api/v1/submitwx';
        @$config->upload->wc->url = 'https://api.weathercloud.net/v01/set';
        @$config->upload->windy->url = 'https://stations.windy.com/pws/update';
        @$config->upload->windguru->url = 'https://www.windguru.cz/upload/api.php';
        @$config->upload->openweather->url = 'https://api.openweathermap.org/data/3.0/measurements';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Email & Moon time bug fixes, dependency updates, cleanup.';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'PWSweather changed to use API Key from Password. Check settings!';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'External Upload URLS migrated to HTTPS';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Webcam upload scripts combined into one. See the <a href="https://docs.acuparse.com/INSTALL/#webcam-installation-optional">Install Docs</a>.';

}
