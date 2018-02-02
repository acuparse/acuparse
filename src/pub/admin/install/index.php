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
 * File: src/admin/install/index.php
 * Install/Update Script
 */

require(dirname(dirname(dirname(__DIR__))) . '/inc/loader.php');

// Process an upgrade
if (isset($_GET['update']) && $installed === true) {
    if (isset($_GET['do'])) {
        $notes = '';
        foreach (glob("scripts/*.php") as $filename)
        {
            include $filename;
        }
        // Save the users config file
        $export = var_export($config, true);
        $export = str_ireplace('stdClass::__set_state', '(object)', $export);
        $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');
        $page_title = 'Acuparse Setup';
        include(APP_BASE_PATH . '/inc/header.php');
        ?>
        <section id="Update System">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Update Complete</h2>
                    <div class="alert alert-warning">
                        <p><strong>Double check your config settings before proceeding!</strong></p>
                    </div>
                    <div><h3>Notes:</h3>
                        <ul class="list-unstyled"><?= $notes; ?></ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button type="button" class="btn btn-primary btn-block" onclick="location.href = '/admin/settings'"><i
                            class="fa fa-check" aria-hidden="true"></i> Edit Settings
                </button>
            </div>
        </section>
        <?php
        // Get app footer
        include(APP_BASE_PATH . '/inc/footer.php');
    } else {
        $page_title = 'Acuparse Setup';
        include(APP_BASE_PATH . '/inc/header.php');
        ?>
        <section id="Update System">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Are you sure you want to proceed?</h2>
                    <div class="alert alert-danger">
                        <p><strong>Make sure you backup your database, config file, and webcam images before
                                proceeding!</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button type="submit" id="submit" value="submit" class="btn btn-success btn-block"
                        onclick="location.href = '/admin/install?update&do'"><i
                            class="fa fa-check"
                            aria-hidden="true"></i>
                    Process Upgrade
                </button>
            </div>
        </section>
        <?php
        // Get app footer
        include(APP_BASE_PATH . '/inc/footer.php');
    }
} // Adding first user account
elseif (isset($_GET['add_admin']) && $installed === true) {
    // If this is the first user account
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
            syslog(LOG_ERR, "Adding first user failed: " . mysqli_error($conn));
        }

        // If adding the account was successful
        if (mysqli_affected_rows($conn) === 1) {

            // Mail it
            require(APP_BASE_PATH . '/fcn/mailer.php');
            $subject = 'Admin Account Created';
            $message = '<h2>Admin Account Created Successfully!</h2><p>Your admin account has been added successfully. You can now login.</p>';
            mailer($email, $subject, $message);
            // Log it
            syslog(LOG_INFO, "First account for $username added successfully");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Added Successfully!</div>';

            // Let's remember the user is logged in
            $_SESSION['UserLoggedIn'] = true;
            $_SESSION['Username'] = $username;
            $_SESSION['UserID'] = (int)mysqli_insert_id($conn);
            $_SESSION['IsAdmin'] = true;
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
                syslog(LOG_ERR, "Saving session failed: " . mysqli_error($conn));
            }

            // Send the session cookie
            setcookie('device_key', $device_key, time() + 60 * 60 * 24 * 30, '/');
            setcookie('token', md5($token), time() + 60 * 60 * 24 * 30, '/');

            // Log
            syslog(LOG_INFO, "$username logged in successfully");

            // Redirect user after successful login
            header("Location: /admin/settings");
        } // Something went wrong ...
        else {
            // Log it
            syslog(LOG_ERR, "Adding first admin $username failed");
            // Display message
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong!</div>';
            header("Location: /admin");
        }
    }
} elseif (isset($_GET['add_user']) && $installed === true) {
    // Check to see if the admin account is added. If not, create it.
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) === 0) {
        $page_title = 'Create First User | ' . $config->site->name;
        include(APP_BASE_PATH . '/inc/header.php');
        ?>
        <section id="add_user" class="add_user_display">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Creating First User</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4">
                    <div id="add_admin_user">
                        <p>Enter the admin user details:</p>
                        <form class="form" role="form" action="?add_admin" method="POST">
                            <div class="form-group">
                                <input type="text" class="form-control" name="username" id="username"
                                       placeholder="Username" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                                       maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" id="pass"
                                       placeholder="Password" maxlength="32" required>
                            </div>
                            <button type="submit" id="submit" value="submit" class="btn btn-primary"><i
                                        class="fa fa-check" aria-hidden="true"></i> Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <?php
        // Get app footer
        include(APP_BASE_PATH . '/inc/footer.php');
    }
} // Configure the database connection
elseif (isset($_GET['config_db']) && $installed === false) {

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
        $sql_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/sql';
        // Load the database with the default schema
        $schema = $sql_path . '/master.sql';
        $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
        $schema = shell_exec($schema);

        // Check and adjust database trim level
        if ($config->mysql->trim === 1) {
            // Load the database with the trim schema
            $schema = $sql_path . '/trim/enable.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "Trim All Enabled");
        } elseif ($config->mysql->trim === 2) {
            // Load the database with the trim schema
            $schema = $sql_path . '/trim/enable_xtower.sql';
            $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
            $schema = shell_exec($schema);
            syslog(LOG_INFO, "Trim All except towers Enabled");
        }

        // Log it
        syslog(LOG_INFO, "Database configuration saved successfully");
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Database Configuration saved successfully!</div>';
        header("Location: /admin/install/?add_user");
    } else {
        // Log it
        syslog(LOG_INFO, "Database configuration failed");
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving Database Configuration failed!</div>';
        header("Location: /admin/install");
    }

}  // New Install
elseif ($installed === false) {
    $page_title = 'Acuparse Setup';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>

    <section id="create_database">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Database Connection Details:</h2>
            </div>
        </div>
        <form class="form" role="form" action="?config_db" method="POST">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group row margin-bottom-05">
                        <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_host">Hostname:</label>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <input type="text" class="form-control" name="mysql[host]" id="mysql_host"
                                   placeholder="MySQL Hostname" maxlength="32" value="localhost" required>
                        </div>
                    </div>
                    <div class="form-group row margin-bottom-05">
                        <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_database">Database:</label>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <input type="text" class="form-control" name="mysql[database]" id="mysql_database"
                                   placeholder="MySQL Database" value="acuparse" maxlength="32" required>
                        </div>
                    </div>
                    <div class="form-group row margin-bottom-05">
                        <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_username">Username:</label>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <input type="text" class="form-control" name="mysql[username]" id="mysql_username"
                                   placeholder="MySQL Username" value="acuparse" maxlength="32" required>
                        </div>
                    </div>
                    <div class="form-group row margin-bottom-05">
                        <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_password">Password:</label>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <input type="text" class="form-control" name="mysql[password]" id="mysql_password"
                                   placeholder="MySQL Password" maxlength="32" required>
                        </div>
                    </div>
                    <div class="form-group row margin-bottom-05">
                        <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_trim">Database Trimming:</label>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <label class="radio-inline bg-danger"><input type="radio" id="mysql_trim"
                                                                         name="mysql[trim]"
                                                                         value="0" <?php if ($config->mysql->trim === 0) {
                                    echo 'checked="checked"';
                                } ?>>Disabled</label>
                            <label class="radio-inline bg-success"><input type="radio" id="mysql_trim"
                                                                          name="mysql[trim]"
                                                                          value="1" <?php if ($config->mysql->trim === 1) {
                                    echo 'checked="checked"';
                                } ?>>Trim All</label>
                            <label class="radio-inline bg-success"><input type="radio" id="mysql_trim"
                                                                          name="mysql[trim]"
                                                                          value="2" <?php if ($config->mysql->trim === 2) {
                                    echo 'checked="checked"';
                                } ?>>Trim all but towers</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4 col-md-4  col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-4 col-xs-offset-4">
                    <button type="submit" id="submit" value="submit" class="btn btn-primary btn-block"><i
                                class="fa fa-check"
                                aria-hidden="true"></i>
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </section>
    <?php
    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} else {
    header("Location: /");
}
