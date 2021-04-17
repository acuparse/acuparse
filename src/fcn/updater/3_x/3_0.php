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
 * File: src/fcn/updater/3_x/3_0.php
 * 3.0 Site Update Tasks
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var string $notes
 */

switch ($config->version->app) {

    // Update from 2.10.0 to 3.0.0-beta
    case '2.10.0-release':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.0.0-beta");
        $schema = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/updates/v3.0/beta.sql';
        $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
        $schema = shell_exec($schema);
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema_version = $result['value'];
        if ($schema_version === '3.0-beta') {
            $config->version->schema = '3.0-beta';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Database schema upgraded to 3.0-beta");
            $config->version->app = '3.0.0-beta';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.0.0-beta");
        } else {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: FAILED to update schema to 3.0-beta!");
            echo "Something went wrong updating Schema";
            exit();
        }

    // Update from 3.0.0-beta to 3.0.0-beta1
    case '3.0.0-beta':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.0.0-beta1");

        // Update the Database
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Updating Database schema");
        $schema = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/updates/v3.0/beta1.sql';
        $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
        $schema = shell_exec($schema);
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema_version = $result['value'];
        if ($schema_version === '3.0-beta1') {
            $config->version->schema = '3.0-beta1';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Database schema upgraded to 3.0-beta1");
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: Updating System Configuration");
            unset($config->station->lightning);
            unset($config->station->baro_source);
            if (($config->station->access_mac !== null) || ($config->station->access_mac !== '')) {
                @$config->station->device = 0;
                @$config->station->hub_mac = null;
            } elseif (($config->station->hub_mac !== null) || ($config->station->hub_mac !== '')) {
                @$config->station->device = 1;
                @$config->station->access_mac = null;
                @$config->station->sensor_atlas = null;
            } else {
                $config->station->device = null;
            }

            @$config->upload->windguru->enabled = false;
            @$config->upload->windguru->uid = null;
            @$config->upload->windguru->id = null;
            @$config->upload->windguru->password = null;
            @$config->upload->windguru->url = 'http://www.windguru.cz/upload/api.php';

            @$config->station->lightning_source = 0;

            @$config->site->dashboard_display_date = 'j M @ H:i';

            @$config->camera->sort->today = 'ascending';
            @$config->camera->sort->archive = 'ascending';

            @$config->station->towers_additional = false;

            @$config->mailgun->enabled = false;
            @$config->mailgun->secret = null;
            @$config->mailgun->domain = null;

            // Generate the install hash
            $installHash1 = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                mt_rand(1, 10))), 0,
                32);
            $installHash2 = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                mt_rand(1, 10))), 0,
                8);
            $installHash = "$installHash1-$installHash2";
            mysqli_query($conn, "INSERT INTO `system` (`name`, `value`) VALUES ('installHash', '$installHash')");
            @$config->version->installHash = $installHash;

            $config->version->app = '3.0.0-beta1';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.0.0-beta1");
        } else {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: FAILED updating schema to 3.0-beta1!");
            echo "Something went wrong updating Schema";
            exit();
        }

    // Update from 3.0.0-beta1 to 3.0.0
    case '3.0.0-beta1':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.0.0");
        mysqli_query($conn,
            "UPDATE `system` SET `value` = '3.0' WHERE `system`.`name` = 'schema';"); // Update Schema Version
        $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema_version = $result['value'];
        if ($schema_version === '3.0') {
            $config->version->schema = '3.0';
            $config->version->app = '3.0.0';
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.0.0");
            $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Major update. Support all Atlas sensors. Framework updates.';
        } else {
            syslog(LOG_INFO, "(SYSTEM){UPDATER}: FAILED updating schema to 3.0");
            echo "Something went wrong updating Schema";
            exit();
        }

    // Update from 3.0.0 to 3.0.1
    case '3.0.0':
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: Starting upgrade from" . $config->version->app . " to 3.0.1");
        $config->version->app = '3.0.1';
        syslog(LOG_INFO, "(SYSTEM){UPDATER}: DONE 3.0.1");
        $notes .= '<li><strong>' . $config->version->app . '</strong> - ' . 'Bug Fixes.';
}
