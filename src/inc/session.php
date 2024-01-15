<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2024 Maxwell Power
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
 * File: src/inc/session.php
 * Builds and starts the session
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 */

if (!isset($_SESSION)) {
    session_name('session');
    session_start();

// Process Login Cookie
    /**
     * @var boolean $installed
     */
    if (!isset($_SESSION['authenticated']) && $installed === true) {
        if (isset($_COOKIE['device'])) {
            $deviceKey = (string)$_COOKIE['device'];
            $result = mysqli_query($conn, "SELECT * FROM `sessions` WHERE `device_key`= '$deviceKey'");

            // Count the rows returned
            $count = mysqli_num_rows($result);
            if ($count === 1) {
                mysqli_query($conn, "DELETE FROM `sessions` WHERE `device_key`= '$deviceKey'");
                $row = mysqli_fetch_assoc($result);
                $presentedToken = (string)$_COOKIE['token'];
                $actualToken = md5($row['token']);
                $uid = (int)$row['uid'];

                // Good token presented
                if ($presentedToken === $actualToken) {
                    $usersRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE `uid`= '$uid'"));
                    $username = (string)$usersRow['username'];

                    // Let's remember the user is logged in
                    $_SESSION['authenticated'] = true;
                    $_SESSION['uid'] = $uid;
                    $_SESSION['username'] = $username;
                    $_SESSION['admin'] = (bool)$usersRow['admin'];

                    include(APP_BASE_PATH . '/fcn/sessionToken.php');

                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully via cookie");

                } // Bad token presented
                else {
                    unset($_COOKIE['device']);
                    unset($_COOKIE['token']);
                    setcookie('device', '', time() - 3600, '/');
                    setcookie('token', '', time() - 3600, '/');
                    $_SESSION = array();
                    session_regenerate_id(true);
                    syslog(LOG_ERR,
                        "(SYSTEM)[ERROR]: Invalid Cookie Presented for UID $uid: $deviceKey - $presentedToken");
                }
            }
        }
    }
}
