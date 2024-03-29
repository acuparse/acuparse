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
 * File: src/fcn/settings/upload.php
 * Upload Settings
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 */
?>
<section class="tab-pane fade" id="nav-upload" role="tabpanel" aria-labelledby="nav-upload-tab">
    <div class="row">
        <div class="col">
            <h2 class="panel-heading">Remote Upload</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-12 mx-auto">
            <div class="row alert alert-secondary">
                <div class="col">
                    <!-- General -->
                    <div class="row">
                        <div class="col">
                            <h3>General Settings</h3>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Master Temp Sensor -->
                        <section class="col-md-6 col-12 border border-light">
                            <div class="row">
                                <div class="col">
                                    <h4>Master Temp/Humidity Sensor</h4>
                                    <p>Choose the main sensor used when uploading Temp/Humidity data to
                                        3rd party sites. This does not affect the main dashboard.</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="upload[sensor][external]"
                                               onclick='document.getElementById("station-updates-sensor-id").disabled=true;document.getElementById("station-updates-sensor-archive-0").disabled=true;document.getElementById("station-updates-sensor-archive-1").disabled=true;'
                                               id="station-updates-sensor-0" value="default"
                                            <?= ($config->upload->sensor->external === 'default') ? 'checked="checked"' : false; ?>>
                                        <label class="form-check-label btn btn-success"
                                               for="station-updates-sensor-0"><strong>Primary</strong></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="upload[sensor][external]"
                                               onclick='document.getElementById("station-updates-sensor-id").disabled=false;document.getElementById("station-updates-sensor-archive-0").disabled=false;document.getElementById("station-updates-sensor-archive-1").disabled=false;'
                                               id="station-updates-sensor-1" value="tower"
                                            <?= ($config->upload->sensor->external === 'tower') ? 'checked="checked"' : false; ?>>
                                        <label class="form-check-label btn btn-warning"
                                               for="station-updates-sensor-1">Tower</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="station-updates-sensor-id">Tower ID</label>
                                </div>
                                <div class="col">
                                    <select name="upload[sensor][id]"
                                            id="station-updates-sensor-id"
                                        <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                            class="form-select">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        $result = mysqli_query($conn,
                                            "SELECT * FROM `towers` ORDER BY `arrange`");
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <option value="<?= $row['sensor']; ?>" <?= ($config->upload->sensor->id === $row['sensor']) ? 'selected="selected"' : false; ?>><?= $row['sensor'] . ' - ' . $row['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h5>Use for Archive?</h5>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[sensor][archive]"
                                                    <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                                       id="station-updates-sensor-archive-0" value="0"
                                                    <?= ($config->upload->sensor->archive === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="station-updates-sensor-archive-0"><strong>Disabled</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="upload[sensor][archive]"
                                                        <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                                           id="station-updates-sensor-archive-1" value="1"
                                                        <?= ($config->upload->sensor->archive === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label btn btn-success"
                                                           for="station-updates-sensor-archive-1">Enabled</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <!-- MyAcuRite -->
                        <section class="col-md-6 col-12 border border-light">
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>MyAcuRite</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <h5>Upload Access Data</h5>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="upload[myacurite][access_enabled]"
                                                               id="myacurite-access-enabled-1" value="1"
                                                               onclick='document.getElementById("myacurite-access-url").disabled=false;'
                                                            <?= ($config->upload->myacurite->access_enabled === true) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label btn btn-success"
                                                               for="myacurite-access-enabled-1"><strong>Enabled</strong></label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="upload[myacurite][access_enabled]"
                                                               id="myacurite-access-enabled-0" value="0"
                                                               onclick='document.getElementById("myacurite-access-url").disabled=true;'
                                                            <?= ($config->upload->myacurite->access_enabled === false) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label btn btn-danger"
                                                               for="myacurite-access-enabled-0">Disabled</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <h5>Upload Unknown Sensor Data</h5>
                                                    <p>Sends all data received by your Access to MyAcuRite.</p>
                                                    <p class="small text-danger">Can include neighbours/noise and is
                                                        generally
                                                        not recommend.</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="upload[myacurite][pass_unknown]"
                                                               id="myacurite-pass-unknown-0" value="0"
                                                               onclick='document.getElementById("myacurite-pass-unknown").disabled=true;'
                                                            <?= ($config->upload->myacurite->pass_unknown === false) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label btn btn-success"
                                                               for="myacurite-pass-unknown-0"><strong>Disabled</strong></label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="upload[myacurite][pass_unknown]"
                                                               id="myacurite-pass-unknown-1" value="1"
                                                               onclick='document.getElementById("myacurite-pass-unknown").disabled=false;'
                                                            <?= ($config->upload->myacurite->pass_unknown === true) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label btn btn-danger"
                                                               for="myacurite-pass-unknown-1">Enabled</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="hr-dashed">
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <h5>Upload URL</h5>
                                                    <p class="alert-info">If installed on the same network as your
                                                        device, use secondary. See <a class="text-danger"
                                                                                      href="https://docs.acuparse.com/other/DNS/#direct-connection-to-acuparse">docs/other/DNS.md</a>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <label class="col-form-label"
                                                           for="myacurite-access-url">Hostname</label>
                                                </div>
                                                <div class="col">
                                                    <select name="upload[myacurite][access_url]"
                                                            id="myacurite-access-url"
                                                            class="form-select">
                                                        <option value="https://atlasapi.myacurite.com" <?= ($config->upload->myacurite->access_url === "https://atlasapi.myacurite.com") ? 'selected="selected"' : false; ?>>
                                                            myacurite.com (official)
                                                        </option>
                                                        <option value="https://atlasapi.acuparse.com" <?= ($config->upload->myacurite->access_url === "https://atlasapi.acuparse.com") ? 'selected="selected"' : false; ?>>
                                                            acuparse.com (secondary)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <hr class="hr-dotted">

            <!-- External -->
            <div class="row">
                <div class="col mx-auto alert alert-secondary">
                    <div class="row">
                        <div class="col">
                            <h3>External Providers</h3>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Weather Underground -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>Weather Underground</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[wu][enabled]"
                                                       id="wu-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("wu-updates-id").disabled=true;document.getElementById("wu-updates-password").disabled=true;'
                                                    <?= ($config->upload->wu->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="wu-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[wu][enabled]"
                                                       id="wu-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("wu-updates-id").disabled=false;document.getElementById("wu-updates-password").disabled=false;'
                                                    <?= ($config->upload->wu->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="wu-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wu-updates-id">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wu][id]"
                                                   id="wu-updates-id"
                                                   maxlength="15"
                                                   placeholder="WU Station ID"
                                                <?= ($config->upload->wu->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->wu->id; ?>">
                                            <small id="wu-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://www.wunderground.com/personal-weather-station/mypws">wunderground</a>
                                                Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label"
                                                   for="wu-updates-password">Password</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wu][password]"
                                                   id="wu-updates-password"
                                                   placeholder="WU Password"
                                                   maxlength="35"
                                                <?= ($config->upload->wu->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->wu->password; ?>">
                                            <small id="wu-updates-password-help" class="form-text text-muted">Your
                                                <a
                                                        href="https://www.wunderground.com/personal-weather-station/mypws">wunderground</a>
                                                Password
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wu-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <div class="col form-group">
                                                <input type="text" class="form-control"
                                                       name="upload[wu][url]"
                                                       id="wu-updates-url"
                                                       readonly
                                                       value="<?= $config->upload->wu->url; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- PWS Weather -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>PWS Weather</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[pws][enabled]"
                                                       id="pws-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("pws-updates-id").disabled=true;document.getElementById("pws-updates-password").disabled=true;'
                                                    <?= ($config->upload->pws->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="pws-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[pws][enabled]"
                                                       id="pws-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("pws-updates-id").disabled=false;document.getElementById("pws-updates-password").disabled=false;'
                                                    <?= ($config->upload->pws->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="pws-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="pws-updates-id">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][id]"
                                                   id="pws-updates-id"
                                                   maxlength="15"
                                                   placeholder="PWS Station ID"
                                                <?= ($config->upload->pws->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->pws->id; ?>">
                                            <small id="pws-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://www.pwsweather.com">PWS</a> Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="pws-updates-key">API Key</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][key]"
                                                   id="pws-updates-key"
                                                   placeholder="PWS API Key"
                                                   maxlength="35"
                                                <?= ($config->upload->pws->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->pws->key; ?>">
                                            <small id="pws-updates-key-help" class="form-text text-muted">
                                                Your <a href="https://www.pwsweather.com">PWS</a> Password
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="pws-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][url]"
                                                   id="pws-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->pws->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- CWOP -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>CWOP</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="upload[cwop][enabled]"
                                                           id="cwop-updates-enabled-0" value="0"
                                                           onclick='document.getElementById("cwop-updates-id").disabled=true;document.getElementById("cwop-updates-interval").disabled=true;document.getElementById("cwop-updates-location").disabled=true;'
                                                        <?= ($config->upload->cwop->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label btn btn-danger"
                                                           for="cwop-updates-enabled-0">Disabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="upload[cwop][enabled]"
                                                           id="cwop-updates-enabled-1" value="1"
                                                           onclick='document.getElementById("cwop-updates-id").disabled=false;document.getElementById("cwop-updates-interval").disabled=false;document.getElementById("cwop-updates-location").disabled=false;'
                                                        <?= ($config->upload->cwop->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label btn btn-success"
                                                           for="cwop-updates-enabled-1">Enabled</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="cwop-updates-id">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[cwop][id]"
                                                   id="cwop-updates-id"
                                                   maxlength="15"
                                                   placeholder="CWOP Station ID"
                                                <?= ($config->upload->cwop->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->cwop->id; ?>">
                                            <small id="cwop-updates-id-help" class="form-text text-muted">Your
                                                <a
                                                        href="http://www.wxqa.com/SIGN-UP.html">CWOP</a>
                                                Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label mr-sm-2"
                                                   for="cwop-updates-interval">Upload Interval</label>
                                        </div>
                                        <div class="col">
                                            <select name="upload[cwop][interval]"
                                                    id="cwop-updates-interval"
                                                <?= ($config->upload->cwop->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                    class="form-select">
                                                <?php
                                                if ($config->upload->cwop->interval === '5 minutes') {
                                                    $config->upload->cwop->interval = '10 minutes';
                                                }
                                                foreach ($config->intervals as $interval) {
                                                    if ($interval != '5 minutes') {
                                                        ?>
                                                        <option value="<?= $interval; ?>" <?= ($config->upload->cwop->interval === $interval) ? 'selected="selected"' : false; ?>><?= $interval; ?></option>
                                                        <?php
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="cwop-updates-location">Location</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[cwop][location]"
                                                   id="cwop-updates-location"
                                                   placeholder="ddmm.hhN/dddmm.hhW"
                                                   maxlength="35"
                                                <?= ($config->upload->cwop->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->cwop->location; ?>">
                                            <small id="cwop-updates-location-help" class="form-text text-muted">
                                                in format <code>ddmm.hhN/dddmm.hhW</code>. See
                                                <a href="http://boulter.com/gps">Degrees, Minutes & Seconds</a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="cwop-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[cwop][url]"
                                                   id="cwop-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->cwop->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weathercloud -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>Weathercloud</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[wc][enabled]"
                                                       id="wc-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("wc-updates-id").disabled=true;document.getElementById("wc-updates-key").disabled=true;document.getElementById("wc-updates-device").disabled=true;'
                                                    <?= ($config->upload->wc->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="wc-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[wc][enabled]"
                                                       id="wc-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("wc-updates-id").disabled=false;document.getElementById("wc-updates-key").disabled=false;document.getElementById("wc-updates-device").disabled=false;'
                                                    <?= ($config->upload->wc->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="wc-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wc-updates-id">ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wc][id]"
                                                   id="wc-updates-id"
                                                   maxlength="35"
                                                   placeholder="ID"
                                                <?= ($config->upload->wc->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->wc->id; ?>">
                                            <small id="wc-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://app.weathercloud.net/devices">Weathercloud</a>
                                                API ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wc-updates-key">API Key</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wc][key]"
                                                   id="wc-updates-key"
                                                   placeholder="Key"
                                                   maxlength="35"
                                                <?= ($config->upload->wc->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->wc->key; ?>">
                                            <small id="wc-updates-key-help" class="form-text text-muted">Your <a
                                                        href="https://app.weathercloud.net/devices">Weathercloud</a>
                                                API Key
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wc-updates-device">Device ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wc][device]"
                                                   id="wc-updates-device"
                                                   maxlength="35"
                                                   placeholder="dxxxxxxxxxx"
                                                <?= ($config->upload->wc->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->wc->device; ?>">
                                            <small id="wc-updates-device-help" class="form-text text-muted">Your <a
                                                        href="https://app.weathercloud.net/">Weathercloud</a>
                                                device ID (Begins with dxxx...)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="wc-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[wc][url]"
                                                   id="wc-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->wc->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- Windy Upload -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>Windy</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[windy][enabled]"
                                                       id="windy-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("windy-updates-id").disabled=true;document.getElementById("windy-updates-key").disabled=true;'
                                                    <?= ($config->upload->windy->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="windy-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[windy][enabled]"
                                                       id="windy-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("windy-updates-id").disabled=false;document.getElementById("windy-updates-key").disabled=false;'
                                                    <?= ($config->upload->windy->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="windy-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="windy-updates-id">ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windy][id]"
                                                   id="windy-updates-id"
                                                   maxlength="35"
                                                   placeholder="ID"
                                                <?= ($config->upload->windy->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windy->id; ?>">
                                            <small id="wc-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://stations.windy.com/stations">Windy</a> ID</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="windy-updates-station">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windy][station]"
                                                   id="windy-updates-station"
                                                   maxlength="1"
                                                <?= ($config->upload->windy->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windy->station; ?>">
                                            <small id="windy-updates-key-help" class="form-text text-muted">Your
                                                Windy Station ID. Default 0.
                                            </small>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3">
                                                <label class="col-form-label" for="windy-updates-key">API Key</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control"
                                                       name="upload[windy][key]"
                                                       id="windy-updates-key"
                                                       maxlength="150"
                                                       placeholder="XXX-API-KEY-XXX"
                                                    <?= ($config->upload->windy->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                       value="<?= $config->upload->windy->key; ?>">
                                                <small id="windy-updates-key-help" class="form-text text-muted">Your
                                                    Windy API Key.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3">
                                                <label class="col-form-label" for="windy-updates-url">URL</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control"
                                                       name="upload[windy][url]"
                                                       id="windy-updates-url"
                                                       disabled="disabled"
                                                       value="<?= $config->upload->windy->url; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Windguru Upload -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>Windguru</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[windguru][enabled]"
                                                       id="windguru-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("windguru-updates-uid").disabled=true;document.getElementById("windguru-updates-id").disabled=true;document.getElementById("windguru-updates-password").disabled=true;'
                                                    <?= ($config->upload->windguru->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="windguru-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[windguru][enabled]"
                                                       id="windguru-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("windguru-updates-uid").disabled=false;document.getElementById("windguru-updates-id").disabled=false;document.getElementById("windguru-updates-password").disabled=false;'
                                                    <?= ($config->upload->windguru->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="windguru-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="windguru-updates-uid">Station UID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windguru][uid]"
                                                   id="windguru-updates-uid"
                                                   maxlength="15"
                                                   placeholder="Station UID"
                                                <?= ($config->upload->windguru->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windguru->uid; ?>">
                                            <small id="windguru-updates-uid-help"
                                                   class="form-text text-muted">Your
                                                <a
                                                        href="https://stations.windguru.cz/register.php?id_type=16">Windguru</a>
                                                Station UID</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label"
                                                   for="windguru-updates-password">Password</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windguru][password]"
                                                   id="windguru-updates-password"
                                                   placeholder="Password"
                                                   maxlength="35"
                                                <?= ($config->upload->windguru->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windguru->password; ?>">
                                            <small id="windguru-updates-password-help"
                                                   class="form-text text-muted">Your Password
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="windguru-updates-id">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windguru][id]"
                                                   id="windguru-updates-id"
                                                   maxlength="5"
                                                   placeholder="Station ID"
                                                <?= ($config->upload->windguru->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windguru->id; ?>">
                                            <small id="windguru-updates-id-help"
                                                   class="form-text text-muted"><code>http://windguru.cz/station/{ID}</code></small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="windguru-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[windguru][url]"
                                                   id="windguru-updates-url"
                                                   disabled="disabled"
                                                   value="<?= $config->upload->windguru->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- OpenWeather Upload -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>OpenWeather</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[openweather][enabled]"
                                                       id="openweather-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("openweather-updates-id").disabled=true;document.getElementById("openweather-updates-key").disabled=true;'
                                                    <?= ($config->upload->openweather->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="openweather-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[openweather][enabled]"
                                                       id="openweather-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("openweather-updates-id").disabled=false;document.getElementById("openweather-updates-key").disabled=false;'
                                                    <?= ($config->upload->openweather->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="openweather-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="openweather-updates-id">Station
                                                ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[openweather][id]"
                                                   id="openweather-updates-id"
                                                   maxlength="24"
                                                   placeholder="Station ID"
                                                <?= ($config->upload->openweather->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->openweather->id; ?>">
                                            <small id="openweather-updates-uid-help" class="form-text text-muted">Your
                                                <a href="https://openweathermap.org/stations#steps">OpenWeather</a>
                                                Station ID.<br>See <a
                                                        href="https://docs.acuparse.com/external/OPENWEATHER">docs</a>
                                                for details.</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="openweather-updates-key">API Key</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[openweather][key]"
                                                   id="openweather-updates-key"
                                                   placeholder="API Key"
                                                   maxlength="32"
                                                <?= ($config->upload->openweather->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->openweather->key; ?>">
                                            <small id="openweather-updates-key-help"
                                                   class="form-text text-muted">Your API Key
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="openweather-updates-url">URL</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[openweather][url]"
                                                   id="openweather-updates-url"
                                                   disabled="disabled"
                                                   value="<?= $config->upload->openweather->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Generic Upload -->
                        <div class="col-md-6 col-12 border border-light">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <h4>Generic Update Server</h4>
                                            <p>Sends data in wunderground format to any compatible provider.</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[generic][enabled]"
                                                       id="generic-updates-enabled-0" value="0"
                                                       onclick='document.getElementById("generic-updates-id").disabled=true;document.getElementById("generic-updates-password").disabled=true;document.getElementById("generic-updates-url").disabled=true;'
                                                    <?= ($config->upload->generic->enabled === false) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-danger"
                                                       for="generic-updates-enabled-0">Disabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[generic][enabled]"
                                                       id="generic-updates-enabled-1" value="1"
                                                       onclick='document.getElementById("generic-updates-id").disabled=false;document.getElementById("generic-updates-password").disabled=false;document.getElementById("generic-updates-url").disabled=false;'
                                                    <?= ($config->upload->generic->enabled === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label btn btn-success"
                                                       for="generic-updates-enabled-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label class="col-form-label" for="generic-updates-id">Station ID</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[generic][id]"
                                                   id="generic-updates-id"
                                                   maxlength="15"
                                                   placeholder="Station ID"
                                                <?= ($config->upload->generic->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->generic->id; ?>">
                                            <small id="generic-updates-id-help" class="form-text text-muted">Your
                                                Station ID, if required.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label"
                                                   for="generic-updates-password">Password</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                   name="upload[generic][password]"
                                                   id="generic-updates-password"
                                                   placeholder="Password"
                                                   maxlength="35"
                                                <?= ($config->upload->generic->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->generic->password; ?>">
                                            <small id="generic-updates-password-help"
                                                   class="form-text text-muted">Your Password, if required.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="generic-updates-url">URL</label>
                                        </div>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[generic][url]"
                                                   id="generic-updates-url"
                                                <?= ($config->upload->generic->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->generic->url; ?>"
                                                   placeholder="Upload URL">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col">
                                            <p><strong>Supported Servers:</strong></p>
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <a href="https://docs.acuparse.com/external/generic/WEATHERPOLY">WeatherPoly</a>
                                                    http(s)://{IP/HOSTNAME}:8080/acuparse
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <!-- MQTT -->
            <div class="row">
                <div class="col mx-auto alert alert-secondary">
                    <div class="row">
                        <div class="col">
                            <h3>MQTT</h3>
                            <p>Lightweight publish/subscribe messaging protocol for machine to machine
                                telemetry.<br>See <a href="https://docs.acuparse.com/external/MQTT">docs</a> for
                                details.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="upload[mqtt][enabled]"
                                       id="mqtt-upload-enabled-0" value="0"
                                       onclick='document.getElementById("mqtt-upload-client").disabled=true;document.getElementById("mqtt-upload-port").disabled=true;document.getElementById("mqtt-upload-server").disabled=true;document.getElementById("mqtt-upload-topic").disabled=true;document.getElementById("mqtt-upload-username").disabled=true;document.getElementById("mqtt-upload-password").disabled=true;'
                                    <?= ($config->upload->mqtt->enabled === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="mqtt-upload-enabled-0">Disabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="upload[mqtt][enabled]"
                                       id="mqtt-upload-enabled-1" value="1"
                                       onclick='document.getElementById("mqtt-upload-client").disabled=false;document.getElementById("mqtt-upload-port").disabled=false;document.getElementById("mqtt-upload-server").disabled=false;document.getElementById("mqtt-upload-topic").disabled=false;document.getElementById("mqtt-upload-username").disabled=false;document.getElementById("mqtt-upload-password").disabled=false;'
                                    <?= ($config->upload->mqtt->enabled === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="mqtt-upload-enabled-1">Enabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- col a -->
                        <div class="col-6">
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-server">Server</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][server]"
                                           id="mqtt-upload-server"
                                           placeholder="mqtt.example.com"
                                           required="required"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->server; ?>">
                                    <small id="mqtt-upload-server-help" class="form-text text-muted">Your MQTT Server
                                        Hostname/IP. (Required)
                                    </small>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-ussername">Username</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][username]"
                                           id="mqtt-upload-username"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->username; ?>">
                                    <small id="mqtt-upload-username-help" class="form-text text-muted">Your MQTT Server
                                        Username. (Optional)
                                    </small>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-password">Password</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][password]"
                                           id="mqtt-upload-password"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->password; ?>">
                                    <small id="mqtt-upload-password-help" class="form-text text-muted">Your MQTT Server
                                        Password. (Optional)
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-port">Port</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][port]"
                                           id="mqtt-upload-port"
                                           placeholder="1883"
                                           maxlength="32"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->port; ?>">
                                    <small id="mqtt-upload-port-help"
                                           class="form-text text-muted">MQTT Server Port.<br>Default = <code>1883</code>.
                                    </small>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-client">Client ID</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][client]"
                                           id="mqtt-upload-client"
                                           maxlength="24"
                                           placeholder="acuparse"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->client; ?>">
                                    <small id="mqtt-upload-client-help" class="form-text text-muted">ClientID used to
                                        connect to your server. (Optional)
                                    </small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="mqtt-upload-topic">Main Topic</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="upload[mqtt][topic]"
                                           id="mqtt-upload-topic"
                                           placeholder="acuparse"
                                        <?= ($config->upload->mqtt->enabled === false) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->upload->mqtt->topic; ?>">
                                    <small id="mqtt-upload-topic-help" class="form-text text-muted">The main topic data
                                        will be published to.<br>Default = <code>acuparse</code>.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
