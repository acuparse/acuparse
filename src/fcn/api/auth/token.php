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
 * File: fcn/api/auth/token.php
 * Check API Token
 */

/** @var mysqli $conn Global MYSQL Connection */

$authToken = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING));

function checkToken($authToken): bool
{
    require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');
    /** @var mysqli $conn Global MYSQL Connection */

    $result = mysqli_query($conn,
        "SELECT `uid`, `username`, `admin` FROM `users` WHERE `token` = '$authToken'");

// If the token is good
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        // Let's remember the user is logged in
        $_SESSION['authenticated'] = true;
        $_SESSION['tokenAuth'] = true;
        $_SESSION['username'] = (string)$row['username'];
        $_SESSION['uid'] = (int)$row['uid'];
        $_SESSION['admin'] = (bool)$row['admin'];

        // Log it
        syslog(LOG_INFO, '(SYSTEM){USER}: ' . $_SESSION['username'] . ' authenticated via API Token');
        return true;
    } else {
        return false;
    }
}
