<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: src/pub/admin/index.php
 * Site Administration Index
 */

require(dirname(__DIR__, 2) . '/inc/loader.php');

/**
 * @var object $config Global Config
 */

// Logged in admin
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {

    $pageTitle = 'Admin Functions';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="admin-functions" class="admin-functions">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Administrator Functions</h1>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="<?= ($config->station->towers === true) ? 'col-md-4 col-12' : 'col-md-6 col-12' ?>">
                <h3>Users</h3>
                <div class="d-grid gap-2">
                    <button id="user-add" class="btn btn-outline-secondary" type="button"
                            onclick="location.href = '/admin/account?add'" data-instant><i class="fas fa-user-plus"
                                                                                           aria-hidden="true"></i> Add
                        New User
                    </button>
                    <button id="user-edit" class="btn btn-outline-secondary" type="button"
                            onclick="location.href = '/admin/account?view'" data-instant><i class="far fa-list-alt"
                                                                                            aria-hidden="true"></i>
                        Manage Users
                    </button>
                </div>
            </div>
            <?php if ($config->station->towers === true) { ?>
                <div class="col-md-4 col-12">
                    <h3>Tower Sensors</h3>
                    <div class="d-grid gap-2">
                        <button id="tower-add" class="btn btn-outline-secondary" type="button"
                                onclick="location.href = '/admin/tower?add'" data-instant><i class="far fa-plus-square"
                                                                                             aria-hidden="true"></i> Add
                            New Tower
                        </button>
                        <button id="tower-edit" class="btn btn-outline-secondary" type="button"
                                onclick="location.href = '/admin/tower?view'" data-instant><i class="far fa-list-alt"
                                                                                              aria-hidden="true"></i>
                            View/Edit Towers
                        </button>
                    </div>
                </div>
            <?php } ?>
            <div class="<?= ($config->station->towers === true) ? 'col-md-4 col-12' : 'col-md-6 col-12' ?>">
                <h3>Configuration</h3>
                <div class="d-grid gap-2">
                    <button type="button" id="system-settings" class="btn btn-outline-secondary"
                            onclick="location.href = '/admin/settings'"><i class="fas fa-cogs"
                                                                           aria-hidden="true"></i> System Settings
                    </button>
                    <button type="button" id="test-email" class="btn btn-outline-secondary"
                            onclick="location.href = '/admin/mailer'"><i class="fas fa-envelope-open-text"
                                                                         aria-hidden="true"></i> Send Test Email
                    </button>
                </div>
                <h4 class="mt-3">Sensors</h4>
                <div class="d-grid gap-2">
                    <button type="button" id="sensor-status" class="btn btn-outline-secondary"
                            onclick="location.href = '/admin/status'"><i class="fas fa-question-circle"
                                                                         aria-hidden="true"></i> Sensor Status
                    </button>
                </div>
                <h4 class="mt-3">Access Tools</h4>
                <div class="d-grid gap-2">
                    <button type="button" id="access-server" class="btn btn-outline-secondary"
                            onclick="location.href = '/admin/access'"><i class="fas fa-server"
                                                                         aria-hidden="true"></i> Upload Server
                    </button>
                </div>
            </div>
        </div>
    </section>
    <?php
    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in or user is not an admin
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
