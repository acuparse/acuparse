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
 * File: src/fcn/account/view.php
 * View a User
 */

$pageTitle = 'View User Accounts';
include(APP_BASE_PATH . '/inc/header.php');

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
?>
    <section id="view-users" class="view-users">
        <div class="row">
            <div class="col">
                <h1 class="page-header">View Users</h1>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <table class="table <?= ($config->site->theme === 'twilight') ? 'table-dark' : 'table-light'; ?> table-hover table-responsive-sm">
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
                                <a href="/admin/account?edit&uid=<?= $row['uid']; ?>"><?= $row['email']; ?></a>
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
        </div>
    </section>
<?php
// Get app footer
include(APP_BASE_PATH . '/inc/footer.php');
