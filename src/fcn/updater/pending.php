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
 * File: src/fcn/updater/pending.php
 * Pending Site Update Tasks
 */

switch ($config->version->app) {

    // Update from 2.4.0-release
    case '2.4.0-release':
        $config->version->app = '';
        $config->upload->wc->enabled = false;
        $config->upload->wc->id = '';
        $config->upload->wc->key = '';
        $config->upload->wc->dashboard = '';
        $config->upload->wc->url = 'http://api.weathercloud.net/v01/set';
        $config->version->schema = '3.0';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '3.0' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        mysqli_query($conn,
            "CREATE TABLE IF NOT EXISTS `wc_updates` (
  `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query`     tinytext COLLATE utf8_bin NOT NULL,
  `result`    tinytext COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;"); // Add Weathercloud table
        mysqli_query($conn,
            "ALTER TABLE `wc_updates` ADD PRIMARY KEY (`timestamp`);"); // Add primary key
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Numerous changes and updates. See changelog.</li>';
}
