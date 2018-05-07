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
 * File: src/pub/admin/access_server.php
 * Sends a POST request to modify the update server in an Access
 */

require(dirname(dirname(__DIR__)) . '/inc/loader.php');

// Logged in admin
if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true && $_SESSION['IsAdmin'] === true) {

    $page_title = 'Change Access Server | ' . $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="change_access_server">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Change Access Update Server</h2>
            </div>
        </div>
        <?php
        if (isset($_GET['ip'])) {
        $ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
        ?>
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4">
                <form name="access_server" id="access_server"
                      action="<?php echo 'http://' . $ip . '/config.cgi'; ?>" method="POST">
                    <div class="form-group">
                        <label>New Server Hostname:</label>
                        <input type="text" class="form-control" name="ser" id="ser"
                               placeholder="New Hostname" value="<?= $config->site->hostname; ?>"
                               required>
                        <p class="margin-top-05"><strong>Default: </strong>
                        <pre>atlasapi.myacurite.com</pre>
                        </p>
                    </div>
                    <button type="submit" class="btn btn-primary center-block"><i
                                class="fas fa-power-off"></i> Reboot
                    </button>
                </form>
                <p class="margin-top-05">When you submit this form, you'll be redirected to your Access. It will then
                    reboot with the new settings.</p>
                <?php
                } else {
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <p>This script sets the hostname where your Access sends it's data; via a POST request from your
                            browser to your Access.</p>
                    </div>
                    <div class="alert alert-warning col-lg-6 col-lg-offset-3">
                        <p><strong>Warning: </strong>Use a browser located on the same network as your Access!</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <form name="access_server" id="access_server" action="" method="GET">
                            <div class="form-group">
                                <label>Access IP:</label>
                                <input type="text" class="form-control" name="ip" id="ip" placeholder="Access IP">
                            </div>
                            <button type="submit" class="btn btn-primary center-block"><i
                                        class="far fa-arrow-alt-circle-right"></i>
                                Next
                            </button>

                        </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
    </section>
    <?php
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in, go home
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
