<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2021 Maxwell Power
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
 * File: src/fcn/settings/site.php
 * Generic Site Settings
 */

/**
 * @var object $config Global Config
 */
?>
<section class="tab-pane fade show active" id="nav-site" role="tabpanel" aria-labelledby="nav-site-tab">
    <div class="row">
        <div class="col">
            <h2 class="panel-heading">Site Settings</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-12 mx-auto">

            <!-- General -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>General Settings</h3>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-name">Name</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[name]"
                                           id="site-name" placeholder="Station Name" maxlength="32"
                                           value="<?= $config->site->name; ?>" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-location">Location</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[location]"
                                           id="site-location" placeholder="Station Location"
                                           maxlength="32"
                                           value="<?= $config->site->location; ?>" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-desc">Description</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[desc]"
                                           id="site-desc" placeholder="Station Description"
                                           maxlength="100"
                                           value="<?= $config->site->desc; ?>" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-hostname">Hostname</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[hostname]"
                                           id="site-hostname" placeholder="www.example.com"
                                           maxlength="32" aria-describedby="hostname-help"
                                           value="<?= $config->site->hostname; ?>" required>
                                    <small id="hostname-help" class="form-text text-muted">FQDN or IP Address</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-email">System Email</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[email]"
                                           id="site-email" aria-describedby="email-help"
                                           placeholder="weather@example.com" maxlength="32"
                                           value="<?= $config->site->email; ?>" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-timezone">Timezone</label>
                                </div>
                                <div class="col">
                                    <select name="site[timezone]" id="site-timezone"
                                            class="form-select" required>
                                        <option value="" disabled>Select Timezone</option>
                                        <?php
                                        $tzlist = DateTimeZone::listIdentifiers();
                                        foreach ($tzlist as $tz) {
                                            ?>
                                            <option value="<?= $tz; ?>" <?= ($config->site->timezone === $tz) ? 'selected="selected"' : false; ?>><?= $tz; ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-lat">Latitude</label>
                                </div>
                                <div class="col">
                                    <input type="number" step=".001" class="form-control"
                                           name="site[lat]" id="site-lat" aria-describedby="lat-help"
                                           placeholder="Station Latitude" max="90" min="-90"
                                           value="<?= $config->site->lat; ?>" required>
                                    <small id="lat-help" class="form-text text-muted">Decimal Format (Eg.
                                        39.764)</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-long">Longitude</label>
                                </div>
                                <div class="col">
                                    <input type="number" step=".001" class="form-control"
                                           name="site[long]" id="site-long" aria-describedby="long-help"
                                           placeholder="Station Longitude" max="180" min="-180"
                                           value="<?= $config->site->long; ?>" required>
                                    <small id="long-help" class="form-text text-muted">Decimal Format (Eg. -104.995)
                                    </small>
                                </div>
                            </div>
                            <?php
                            $themes = str_replace('.css', '',
                                preg_grep('/^(.{0,3}|.*(?!base)(?!\.min).{4})\.css$/',
                                    array_map('basename',
                                        glob(APP_BASE_PATH . '/pub/themes/*.{css}', GLOB_BRACE))));
                            ?>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="site-theme">Theme</label>
                                </div>
                                <div class="col">
                                    <select name="site[theme]" id="site-theme"
                                            class="form-select"
                                            required>
                                        <option value="" disabled>Select Theme</option>
                                        <?php
                                        foreach ($themes as $theme) {
                                            ?>
                                            <option value="<?= $theme; ?>" <?= ($config->site->theme === $theme) ? 'selected="selected"' : false; ?>><?= ucfirst($theme); ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Display -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>Display Formats</h3>
                            <p>By default, readings are displayed in Metric with Imperial in brackets. Eg. 0℃ (32℉)</p>
                        </div>
                    </div>

                    <!-- Primary -->
                    <section class="row mb-3">
                        <div class="col">
                            <h4>Primary Display Format</h4>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[imperial]"
                                       id="station-imperial-0" value="0"
                                    <?= ($config->site->imperial === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-imperial-0"><strong>Metric</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[imperial]"
                                       id="station-imperial-1" value="1"
                                    <?= ($config->site->imperial === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary"
                                       for="station-imperial-1">Imperial</label>
                            </div>
                        </div>
                    </section>

                    <!-- Alternate -->
                    <section class="row mb-3">
                        <div class="col">
                            <h4>Hide Alternate Readings</h4>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-0"
                                       value="false"
                                    <?= ($config->site->hide_alternate === 'false') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="site-hide-alt-0"><strong>Disabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-1"
                                       value="true"
                                    <?= ($config->site->hide_alternate === 'true') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="site-hide-alt-1">Enabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-2"
                                       value="live"
                                    <?= ($config->site->hide_alternate === 'live') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="site-hide-alt-2">Live</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-3"
                                       value="archive"
                                    <?= ($config->site->hide_alternate === 'archive') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="site-hide-alt-3">Archive</label>
                            </div>
                        </div>
                    </section>

                    <!-- Date Formats -->
                    <section class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h4>Date/Time</h4>
                                    <p>(See: <a href="https://www.php.net/manual/en/datetime.format.php">PHP Date
                                            Formats</a>)
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <label class="col-form-label" for="header-display-date">Live</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="site[display_date]"
                                           id="header-display-date"
                                           aria-describedby="header-display-date-help"
                                           placeholder="l, j F Y G:i:s T" maxlength="25"
                                           value="<?= $config->site->display_date; ?>" required>
                                    <small id="header-display-date-help" class="form-text text-muted">Default =
                                        <code>l, j F Y G:i:s T</code></small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="dashboard-display-time">Time</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="site[dashboard_display_time]"
                                           id="dashboard-display-time"
                                           aria-describedby="dashboard-display-time-help"
                                           placeholder="H:i" maxlength="10"
                                           value="<?= $config->site->dashboard_display_time; ?>" required>
                                    <small id="dashboard-display-time-help" class="form-text text-muted">Default =
                                        <code>H:i</code></small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="dashboard-display-date">Short Date</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="site[dashboard_display_date]"
                                           id="dashboard-display-date"
                                           aria-describedby="dashboard-display-date-help"
                                           placeholder="j M @ H:i" maxlength="15"
                                           value="<?= $config->site->dashboard_display_date; ?>" required>
                                    <small id="dashboard-display-date-help" class="form-text text-muted">Default =
                                        <code>j M @
                                            H:i</code></small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="dashboard-display-date_full">Full
                                        Date</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="site[dashboard_display_date_full]"
                                           id="dashboard-display-date_full"
                                           aria-describedby="dashboard-display-date-full-help"
                                           placeholder="j M Y @ H:i" maxlength="20"
                                           value="<?= $config->site->dashboard_display_date_full; ?>" required>
                                    <small id="dashboard-display-date-full-help" class="form-text text-muted">Default
                                        =
                                        <code>j M Y @ H:i</code></small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label class="col-form-label" for="date_api_json">JSON API</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           name="site[date_api_json]"
                                           id="date_api_json"
                                           aria-describedby="date-api-json-help"
                                           placeholder="c" maxlength="25"
                                           value="<?= $config->site->date_api_json; ?>" required>
                                    <small id="date-api-json-help" class="form-text text-muted">Default =
                                        <code>c</code></small>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>

            <hr class="hr-dotted">

            <!-- Updates -->
            <section class="row alert alert-secondary">
                <div class="col mt-2 mb-2">
                    <div class="row">
                        <div class="col">
                            <h3>Check for Updates</h3>
                            <p>Checking for updates sends a small <a target="_blank"
                                                                     href="https://docs.acuparse.com/other/TELEMETRY">telemetry
                                    packet</a> to Acuparse servers.<br>This data provides essential insight on
                                the projects user base.</p>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="site[updates]"
                                       id="site-updates-0" value="1"
                                    <?= ($config->site->updates === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-success"
                                       for="site-updates-0"><strong>Enabled</strong></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="site[updates]"
                                       id="site-updates-1" value="0"
                                    <?= ($config->site->updates === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-danger"
                                       for="site-updates-1">Disabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <p>View your install data stored on Acuparse servers.</p>
                            <?php
                            if ($config->site->updates === true) { ?>
                                <button type="button" id="telemetry"
                                        class="btn btn-outline-secondary center-block mb-2"
                                        onclick="window.open(
                                                'https://version.acuparse.com/view/<?= $config->version->installHash ?>',
                                                '_blank');">
                                    <i class="fas fa-database" aria-hidden="true"></i> View Telemetry Data
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
