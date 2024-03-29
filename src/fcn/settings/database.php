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
 * File: src/fcn/settings/database.php
 * Database Settings
 */

/**
 * @var object $config Global Config
 */
?>
<section class="tab-pane fade" id="nav-database" role="tabpanel" aria-labelledby="nav-database-tab">
    <div class="row">
        <div class="col">
            <h2 class="panel-heading">Database Settings</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-12 mx-auto">

            <!-- Connection -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>Connection Settings</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <label class="col-form-label" for="mysql-host">Hostname</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control"
                                   name="mysql[host]"
                                   id="mysql-host"
                                   placeholder="localhost"
                                   maxlength="35"
                                   value="<?= $config->mysql->host; ?>">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                            <label class="col-form-label" for="mysql-database">Database</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control"
                                   name="mysql[database]"
                                   id="mysql-database"
                                   placeholder="acuparse"
                                   maxlength="35"
                                   value="<?= $config->mysql->database; ?>">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                            <label class="col-form-label" for="mysql-username">Username</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control"
                                   name="mysql[username]"
                                   id="mysql-username"
                                   placeholder="acuparse.dbadmin"
                                   maxlength="35"
                                   value="<?= $config->mysql->username; ?>">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                            <label class="col-form-label" for="mysql-password">Password</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control"
                                   name="mysql[password]"
                                   id="mysql-password"
                                   placeholder="Password"
                                   maxlength="32"
                                   value="<?= $config->mysql->password; ?>">
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Trimming -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>Database Trimming</h3>
                            <p><a href="https://docs.acuparse.com/INSTALL/#database-trimming">Database Trimming</a>
                                keeps your database tables clean.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="mysql[trim]"
                                       id="mysql-trim-enabled-1" value="1"
                                    <?= ($config->mysql->trim === 1) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="mysql-trim-enabled-1"><strong>Enabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="mysql[trim]"
                                       id="mysql-trim-enabled-2" value="2"
                                    <?= ($config->mysql->trim === 2) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-warning"
                                       for="mysql-trim-enabled-2">Enabled, <strong>EXCEPT</strong>
                                    Towers</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="mysql[trim]"
                                       id="mysql-trim-enabled-0" value="0"
                                    <?= ($config->mysql->trim === 0) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="mysql-trim-enabled-0">Disabled</label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
