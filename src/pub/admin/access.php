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
 * File: src/pub/admin/access.php
 * Sends a POST request to modify the update server in an Access
 */

require(dirname(__DIR__, 2) . '/inc/loader.php');

/**
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
        <div class="col-md-8 col-12 mx-auto alert alert-secondary">
            <?php
            if (isset($_GET['ip'])) {
                $ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
                ?>

                <form name="access-server" id="access-server" action="http://<?= $ip; ?>/config.cgi" method="POST">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <p class="alert alert-warning mb-3"><strong>Cannot be an IP address!</strong><br>Your
                                        Access must be able to resolve this hostname using your local DNS server.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <label class="col-form-label" for="server-hostname">Upload Hostname</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="ser" id="server-hostname"
                                           placeholder="atlasapi.myacurite.com" value="<?= $config->site->hostname; ?>"
                                           required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <p class="alert alert-info mt-3">Default: <code>atlasapi.myacurite.com</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <p class="mb-3">After clicking Reboot below, you'll be redirected to your Access. It will
                                reboot with your new settings.</p>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-power-off"></i> Reboot
                            </button>
                        </div>
                    </div>
                </form>
                <?php
            } else {
                ?>
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <p>This script sets the hostname where your Access sends it's data; via a POST request
                                    from
                                    your
                                    browser to your Access.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col alert alert-warning">
                                <p><strong>Notice: </strong>Use a browser located on the same network as your Access!
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <form name="access-server" id="access-server" action="/admin/access" method="GET">
                                    <div class="row">
                                        <div class="col-3">
                                            <label class="col-form-label" for="access-ip">Access IP</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="ip" id="access-ip"
                                                   placeholder="192.168.0.10">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary"><i
                                                        class="far fa-arrow-alt-circle-right"></i> Next
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col mx-auto alert alert-info">
                                <p><strong>Having Trouble?</strong><br/>See the <a
                                            href="https://docs.acuparse.com/TROUBLESHOOTING">Troubleshooting Guide</a>
                                    for
                                    more details.</p>
                            </div>
                        </div>
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
