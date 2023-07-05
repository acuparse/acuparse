<?php

/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: src/api/system/config.php
 * System Configuration API
 */

// Get the loader
require(dirname(__DIR__, 3) . '/inc/loader.php');

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $appInfo App Info
 */

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

// PHP Info
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {
    $env = getenv();
    echo "<h1>Acuparse Data</h1><h2>Application Data</h2>";
    print("<pre>" . print_r($appInfo, true) . "</pre><h2>Configuration Data</h2>");
    print("<pre>" . print_r($config, true) . "</pre><h2>Environment</h2>");
    print("<pre>" . print_r($env, true) . "</pre>");
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
    echo 'Unauthorized';
}

// End Token
include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');

exit();
