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
 * File: src/fcn/trim.php
 * Check Database Trim
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

syslog(LOG_DEBUG, "(SYSTEM){TRIM}: Checking Database Trimming ...");

if ($config->mysql->trim !== 0) {
    $result = mysqli_fetch_assoc(mysqli_query($conn, "SHOW VARIABLES WHERE VARIABLE_NAME = 'event_scheduler'"));
    $scheduler = $result['Value'];
    if ($scheduler === 'OFF') {
        if ($config->mysql->trim === 1) {
            $schema = dirname(dirname(__DIR__)) . '/sql/trim/enable.sql';
        } elseif ($config->mysql->trim === 2) {
            $schema = dirname(dirname(__DIR__)) . '/sql/trim/enable_xtower.sql';
        }
        $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < $schema";
        $schema = exec($schema, $schemaOutput, $schemaReturn);
        if ($schemaReturn !== 0) {
            syslog(LOG_WARNING, "(SYSTEM){TRIM}[WARNING]: Failed Enabling Database Trimming");
        } else {
            syslog(LOG_INFO, "(SYSTEM){TRIM}: Successfully Enabled Database Trimming");
        }
    } else {
        syslog(LOG_DEBUG, "(SYSTEM){TRIM}: Database Trimming OK");
    }
} else {
    syslog(LOG_DEBUG, "(SYSTEM){TRIM}: Database Trimming Disabled");
}
