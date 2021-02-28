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
 * File: src/fcn/account/password.php
 * Processes password changes
 */

/** @var mysqli $conn Global MYSQL Connection */

// Check if password change form was submitted
if (isset($_GET['do'])) {
    $user = (int)mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));

    // Can this user edit this uid?
    if ($_SESSION['admin'] !== true) {
        $uid = $_SESSION['uid'];
        if ($user !== $uid) {
            // Log it
            syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: No permissions to modify $user. $uid is not an admin");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No permissions to edit this user!</div>';
            header("Location: /");
            exit();
        }
    }

    $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);

    // Check and see if the password actually changed
    if ($user === $_SESSION['uid']) {
        $userRow = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM `users` WHERE `uid` = '$user'"));
        if (password_verify(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), $userRow['password'])) {
            // Log it
            syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: Password change request for UID $uid failed");
            $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Seems like you entered your current password.</div>';
            header("Location: /admin/account?password");
            exit();
        }
    }
    // Process Update
    mysqli_query($conn, "UPDATE `users` SET `password` = '$password' WHERE `uid` = '$user'");

    // If the insert Query was successful.

    if (mysqli_affected_rows($conn) === 1) {

        $email = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `email` FROM `users` WHERE `uid` = '$user'"));
        $subject = 'Password Change Successful';

        // Is this an admin user changing someone else's password? If so, we should tell them what it is!
        if ($_SESSION['admin'] === true) {
            $uid = $_SESSION['uid'];
            if ($user !== $uid) {
                $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
                $message = '<h2>Password Changed Successfully</h2><p>Your password was changed by an admin.</p><h3>Your new Password is: ' . $password . '</h3>';
            } else {
                $message = '<h2>Password Changed Successfully!</h2><p>You can now use it when logging in.</p>';
            }
        } else {
            $message = '<h2>Password Changed Successfully!</h2><p>You can now use it when logging in.</p>';
        }

        // Mail it
        mailer($email['email'], $subject, $message);
        // Log it
        syslog(LOG_INFO, "(SYSTEM){USER}: Password change for UID $user successful");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Password Updated Successfully!</div>';
    } else {
        syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: Password change for UID $user failed: " . mysqli_error($conn));
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Nothing changed! Password was probably the same ...</div>';
    }
    header("Location: /");
    exit();
} // Display the password change form
else {
    $pageTitle = 'Change User Password';
    include(APP_BASE_PATH . '/inc/header.php');
    // Get UID
    if (isset($_GET['uid'])) {
        if ($_SESSION['admin'] === true) {
            $user = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));
        } else {
            // Redirect to the password page without a uid set
            header("Location: /admin/account?password");
            exit();
        }
    } else {
        $user = $_SESSION['uid'];
    }

    // Get Username
    $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE `uid` = '$user'"));
    $username = $result['username'];
    ?>
    <section id="change-password" class="change-password">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Change Password for <?= $username; ?></h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-8 col-md-6 mx-auto">
                <form class="form" action="/admin/account?password&do" method="POST">
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" class="form-control" name="password" id="password" maxlength="32"
                               placeholder="Password" required onkeyup='verifyPassword();'>
                    </div>
                    <div class="form-group">
                        <label for="password2">Verify Password:</label>
                        <input type="password" class="form-control" name="password2" id="password2"
                               placeholder="Password" maxlength="32" required onkeyup='verifyPassword();'>
                    </div>
                    <input type="hidden" name="uid" id="uid" value="<?= $user; ?>">
                    <button type="submit" id="submit" value="submit"
                            class="btn btn-success margin-top-05">
                        <i class="fas fa-save" aria-hidden="true"></i> Save
                    </button>
                    <button type="button" class="btn btn-danger margin-top-05"
                            onclick="location.href = '/admin'"><i
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
