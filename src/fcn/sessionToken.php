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
 * File: src/fcn/sessionToken.php
 * Create a session token
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var string $uid User ID
 */

// Generate the device key and token for this session
$deviceKey = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    mt_rand(1, 10))), 0,
    40);
$token = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    mt_rand(1, 10))),
    0, 40);
$tokenHash = md5($token);
$userAgent = (string)$_SERVER['HTTP_USER_AGENT'];

// Save the session to the database
$result = mysqli_query($conn,
    "INSERT INTO `sessions` (`uid`, `device_key`, `token`, `user_agent`) VALUES ('$uid', '$deviceKey', '$token', '$userAgent')");
if (!$result) {
    // Log it
    syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: Saving session failed! Raw = " . mysqli_error($conn));
}

// Send the session cookie
setcookie('device', $deviceKey, time() + 60 * 60 * 24 * 30, '/');
setcookie('token', $tokenHash, time() + 60 * 60 * 24 * 30, '/');
