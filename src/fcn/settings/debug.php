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
 * File: src/fcn/settings/debug.php
 * Debug Settings
 */

/**
 * @return array
 * @var object $config Global Config
 */
?>
<div class="tab-pane fade" id="nav-debug" role="tabpanel"
     aria-labelledby="nav-debug-tab">
    <div class="row">
        <div class="col">
            <h2 class="panel-heading">Debug Server</h2>
            <p>Sends MyAcuRite data to a debug/testing server.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-12 mx-auto border">
            <div class="form-group">
                <h4>Status:</h4>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="debug[server][enabled]"
                           onclick='document.getElementById("debug-server-url").disabled=true;'
                           id="debug-server-enabled-0" value="0"
                        <?= ($config->debug->server->enabled === false) ? 'checked="checked"' : false; ?>>
                    <label class="form-check-label alert alert-danger"
                           for="debug-server-enabled-0">Disabled</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="debug[server][enabled]"
                           onclick='document.getElementById("debug-server-url").disabled=false;'
                           id="debug-server-enabled-1" value="1"
                        <?= ($config->debug->server->enabled === true) ? 'checked="checked"' : false; ?>>
                    <label class="form-check-label alert alert-success"
                           for="debug-server-enabled-1">Enabled</label>
                </div>
            </div>
            <div class="form-row">
                <label class="col-form-label" for="debug-server-url">URL:</label>
                <div class="col form-group">
                    <input type="text" class="form-control"
                           name="debug[server][url]"
                           id="debug-server-url"
                           placeholder="www.example.com"
                        <?= ($config->debug->server->enabled === false) ? 'disabled="disabled"' : false; ?>
                           value="<?= $config->debug->server->url; ?>">
                    <small id="debug-server-url-help" class="form-text text-muted">
                        Hostname/IP only. No HTTP/HTTPS!
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>