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
 * File: src/pub/admin/account.php
 * Processes User Login/Out and password changes
 */

// Get the loader
require(dirname(__DIR__, 2) . '/inc/loader.php');
require(APP_BASE_PATH . '/fcn/mailer.php');
/**
 * @var mysqli $conn Global MYSQL Connection
 */

// Logged in user stuff
if (isset($_SESSION['authenticated'])) {

// Logout and end the session
    if (isset($_GET['deauth'])) {

        require(APP_BASE_PATH . '/fcn/account/deauth.php');

    } // Process password change requests
    elseif (isset($_GET['password'])) {

        require(APP_BASE_PATH . '/fcn/account/password.php');

    } // Generate API Token
    elseif (isset($_GET['token'])) {

        require(APP_BASE_PATH . '/fcn/account/apiToken.php');

    } // Edit User
    elseif (isset($_GET['edit'])) {

        require(APP_BASE_PATH . '/fcn/account/edit.php');

    } // Admin Only Stuff
    elseif ($_SESSION['admin'] === true) {
        // Add a new user
        if (isset($_GET['add'])) {

            require(APP_BASE_PATH . '/fcn/account/add.php');

        } // View Users
        elseif (isset($_GET['view'])) {

            require(APP_BASE_PATH . '/fcn/account/view.php');

        } // Delete User
        elseif (isset($_GET['delete'])) {

            require(APP_BASE_PATH . '/fcn/account/delete.php');

        } else {
            // Nothing to do, goto home
            header("Location: /");
            exit();
        }
    } // Done with Admin Stuff
    else {
        // Nothing to do, goto home
        header("Location: /");
        exit();
    }
}

// User is not logged in

// Process a user authentication request
elseif (isset($_GET['auth'])) {

    require(APP_BASE_PATH . '/fcn/account/auth.php');

}  // Show the authentication form
elseif (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) !== 0) {
    require(APP_BASE_PATH . '/fcn/account/login.php');
} else {
    header("Location: /admin/install");
}
