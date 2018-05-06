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
 * File: src/pub/recover.php
 * Recover user password
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

if (!isset($_SESSION['UserLoggedIn'])) {

    require(APP_BASE_PATH . '/fcn/mailer.php');

// Get Header
    $page_title = 'Recover Account Password | ' . $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');

// If we are submitting a recovery request
    if (isset($_GET['start'])) {

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

        // Success
        if ($response['success'] === true) {

            $email = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

            $result = mysqli_query($conn, "SELECT `uid` FROM `users` WHERE `email`='$email'");

            // If there are no results
            if (mysqli_num_rows($result) === 0) {
                // Display success message anyway, because security?
                echo '<div class="row"><div class="col-lg-12"><div class="alert alert-success">If that email exists in our database, you will receive an email with instructions to reset your password.</div></div></div>';
            } // If there is a member with that email
            else {
                // Get uid
                $uid = mysqli_fetch_array($result);
                $uid = $uid['uid'];

                // Check if a request was already submitted
                $result = mysqli_query($conn, "SELECT `hash` FROM `password_recover` WHERE `uid`='$uid'");

                // If a request was already made
                if (mysqli_num_rows($result) === 1) {
                    $hash = mysqli_fetch_array($result);
                    $hash = $hash['hash'];

                    $subject = 'Password Change Request';
                    $message = '<h2>Reset Account Password</h2><p>You or someone pretending to be you requested to reset your password for ' . $config->site->name .
                        '.</p><p><b>To finish resetting your password. Please visit the link below.</b><br><a href="http://' . $config->site->hostname . '/recover?do&hash=' . $hash . '">Reset Password</a></p>';

                    // Mail it
                    mailer($email, $subject, $message);
                    // Log it
                    syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid received");
                    // Display message
                    echo '<div class="row"><div class="col-lg-12"><div class="alert alert-success">If that email exists in our database, you will receive an email with instructions to reset your password.</div></div></div>';
                } //Otherwise
                else {
                    $uuid = uniqid();
                    $hash = md5($uuid);
                    mysqli_query($conn, "INSERT INTO `password_recover` (`uid`, `hash`) VALUES ('$uid', '$hash')");

                    // If the insert Query was successful.
                    if (mysqli_affected_rows($conn) === 1) {
                        $subject = 'Password Change Request';
                        $message = '<h2>Reset Account Password</h2><p>You or someone pretending to be you requested to reset your password for ' . $config->site->name .
                            '.</p><p><b>To finish resetting your password. Please visit the link below.</b><br><a href="http://' . $config->site->hostname . '/recover?do&hash=' . $hash . '">Reset Password</a></p>';

                        // Mail it
                        mailer($email, $subject, $message);
                        // Log it
                        syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid received");
                        // Display message
                        echo '<div class="row"><div class="col-lg-12"><div class="alert alert-success">If that email exists in our database, you will receive an email with instructions to reset your password.</div></div></div>';
                    } // Something went wrong
                    else {
                        // Log it
                        syslog(LOG_ERR, "(SYSTEM)[INFO]: Failed to save password reset request for UID $uid");
                        //Display message
                        echo '<div class="row"><div class="col-lg-12"><div class="alert alert-danger">Something went wrong while completing your request. Please try again.</div></div></div>';
                    }
                }
            }
        }
    }// Display the password change form
    elseif (isset($_GET['do'])) {
        //Get the User ID we're working on
        $hash = mysqli_real_escape_string($conn, filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING));

        // Count the rows returned
        $count = mysqli_num_rows(mysqli_query($conn, "SELECT `uid` FROM `password_recover` WHERE `hash`='$hash'"));
        // If there are no results
        if ($count === 1) {
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Recover Password</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4">
                    <div id="change_password_form">
                        <p>Enter your new password below:</p>
                        <form class="form" role="form" action="recover?password" method="POST">
                            <div class="form-group">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" class="form-control" name="pass" id="pass" placeholder="Password"
                                       maxlength="32" required>
                            </div>
                            <input type="hidden" name="hash" value="<?= $hash; ?>">
                            <button type="submit" id="submit" value="submit" class="btn btn-primary"><i
                                        class="fas fa-key" aria-hidden="true"></i> Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            // Done with the password form
        } // That hash does not exist
        else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM)[ERROR]:Invalid hash received. $hash");
            $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Your request cannot be processed. Try submitting your password reset request again.</div>';
            header("Location: /");
        }
// Done with resetting password
    } // Process Password Change
    elseif (isset($_GET['password'])) {
// Get id
        $hash = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_STRING));

        // Get user id
        $uid = mysqli_fetch_array(mysqli_query($conn, "SELECT `uid` FROM `password_recover` WHERE `hash`='$hash'"));
        $uid = $uid['uid'];

        // Get user details
        $userRow = mysqli_fetch_array(mysqli_query($conn,
            "SELECT `username`, `email`, `password` FROM `users` WHERE `uid`='$uid'"));
        $email = $userRow['email'];
        $username = $userRow['username'];

        // Get requested password
        $password = password_hash(mysqli_real_escape_string($conn,
            filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING)), PASSWORD_DEFAULT);

        // Check and see if the password actually changed
        if (password_verify(mysqli_real_escape_string($conn,
            filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING)), $userRow['password'])) {
            mysqli_query($conn, "DELETE FROM `password_recover` WHERE `uid` = '$uid'");
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid failed");
            $_SESSION['messages'] = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a>Seems like you entered your current password. Try logging in with that password.</div>';
            header("Location: /admin/account");
            die();

        } else {
            mysqli_query($conn, "UPDATE `users` SET `password`='$password' WHERE `uid`='$uid'");

            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {

                // Remove password recovery entries
                mysqli_query($conn, "DELETE FROM `password_recover` WHERE `uid` = '$uid'");

                $subject = 'Password Change Successful';
                $message = '<h2>Password Changed Successfully!</h2><p>You can now use it when logging in.</p><p>Your username is: <strong>' . $username . '</strong></p>';

                // Mail it
                mailer($email, $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Password change request for UID $uid processed successfully");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Password Updated Successfully!</div>';
                header("Location: /admin/account");

            } else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Password change request for UID $uid failed");
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Something went wrong while completing your request. Please try again.</div>';
                header("Location: /recover&do?hash=$hash");
            }

        }

    } // Display the initial request form
    else {
        ?>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Recover Password</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4">
                <div id="recover_password_form">
                    <p>To reset your password, enter your email below:</p>
                    <form id="recaptcha-form" role="form" class="form-signin" action="recover?start" method="POST">
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <input type="email" class="form-control" name="email" id="email" maxlength="64"
                                   placeholder="Email" required>
                        </div>
                        <?php
                        if ($config->google->recaptcha->enabled === true) { ?>
                            <button class="margin-top-05 btn btn-lg btn-primary btn-block g-recaptcha"
                                    data-sitekey="<?= $config->google->recaptcha->sitekey; ?>" data-callback="onSubmit">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Submit
                            </button>
                            <?php
                        } else { ?>
                            <button class="margin-top-05 btn btn-lg btn-primary btn-block" type="submit"><i
                                        class="fas fa-sign-in-alt" aria-hidden="true"></i> Submit
                            </button>
                            <?php
                        } ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

// Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} // User logged in, maybe they want to change password?
else {
    header("Location: /admin/account?password");
}
