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
 * File: src/contact.php
 * Contact site owner
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

if (isset($_GET['do'])) {

    // Check for google recaptcha
    if ($config->google->recaptcha->enabled === true && !isset($_SESSION['authenticated'])) {
        // Check that Google captcha is correct
        $captcha = $_POST['g-recaptcha-response'];
        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $config->google->recaptcha->secret . "&response=" . $captcha),
            true);
    } // Recaptcha not enabled, set response to true by default
    else {
        $response['success'] = true;
    }

    // Captcha Success, process the contact form
    if ($response['success'] === true) {

        require(APP_BASE_PATH . '/fcn/mailer.php');

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['messages'] = '<div class="alert alert-danger text-center"><a href="#" class="close" data-dismiss="alert">&times;</a>Error: ' . $email . ' is not a valid email</div>';
            header("Location: /contact");
            exit();
        }
        if ($_POST['captcha']) {
            $_SESSION['messages'] = '<div class="alert alert-warning text-center"></a>Success: Message Sent</div>';
            header("Location: /");
            exit();
        }
        $post_subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
        $subject = 'Contact Form Submission - ' . $post_subject;
        $message = htmlentities(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

        $message = '<p><strong>You have received a new message from:</strong> <a href="mailto:' . $email . '?subject=' . $post_subject . '">' . $name . ' &lt;' . $email . '&gt;</a></p><p>' . $message . '</p>';

        $sql = mysqli_query($conn, "SELECT `email` FROM `users` WHERE `admin` = '1'");
        while ($row = mysqli_fetch_assoc($sql)) {
            $admin_email[] = $row['email'];
        }

        // Mail it
        /** @var array $admin_email Admins email addresses */
        foreach ($admin_email as $to) {
            mailer($to, $subject, $message, $email, $name, false);
        }
        // Log it
        syslog(LOG_INFO, "(SYSTEM){CONTACT}: Mail sent to admin successfully");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Your message has been sent successfully.</div>';
        header("Location: /");
        exit();
    }
} else {
    if ($config->contact->enabled === true) {
        // Get Header
        $pageTitle = 'Contact Owner';
        include(APP_BASE_PATH . '/inc/header.php');
        ?>

        <div class="row">
            <div class="col">
                <h1 class="page-header">Contact Station Owner</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-8 mx-auto contact">
                <form name="message" id="recaptcha-form" action="/contact?do" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name:</label>
                        <input type="text"
                               class="form-control" <?= (isset($_SESSION['authenticated'])) ? 'value="' . $_SESSION['username'] . '"' : false; ?>
                               name="name" id="name" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" <?php if (isset($_SESSION['authenticated'])) {
                            $uid = $_SESSION['uid'];
                            $email = mysqli_fetch_assoc(mysqli_query($conn,
                                "SELECT `email` FROM `users` WHERE `uid`='$uid'"));
                            echo 'value="', $email['email'], '"';
                        } ?> class="form-control" name="email" id="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea rows="10" cols="100" class="form-control" name="message" id="message"
                                  required></textarea>
                    </div>
                    <div class="form-group">
                        <label hidden for="subject">Captcha:</label>
                        <input type="text" class="form-control" name="captcha" id="captcha" placeholder="captcha"
                               hidden>
                    </div>
                    <?php
                    if ($config->google->recaptcha->enabled === true && !isset($_SESSION['authenticated'])) { ?>
                        <button type="submit" class="margin-top-05 btn btn-outline-success g-recaptcha"
                                data-sitekey="<?= $config->google->recaptcha->sitekey; ?>" data-callback="onSubmit"><i
                                    class="fas fa-paper-plane"></i> Send Message
                        </button>
                        <?php
                    } else { ?>
                        <button type="submit" class="margin-top-05 btn btn-lg btn-success btn-block"><i
                                    class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    <?php } ?>
                </form>
            </div>

        </div>
        <?php

        // Get footer
        include(APP_BASE_PATH . '/inc/footer.php');
    } // Contact not enabled, go home.
    else {
        header("Location: /");
    }
}
