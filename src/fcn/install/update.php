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
 * File: src/fcn/install/update.php
 * Update Script
 */

/**
 * @return array
 * @return array
 * @var object $config Global Config
 * @var object $appInfo Application Info
 */

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {

    // Process Upgrade
    if (isset($_GET['do'])) {
        $notes = '';
        if (version_compare($config->version->app, '2.10.0-release', '>=')) {
            $updatePattern = dirname(dirname(__DIR__)) . '/fcn/updater/3_x/*.php';
        } else {
            $updatePattern = dirname(dirname(__DIR__)) . '/fcn/updater/*/*.php';
        }
        set_time_limit(0);
        foreach (glob($updatePattern) as $filename) {
            include $filename;
        }

        // Save the users config file
        $export = var_export($config, true);
        $export = str_ireplace('stdClass::__set_state', '(object)', $export);
        $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');

        if ($save) {
            $updateComplete = true;
            if ($config->site->updates === true) {
                require(APP_BASE_PATH . '/fcn/cron/checkUpdates.php');
            }

            // Rebuild the event scheduler
            require(APP_BASE_PATH . '/fcn/trim.php');

        $pageTitle = 'Update Complete';
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
            $pageTitle = 'Update Failed!';
            include(APP_BASE_PATH . '/inc/header.php');
            ?>
            <section id="update-system">
                <div class="row">
                    <div class="col">
                        <h2 class="page-header">Update Failed</h2>
                        <div class="alert alert-danger text-center">
                            <p><strong>Errors were encountered during the update.</strong></p>
                        </div>
                    </div>
                </div>
            </section>
            <?php
        }
    } else {
        $pageTitle = 'Ready to Update';
        include(APP_BASE_PATH . '/inc/header.php');
        ?>
        <section id="update-system">
            <div class="row">
                <div class="col">
                    <h2 class="page-header">Are you sure you want to proceed?</h2>
                    <?php if (version_compare($config->version->app, '2.10.0-release', '==')) { ?>
                        <div class="alert alert-dark text-center">
                            <h3 class="font-weight-bolder">Updating to Version 3</h3>
                            <p><strong>Review the <a class="btn btn-warning btn-lg"
                                                     href="https://docs.acuparse.com/updates/v3">Update
                                        Guide</a> before proceeding!</strong></p>
                        </div>
                    <?php } ?>
                    <div class="alert alert-warning text-center">
                        <p><strong>Backup your database, config file, and webcam images before proceeding!</strong></p>
                    </div>
                    <div class="alert alert-danger text-center">
                        <h4 class="font-weight-bolder">DO NOT NAVIGATE AWAY FROM THIS PAGE!</h4>
                        <p>Updating can take a while! This page will automatically refresh when the update is
                            complete.</p>
                    </div>
                </div>
            </div>
            <div class="col text-center">
                <button type="submit" id="submit" value="submit" class="btn btn-danger"
                        onclick="location.href = '/admin/install?update&do'"><i
                            class="fas fa-play-circle"
                            aria-hidden="true"></i>
                    <?= (version_compare($config->version->app, '2.10.0-release', '==')) ? 'I\'ve reviewed the update guide!<br>Begin Update' : 'Begin Update'; ?>
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
    syslog(LOG_WARNING, "(SYSTEM){UPDATER}[WARNING]: UNAUTHORIZED UPDATE ATTEMPT");

    // Bailout
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
exit();
