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
 * File: src/fcn/updater/3_x/3_1.php
 * 3.1 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {

    // Update from 3.0.1 to 3.1.0
    case '3.0.1':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.1.0");
        $config->version->app = '3.1.0';
        $config->version->schema = '3.1';
        $config->upload->openweather = (object)array();
        $config->upload->openweather->enabled = false;
        $config->upload->openweather->id = '';
        $config->upload->openweather->key = '';
        $config->upload->openweather->url = 'http://api.openweathermap.org/data/3.0/measurements';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '3.1' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        mysqli_query($conn,
            "CREATE TABLE IF NOT EXISTS `openweather_updates` (
                      `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `query`     tinytext COLLATE utf8_bin NOT NULL,
                      `result`    tinytext COLLATE utf8_bin NOT NULL
                    )
                      ENGINE = InnoDB
                      DEFAULT CHARSET = utf8
                      COLLATE = utf8_bin;");
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.1.0");
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Support Open Weather Map and Bug Fixes.';

    // Update from 3.1.0 to 3.1.1
    case '3.1.0':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.1.1");
        $config->version->app = '3.1.1';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor Bug Fixes.';
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.1.1");

    // Update from 3.1.1 to 3.1.2
    case '3.1.1':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.1.2");
        $config->version->app = '3.1.2';
        $config->site->dashboard_display_date_full = 'j M Y @ H:i';
        $config->site->dashboard_display_time = 'H:i';
        $config->site->date_api_json = 'c';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'API, Doc, and Bug Fixes.';
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.1.2");
}
