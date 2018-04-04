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
                <p>This script will attempt to change the server where your Access sends updates.<br>
                    This is accomplished by sending a POST request from your browser to the IP address of your Access.</p>
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
                        <p class="margin-top-05"><strong>Default: </strong><pre>atlasapi.myacurite.com</pre></p>
                    </div>
                    <button type="submit" class="btn btn-primary center-block"><i
                                class="fa fa-power-off"></i> Reboot
                    </button>
                </form>
                <p class="margin-top-05">When you submit the form, you'll be redirected to your Access where it will reboot.</p>
                <?php
                } else {
                ?>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <form name="access_server" id="access_server" action="" method="GET">
                            <div class="form-group">
                                <label>Access IP:</label>
                                <input type="text" class="form-control" name="ip" id="ip" placeholder="Access IP">
                            </div>
                            <button type="submit" class="btn btn-primary center-block"><i
                                        class="fa fa-arrow-circle-o-right"></i>
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
}
else {
    header("Location: /");
}