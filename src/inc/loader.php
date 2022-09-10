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
 * File: src/inc/loader.php
 * Loads the config, sets up database connection and session
 */

ini_set('display_errors', 0);

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__));
}

// Load config
if (!isset($config)) {
    if (file_exists(APP_BASE_PATH . '/usr/config.php')) {
        $config = require(APP_BASE_PATH . '/usr/config.php');
        $installed = true;
        if ($config->debug->logging === true) {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
    } else {
        $config = require(APP_BASE_PATH . '/usr/config.new.php');
        $installed = false;
    }
}

if (!isset($appInfo)) {
    $appInfo = json_decode(file_get_contents(dirname(__DIR__, 2) . '/.version'), true);
    $appInfo = array_change_key_case($appInfo);
    $appInfo = (object)$appInfo;
}

// Set timezone
if (date_default_timezone_get() != $config->site->timezone) {
    date_default_timezone_set($config->site->timezone);
}

// Open Database Connection
if (!isset($conn)) {
    /**
     * @var boolean $installed
     */
    if ($installed === true) {
        ini_set('mysqlnd_qc.enable_qc', 1);
        ini_set('mysqlnd_qc.cache_by_default', 1);

        $conn = mysqli_connect($config->mysql->host, $config->mysql->username, $config->mysql->password,
            $config->mysql->database);
        if (!$conn) {
            header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable");
            require_once('templates/dbConnectFailed.php');
            syslog(LOG_ERR, "(SYSTEM){LOADER}[ERROR]: MySQL Connection failed: " . mysqli_connect_error());
            exit();
        }
    }
}

// Start Logging
if (!isset($openlog)) {
    $facility = (php_uname("s") === 'Linux') ? LOG_LOCAL0 : LOG_USER;
    $openLog = openlog("$appInfo->name($appInfo->version)", LOG_ODELAY, $facility);
}

// Start the session
if (!isset($_SESSION)) {
    include(APP_BASE_PATH . '/inc/session.php');
}

// Output buffering
if (!isset($obStart)) {
    $obStart = ob_start();
    header('Cache-Control: no-cache');
    header('Content-Type-Options: nosniff');
    header_remove('Pragma');
    header_remove('Expires');
}
