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
 * File: src/admin/install/index.php
 * Install/Update Script
 */

require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');

// Process an update
if (isset($_GET['update']) && $installed === true) {
    if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {

        // Logged in, process update
        if (isset($_GET['do'])) {
            set_time_limit(0);
            $notes = '';
            $updatePattern = dirname(dirname(dirname(__DIR__))) . '/fcn/updater/*/*.php';
            foreach (glob($updatePattern) as $filename) {
                include $filename;
            }

            // Save the users config file
            $export = var_export($config, true);
            $export = str_ireplace('stdClass::__set_state', '(object)', $export);
            $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');
            $pageTitle = 'Acuparse Setup';
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <section id="update-system">
                <div class="row">
                    <div class="col">
                        <h2 class="page-header">Update Complete</h2>
                        <div class="alert alert-warning text-center">
                            <p><strong>Double check your config settings before proceeding!</strong></p>
                        </div>
                        <div><h3>Notes:</h3>
                            <ul class="list-unstyled"><?= $notes; ?></ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block"
                                onclick="location.href = '/admin/settings'">
                            <i class="fas fa-cogs" aria-hidden="true"></i> Edit Settings
                        </button>
                    </div>
                </div>
            </section>
            <?php
            // Get app footer
            include(APP_BASE_PATH . '/inc/footer.php');
        } else {
            $pageTitle = 'Acuparse Setup';
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <section id="update-system">
                <div class="row">
                    <div class="col">
                        <h2 class="page-header">Are you sure you want to proceed?</h2>
                        <div class="alert alert-danger text-center">
                            <p><strong>Make sure you backup your database, config file, and webcam images before
                                    proceeding!</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button type="submit" id="submit" value="submit" class="btn btn-success btn-block"
                            onclick="location.href = '/admin/install?update&do'"><i
                                class="fas fa-wrench"
                                aria-hidden="true"></i>
                        Process Upgrade
                    </button>
                </div>
            </section>
            <?php
            // Get app footer
            include(APP_BASE_PATH . '/inc/footer.php');
        }
    } // Not logged in
    else {
        // Log it
        syslog(LOG_WARNING, "(SYSTEM)[WARNING]: UNAUTHORIZED UPDATE ATTEMPT");

        // Bailout
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
        header("Location: /");
    }
    die();
} // Create initial administrator account
elseif (isset($_GET['account']) && $installed === true) {
    // Process the new account
    if (isset($_GET['do'])) {

        // Check to ensure there are no other accounts
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) === 0) {
            $username = mysqli_real_escape_string($conn,
                strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)));
            $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
            $email = mysqli_real_escape_string($conn,
                strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
            $result = mysqli_query($conn,
                "INSERT INTO `users` (`username`, `password`, `email`, `admin`) VALUES ('$username', '$password', '$email', '1')");
            if (!$result) {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding first user failed: " . mysqli_error($conn));
            }

            // If adding the account was successful
            if (mysqli_affected_rows($conn) === 1) {

                // Mail it
                require(APP_BASE_PATH . '/fcn/mailer.php');
                $subject = 'Admin Account Created';
                $message = '<h2>Admin Account Created Successfully!</h2><p>Your admin account has been added successfully. You can now sign in.</p>';
                mailer($email, $subject, $message);
                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: First account for $username added successfully");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Added Successfully!</div>';

                // Let's remember the user is logged in
                $_SESSION['authenticated'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['uid'] = (int)mysqli_insert_id($conn);
                $_SESSION['admin'] = true;
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
                    syslog(LOG_ERR, "(SYSTEM)[ERROR]: Saving session failed: " . mysqli_error($conn));
                }

                // Send the session cookie
                setcookie('device', $deviceKey, time() + 60 * 60 * 24 * 30, '/');
                setcookie('token', $tokenHash, time() + 60 * 60 * 24 * 30, '/');

                // Log it
                syslog(LOG_INFO, "(SYSTEM)[INFO]: $username logged in successfully");

                // Redirect user after successful authentication
                header("Location: /admin/settings");
                die();
            } // Something went wrong ...
            else {
                // Log it
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding first admin $username failed");
                // Display message
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong!</div>';
                header("Location: /admin");
                die();
            }
        } // Woah, there is already an account in the DB
        else {
            // Log it
            syslog(LOG_WARNING, "(SYSTEM)[WARNING]: ATTEMPTED TO ADD ADMIN WHEN ONE EXISTS");

            // Bailout
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            header("Location: /");
            die();
        }
    } // Show the initial user form
    else {
        // Check to ensure there are no other accounts
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) === 0) {
            $pageTitle = 'Create First User';
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <section id="add-user" class="add-user">
                <div class="row">
                    <div class="col">
                        <h2 class="page-header">Add Administrator Account</h2>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-8 col-12 mx-auto">
                        <form class="form" role="form" action="?account&do" method="POST">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" name="username" id="username"
                                       placeholder="Username" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                                       maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" name="password" id="pass"
                                       placeholder="Password" maxlength="32" required>
                            </div>
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save
                            </button>
                        </form>
                    </div>
                </div>
            </section>
            <?php
            // Get app footer
            include(APP_BASE_PATH . '/inc/footer.php');
        } // Woah, there is already an account in the DB
        else {
            // Log it
            syslog(LOG_WARNING, "(SYSTEM)[WARNING]: ATTEMPTED TO ADD ADMIN WHEN ONE EXISTS");

            // Bailout
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            header("Location: /");
            die();
        }
    }
} // Configure the database connection
elseif (isset($_GET['database']) && $installed === false) {

    // Do some input filtering
    $raw = $_POST;
    array_walk_recursive($raw, function (&$i) {
        $i = filter_var(trim($i), FILTER_SANITIZE_STRING);
    });
    $_POST = $raw;

    // Configure the database details
    $config->mysql->host = $_POST['mysql']['host'];
    $config->mysql->database = $_POST['mysql']['database'];
    $config->mysql->username = $_POST['mysql']['username'];
    $config->mysql->password = $_POST['mysql']['password'];
    $config->mysql->trim = (int)$_POST['mysql']['trim'];

    // Save the users config file
    $export = var_export($config, true);
    $export = str_ireplace('stdClass::__set_state', '(object)', $export);
    $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');
    if ($save !== false) {
        $sqlPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql';
        // Load the database with the default schema
        $schema = $sqlPath . '/master.sql';
        $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
        $schema = shell_exec($schema);

        // Check and adjust database trim level
        if ($config->mysql->trim === 1) {
            // Load the database with the trim schema
            $schema = $sqlPath . '/trim/enable.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Trim All Enabled");
        } elseif ($config->mysql->trim === 2) {
            // Load the database with the trim schema
            $schema = $sqlPath . '/trim/enable_xtower.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Trim All except towers Enabled");
        }

        // Log it
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Database configuration saved successfully");
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Database Configuration saved successfully!</div>';
        header("Location: /admin/install/?account");
        die();
    } else {
        // Log it
        syslog(LOG_INFO, "(SYSTEM)[INFO]: Database configuration failed");
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving Database Configuration failed!</div>';
        header("Location: /admin/install");
        die();
    }

}  // New Install, setup site config
elseif ($installed === false) {
    $pageTitle = 'Acuparse Setup';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="config-database" class="config-database">
        <div class="row">
            <div class="col">
                <h2 class="page-header">Initial Database Settings</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <form class="form" role="form" action="?database" method="POST">
                    <div class="form-row">
                        <label class="col-form-label" for="mysql-host">Hostname:</label>
                        <div class="col form-group">
                            <input type="text" class="form-control"
                                   name="mysql[host]"
                                   id="mysql-host"
                                   value="localhost"
                                   maxlength="35">
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-form-label" for="mysql-database">Database:</label>
                        <div class="col form-group">
                            <input type="text" class="form-control"
                                   name="mysql[database]"
                                   id="mysql-database"
                                   value="acuparse"
                                   maxlength="35">
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-form-label" for="mysql-username">Username:</label>
                        <div class="col form-group">
                            <input type="text" class="form-control"
                                   name="mysql[username]"
                                   id="mysql-username"
                                   value="acuparse"
                                   maxlength="35">
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-form-label" for="mysql-password">Password:</label>
                        <div class="col form-group">
                            <input type="text" class="form-control"
                                   name="mysql[password]"
                                   id="mysql-password"
                                   placeholder="Password"
                                   maxlength="32">
                        </div>
                    </div>
                    <div class="form-group">
                        <p><strong>Database Trimming?</strong></p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="mysql[trim]"
                                   id="mysql-trim-enabled-0" value="0">
                            <label class="form-check-label alert-danger"
                                   for="mysql-trim-enabled-0">Disabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="mysql[trim]"
                                   id="mysql-trim-enabled-1" value="1" checked="checked">
                            <label class="form-check-label alert-success"
                                   for="mysql-trim-enabled-1">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="mysql[trim]"
                                   id="mysql-trim-enabled-2" value="2">
                            <label class="form-check-label alert-warning"
                                   for="mysql-trim-enabled-2">Enabled, <strong>EXCEPT</strong>
                                Towers</label>
                        </div>
                    </div>
                    <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                class="fas fa-save" aria-hidden="true"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
    </section>
    <?php
    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
