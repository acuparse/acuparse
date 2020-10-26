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
 * File: src/pub/admin/access.php
 * Sends a POST request to modify the update server in an Access
 */

require(dirname(dirname(__DIR__)) . '/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 */

// Logged in admin
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {

    $pageTitle = 'Change Access Server';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>

    <div class="row">
        <div class="col">
            <h2 class="page-header">Change Access Update Server</h2>
        </div>
    </div>
    <hr>
    <div id="change-access-server" class="row change-access-server">
        <div class="col-md-8 col-12 mx-auto">
            <?php
            if (isset($_GET['ip'])) {
                $ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
                ?>

                <form name="access-server" id="access-server" action="http://<?= $ip; ?>/config.cgi" method="POST">
                    <div class="form-group">
                        <label for="server-hostname">New Server Hostname:</label>
                        <input type="text" class="form-control" name="ser" id="server-hostname"
                               placeholder="atlasapi.myacurite.com" value="<?= $config->site->hostname; ?>"
                               required>
                        <p class="alert alert-info margin-top-05">Default: <code>atlasapi.myacurite.com</code></p>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-power-off"></i> Reboot</button>
                </form>
                <p class="margin-top-05">When you submit this form, you'll be redirected to your Access. It will then
                    reboot with the new settings.</p>
                <?php
            } else {
                ?>
                <div class="row">
                    <div class="col">
                        <p>This script sets the hostname where your Access sends it's data; via a POST request from your
                            browser to your Access.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="alert alert-warning mx-auto">
                        <p><strong>Warning: </strong>Use a browser located on the same network as your Access!</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 col-12 mx-auto">
                        <form name="access-server" id="access-server" action="/admin/access" method="GET">
                            <div class="form-group">
                                <label for="access-ip">Access IP:</label>
                                <input type="text" class="form-control" name="ip" id="access-ip"
                                       placeholder="192.168.0.10">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="far fa-arrow-alt-circle-right"></i>
                                Next
                            </button>
                        </form>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in, go home
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
