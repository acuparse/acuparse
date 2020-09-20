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
 * File: src/fcn/account/auth.php
 * Authorize a User
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

// Check for google recaptcha
if ($config->google->recaptcha->enabled === true) {
    // Check that Google captcha is correct
    $captcha = $_POST['g-recaptcha-response'];
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $config->google->recaptcha->secret . "&response=" . $captcha),
        true);
} // Recaptcha not enabled, set response to true by default
else {
    $response['success'] = true;
}

// Success, begin authentication
if ($response['success'] === true) {
    $username = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $result = mysqli_query($conn,
        "SELECT * FROM `users` WHERE `username` = '$username' OR `email` = '$username'");

    // If the username is good
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Check the password
        if (password_verify(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), $row['password'])) {
            // Let's remember the user is logged in
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = (string)$row['username'];
            $_SESSION['uid'] = (int)$row['uid'];
            $_SESSION['admin'] = (bool)$row['admin'];
            $uid = $_SESSION['uid'];

            include(APP_BASE_PATH . '/fcn/sessionToken.php');

            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully");

            // Redirect user after successful authentication
            header("Location: /");
            exit();
        } // Invalid password entered
        else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid password for $username");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: You have entered an invalid username or password.</div>';
            header("Location: /admin/account");
            exit();
        }
    } // No rows found, user not authorized
    else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid username $username");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: You have entered an invalid username or password.</div>';
        header("Location: /admin/account");
        exit();
    }
} // Captcha Failed
else {
    // Log it
    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid captcha response");
    // Display message
    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: Could not verify Captcha.</div>';
    header("Location: /admin/account");
    exit();
}
