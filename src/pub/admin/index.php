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
 * File: src/pub/admin/index.php
 * Site Administration Index
 */

require(dirname(dirname(__DIR__)) . '/inc/loader.php');

// Logged in admin
if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true && $_SESSION['IsAdmin'] === true) {

    $page_title = 'Admin Functions | ' . $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="admin_functions" class="admin_functions_display">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Admin Functions</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <h3>User Functions:</h3>
                <button type="button" id="add_user" class="btn btn-default center-block"
                        onclick="location.href = '/admin/account?add_user'"><i class="fa fa fa-user-plus"
                                                                               aria-hidden="true"></i> Add New User
                </button>
                <button type="button" id="view_users" class="btn btn-default center-block margin-top-10"
                        onclick="location.href = '/admin/account?view'"><i class="fa fa fa-list-alt"
                                                                           aria-hidden="true"></i> View/Edit Users
                </button>
            </div>
            <?php if ($config->station->towers === true) { ?>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <h3>Tower Sensors:</h3>
                    <button type="button" id="add_tower" class="btn btn-default center-block"
                            onclick="location.href = '/admin/tower?add'"><i class="fa fa fa-plus-square-o"
                                                                            aria-hidden="true"></i> Add New Tower
                    </button>
                    <button type="button" id="view_towers" class="btn btn-default center-block margin-top-10"
                            onclick="location.href = '/admin/tower?view'"><i class="fa fa fa-list-alt"
                                                                             aria-hidden="true"></i> View/Edit Towers
                    </button>
                </div>
            <?php } ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <h3>Admin Functions:</h3>
                <button type="button" id="add_user" class="btn btn-default center-block"
                        onclick="location.href = '/admin/settings'"><i class="fa fa fa-cogs"
                                                                       aria-hidden="true"></i> Modify Config
                </button>
                <h4>Access Tools:</h4>
                <button type="button" id="access_server" class="btn btn-default center-block margin-top-10"
                        onclick="location.href = '/admin/access_server'"><i class="fa fa fa-cogs"
                                                                            aria-hidden="true"></i> Change Upload Server
                </button>
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
