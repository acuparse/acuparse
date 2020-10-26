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
 * File: src/fcn/updater/2_x/2_9.php
 * 2.9 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {

    // Update from 2.8.0
    case '2.8.0-release':
        $config->version->app = '2.9.0-release';
        $config->version->schema = '2.9';
        $config->upload->windy = (object)array();
        $config->upload->windy->enabled = false;
        $config->upload->windy->id = '';
        $config->upload->windy->key = '';
        $config->upload->windy->url = 'http://stations.windy.com/pws/update';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '2.9' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        mysqli_query($conn,
            "CREATE TABLE IF NOT EXISTS `windy_updates` (
                      `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `query`     tinytext COLLATE utf8_bin NOT NULL,
                      `result`    tinytext COLLATE utf8_bin NOT NULL
                    )
                      ENGINE = InnoDB
                      DEFAULT CHARSET = utf8
                      COLLATE = utf8_bin;");
        mysqli_query($conn,
            "ALTER TABLE `windy_updates` ADD PRIMARY KEY (`timestamp`);"); // Add primary key
        mysqli_query($conn,
            "CREATE TABLE IF NOT EXISTS `generic_updates` (
                      `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `query`     tinytext COLLATE utf8_bin NOT NULL,
                      `result`    tinytext COLLATE utf8_bin NOT NULL
                    )
                      ENGINE = InnoDB
                      DEFAULT CHARSET = utf8
                      COLLATE = utf8_bin;");
        mysqli_query($conn,
            "ALTER TABLE `generic_updates` ADD PRIMARY KEY (`timestamp`);"); // Add primary key
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Adding Windy Support, Doc. Updates';

    // Update from 2.9.0
    case '2.9.0-release':
        $config->version->app = '2.9.1-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Buster Support, Doc. Updates';

    // Update from 2.9.1
    case '2.9.1-release':
        $config->version->app = '2.9.2-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Fixes Wind Direction';

    // Update from 2.9.2
    case '2.9.2-release':
        $config->version->app = '2.9.3-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Fixes Lightbox';

    // Update from 2.9.3
    case '2.9.3-release':
        $config->version->app = '2.9.4-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Fix Wind Regression';
}
