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
 * File: src/fcn/account/add.php
 * Add a user
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

// Process new user addition
if (isset($_GET['do'])) {
    $username = mysqli_real_escape_string($conn,
        strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)));
    $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
    $email = mysqli_real_escape_string($conn,
        strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
    $admin = mysqli_real_escape_string($conn,
        filter_input(INPUT_POST, 'admin', FILTER_SANITIZE_STRING));

    // Check for existing user with that username or email
    $checkUsername = mysqli_num_rows(mysqli_query($conn,
        "SELECT `username` FROM `users` WHERE `username` = '$username'"));
    if ($checkUsername !== 0) {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding user failed, username already exists");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Adding user failed, username already exists.</div>';
        header("Location: /admin/account?add_user");
        exit();
    }
    $checkEmail = mysqli_num_rows(mysqli_query($conn,
        "SELECT `email` FROM `users` WHERE `email` = '$email'"));
    if ($checkEmail !== 0) {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding user failed, email already exists");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Adding user failed, email already exists.</div>';
        header("Location: /admin/account?add_user");
        exit();
    }
    $result = mysqli_query($conn,
        "INSERT INTO `users` (`username`, `password`, `email`, `admin`) VALUES ('$username', '$password', '$email', '$admin')");
    if (mysqli_affected_rows($conn) === 1) {

        // Mail it
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
        $subject = 'New Account Created';
        $message = '<h2>New Account Created Successfully!</h2><p>Your new account has created successfully. You can now sign in at <a href="http://' . $config->site->hostname . '">' . $config->site->hostname . '</a> with the details below.</p><h3>Username: ' . $username . '</h3><h3>Password: ' . $password . '</h3>';
        mailer($email, $subject, $message);
        // Log it
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Account for $username added successfully");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Account added successfully.</div>';
    } else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding user failed: " . mysqli_error($conn));
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Adding user failed.</div>';
    }
    header("Location: /admin");
    exit();
} // Show the add new user form
else {
    $pageTitle = 'Add New User';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="add-user" class="add-user">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Add New User</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-8 mx-auto">
                <form class="form" action="/admin/account?add&do" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username"
                               placeholder="Username" maxlength="32" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" name="email" id="email"
                               placeholder="username@example.com"
                               maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password"
                               placeholder="Password" maxlength="32" required onkeyup='verifyPassword();'>
                    </div>
                    <div class="form-group">
                        <label for="password2">Verify Password</label>
                        <input type="password" class="form-control" name="password2" id="password2"
                               placeholder="Password" maxlength="32" required onkeyup='verifyPassword();'>
                    </div>
                    <div class="form-group border">
                        <strong>Admin Access?</strong><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="admin"
                                   id="admin-access-0"
                                   value="0" checked="checked">
                            <label class="form-check-label alert alert-success"
                                   for="admin-access-0">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input <?= ($_SESSION['admin'] !== true) ? 'disabled="disabled"' : false; ?>
                                    class="form-check-input" type="radio" name="admin"
                                    id="admin-access-1"
                                    value="1">
                            <label class="form-check-label alert alert-danger"
                                   for="admin-access-1">Yes</label>
                        </div>
                    </div>
                    <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                class="fas fa-save" aria-hidden="true"></i> Save
                    </button>
                    <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                class="fas fa-ban" aria-hidden="true"></i> Cancel
                    </button>
                </form>
            </div>
        </div>
    </section>
    <script>
        function verifyPassword() {
            const pass = document.getElementById('password');
            const pass2 = document.getElementById('password2');

            document.getElementById("submit").disabled = pass.value.length === 0 ||
                pass.value !== pass2.value;
        }

        verifyPassword();
    </script>
    <?php
    include(APP_BASE_PATH . '/inc/footer.php');
}
