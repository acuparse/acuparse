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
 * File: src/fcn/updater/2_x/2_5.php
 * 2.5 Site Update Tasks
 */

switch ($config->version->app) {

    // Update from 2.4.0-release
    case '2.4.0-release':
        $config->version->app = '2.5.0-release';
        $config->version->schema = '2.5';
        $config->upload->wc = (object)array();
        $config->upload->wc->enabled = false;
        $config->upload->wc->id = '';
        $config->upload->wc->key = '';
        $config->upload->wc->device = '';
        $config->upload->wc->url = 'http://api.weathercloud.net/v01/set';
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '2.5' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        mysqli_query($conn,
            "CREATE TABLE IF NOT EXISTS `wc_updates` (
                      `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `query`     tinytext COLLATE utf8_bin NOT NULL,
                      `result`    tinytext COLLATE utf8_bin NOT NULL
                    )
                      ENGINE = InnoDB
                      DEFAULT CHARSET = utf8
                      COLLATE = utf8_bin;");
        mysqli_query($conn,
            "ALTER TABLE `wc_updates` ADD PRIMARY KEY (`timestamp`);"); // Add primary key
        // Check the event scheduler
        if ($config->mysql->trim !== 0) {
            if ($config->mysql->trim === 1) {
                $schema = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/trim/enable.sql';
                $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
                $schema = shell_exec($schema);
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Event Scheduler Reset");
            } elseif ($config->mysql->trim === 2) {
                // Load the database with the trim schema
                $schema = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/trim/enable_xtower.sql';
                $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
                $schema = shell_exec($schema);
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Event Scheduler Reset");
            }
        }
        mysqli_query($conn, "truncate `sessions`;");
        setcookie('device_key', '', time() - 3600, '/');
        unset($_COOKIE['device_key']);
        $deviceKey = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz',
            mt_rand(1, 10))), 1,
            40);
        $token = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1, 10))),
            1, 40);
        $tokenHash = (string)md5($token);
        $userAgent = (string)$_SERVER['HTTP_USER_AGENT'];
        $uid = $_SESSION['uid'];
        mysqli_query($conn,
            "INSERT INTO `sessions` (`uid`, `device_key`, `token`, `user_agent`) VALUES ('$uid', '$deviceKey', '$token', '$userAgent')");
        setcookie('device', $deviceKey, time() + 60 * 60 * 24 * 30, '/');
        setcookie('token', $tokenHash, time() + 60 * 60 * 24 * 30, '/');
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Numerous changes and updates. See changelog.<br> <strong>NOTE:</strong> PHP 7.2 and Ubuntu 18.04 LTS are now supported! See <a href="https://acuparse.github.io/acuparse/updates/from2_4">docs/updates/from2_4.md</a></li>';

    // Update from 2.5.0-release
        case '2.5.0-release':
        $config->version->app = '2.5.1-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Bootstrap to 4.1.2</li>';

    // Update from 2.5.0-release
    case '2.5.1-release':
        $config->version->app = '2.5.2-release';
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Fixed dew point when uploading using tower data.<br>Bootstrap and Font Awesome updates.</li>';
}
