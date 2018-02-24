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
 * File: src/inc/session.php
 * Builds and starts the session
 */

if (!isset($_SESSION)) {
    session_start();
}

// Process Login Cookie
if (!isset($_SESSION['UserLoggedIn'])) {
    if (isset($_COOKIE['device_key'])) {
        $device_key = $_COOKIE['device_key'];
        $result = mysqli_query($conn, "SELECT * FROM `sessions` WHERE `device_key`= '$device_key'");

        // Count the rows returned
        $count = mysqli_num_rows($result);
        if ($count === 1) {
            mysqli_query($conn, "DELETE FROM `sessions` WHERE `device_key`= '$device_key'");
            $row = mysqli_fetch_assoc($result);
            $presented_token = $_COOKIE['token'];
            $actual_token = md5($row['token']);
            $uid = $row['uid'];

            // Good token presented
            if ($presented_token === $actual_token) {
                // Let's remember the user is logged in
                $_SESSION['UserLoggedIn'] = true;
                $_SESSION['UserID'] = $uid;

                $row_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE `uid`= '$uid'"));
                $username = $row_users['username'];
                $_SESSION['Username'] = $username;
                $_SESSION['IsAdmin'] = (bool)$row_users['admin'];

                // Generate the device key and token for this session
                $device_key = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1, 10))), 1,
                    40);
                $token = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1, 10))), 1, 40);
                $user_agent = $_SERVER['HTTP_USER_AGENT'];

                // Save the session to the database
                $result = mysqli_query($conn,
                    "INSERT INTO `sessions` (`uid`, `device_key`, `token`, `user_agent`) VALUES ('$uid', '$device_key', '$token', '$user_agent')");
                if (!$result) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Saving session failed! Raw = " . mysqli_error($conn));
                }

                // Send the session cookie
                setcookie('device_key', $device_key, time() + 60 * 60 * 24 * 30, '/');
                setcookie('token', md5($token), time() + 60 * 60 * 24 * 30, '/');

                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully via cookie");

            } // Bad token presented
            else {
                unset($_COOKIE['device_key']);
                unset($_COOKIE['token']);
                setcookie('device_key', '', time() - 3600, '/');
                setcookie('token', '', time() - 3600, '/');
                $_SESSION = array();
                session_regenerate_id(true);
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid Cookie Presented for UID $uid: $device_key - $presented_token");
            }
        }
    }
}
