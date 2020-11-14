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
 * File: src/fcn/account/deauth.php
 * Deatuh a User
 */

/** @var mysqli $conn Global MYSQL Connection */

if (isset($_COOKIE['device'])) {
    $username = $_SESSION['username'];
    $deviceKey = (string)$_COOKIE['device'];
    mysqli_query($conn, "DELETE FROM `sessions` WHERE `device_key` = '$deviceKey'");
    unset($_COOKIE['device']);
    unset($_COOKIE['token']);
    setcookie('device', '', time() - 3600, '/');
    setcookie('token', '', time() - 3600, '/');
}
$_SESSION = array();
session_regenerate_id(true);

// Log it
syslog(LOG_INFO, "(SYSTEM){USER}: $username logged out successfully");

header("Location: /");
exit();
