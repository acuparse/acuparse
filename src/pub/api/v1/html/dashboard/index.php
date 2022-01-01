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
 * File: src/pub/api/v1/html/dashboard/index.php
 * Get Dashboard weather HTML data
 */

// Get the loader
require(dirname(__DIR__, 5) . '/inc/loader.php');

// Access Token
include(APP_BASE_PATH . '/fcn/api/auth/getToken.php');

// Get Weather HTML
if (isset($_GET['main'])) {
    require(APP_BASE_PATH . '/fcn/weather/getCurrentDashboardHTML.php');
    getCurrentDashboardHTML();
} else {
    include_once(APP_BASE_PATH . '/fcn/weather/getCurrentHTML.php');
    getCurrentHTML();
}

// End Token
include(APP_BASE_PATH . '/fcn/api/auth/endToken.php');
