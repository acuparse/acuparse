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
if (isset($_SESSION['authenticated'])) {

// Logout and end the session
    if (isset($_GET['deauth'])) {
        if (isset($_COOKIE['device'])) {
            $deviceKey = (string)$_COOKIE['device'];
            mysqli_query($conn, "DELETE FROM `sessions` WHERE `device_key` = '$deviceKey'");
            unset($_COOKIE['device']);
            unset($_COOKIE['token']);
            setcookie('device', '', time() - 3600, '/');
            setcookie('token', '', time() - 3600, '/');
        }
        $_SESSION = array();
        session_regenerate_id(true);
        header("Location: /");
        die();
    } // Process password change requests
    elseif (isset($_GET['password'])) {

        // Check if password change form was submitted
        if (isset($_GET['do'])) {
            $user = (int)mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));

            // Can this user edit this uid?
            if ($_SESSION['admin'] !== true) {
                $uid = $_SESSION['uid'];
                if ($user !== $uid) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: No permissions to modify $user. $uid is not an admin");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No permissions to edit this user!</div>';
                    header("Location: /");
                    die();
                }
            }

            $password = password_hash(filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);

            // Check and see if the password actually changed
            if ($user === $_SESSION['uid']) {
                $userRow = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT * FROM `users` WHERE `uid` = '$user'"));
                if (password_verify(filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW), $userRow['password'])) {
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid failed");
                    $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Seems like you entered your current password.</div>';
                    header("Location: /admin/account?password");
                    die();
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
                        $password = filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW);
                        $message = '<h2>Password Changed Successfully</h2><p>Your password was changed by an admin.</p><h3>Your new Password is: ' . $password . '</h3>';
                    } else {
                        $message = '<h2>Password Changed Successfully!</h2><p>You can now use it when logging in.</p>';
                    }
                }

                // Mail it
                mailer($email['email'], $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change for UID $user successful");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Password Updated Successfully!</div>';
            } else {
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Password change for UID $user failed: " . mysqli_error($conn));
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Nothing changed! Password was probably the same ...</div>';
            }
            header("Location: /");
            die();
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
                    die();
                }
            } else {
                $user = $_SESSION['uid'];
            }

            // Get Username
            $result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE `uid` = '$user'"));
            $username = $result['username'];
            ?>
            <div class="row">
                <div class="col">
                    <h1 class="page-header">Change Password for <?= $username; ?></h1>
                </div>
            </div>
            <hr>
            <section id="change-password" class="row change-password">
                <div class="col-8 col-md-6 mx-auto">
                    <form class="form" role="form" action="/admin/account?password&do" method="POST">
                        <div class="form-group">
                            <label for="pass">New Password:</label>
                            <input type="password" class="form-control" name="pass" id="pass" maxlength="32"
                                   placeholder="Password" required>
                        </div>
                        <input type="hidden" name="uid" id="uid" value="<?= $user; ?>">
                        <button type="submit" id="submit" value="submit"
                                class="btn btn-success margin-top-05">
                            <i class="fas fa-save" aria-hidden="true"></i> Save
                        </button>
                        <button type="button" class="btn btn-danger margin-top-05" onclick="location.href = '/admin'"><i
                                    class="fas fa-ban" aria-hidden="true"></i> Cancel
                        </button>
                    </form>
                </div>
            </section>
            <?php
            include(APP_BASE_PATH . '/inc/footer.php');
        }
    }  // Edit User
    elseif (isset($_GET['edit'])) {
        // Process the update
        if (isset($_GET['do'])) {
            $uid = $_SESSION['uid'];

            $user = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));

            // Can this user edit this uid?
            if ($_SESSION['admin'] !== true) {
                if ($user !== $uid) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: No permissions to modify $user. $uid is not an admin");
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
            $oldInfo = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT `username`, `email`, `admin` FROM `users` WHERE `uid` = '$user'"));
            if (($username === $oldInfo['username']) && ($email === $oldInfo['email']) && ($admin === $oldInfo['admin'])) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing user failed, nothing changed");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit User failed! Nothing to change!</div>';
                header("Location: /");
                die();
            } elseif ($username !== $oldInfo['username']) {
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
            } elseif ($email !== $oldInfo['email']) {
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
            } elseif ($admin < $oldInfo['admin']) {
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
                "UPDATE `users` SET `username` = '$username', `email` = '$email', `admin` = '$admin' WHERE `uid` = '$user'");
            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {

                // Send mail
                $subject = 'Account Modified';
                $message = '<h2>Account Modified Successfully!</h2><p>Your account details have been modified successfully.</p>';
                mailer($email, $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: User $user - $username updated successfully");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Updated Successfully!</div>';

                if (($username !== $old_info['username']) && ($user === $uid)) {
                    $_SESSION['username'] = $username;
                }
            } else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating user $user - $username failed: " . mysqli_error($conn));
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong updating the user!</div>';
            }
            // Redirect home
            header("Location: /");
            die();
        } // Show the edit form
        else {
            // Get User ID
            if (isset($_GET['uid'])) {
                if ($_SESSION['admin'] === true) {
                    $user = (int)mysqli_real_escape_string($conn,
                        filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));
                } else {
                    // Redirect to edit account without uid set
                    header("Location: /admin/account?edit");
                    die();
                }
            } else {
                $user = $_SESSION['uid'];
            }

            $sql = "SELECT `username`, `email`, `admin` FROM `users` WHERE `uid` = '$user'";
            // Make sure there is a user to edit
            $count = mysqli_num_rows(mysqli_query($conn, $sql));
            if ($count === 0) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating user $user failed. Does not exist");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No User with that User ID.</div>';
                header("Location: /");
                die();
            } // User exists
            else {
                $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
                $pageTitle = ($_SESSION['uid'] === $user) ? 'My Account' : 'Edit User Account';
                include(APP_BASE_PATH . '/inc/header.php');
                ?>
                <div class="row">
                    <div class="col">
                        <h1 class="page-header"><?= ($_SESSION['uid'] === $user) ? 'My Account' : 'Edit User Account'; ?></h1>
                    </div>
                </div>

                <hr>

                <div id="edit-user" class="edit-user">
                <section id="edit-user-data" class="row edit-user-data">
                    <div class="col">
                        <div class="row">
                            <div class="col-8 col-md-6 mx-auto">
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
                                    <div class="form-group border">
                                        <strong>Admin Access?</strong><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="admin"
                                                   id="admin-access-0"
                                                   value="0"
                                                <?= ((bool)$row['admin'] === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="admin-access-0">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input <?= ($_SESSION['admin'] !== true) ? 'disabled="disabled"' : false; ?>
                                                    class="form-check-input" type="radio" name="admin"
                                                    id="admin-access-1"
                                                    value="1"
                                                <?= ((bool)$row['admin'] === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="admin-access-1"><?= ((bool)$row['admin'] === true) ? '<strong>Yes</strong>' : 'Yes'; ?></label>
                                        </div>
                                    </div>
                                    <input type="hidden" value="<?= $user; ?>" name="uid">
                                    <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                                class="fas fa-save" aria-hidden="true"></i> Save
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                                class="fas fa-ban" aria-hidden="true"></i> Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="hr-dotted">

                <div class="row">
                    <div class="col">
                        <h2 class="panel-heading">Change User Password?</h2>
                    </div>
                </div>

                <section id="change-user-password" class="row change-user-password">
                    <div class="col-8 col-md-6 mx-auto alert alert-warning">
                        <p>Click below to change <?= $row['username']; ?>'s password.</p>
                        <button type="button" id="password" class="btn btn-warning margin-bottom-05"
                                onclick="location.href = '/admin/account?password<?= ($user !== $_SESSION['uid']) ? '&uid=' . $user : false; ?>'">
                            <i class="fas fa-key" aria-hidden="true"></i> Change Password
                        </button>
                    </div>
                </section>

                <?php if ($_SESSION['admin'] === true) { ?>
                    <hr class="hr-dotted">

                    <div class="row">
                        <div class="col">
                            <h2 class="panel-heading">Delete User?</h2>
                        </div>
                    </div>

                    <section id="delete-user" class="row delete-user">
                        <div class="col-8 col-md-6 mx-auto alert alert-danger">
                            <p>Click below to remove <?= $row['username']; ?>.
                            </p>
                            <button <?= ($user === $_SESSION['uid']) ? 'disabled="disabled"' : false; ?>
                                    type="button" id="delete" class="btn btn-danger margin-bottom-05"
                                    onClick="confirmDelete('/admin/account?delete&uid=<?= $user; ?>')"><i
                                        class="fas fa-user-times" aria-hidden="true"></i> Delete User
                            </button>
                        </div>
                    </section>
                    </div>
                <?php }
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
    elseif ($_SESSION['admin'] === true) {
        // Add a new user
        if (isset($_GET['add'])) {

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
                    die();
                }
                $checkEmail = mysqli_num_rows(mysqli_query($conn,
                    "SELECT `email` FROM `users` WHERE `email` = '$email'"));
                if ($checkEmail !== 0) {
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
                die();
            } // Show the add new user form
            else {
                $pageTitle = 'Add New User';
                include(APP_BASE_PATH . '/inc/header.php');
                ?>
                <div class="row">
                    <div class="col">
                        <h1 class="page-header">Add New User</h1>
                    </div>
                </div>
                <hr>
                <section id="add-user" class="row add-user">
                    <div class="col-8 col-md-6 mx-auto">
                        <form class="form" role="form" action="/admin/account?add&do" method="POST">
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
                                <input type="password" class="form-control" name="password" id="pass"
                                       placeholder="Password" maxlength="32" required>
                            </div>
                            <div class="form-group border">
                                <strong>Admin Access?</strong><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="admin"
                                           id="admin-access-0"
                                           value="0" checked="checked">
                                    <label class="form-check-label alert alert-success" for="admin-access-0">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input <?= ($_SESSION['admin'] !== true) ? 'disabled="disabled"' : false; ?>
                                            class="form-check-input" type="radio" name="admin"
                                            id="admin-access-1"
                                            value="1">
                                    <label class="form-check-label alert alert-danger" for="admin-access-1">Yes</label>
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
                </section>
                <?php
                include(APP_BASE_PATH . '/inc/footer.php');
            }
        } // View Users
        elseif (isset($_GET['view'])) {
            $pageTitle = 'View User Accounts';
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <div class="row">
                <div class="col">
                    <h1 class="page-header">View Users</h1>
                </div>
            </div>
            <hr>
            <section id="view-users" class="row view-users">

                <div class="col-md-8 col-12 mx-auto">
                    <table class="table table-light table-hover table-responsive-sm">
                        <thead>
                        <tr>
                            <th scope="col"><strong>Username</strong></th>
                            <th scope="col"><strong>Email</strong></th>
                            <th scope="col"><strong>Admin Access</strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM `users` ORDER BY `uid` DESC");
                        while ($row = mysqli_fetch_assoc($result)) {
                            $admin = ($row['admin'] === '1') ? 'Yes' : 'No';

                            ?>
                            <tr>
                                <th scope="row">
                                    <strong><a href="/admin/account?edit&uid=<?= $row['uid']; ?>"><?= $row['username']; ?>
                                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span></a></strong>
                                </th>
                                <td>
                                    <a href="/admin/account?edit&uid=<?= $row['uid']; ?>"><?= $row['email']; ?>
                                </td>
                                <td><?= $admin; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" onclick="location.href = '/admin'"><i
                                class="fas fa-check-circle" aria-hidden="true"></i> Done
                    </button>
                </div>
            </section>
            <?php
            // Get app footer
            include(APP_BASE_PATH . '/inc/footer.php');
        } // Delete User
        elseif (isset($_GET['delete'])) {
            $user = (int)mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));

            // Don't delete the logged in user
            if ($user === $_SESSION['uid']) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: User $user cannot delete themselves");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Cannot delete your own account!</div>';
            } else {
                $result = mysqli_query($conn, "DELETE FROM `users` WHERE `uid` = '$user'");
                // If the insert Query was successful.
                if (mysqli_affected_rows($conn) === 1) {
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: User $user deleted successfully");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Deleted Successfully!</div>';
                } else {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Deleting user $user failed!");
                    // Display message
                    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the user!</div>';
                }
            }
            // Redirect to view accounts
            header("Location: /admin/account?view");
            die();
        } else {
            // Nothing to do, goto home
            header("Location: /");
            die();
        }
    } // Done with Admin Stuff
    else {
        // Nothing to do, goto home
        header("Location: /");
        die();
    }
}

// User is not logged in

// Process a user authentication request
elseif (isset($_GET['auth'])) {

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

                // Generate the device key and token for this session
                $deviceKey = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz',
                    mt_rand(1, 10))), 1,
                    40);
                $token = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1, 10))),
                    1, 40);
                $tokenHash = (string)md5($token);
                $userAgent = (string)$_SERVER['HTTP_USER_AGENT'];

                // Save the session to the database
                $result = mysqli_query($conn,
                    "INSERT INTO `sessions` (`uid`, `device_key`, `token`, `user_agent`) VALUES ('$uid', '$deviceKey', '$token', '$userAgent')");
                if (!$result) {
                    // Log it
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Saving session failed! Raw = " . mysqli_error($conn));
                }

                // Send the session cookie
                setcookie('device', $deviceKey, time() + 60 * 60 * 24 * 30, '/');
                setcookie('token', $tokenHash, time() + 60 * 60 * 24 * 30, '/');

                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully");

                // Redirect user after successful authentication
                header("Location: /");
                die();
            } // Invalid password entered
            else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid password for $username");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: You have entered an invalid username or password.</div>';
                header("Location: /admin/account");
                die();
            }
        } // No rows found, user not authorized
        else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid username $username");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: You have entered an invalid username or password.</div>';
            header("Location: /admin/account");
            die();
        }
    } // Captcha Failed
    else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Invalid captcha response");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: Could not verify Captcha.</div>';
        header("Location: /admin/account");
        die();
    }
}  // Show the authentication form
elseif (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) !== 0) {
    $pageTitle = 'Sign In';
    $welcome = date("H");
    $welcome = ($welcome < '12') ? 'Morning' : (($welcome >= '12' && $welcome < '17') ? 'Afternoon' : 'Evening');
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="user-authentication" class="user-authentication">
        <div class="row">
            <div class="col-md-5 col-12 mx-auto border">
                <h1 class="page-header alert alert-heading alert-secondary">Good <?= $welcome; ?>!</h1>
                <form id="recaptcha-form" action="/admin/account?auth" method="POST">

                    <div class="form-group">
                        <label class="col-form-label" for="username">Username/Email:</label>
                        <input type="text" name="username" id="username" class="form-control border-primary"
                               placeholder="username@example.com" aria-describedby="user-help" required autofocus>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="password">Password:</label>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="Password" required>
                    </div>
                    <?php
                    if ($config->google->recaptcha->enabled === true) { ?>
                        <button class="margin-top-05 margin-bottom-05 btn btn-lg btn-success g-recaptcha"
                                data-sitekey="<?= $config->google->recaptcha->sitekey; ?>" data-callback="onSubmit">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
                        </button>
                        <?php
                    } else { ?>
                        <button class="margin-top-05 margin-bottom-05 btn btn-lg btn-success" type="submit"><i
                                    class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
                        </button>
                        <?php
                    } ?>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-auto mx-auto margin-top-10">
                <div><a href="/recover">Forgot your password?</a></div>
            </div>
        </div>
    </section>
    <?php
// Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} else {
    header("Location: /admin/install");
}
