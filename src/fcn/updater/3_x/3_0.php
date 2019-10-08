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
 * File: src/fcn/updater/3_x/3_0.php
 * 3.0 Site Update Tasks
 */

switch ($config->version->app) {

    // Update from 2.10.0
    case '2.10.0-release':
        $config->station->lightning = 0;
        $schema = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/updates/v3.sql';
        $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
        $schema = shell_exec($schema);
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Database schema upgraded to 3.0");
        $config->version->app = '3.0.0-beta';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Major update. Support all Atlas sensors. Framework updates. See release notes!';
}
