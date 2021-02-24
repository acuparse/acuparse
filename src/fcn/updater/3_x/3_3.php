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
 * File: src/fcn/updater/3_x/3_3.php
 * 3.3 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {

    // Update from 3.2.2 to 3.3.0
    case '3.2.2':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.3.0");
        mysqli_query($conn,
            "ALTER TABLE `archive` CHANGE `feelsF` `feelsF` FLOAT(5,2) NULL;");
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '3.3' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema_version = $result['value'];
        if ($schema_version === '3.3') {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Database schema upgraded to 3.3");
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Updating System Configuration");
            @$config->station->filter_access = true;
            $config->version->app = '3.3.0';
            $config->version->schema = '3.3';
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Fix feels and Archiving';
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . '<strong>Important</strong>: Feels temp now reports "NULL" not "0" when unset!';
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . '<strong>Notice</strong>: Acuparse now filters Access data for erroneous data by default! See: <a href="https://docs.acuparse.com/INSTALL/#filter-access-readings">Filter Access Readings</a> for more details.';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.3.0");
        } else {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: FAILED updating schema to 3.3!");
            echo "Something went wrong updating Schema";
            exit();
        }

    // Update from 3.3.0 to 3.3.1
    case '3.3.0':
        $config->version->app = '3.3.1';
        $config->upload->windy->station = '0';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Add Windy StationID, Fix Uploaders using wrong Temp.';

}
