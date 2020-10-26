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
 * File: src/fcn/account/edit.php
 * Edit a user
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $old_info */

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
            exit();
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
        exit();
    } elseif ($username !== $oldInfo['username']) {
        $count = mysqli_num_rows(mysqli_query($conn,
            "SELECT `username` FROM `users` WHERE `username` = '$username'"));
        if ($count !== 0) {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: Editing user failed, username already exists");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Edit User failed! Duplicate Username</div>';
            header("Location: /");
            exit();
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
            exit();
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
            exit();
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
    exit();
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
            exit();
        }
    } else {
        $user = $_SESSION['uid'];
    }

    $sql = "SELECT `username`, `email`, `admin`, `token` FROM `users` WHERE `uid` = '$user'";
    // Make sure there is a user to edit
    $count = mysqli_num_rows(mysqli_query($conn, $sql));
    if ($count === 0) {
        // Log it
        syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating user $user failed. Does not exist");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>No User with that User ID.</div>';
        header("Location: /");
        exit();
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
                    <h2 class="panel-heading">API Token</h2>
                </div>
            </div>

            <section id="api-token" class="row">
                <div class="col-8 col-md-6 mx-auto alert alert-secondary">
                    <?php
                    if (!empty($row['token'])) { ?>
                        <p>Click below to replace <?= $row['username']; ?>'s API Token.</p>
                        <button type="button" id="token" class="btn btn-success margin-bottom-05"
                                onclick="location.href = '/admin/account?token&uid=<?= $user; ?>'">
                            <i class="fas fa-lock" aria-hidden="true"></i> Replace API Token
                        </button>
                    <?php } else { ?>
                        <p>Click below to add a new API Token for <?= $row['username']; ?>.</p>
                        <button type="button" id="token" class="btn btn-success margin-bottom-05"
                                onclick="location.href = '/admin/account?token&uid=<?= $user; ?>'">
                            <i class="fas fa-lock" aria-hidden="true"></i> Add API Token
                        </button>
                    <?php } ?>
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
        <script>
            function confirmDelete(url) {
                if (confirm("Are you sure you want to delete this user?")) {
                    window.open(url, "_self");
                }
            }
        </script>
    <?php }
        include(APP_BASE_PATH . '/inc/footer.php');
    }
}
