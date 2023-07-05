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
 * File: src/fcn/install/adminAccount.php
 * Add the initial administrator account
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */

// Check to ensure there are no other accounts
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) === 0) {
    $username = mysqli_real_escape_string($conn,
        strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)));
    $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
    $email = mysqli_real_escape_string($conn,
        strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
    $result = mysqli_query($conn,
        "INSERT INTO `users` (`username`, `password`, `email`, `admin`) VALUES ('$username', '$password', '$email', '1')");

    // If adding the account was successful
    if (mysqli_affected_rows($conn) === 1) {
        $createdUserID = (int)mysqli_insert_id($conn);
        $installHash = $config->version->installHash;
        mysqli_query($conn, "INSERT INTO `system` (`name`, `value`) VALUES ('installHash', '$installHash')");

        // Mail it
        require(APP_BASE_PATH . '/fcn/mailer.php');
        $subject = 'Admin Account Created';
        $message = '<h2>Admin Account Created Successfully!</h2><p>Your admin account has been added successfully. You can now sign in.</p>';
        mailer($email, $subject, $message);
        // Log it
        syslog(LOG_INFO, "(SYSTEM){INSTALLER}: First account for $username added successfully");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>User Added Successfully!</div>';

        // Let's remember the user is logged in
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['uid'] = $createdUserID;
        $_SESSION['admin'] = true;
        $uid = $_SESSION['uid'];

        include(APP_BASE_PATH . '/fcn/sessionToken.php');

        // Log it
        syslog(LOG_INFO, "(SYSTEM){INSTALLER}: $username logged in successfully");

        // Redirect user after successful authentication
        header("Location: /admin/settings");
    } // Something went wrong ...
    else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM){INSTALLER}[ERROR]: Adding first admin $username failed");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Oops, something went wrong!</div>';
        header("Location: /admin");
    }
    exit();
} // There is already an account in the DB
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
    exit(syslog(LOG_EMERG, "(SYSTEM){INSTALLER}[WARNING]: ATTEMPTED TO ADD ADMIN WHEN ONE EXISTS"));
}
