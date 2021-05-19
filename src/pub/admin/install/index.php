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
 * File: src/admin/install/index.php
 * Install/Update Script
 */

require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');

// Process an update
/**
 * @var boolean $installed
 */
if (isset($_GET['update']) && $installed === true) {
    require(APP_BASE_PATH . '/fcn/install/update.php');
} // Create initial administrator account
elseif (isset($_GET['account']) && $installed === true) {
    // Process the new account
    if (isset($_GET['do'])) {
        require(APP_BASE_PATH . '/fcn/install/adminAccount.php');
    } // Show the initial user form
    else {
        require(APP_BASE_PATH . '/fcn/install/initialUser.php');
    }
} // Configure the database connection
elseif (isset($_GET['database']) && $installed === false) {
    require(APP_BASE_PATH . '/fcn/install/database.php');
}  // New Install, setup site config
elseif ($installed === false) {
    require(APP_BASE_PATH . '/fcn/install/databaseSettings.php');
} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
