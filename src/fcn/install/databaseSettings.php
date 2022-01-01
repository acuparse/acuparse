<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/fcn/install/databaseSettings.php
 * Initial database settings
 */

$pageTitle = 'Acuparse Setup';
include(APP_BASE_PATH . '/inc/header.php');
?>
    <section id="config-database" class="config-database">
        <div class="row">
            <div class="col">
                <h2 class="page-header">Initial Database Settings</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <?php
            $envDatabase = getenv('MYSQL_DATABASE');
            $envDatabaseHost = getenv('MYSQL_HOSTNAME');
            $envDatabaseUser = getenv('MYSQL_USER');
            $envDatabasePassword = getenv('MYSQL_PASSWORD');
            ?>
            <div class="col-md-8 col-12 mx-auto">
                <form class="form" role="form" action="?database" method="POST">
                    <div class="row alert alert-secondary">
                        <div class="col">
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="mysql-host">Hostname</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="mysql[host]"
                                           id="mysql-host"
                                           maxlength="35"
                                           value="<?= (!empty($envDatabaseHost)) ? $envDatabaseHost : "localhost" ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mysql-database">Database</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="mysql[database]"
                                           id="mysql-database"
                                           maxlength="35"
                                           value="<?= (!empty($envDatabase)) ? $envDatabase : "acuparse" ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mysql-username">Username</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="mysql[username]"
                                           id="mysql-username"
                                           maxlength="35"
                                           value="<?= (!empty($envDatabaseUser)) ? $envDatabaseUser : "acuparse" ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mysql-password">Password</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="mysql[password]"
                                           id="mysql-password"
                                           maxlength="32"
                                        <?= (!empty($envDatabasePassword)) ? 'value="' . $envDatabasePassword . '"' : "placeholder=\"Password\"" ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 alert alert-secondary">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <p><strong>Database Trimming</strong></p>
                                    <p><a href="https://docs.acuparse.com/INSTALL/#database-trimming">Database
                                            Trimming</a> keeps your database tables clean.</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="mysql[trim]"
                                               id="mysql-trim-enabled-0" value="0">
                                        <label class="form-check-label btn btn-danger"
                                               for="mysql-trim-enabled-0">Disabled</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="mysql[trim]"
                                               id="mysql-trim-enabled-1" value="1" checked="checked">
                                        <label class="form-check-label btn btn-success"
                                               for="mysql-trim-enabled-1"><strong>Enabled</strong></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="mysql[trim]"
                                               id="mysql-trim-enabled-2" value="2">
                                        <label class="form-check-label btn btn-warning"
                                               for="mysql-trim-enabled-2">Enabled, <strong>EXCEPT</strong>
                                            Towers</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php
// Get app footer
include(APP_BASE_PATH . '/inc/footer.php');
