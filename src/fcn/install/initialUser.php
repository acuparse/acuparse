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
 * File: src/fcn/install/initialUser.php
 * Add the initial user
 */

/** @var mysqli $conn Global MYSQL Connection */

$sql = "SHOW TABLES FROM `acuparse`;";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "DB Error, could not list tables\n";
    echo 'MySQL Error: ' . mysqli_error($conn);
    exit;
} else {
    // Check to ensure there are no other accounts
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `users`")) === 0) {
        $pageTitle = 'Acuparse Installer | Create First User';
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
                                   placeholder="Password" maxlength="32" required onkeyup='verifyPassword();'>
                        </div>
                        <div class="form-group">
                            <label for="password2">Verify Password:</label>
                            <input type="password" class="form-control" name="password2" id="pass2"
                                   placeholder="Password" maxlength="32" required onkeyup='verifyPassword();'>
                        </div>
                        <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                    class="fas fa-save" aria-hidden="true"></i> Save
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <script>
            function verifyPassword() {
                const pass = document.getElementById('pass');
                const pass2 = document.getElementById('pass2');

                document.getElementById("submit").disabled = pass.value.length === 0 ||
                    pass.value !== pass2.value;
            }

            verifyPassword();
        </script>
        <?php
        // Get app footer
        include(APP_BASE_PATH . '/inc/footer.php');
    } // There is already an account in the DB
    else {
        // Bailout
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
        header("Location: /");
        exit(syslog(LOG_WARNING, "(SYSTEM){INSTALLER}[WARNING]: ATTEMPTED TO ADD ADMIN WHEN ONE EXISTS"));
    }
}
