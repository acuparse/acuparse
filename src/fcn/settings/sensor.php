<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2024 Maxwell Power
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
 * @var object $config Global Config
 */
?>
<section class="tab-pane fade" id="nav-sensor" role="tabpanel" aria-labelledby="nav-sensor-tab">
    <div class="row">
        <div class="col">
            <h2 class="panel-heading">Sensor Settings</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-12 mx-auto">

            <!-- MAC Address -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>MAC Address</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <p class="alert alert-info">Enter the MAC address from your device below.<br>
                                You can only use one device to report readings at a time.</p>
                            <p class="alert alert-warning">Check your settings after making changes!</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[device]"
                                       id="station-device-atlas"
                                       onclick='document.getElementById("station-filter-access-0").disabled=false;document.getElementById("station-filter-access-1").disabled=false;document.getElementById("station-sensor-iris").disabled=false;document.getElementById("station-sensor-atlas").disabled=false;document.getElementById("station-hub-mac").disabled=true;document.getElementById("station-primary-sensor-0").disabled=false;document.getElementById("station-access-mac").disabled=false;document.getElementById("station-lightning-source-1").disabled=false;<?= ($config->station->towers === true) ? 'document.getElementById("station-lightning-source-2").disabled=false;document.getElementById("station-lightning-source-3").disabled=false' : false; ?>'
                                       value="0"
                                    <?= ($config->station->device === 0) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-device-atlas">Access</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[device]"
                                       id="station-device-smarthub"
                                       onclick='document.getElementById("station-filter-access-0").disabled=true;document.getElementById("station-filter-access-1").disabled=true;document.getElementById("station-sensor-iris").disabled=false;document.getElementById("station-sensor-atlas").disabled=true;document.getElementById("station-primary-sensor-0").disabled=true;document.getElementById("station-primary-sensor-1").checked=true;document.getElementById("station-access-mac").disabled=true;document.getElementById("station-hub-mac").disabled=false;document.getElementById("station-lightning-source-0").checked="checked";document.getElementById("station-lightning-source-1").disabled=true;document.getElementById("station-lightning-source-2").disabled=true;document.getElementById("station-lightning-source-3").disabled=true;document.getElementById("myacurite-access-enabled-1").disabled=true;document.getElementById("myacurite-access-enabled-0").checked="checked"'
                                       value="1"
                                    <?= ($config->station->device === 1) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-device-smarthub">smartHUB</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="station-access-mac">Access</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="station[access_mac]"
                                           id="station-access-mac" placeholder="Access MAC"
                                        <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                        <?= !isset($config->station->device) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->station->access_mac; ?>">
                                    <small id="station-sensor-atlas-help" class="form-text text-muted">Enter ONLY the
                                        digits. No Colons, Spaces, or Dashes.</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="station-hub-mac">smartHUB</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="station[hub_mac]"
                                           id="station-hub-mac" placeholder="smartHUB MAC"
                                           maxlength="12"
                                        <?= $config->station->device === 0 ? 'disabled="disabled"' : false; ?>
                                        <?= !isset($config->station->device) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->station->hub_mac; ?>">
                                    <small id="station-sensor-atlas-help" class="form-text text-muted">Enter ONLY the
                                        digits. No Colons, Spaces, or Dashes.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Source -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>Primary Data Source</h3>
                            <p class="alert alert-warning">You can use an Iris (5-in-1) or Atlas (7-in-1) sensor as
                                your primary sensor.<br/><strong>You must have an Access to receive Atlas data.</strong>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[primary_sensor]"
                                       id="station-primary-sensor-0"
                                       onclick='document.getElementById("station-sensor-iris").disabled=true;document.getElementById("station-sensor-atlas").disabled=false;document.getElementById("station-lightning-source-1").disabled=false;'
                                       value="0"
                                    <?= ($config->station->primary_sensor === 0) ? 'checked="checked"' : false; ?>
                                    <?= ($config->station->device === 1) ? 'disabled="disabled"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-primary-sensor-0">Atlas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[primary_sensor]"
                                       id="station-primary-sensor-1"
                                       onclick='document.getElementById("station-sensor-iris").disabled=false;document.getElementById("station-sensor-atlas").disabled=true;document.getElementById("station-lightning-source-1").disabled=true;document.getElementById("station-lightning-source-0").checked=true;'
                                       value="1"
                                    <?= ($config->station->primary_sensor === 1) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-primary-sensor-1">Iris</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="station-sensor-atlas">Atlas (7-in-1) Station
                                        ID</label>
                                </div>
                                <div class="col">
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
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="station-sensor-iris">Iris (5-in-1) Station
                                        ID</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="station[sensor_iris]"
                                           id="station-sensor-iris" placeholder="00000000"
                                           maxlength="8" pattern="[0-9]{8}"
                                           title="8 Digits including leading 0's"
                                        <?= $config->station->primary_sensor === 0 ? 'disabled="disabled"' : false; ?>
                                        <?= !isset($config->station->primary_sensor) ? 'disabled="disabled"' : false; ?>
                                           value="<?= $config->station->sensor_iris; ?>">
                                    <small id="station-sensor-iris-help" class="form-text text-muted">8
                                        Digits including leading 0's
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Access -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h2>Access Specific Settings</h2>
                            <p><small><strong>(AcuRite Access Required)</strong></small></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <h3>Lightning Sensor</h3>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-0"
                                       value="0"
                                    <?= ($config->station->lightning_source === 0) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="station-lightning-source-0"><strong>Disabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-1"
                                       value="1"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 1) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="station-lightning-source-1">Atlas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-2"
                                       value="2"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 2) ? 'checked="checked"' : false; ?>
                                    <?= ($config->station->towers === false) ? 'disabled="disabled"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="station-lightning-source-2">Tower</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[lightning_source]"
                                       id="station-lightning-source-3"
                                       value="3"
                                    <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                    <?= ($config->station->lightning_source === 3) ? 'checked="checked"' : false; ?>
                                    <?= ($config->station->towers === false) ? 'disabled="disabled"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="station-lightning-source-3">Both</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h3>Filter Erroneous Atlas Readings</h3>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="station[filter_access]"
                                               id="station-filter-access-1"
                                               value="1"
                                            <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                            <?= ($config->station->filter_access === true) ? 'checked="checked"' : false; ?>>
                                        <label class="form-check-label btn btn-success"
                                               for="station-filter-access-1"><strong>Enabled</strong></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="station[filter_access]"
                                               id="station-filter-access-0"
                                               value="0"
                                            <?= $config->station->device === 1 ? 'disabled="disabled"' : false; ?>
                                            <?= ($config->station->filter_access === false) ? 'checked="checked"' : false; ?>>
                                        <label class="form-check-label btn btn-danger"
                                               for="station-filter-access-0">Disabled</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Barometer -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h3>Barometer Settings</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <label class="col-form-label" for="station-baro-offset">
                                                Offset</label>
                                        </div>
                                        <div class="col">
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
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Wind Direction -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h3>Southern Hemisphere Support</h3>
                                    <p><a href="https://docs.acuparse.com/INSTALL/#southern-hemisphere-support">See the
                                            docs</a> for more details.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <p><strong>Reverse Wind Direction?</strong></p>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[reverse_wind]"
                                                       id="station-reverse-wind-0" value="0"
                                                    <?= ($config->station->reverse_wind === false) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-danger"
                                                       for="station-reverse-wind-0"><strong>Disabled</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[reverse_wind]"
                                                       id="station-reverse-wind-1" value="1"
                                                    <?= ($config->station->reverse_wind === true) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-success"
                                                       for="station-reverse-wind-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RTL Relay -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h3>Realtime Updates</h3>
                                    <p><small><strong>(RTL Dongle and Acuparse Relay Service Required)</strong></small>
                                    </p>
                                    <p><a href="https://docs.acuparse.com/REALTIME/">See the docs</a> for more
                                        information
                                        on enabling realtime updates.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <p><strong>Enable RTL Relay and Realtime Updates?</strong></p>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[realtime]"
                                                       id="station-realtime-0" value="0"
                                                    <?= ($config->station->realtime === false) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-danger"
                                                       for="station-realtime-0"><strong>Disabled</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[realtime]"
                                                       id="station-realtime-1" value="1"
                                                    <?= ($config->station->realtime === true) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-success"
                                                       for="station-realtime-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <p class="small"><strong>Use System Timezone?</strong></p>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[realtimeUTC]"
                                                       id="station-realtimeUTC-0" value="0"
                                                    <?= ($config->station->realtimeUTC === true) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-danger btn-sm"
                                                       for="station-realtimeUTC-0"><strong>Disabled</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="station[realtimeUTC]"
                                                       id="station-realtimeUTC-1" value="1"
                                                    <?= ($config->station->realtimeUTC === false) ? 'checked="checked"' : false; ?>
                                                >
                                                <label class="form-check-label btn btn-success btn-sm"
                                                       for="station-realtimeUTC-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Towers -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h2>Tower Sensors</h2>
                            <p><strong>Enable Tower Sensors?</strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers]"
                                       id="station-towers-0" value="0"
                                       onclick='document.getElementById("station-towers-additional-0").disabled=true;document.getElementById("station-towers-additional-1").disabled=true;document.getElementById("station-lightning-source-2").disabled=true;document.getElementById("station-lightning-source-3").disabled=true;'
                                    <?= ($config->station->towers === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="station-towers-0"><strong>Disabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers]"
                                       id="station-towers-1" value="1"
                                       onclick='document.getElementById("station-towers-additional-0").disabled=false;document.getElementById("station-towers-additional-1").disabled=false;document.getElementById("station-towers-additional-1").checked=true;document.getElementById("station-lightning-source-2").disabled=false;document.getElementById("station-lightning-source-3").disabled=false;'
                                    <?= ($config->station->towers === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="station-towers-1">Enabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <p><strong>Show High/Low Readings?</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers_additional]"
                                       id="station-towers-additional-1" value="1"
                                    <?= ($config->station->towers_additional === true) ? 'checked="checked"' : false; ?>
                                    <?= ($config->station->towers === false) ? 'disabled="disabled"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="station-towers-additional-1"><strong>Enabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="station[towers_additional]"
                                       id="station-towers-additional-0" value="0"
                                    <?= ($config->station->towers_additional === false) ? 'checked="checked"' : false; ?>
                                    <?= ($config->station->towers === false) ? 'disabled="disabled"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="station-towers-additional-0">Disabled</label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
