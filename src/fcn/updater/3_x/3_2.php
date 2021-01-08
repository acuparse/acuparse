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
 * File: src/fcn/updater/3_x/3_2.php
 * 3.2 Site Update Tasks
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $notes */

switch ($config->version->app) {

    // Update from 3.1.2 to 3.2.0
    case '3.1.2':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.2.0");
        // Update the Database
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Updating Database schema");
        mysqli_query($conn,
            "RENAME TABLE `5n1_status` TO `iris_status`;");
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '3.2' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema_version = $result['value'];
        if ($schema_version === '3.2') {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Database schema upgraded to 3.2");
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Updating System Configuration");
            $config->version->app = '3.2.0';
            $config->version->schema = '3.2';
            $config->station->sensor_iris = $config->station->sensor_5n1;
            unset ($config->station->sensor_5n1);
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Minor Bug/Doc Fixes.';
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . '<strong>Important</strong>: References to <strong>5-in-1</strong> updated to <strong>Iris<strong>!';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.1.3");
        } else {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: FAILED updating schema to 3.2!");
            echo "Oops, Something went wrong updating Schema";
            exit();
        }
}
