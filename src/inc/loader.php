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
 * File: src/inc/loader.php
 * Loads the config, sets up database connection and session
 */

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__));
}

// Load config
if (!isset($config)) {
    if (file_exists(APP_BASE_PATH . '/usr/config.php')) {
        $config = require(APP_BASE_PATH . '/usr/config.php');
        $installed = true;
    } else {
        $config = require(APP_BASE_PATH . '/usr/config.new.php');
        $installed = false;
    }
}

if (!isset($appInfo)) {
    $appInfo = json_decode(file_get_contents(dirname(dirname(__DIR__)) . '/.version'), true);
    $appInfo = (object)array(
        'name' => $appInfo['name'], // Application Name
        'version' => $appInfo['version'], // Application version
        'schema' => $appInfo['schema'], // Database Schema Version
        'repo' => $appInfo['repo'], // Git Repository
        'homepage' => $appInfo['homepage'] // Project Homepage
    );
}

// Set timezone
if (date_default_timezone_get() != $config->site->timezone) {
    date_default_timezone_set($config->site->timezone);
}

// Open Database Connection
if (!isset($conn)) {
    if ($installed === true) {
        $conn = mysqli_connect($config->mysql->host, $config->mysql->username, $config->mysql->password,
            $config->mysql->database);
        if (!$conn) {
            die(syslog(LOG_ERR, "(SYSTEM)[ERROR]: MySQL Connection failed: " . mysqli_connect_error()));
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
    if ((isset($_SERVER["HTTP_X_PURPOSE"]) and (strtolower($_SERVER["HTTP_X_PURPOSE"]) == "preview")) or
        (isset($_SERVER["HTTP_X_MOZ"]) and (strtolower($_SERVER["HTTP_X_MOZ"]) == "prefetch"))) {
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Prefetch Detected");
    } else {
        include(APP_BASE_PATH . '/inc/session.php');
    }
}

// Output buffering
if (!isset($obStart)) {
    $obStart = ob_start();
}
