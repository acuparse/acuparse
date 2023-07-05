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
 * File: src/fcn/account/apiToken.php
 * Generate an API Token
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 */

$user = (int)mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));

// Can this user edit this uid?
if ($_SESSION['admin'] !== true) {
    $uid = $_SESSION['uid'];
    if ($user !== $uid) {
        // Log it
        syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: No permissions to modify $user. $uid is not an admin");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>No permissions to edit this user!</div>';
        header("Location: /");
        exit();
    }
}
$token = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    mt_rand(1, 10))),
    0, 39);

// Process Update
mysqli_query($conn, "UPDATE `users` SET `token` = '$token' WHERE `uid` = '$user'");

// If the insert Query was successful.

if (mysqli_affected_rows($conn) === 1) {

    $email = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT `email` FROM `users` WHERE `uid` = '$user'"));
    $subject = 'API Token Generated';

    $message = '<h2>API TOKEN Generated Successfully</h2><p>Your generated API Token is </p><h3>' . $token . '</h3>';

    // Mail it
    mailer($email['email'], $subject, $message);
    // Log it
    syslog(LOG_INFO, "(SYSTEM){USER}: Successfully generated API Token for UID $user");
    // Display message
    $_SESSION['messages'] = '<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>API Token Generated Successfully. Token is <code>' . $token . '</code></div>';
} else {
    syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: Generating API Token for UID $user failed: " . mysqli_error($conn));
    $_SESSION['messages'] = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Failed Generating API Token ' . $token . ' ...</div>';
}
header("Location: /");
exit();
