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
 * File: src/fcn/settings/sensor.php
 * Sensor Settings
 */

/**
 * @return array
 * @var object $config Global Config
 */
?>
<div class="tab-pane fade" id="nav-sensor" role="tabpanel" aria-labelledby="nav-sensor-tab">
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="col">
                    <h2 class="panel-heading">Sensor Settings</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-12 mx-auto alert alert-secondary">
                    <h3>MAC Address:</h3>
                    <p class="alert alert-info">Enter the addresses for your devices below.
                        You can only use one device to report readings at a time.</p>
                    <p class="alert alert-warning">Check your settings after making changes!</p>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio"
                               name="station[device]"
                               id="station-device-atlas"
                               onclick='document.getElementById("station-sensor-5n1").disabled=false;document.getElementById("station-sensor-atlas").disabled=false;document.getElementById("station-hub-mac").disabled=true;document.getElementById("station-primary-sensor-0").disabled=false;document.getElementById("station-access-mac").disabled=false;document.getElementById("station-lightning-source-1").disabled=false;document.getElementById("station-lightning-source-2").disabled=false;document.getElementById("station-lightning-source-3").disabled=false'
                               value="0"
                            <?= ($config->station->device === 0) ? 'checked="checked"' : false; ?>>
                        <label class="form-check-label alert bg-dark"
                               for="station-device-atlas">Access</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio"
                               name="station[device]"
                               id="station-device-smarthub"
                               onclick='document.getElementById("station-sensor-5n1").disabled=false;document.getElementById("station-sensor-atlas").disabled=true;document.getElementById("station-primary-sensor-0").disabled=true;document.getElementById("station-primary-sensor-1").checked=true;document.getElementById("station-access-mac").disabled=true;document.getElementById("station-hub-mac").disabled=false;document.getElementById("station-lightning-source-0").checked="checked";document.getElementById("station-lightning-source-1").disabled=true;document.getElementById("station-lightning-source-2").disabled=true;document.getElementById("station-lightning-source-3").disabled=true;document.getElementById("myacurite-access-enabled-1").disabled=true;document.getElementById("myacurite-access-enabled-0").checked="checked"'
                               value="1"
                            <?= ($config->station->device === 1) ? 'checked="checked"' : false; ?>>
                        <label class="form-check-label alert bg-dark"
                               for="station-device-smarthub">smartHUB</label>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="station-access-mac">Access:</label>
                        <input type="text" class="form-control" name="station[access_mac]"
                               id="station-access-mac" placeholder="Access MAC"
                            <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                            <?= !isset($config->station->device) ? 'disabled="disabled"' : false; ?>
                               value="<?= $config->station->access_mac; ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="station-hub-mac">smartHUB:</label>
                        <input type="text" class="form-control" name="station[hub_mac]"
                               id="station-hub-mac" placeholder="smartHUB MAC"
                               maxlength="12"
                            <?= $config->station->device === 0 ? 'disabled="disabled"' : false; ?>
                            <?= !isset($config->station->device) ? 'disabled="disabled"' : false; ?>
                               value="<?= $config->station->hub_mac; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-12 mx-auto alert alert-primary">
                    <div class="form-group">
                        <h3>Primary Data Source:</h3>
                        <p class="alert alert-warning">You can use an Atlas or 5-in-1 sensor as
                            your
                            primary sensor. You must have an Access to receive Atlas data.</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="station[primary_sensor]"
                                   id="station-primary-sensor-0"
                                   onclick='document.getElementById("station-sensor-5n1").disabled=true;document.getElementById("station-sensor-atlas").disabled=false;'
                                   value="0"
                                <?= ($config->station->primary_sensor === 0) ? 'checked="checked"' : false; ?>>
                            <label class="form-check-label alert bg-dark"
                                   for="station-primary-sensor-0">Atlas</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="station[primary_sensor]"
                                   id="station-primary-sensor-1"
                                   onclick='document.getElementById("station-sensor-5n1").disabled=false;document.getElementById("station-sensor-atlas").disabled=true;'
                                   value="1"
                                <?= ($config->station->primary_sensor === 1) ? 'checked="checked"' : false; ?>>
                            <label class="form-check-label alert bg-dark"
                                   for="station-primary-sensor-1">5-in-1</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="station-sensor-atlas">Atlas Station
                            ID:</label>
                        <input type="text" class="form-control"
                               name="station[sensor_atlas]"
                               id="station-sensor-atlas" placeholder="00000000"
                               maxlength="8" pattern="[0-9]{8}"
                               title="8 Digits including leading 0's"
                            <?= $config->station->primary_sensor === 1 ? 'disabled="disabled"' : false; ?>
                            <?= !isset($config->station->primary_sensor) ? 'disabled="disabled"' : false; ?>
                               value="<?= $config->station->sensor_atlas; ?>">
                        <small id="station-sensor-atlas-help" class="form-text text-muted">8
                            Digits including leading 0's
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="station-sensor-5n1">5-in-1 Station
                            ID:</label>
                        <input type="text" class="form-control"
                               name="station[sensor_5n1]"
                               id="station-sensor-5n1" placeholder="00000000"
                               maxlength="8" pattern="[0-9]{8}"
                               title="8 Digits including leading 0's"
                            <?= $config->station->primary_sensor === 0 ? 'disabled="disabled"' : false; ?>
                            <?= !isset($config->station->primary_sensor) ? 'disabled="disabled"' : false; ?>
                               value="<?= $config->station->sensor_5n1; ?>">
                        <small id="station-sensor-5n1-help" class="form-text text-muted">8
                            Digits including leading 0's
                        </small>
                    </div>
                </div>
                <div class="col-md-8 col-12 mx-auto">
                    <hr>
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="form-group">
                            <label class="col-form-label" for="station-baro-offset"><strong>Barometer
                                    Offset</strong></label>
                            <input type="number" class="form-control"
                                   name="station[baro_offset]"
                                   id="station-baro-offset" step=".01"
                                   placeholder="Barometer Offset"
                                   value="<?= $config->station->baro_offset; ?>">
                            <small id="station-sensor-baro-offset-help"
                                   class="form-text text-muted">
                                inHg. Adjust this as required to match the offset for your
                                elevation.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-12 mx-auto">
                    <hr>
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="form-group">
                            <p><strong>Tower Sensors</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers]"
                                       id="station-towers-0" value="0"
                                    <?= ($config->station->towers === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-danger"
                                       for="station-towers-0">Disabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers]"
                                       id="station-towers-1" value="1"
                                    <?= ($config->station->towers === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-success"
                                       for="station-towers-1">Enabled</label>
                            </div>
                            <small id="station-towers-help"
                                   class="form-text text-muted">Enable Tower Sensors?
                            </small>
                        </div>
                        <div class="form-group">
                            <p><strong>Show High/Low Readings?</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers_additional]"
                                       id="station-towers-additional-0" value="0"
                                    <?= ($config->station->towers_additional === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-danger"
                                       for="station-towers-additional-0">Disabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers_additional]"
                                       id="station-towers-additional-1" value="1"
                                    <?= ($config->station->towers_additional === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-success"
                                       for="station-towers-additional-1">Enabled</label>
                            </div>
                            <small id="station-towers-help"
                                   class="form-text text-muted">Show High/Low Readings?
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-12 mx-auto">
                    <hr>
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="form-group">
                            <p><strong>Lightning Sensor</strong><br><small>(Acurite Access
                                    Required)</small></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-0"
                                       value="0"
                                    <?= ($config->station->lightning_source === 0) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-danger"
                                       for="station-lightning-source-0">Disabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-1"
                                       value="1"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 1) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="station-lightning-source-1">Atlas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-2"
                                       value="2"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 2) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="station-lightning-source-2">Tower</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-3"
                                       value="3"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 3) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="station-lightning-source-3">Both</label>
                            </div>
                            <small id="station-lightning-source-help"
                                   class="form-text text-muted">Do you have a lightning sensor?
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>