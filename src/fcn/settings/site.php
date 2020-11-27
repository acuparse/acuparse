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
 * File: src/fcn/settings/site.php
 * Generic Site Settings
 */

/**
 * @return array
 * @var object $config Global Config
 */
?>
<div class="tab-pane fade show active" id="nav-site" role="tabpanel"
     aria-labelledby="nav-site-tab">
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="col">
                    <h2 class="panel-heading">Site Settings</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-12 mx-auto">
                    <div class="form-group">
                        <label class="col-form-label" for="site-name">Name:</label>
                        <input type="text" class="form-control" name="site[name]"
                               id="site-name" placeholder="Station Name" maxlength="32"
                               value="<?= $config->site->name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-desc">Description:</label>
                        <input type="text" class="form-control" name="site[desc]"
                               id="site-desc" placeholder="Station Description"
                               maxlength="100"
                               value="<?= $config->site->desc; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-location">Location:</label>
                        <input type="text" class="form-control" name="site[location]"
                               id="site-location" placeholder="Station Location"
                               maxlength="32"
                               value="<?= $config->site->location; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-hostname">Hostname:</label>
                        <input type="text" class="form-control" name="site[hostname]"
                               id="site-hostname" placeholder="www.example.com"
                               maxlength="32" aria-describedby="hostname-help"
                               value="<?= $config->site->hostname; ?>" required>
                        <small id="hostname-help" class="form-text text-muted">FQDN/IP
                            Address
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-email">Email:</label>
                        <input type="text" class="form-control" name="site[email]"
                               id="site-email" aria-describedby="email-help"
                               placeholder="weather@example.com" maxlength="32"
                               value="<?= $config->site->email; ?>" required>
                        <small id="email-help" class="form-text text-muted">System Email
                            Address (mail from)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-timezone">Timezone:</label>
                        <select name="site[timezone]" id="site-timezone"
                                class="form-control" required>
                            <option value="" disabled>Select Timezone</option>
                            <?php
                            $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                            foreach ($tzlist as $tz) {
                                ?>
                                <option value="<?= $tz; ?>" <?= ($config->site->timezone === $tz) ? 'selected="selected"' : false; ?>><?= $tz; ?></option>
                                <?php
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="header-display-date">Header Date/Time
                            Format:</label>
                        <input type="text" class="form-control" name="site[display_date]"
                               id="header-display-date"
                               aria-describedby="header-display-date-help"
                               placeholder="l, j F Y G:i:s T" maxlength="32"
                               value="<?= $config->site->display_date; ?>" required>
                        <small id="header-display-date-help" class="form-text text-muted">See:
                            <a
                                    href="http://php.net/manual/en/function.date.php">PHP
                                Date</a> (Default = l, j F Y G:i:s T)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="dashboard-display-date">Dashboard
                            Date/Time
                            Format:</label>
                        <input type="text" class="form-control"
                               name="site[dashboard_display_date]"
                               id="dashboard-display-date"
                               aria-describedby="dashboard-display-date-help"
                               placeholder="j M @ H:i" maxlength="32"
                               value="<?= $config->site->dashboard_display_date; ?>" required>
                        <small id="dashboard-display-date-help" class="form-text text-muted">See:
                            <a
                                    href="http://php.net/manual/en/function.date.php">PHP
                                Date</a> (Default = j M @ H:i)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-lat">Latitude:</label>
                        <input type="number" step=".001" class="form-control"
                               name="site[lat]" id="site-lat" aria-describedby="lat-help"
                               placeholder="Station Latitude" max="90" min="-90"
                               value="<?= $config->site->lat; ?>" required>
                        <small id="lat-help" class="form-text text-muted">Decimal Format
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="site-long">Longitude:</label>
                        <input type="number" step=".001" class="form-control"
                               name="site[long]" id="site-long" aria-describedby="long-help"
                               placeholder="Station Longitude" max="180" min="-180"
                               value="<?= $config->site->long; ?>" required>
                        <small id="long-help" class="form-text text-muted">Decimal Format
                        </small>
                    </div>
                    <?php
                    $themes = str_replace('.css', '',
                        preg_grep('/^(.{0,3}|.*(?!base)(?!\.min).{4})\.css$/',
                            array_map('basename',
                                glob(APP_BASE_PATH . '/pub/themes/*.{css}', GLOB_BRACE))));
                    ?>
                    <div class="form-group">
                        <label class="col-form-label" for="site-theme">Theme:</label>
                        <select name="site[theme]" id="site-theme"
                                class="form-control"
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
                    <hr class="hr-dotted">
                    <div class="col border">
                        <h2>Display Format</h2>
                        <p>By default, readings are displayed in Metric with Imperial in
                            brackets. Eg. 0℃ (32℉)</p>
                        <div class="form-group">
                            <p><strong>Primary Display Format</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[imperial]"
                                       id="station-imperial-0" value="0"
                                    <?= ($config->site->imperial === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-success"
                                       for="station-imperial-0">Metric</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[imperial]"
                                       id="station-imperial-1" value="1"
                                    <?= ($config->site->imperial === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="station-imperial-1">Imperial</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <p><strong>Hide Alternate Readings?</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-0"
                                       value="false"
                                    <?= ($config->site->hide_alternate === 'false') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-success"
                                       for="site-hide-alt-0">Disabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-1"
                                       value="true"
                                    <?= ($config->site->hide_alternate === 'true') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-danger"
                                       for="site-hide-alt-1">Enabled</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-2"
                                       value="live"
                                    <?= ($config->site->hide_alternate === 'live') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="site-hide-alt-2">Live</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="site[hide_alternate]" id="site-hide-alt-3"
                                       value="archive"
                                    <?= ($config->site->hide_alternate === 'archive') ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label alert alert-warning"
                                       for="site-hide-alt-3">Archive</label>
                            </div>
                        </div>
                    </div>
                    <hr class="hr-dotted">
                    <div class="form-group">
                        <h2>Check for Updates</h2>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="site[updates]"
                                   id="site-updates-0" value="1"
                                <?= ($config->site->updates === true) ? 'checked="checked"' : false; ?>>
                            <label class="form-check-label alert alert-success"
                                   for="site-updates-0">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="site[updates]"
                                   id="site-updates-1" value="0"
                                <?= ($config->site->updates === false) ? 'checked="checked"' : false; ?>>
                            <label class="form-check-label alert alert-danger"
                                   for="site-updates-1">Disabled</label>
                        </div>
                        <div>
                            <?php
                            if ($config->site->updates === true) { ?>
                                <button type="button" id="telemetry" class="btn btn-outline-secondary center-block"
                                        onclick="window.open(
                                                'https://version.acuparse.com/view/<?= $config->version->installHash ?>',
                                                '_blank'
                                                );">
                                    <i class="fas fa-database" aria-hidden="true"></i> View Telemetry Data
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>