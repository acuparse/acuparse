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
 * File: src/pub/admin/account.php
 * Processes User Login/Out and password changes
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');
require(APP_BASE_PATH . '/fcn/mailer.php');

// Logged in user stuff
if (isset($_SESSION['UserLoggedIn'])) {

// Logout and end the session
    if (isset($_GET['logout'])) {
        if (isset($_COOKIE['device_key'])) {
            $device_key = $_COOKIE['device_key'];
            mysqli_query($conn, "DELETE FROM `sessions` WHERE `device_key`= '$device_key'");
            unset($_COOKIE['device_key']);
            unset($_COOKIE['token']);
            setcookie('device_key', '', time() + 60 * 60 * 24 * 30, '/');
            setcookie('token', '', time() + 60 * 60 * 24 * 30, '/');
        }
        $_SESSION = array();
        session_regenerate_id(true);
        header("Location: /");
    } // Process password change requests
    elseif (isset($_GET['password'])) {

        // Check if password change form was submitted
        if (isset($_GET['do'])) {
            $user_id = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));

            // Can this user edit this uid?
            if ($_SESSION['IsAdmin'] !== true) {
                $uid = $_SESSION['UserID'];
                if ($user_id !== $uid) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: No permissions to modify $user_id. $uid is not an admin");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No permissions to edit this user!</div>';
                    header("Location: /");
                    die();
                }
            }

            $password = password_hash(filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);

            // Check and see if the password actually changed
            if ($user_id === $_SESSION['UserID']) {
                $userRow = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT * FROM `users` WHERE `uid` = '$user_id'"));
                if (password_verify(filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW), $userRow['password'])) {
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid failed");
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Seems like you entered your current password.</div>';
                    header("Location: /admin/account?password");
                    die();
                }
            }
            // Process Update
            mysqli_query($conn, "UPDATE `users` SET `password` = '$password' WHERE `uid` = '$user_id'");

            // If the insert Query was successful.

            if (mysqli_affected_rows($conn) === 1) {

                $email = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT `email` FROM `users` WHERE `uid` = '$user_id'"));
                $subject = 'Password Change Successful';

                // Is this an admin user changing someone else's password? If so, we should tell them what it is!
                if ($_SESSION['IsAdmin'] === true) {
                    $uid = $_SESSION['UserID'];
                    if ($user_id !== $uid) {
                        $password = filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW);
                        $message = '<h2>Password Changed Successfully</h2><p>Your password was changed by an admin.</p><h3>Your new Password is: ' . $password . '</h3>';
                    } else {
                        $message = '<h2>Password Changed Successfully!</h2><p>You can now use it when logging in.</p>';
                    }
                }

                // Mail it
                mailer($email['email'], $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change for UID $user_id successful");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Password Updated Successfully!</div>';
            } else {
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Password change for UID $user_id failed: " . mysqli_error($conn));
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Nothing changed! Password was probably the same ...</div>';
            }
            header("Location: /");

        } // Display the password change form
        else {
            $page_title = 'Change User Password | ' . $config->site->name;
            include(APP_BASE_PATH . '/inc/header.php');
            // Get UID
            if (isset($_GET['uid'])) {
                if ($_SESSION['IsAdmin'] === true) {
                    $user_id = mysqli_real_escape_string($conn,
                        filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));
                } else {
                    // Redirect to the password page without a uid set
                    header("Location: /admin/account?password");
                    die();
                }
            } else {
                $user_id = $_SESSION['UserID'];
            }

            // Get Username
            $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE `uid` = '$user_id'"));
            $username = $result['username'];
            ?>
            <section id="change_password" class="change_password_display">
                <div class="row">
                    <h1 class="page-header">Change Password for <?= $username; ?></h1>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <form class="form" role="form" action="/admin/account?password&do" method="POST">
                            <div class="form-group">
                                <label for="pass">Password:</label>
                                <input type="password" class="form-control" name="pass" id="pass" maxlength="32"
                                       required>
                            </div>
                            <input type="hidden" name="uid" id="uid" value="<?= $user_id; ?>">
                            <button type="submit" id="submit" value="submit"
                                    class="margin-top-05 btn btn-primary center-block">
                                <i class="fas fa-save" aria-hidden="true"></i> Save
                            </button>
                        </form>
                    </div>
                </div>
            </section>
            <?php
            include(APP_BASE_PATH . '/inc/footer.php');
        }
    }  // Edit User
    elseif (isset($_GET['edit'])) {
        // Process the update
        if (isset($_GET['do'])) {
            $uid = $_SESSION['UserID'];

            $user_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));

            // Can this user edit this uid?
            if ($_SESSION['IsAdmin'] !== true) {
                if ($user_id !== $uid) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: No permissions to modify $user_id. $uid is not an admin");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No permissions to edit this user!</div>';
                    header("Location: /");
                    die();
                }
            }
            $username = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
            $email = mysqli_real_escape_string($conn,
                strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
            $admin = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'admin', FILTER_SANITIZE_STRING));

            // Check for changes and for existing user with that username or email
            $old_info = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `username`, `email`, `admin` FROM `users` WHERE `uid` = '$user_id'"));
            if (($username === $old_info['username']) && ($email === $old_info['email']) && ($admin === $old_info['admin'])) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing user failed, nothing changed");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit User failed! Nothing to change!</div>';
                header("Location: /");
                die();
            } elseif ($username !== $old_info['username']) {
                $count = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `username` FROM `users` WHERE `username` = '$username'"));
                if ($count !== 0) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing user failed, username already exists");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit User failed! Duplicate Username</div>';
                    header("Location: /");
                    die();
                }
            } elseif ($email !== $old_info['email']) {
                $count = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `email` FROM `users` WHERE `email` = '$email'"));
                if ($count !== 0) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing user failed, email already exists");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit User failed! Duplicate Email</div>';
                    header("Location: /");
                    die();
                }
            } elseif ($admin < $old_info['admin']) {
                $count = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `uid` FROM `users` WHERE `admin` = '1'"));
                if ($count === 1) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing a user failed! Can't demote only admin.");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit user failed! Can\'t demote the only admin.</div>';
                    header("Location: /");
                    die();
                }
            }
            // Update user account details
            $result = mysqli_query($conn,
                "UPDATE `users` SET `username` = '$username', `email` = '$email', `admin` = '$admin' WHERE `uid` = '$user_id'");
            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {

                // Send mail
                $subject = 'Account Modified';
                $message = '<h2>Account Modified Successfully!</h2><p>Your account details have been modified successfully.</p>';
                mailer($email, $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: User $user_id - $username updated successfully");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Updated Successfully!</div>';

                if (($username !== $old_info['username']) && ($user_id === $uid)) {
                    $_SESSION['Username'] = $username;
                }
            } else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating user $user_id - $username failed: " . mysqli_error($conn));
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong updating the user!</div>';
            }
            // Redirect home
            header("Location: /");
        } // Show the edit form
        else {
            // Get User ID
            if (isset($_GET['uid'])) {
                if ($_SESSION['IsAdmin'] === true) {
                    $user_id = mysqli_real_escape_string($conn,
                        filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));
                } else {
                    // Redirect to edit account without uid set
                    header("Location: /admin/account?edit");
                    die();
                }
            } else {
                $user_id = $_SESSION['UserID'];
            }

            $sql = "SELECT `username`, `email`, `admin` FROM `users` WHERE `uid` = '$user_id'";
            // Make sure there is a user to edit
            $count = mysqli_num_rows(mysqli_query($conn, $sql));
            if ($count === 0) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating user $user_id failed. Does not exist");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No User with that User ID.</div>';
                header("Location: /");
            } // User exists
            else {
                $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
                $page_title = 'Edit User Account | ' . $config->site->name;
                include(APP_BASE_PATH . '/inc/header.php');
                ?>
                <section id="edit_user" class="edit_user_display">
                    <div class="row">
                        <h1 class="page-header">Edit User</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4">
                            <p>Enter the new details for user <?= $row['username']; ?>:</p>
                            <form class="form" role="form" action="/admin/account?edit&do" method="POST">
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" name="username" id="username"
                                           placeholder="Username" maxlength="255"
                                           value="<?= $row['username']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="user_email">Email:</label>
                                    <input type="text" class="form-control" name="email" id="email"
                                           placeholder="Email" maxlength="255"
                                           value="<?= $row['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <strong>Is Admin?</strong><br>
                                    <label class="radio-inline">
                                        <input <?php if ($_SESSION['IsAdmin'] !== true) {
                                            echo 'disabled';
                                        } ?> type="radio" name="admin" id="admin"
                                             value="1" <?php if ((bool)$row['admin'] === true) {
                                            echo 'checked="checked"';
                                        } ?> required>Yes</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="admin" id="admin"
                                               value="0" <?php if ((bool)$row['admin'] === false) {
                                            echo 'checked="checked"';
                                        } ?>>No</label>
                                </div>
                                <input type="hidden" value="<?= $user_id; ?>" name="uid">
                                <button type="submit" id="submit" value="submit" class="btn btn-primary"><i
                                            class="fas fa-save" aria-hidden="true"></i> Save
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr class="hr-dotted">
                    <div class="row">
                        <h2>Change User Password?</h2>
                        <div class="col-lg-6 col-lg-offset-3  alert-warning">
                            <p>Click below to change <?= $row['username']; ?>'s password.</p>
                            <button type="button" id="password" class="btn btn-warning center-block"
                                    onclick="location.href = '/admin/account?password<?php if ($user_id !== $_SESSION['UserID']) {
                                        echo '&uid=' . $user_id;
                                    } ?>'"><i class="fas fa-key" aria-hidden="true"></i> Change Password
                            </button>
                        </div>
                    </div>
                    <?php if ($_SESSION['IsAdmin'] === true) { ?>
                        <hr class="hr-dotted">
                        <div class="row">
                            <h2 class="">Delete User?</h2>
                            <div class="col-lg-6 col-lg-offset-3  alert-danger">
                                <p>Click below to remove <?= $row['username']; ?>.
                                </p>
                                <button <?php if ($user_id === $_SESSION['UserID']) {
                                    echo 'disabled="disabled" ';
                                } ?>type="button" id="delete" class="btn btn-danger center-block"
                                        onClick="confirmDelete('/admin/account?delete&uid=<?= $user_id; ?>')"><i
                                            class="fas fa-user-times" aria-hidden="true"></i> Delete User
                                </button>
                            </div>
                        </div>
                    <?php }
                    ?>
                </section>
                <?php
                // Set the page footer to include the delete warning dialogue.
                $page_footer = '
                    <script>    
                    function confirmDelete(url) {
                        if (confirm("Are you sure you want to delete this user?")) {
                            window.open(url,"_self");
                        } else {
                            false;
                        }       
                    }
                    </script>';
                include(APP_BASE_PATH . '/inc/footer.php');
            }
        }
    } // Admin Only Stuff
    elseif ($_SESSION['IsAdmin'] === true) {
        // Add a new user
        if (isset($_GET['add_user'])) {

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
                $check_username = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `username` FROM `users` WHERE `username` = '$username'"));
                if ($check_username !== 0) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding user failed, username already exists");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Adding user failed, username already exists.</div>';
                    header("Location: /admin/account?add_user");
                    die();
                }
                $check_email = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `email` FROM `users` WHERE `email` = '$email'"));
                if ($check_email !== 0) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding user failed, email already exists");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Adding user failed, email already exists.</div>';
                    header("Location: /admin/account?add_user");
                    die();
                }
                $result = mysqli_query($conn,
                    "INSERT INTO `users` (`username`, `password`, `email`, `admin`) VALUES ('$username', '$password', '$email', '$admin')");
                if (mysqli_affected_rows($conn) === 1) {

                    // Mail it
                    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
                    $subject = 'New Account Created';
                    $message = '<h2>New Account Created Successfully!</h2><p>Your new account has created successfully. You can now login at <a href="http://' . $config->site->hostname . '">' . $config->site->hostname . '</a> with the details below.</p><h3>Username: ' . $username . '</h3><h3>Password: ' . $password . '</h3>';
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
            } // Show the add new user form
            else {
                $page_title = 'Add New User | ' . $config->site->name;
                include(APP_BASE_PATH . '/inc/header.php');
                ?>
                <section id="add_user" class="add_user_display">
                    <div class="row">
                        <h1 class="page-header">Add New User</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4">
                            <div id="add_admin_user">
                                <p>Enter the new user details below:</p>
                                <form class="form" role="form" action="/admin/account?add_user&do" method="POST">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="username" id="username"
                                               placeholder="Username" maxlength="32" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" id="email"
                                               placeholder="Email"
                                               maxlength="255" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="password" id="pass"
                                               placeholder="Password" maxlength="32" required>
                                    </div>
                                    <div class="form-group">
                                        <strong>Is Admin?</strong><br>
                                        <label class="radio-inline"><input type="radio" name="admin" id="admin"
                                                                           value="1" required>Yes</label>
                                        <label class="radio-inline"><input type="radio" name="admin" id="admin"
                                                                           value="0" checked="checked">No</label>
                                    </div>
                                    <button type="submit" id="submit" value="submit" class="btn btn-primary"><i
                                                class="fas fa-save" aria-hidden="true"></i> Save
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                include(APP_BASE_PATH . '/inc/footer.php');
            }
        } // View Users
        elseif (isset($_GET['view'])) {
            $page_title = 'View User Accounts | ' . $config->site->name;
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <section id="view_users" class="view_users_display">
                <div class="row">
                    <h1 class="page-header">View Users</h1>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <div id="users">
                            <table class="table table-responsive" id="users_table">
                                <thead>
                                <tr>
                                    <td><strong>Username</strong></td>
                                    <td><strong>Email</strong></td>
                                    <td><strong>Is Admin?</strong></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM `users` ORDER BY `uid` DESC");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $admin = ($row['admin'] === '1') ? 'Yes' : 'No';

                                    ?>
                                    <tr>
                                        <td>
                                            <strong><a href="/admin/account?edit&uid=<?= $row['uid']; ?>"><?= $row['username']; ?>
                                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span></a></strong>
                                        </td>
                                        <td>
                                            <a href="/admin/account?edit&uid=<?= $row['uid']; ?>"><?= $row['email']; ?>
                                        </td>
                                        <td><?= $admin; ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary center-block" onclick="location.href = '/admin'"><i
                                    class="fas fa-arrow-circle-left" aria-hidden="true"></i> Done
                        </button>
                    </div>
                </div>
            </section>
            <?php
            // Get app footer
            include(APP_BASE_PATH . '/inc/footer.php');
        } // Delete User
        elseif (isset($_GET['delete'])) {
            $user_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));

            // Don't delete the logged in user
            if ($user_id === $_SESSION['UserID']) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: User $user_id cannot delete themselves");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Cannot delete your own account!</div>';
            } else {
                $result = mysqli_query($conn, "DELETE FROM `users` WHERE `uid` = '$user_id'");
                // If the insert Query was successful.
                if (mysqli_affected_rows($conn) === 1) {
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: User $user_id deleted successfully");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Deleted Successfully!</div>';
                } else {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Deleting user $user_id failed!");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the user!</div>';
                }
            }
            // Redirect to view accounts
            header("Location: /admin/account?view");
        }
    } // Done with Admin Stuff
    else {
        // Nothing to do, goto home
        header("Location: /");
    }
}

// User is not logged in

// Process a user login request
elseif (isset($_GET['login'])) {

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

    // Success, process the login
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
                $_SESSION['UserLoggedIn'] = true;
                $_SESSION['Username'] = $row['username'];
                $_SESSION['UserID'] = (int)$row['uid'];
                $_SESSION['IsAdmin'] = (bool)$row['admin'];
                $uid = $_SESSION['UserID'];

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
                syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully");

                // Redirect user after successful login
                header("Location: /");
            } // Invalid password entered
            else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid password for $username");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Sorry, you have entered an invalid username or password!</div>';
                header("Location: /admin/account");
            }
        } // No rows found, user not authorized
        else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid username $username");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Sorry, you have entered an invalid username or password!</div>';
            header("Location: /admin/account");
        }
    } // Captcha Failed
    else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid captcha response");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Sorry, Captcha verification failed!</div>';
        header("Location: /admin/account");
    }
}  // Show the login form
elseif (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) !== 0) {
    $page_title = 'User Login | ' . $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="user_login" class="user_login_display">
        <div class="row">
            <form id="recaptcha-form" class="form-signin" action="/admin/account?login" method="POST">
                <h2 class="form-signin-heading">Login to <?= $config->site->hostname; ?></h2>
                <div class="form-group">
                    <label for="username" class="sr-only">Username/Email</label>
                    <input type="text" name="username" id="username" class="form-control"
                           placeholder="Username/Email" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Password" required>
                </div>
                <?php
                if ($config->google->recaptcha->enabled === true) { ?>
                    <button class="margin-top-05 btn btn-lg btn-primary btn-block g-recaptcha"
                            data-sitekey="<?= $config->google->recaptcha->sitekey; ?>" data-callback="onSubmit">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Login
                    </button>
                    <?php
                } else { ?>
                    <button class="margin-top-05 btn btn-lg btn-primary btn-block" type="submit"><i
                                class="fas fa-sign-in-alt" aria-hidden="true"></i> Login
                    </button>
                    <?php
                } ?>
            </form>
            <div><a href="/recover">Forgot your password?</a></div>
        </div>
    </section>
    <?php
    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} else {
    header("Location: /admin/install");
}
