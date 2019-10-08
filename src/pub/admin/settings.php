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
 * File: src/pub/admin/settings.php
 * Modify Config Settings
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {

    // Process the changes
    if (isset($_GET['do'])) {

        // Do some quick input filtering
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Database
        $config->mysql->host = $_POST['mysql']['host'];
        $config->mysql->database = $_POST['mysql']['database'];
        $config->mysql->username = $_POST['mysql']['username'];
        $config->mysql->password = $_POST['mysql']['password'];
        // Check and adjust database trim level
        if ($config->mysql->trim != $_POST['mysql']['trim']) {
            if ($_POST['mysql']['trim'] === '0') {
                // Load the database with the trim schema
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/disable.sql';
                $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
                $schema = shell_exec($schema);
                $config->mysql->trim = (int)$_POST['mysql']['trim'];
            } elseif ($_POST['mysql']['trim'] === '1') {
                // Load the database with the trim schema
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable.sql';
                $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
                $schema = shell_exec($schema);
                $config->mysql->trim = (int)$_POST['mysql']['trim'];
            } elseif ($_POST['mysql']['trim'] === '2') {
                // Load the database with the trim schema
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable_xtower.sql';
                $schema = "mysql -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema} > /dev/null 2>&1";
                $schema = shell_exec($schema);
                $config->mysql->trim = (int)$_POST['mysql']['trim'];
            }
        }

        // Station
        $config->station->hub_mac = (isset($_POST['station']['hub_mac'])) ? $_POST['station']['hub_mac'] : '';
        $config->station->access_mac = (isset($_POST['station']['access_mac'])) ? $_POST['station']['access_mac'] : '';
        $config->station->primary_sensor = (int)$_POST['station']['primary_sensor'];
        $config->station->sensor_5n1 = (isset($_POST['station']['sensor_5n1'])) ? sprintf('%08d',
            $_POST['station']['sensor_5n1']) : '00000000';
        $config->station->sensor_atlas = (isset($_POST['station']['sensor_atlas'])) ? sprintf('%08d',
            $_POST['station']['sensor_atlas']) : '00000000';
        $config->station->baro_offset = (isset($_POST['station']['baro_offset'])) ? (float)$_POST['station']['baro_offset'] : 0;
        $config->station->baro_source = (int)$_POST['station']['baro_source'];
        $config->station->towers = (bool)$_POST['station']['towers'];
        $config->station->lightning_source = (int)$_POST['station']['lightning_source'];

        // Site
        $config->site->name = $_POST['site']['name'];
        $config->site->desc = $_POST['site']['desc'];
        $config->site->location = $_POST['site']['location'];
        $config->site->hostname = $_POST['site']['hostname'];
        $config->site->email = $_POST['site']['email'];
        $config->site->timezone = $_POST['site']['timezone'];
        $config->site->display_date = $_POST['site']['display_date'];
        $config->site->lat = (float)$_POST['site']['lat'];
        $config->site->long = (float)$_POST['site']['long'];
        $config->site->imperial = (bool)$_POST['site']['imperial'];
        $config->site->hide_alternate = $_POST['site']['hide_alternate'];
        $config->site->theme = $_POST['site']['theme'];
        $config->site->updates = (bool)$_POST['site']['updates'];

        // Webcam
        $config->camera->enabled = (bool)$_POST['camera']['enabled'];
        $config->camera->text = (isset($_POST['camera']['text'])) ? $_POST['camera']['text'] : 'Image updated every X minutes.';

        // Archive
        $config->archive->enabled = (bool)$_POST['archive']['enabled'];

        // Contact
        $config->contact->enabled = (bool)$_POST['contact']['enabled'];

        // Email Outage Alerts
        $config->outage_alert->enabled = (bool)$_POST['outage_alert']['enabled'];
        $config->outage_alert->offline_for = (isset($_POST['outage_alert']['offline_for'])) ? $_POST['outage_alert']['offline_for'] : '5 minutes';
        $config->outage_alert->interval = (isset($_POST['outage_alert']['interval'])) ? $_POST['outage_alert']['interval'] : '1 hour';

        // Google

        // reCAPTCHA
        $config->google->recaptcha->enabled = (bool)$_POST['google']['recaptcha']['enabled'];
        $config->google->recaptcha->secret = (isset($_POST['google']['recaptcha']['secret'])) ? $_POST['google']['recaptcha']['secret'] : null;
        $config->google->recaptcha->sitekey = (isset($_POST['google']['recaptcha']['sitekey'])) ? $_POST['google']['recaptcha']['sitekey'] : null;

        // Analytics
        $config->google->analytics->enabled = (bool)$_POST['google']['analytics']['enabled'];
        $config->google->analytics->id = (isset($_POST['google']['analytics']['id'])) ? $_POST['google']['analytics']['id'] : null;

        // Uploader

        // Master Sensor
        $config->upload->sensor->external = $_POST['upload']['sensor']['external'];
        $config->upload->sensor->id = (isset($_POST['upload']['sensor']['id'])) ? $_POST['upload']['sensor']['id'] : null;
        $config->upload->sensor->archive = (isset($_POST['upload']['sensor']['archive'])) ? (bool)$_POST['upload']['sensor']['archive'] : false;

        // WU
        $config->upload->wu->enabled = (bool)$_POST['upload']['wu']['enabled'];
        $config->upload->wu->id = (isset($_POST['upload']['wu']['id'])) ? $_POST['upload']['wu']['id'] : null;
        $config->upload->wu->password = (isset($_POST['upload']['wu']['password'])) ? $_POST['upload']['wu']['password'] : null;
        $config->upload->wu->url = (isset($_POST['upload']['wu']['url'])) ? $_POST['upload']['wu']['url'] : null;

        // PWS
        $config->upload->pws->enabled = (bool)$_POST['upload']['pws']['enabled'];
        $config->upload->pws->id = (isset($_POST['upload']['pws']['id'])) ? $_POST['upload']['pws']['id'] : null;
        $config->upload->pws->password = (isset($_POST['upload']['pws']['password'])) ? $_POST['upload']['pws']['password'] : null;
        $config->upload->pws->url = (isset($_POST['upload']['pws']['url'])) ? $_POST['upload']['pws']['url'] : null;

        // CWOP
        $config->upload->cwop->enabled = (bool)$_POST['upload']['cwop']['enabled'];
        $config->upload->cwop->id = (isset($_POST['upload']['cwop']['id'])) ? $_POST['upload']['cwop']['id'] : null;
        $config->upload->cwop->location = (isset($_POST['upload']['cwop']['location'])) ? $_POST['upload']['cwop']['location'] : null;
        $config->upload->cwop->interval = (isset($_POST['upload']['cwop']['interval'])) ? $_POST['upload']['cwop']['interval'] : '10 minutes';
        $config->upload->cwop->url = (isset($_POST['upload']['cwop']['url'])) ? $_POST['upload']['cwop']['url'] : null;

        // WC
        $config->upload->wc->enabled = (bool)$_POST['upload']['wc']['enabled'];
        $config->upload->wc->id = (isset($_POST['upload']['wc']['id'])) ? $_POST['upload']['wc']['id'] : null;
        $config->upload->wc->key = (isset($_POST['upload']['wc']['key'])) ? $_POST['upload']['wc']['key'] : null;
        $config->upload->wc->device = (isset($_POST['upload']['wc']['device'])) ? $_POST['upload']['wc']['device'] : null;
        $config->upload->wc->url = (isset($_POST['upload']['wc']['url'])) ? $_POST['upload']['wc']['url'] : null;

        // Windy
        $config->upload->windy->enabled = (bool)$_POST['upload']['windy']['enabled'];
        $config->upload->windy->id = (isset($_POST['upload']['windy']['id'])) ? $_POST['upload']['windy']['id'] : null;
        $config->upload->windy->key = (isset($_POST['upload']['windy']['key'])) ? $_POST['upload']['windy']['key'] : null;

        // Generic
        $config->upload->generic->enabled = (bool)$_POST['upload']['generic']['enabled'];
        $config->upload->generic->id = (isset($_POST['upload']['generic']['id'])) ? $_POST['upload']['generic']['id'] : null;
        $config->upload->generic->password = (isset($_POST['upload']['generic']['password'])) ? $_POST['upload']['generic']['password'] : null;
        $config->upload->generic->url = (isset($_POST['upload']['generic']['url'])) ? $_POST['upload']['generic']['url'] : null;

        // MyAcurite
        $config->upload->myacurite->access_enabled = (bool)$_POST['upload']['myacurite']['access_enabled'];
        $config->upload->myacurite->access_url = $_POST['upload']['myacurite']['access_url'];
        $config->upload->myacurite->pass_unknown = (bool)$_POST['upload']['myacurite']['pass_unknown'];

        // Debug
        $config->debug->logging = (bool)$_POST['debug']['logging'];
        $config->debug->server->enabled = (bool)(isset($_POST['debug']['server']['enabled'])) ? (bool)$_POST['debug']['server']['enabled'] : 0;
        $config->debug->server->url = (isset($_POST['debug']['server']['url'])) ? $_POST['debug']['server']['url'] : null;

        // Save the config file
        $export = var_export($config, true);
        $export = str_ireplace('stdClass::__set_state', '(object)', $export);
        $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');
        if ($save !== false) {
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Site configuration saved successfully");
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Configuration saved successfully!</div>';
            header("Location: /admin");
            die();
        } else {
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Saving configuration failed");
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving configuration failed!</div>';
            header("Location: /admin");
            die();
        }
    } // Show the change form
    else {
        $pageTitle = 'Global System Configuration';
        include(APP_BASE_PATH . '/inc/header.php');
        ?>

        <div class="row">
            <div class="col">
                <h2 class="page-header">Global System Configuration</h2>
            </div>
        </div>

        <hr>

        <section id="modify-settings" class="row modify-settings">
            <div class="col">

                <!-- Modify Settings Navigation -->
                <nav>
                    <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-site-tab" data-toggle="tab" href="#nav-site"
                           role="tab" aria-controls="nav-site" aria-selected="true">Site</a>
                        <a class="nav-item nav-link" id="nav-sensor-tab" data-toggle="tab" href="#nav-sensor" role="tab"
                           aria-controls="nav-sensor" aria-selected="false">Sensor</a>
                        <a class="nav-item nav-link" id="nav-features-tab" data-toggle="tab" href="#nav-features"
                           role="tab" aria-controls="nav-features" aria-selected="false">Features</a>
                        <a class="nav-item nav-link" id="nav-upload-tab" data-toggle="tab" href="#nav-upload"
                           role="tab" aria-controls="nav-upload" aria-selected="false">Upload</a>
                        <a class="nav-item nav-link" id="nav-database-tab" data-toggle="tab" href="#nav-database"
                           role="tab" aria-controls="nav-database" aria-selected="false">Database</a>
                        <?php if ($config->debug->server->show === true) { ?><a class="nav-item nav-link"
                                                                                id="nav-debug-tab" data-toggle="tab"
                                                                                href="#nav-debug"
                                                                                role="tab" aria-controls="nav-debug"
                                                                                aria-selected="false">Debug</a> <?php }; ?>
                    </div>
                </nav>
                <form action="/admin/settings?do" method="POST">
                    <!-- Content Tabs-->
                    <div class="tab-content margin-top-15" id="nav-tabContent">

                        <!-- Site Settings -->
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
                                                <label class="col-form-label" for="site-display-date">Date/Time
                                                    Format:</label>
                                                <input type="text" class="form-control" name="site[display_date]"
                                                       id="site-display-date" aria-describedby="date-help"
                                                       placeholder="l, j F Y G:i:s T" maxlength="32"
                                                       value="<?= $config->site->display_date; ?>" required>
                                                <small id="date-help" class="form-text text-muted">See: <a
                                                            href="http://php.net/manual/en/function.date.php">PHP
                                                        Date</a> (Default = l, j F Y G:i:s T)
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
                                                <h2>Display Format:</h2>
                                                <p>By default, readings are displayed in Metric with Imperial in
                                                    brackets. Eg. 0℃ (32℉)</p>
                                                <div class="form-group">
                                                    <p><strong>Primary Display Format:</strong></p>
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
                                                <p><strong>Check for updates?</strong></p>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END: Site Settings -->

                        <!-- Sensor Settings -->
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
                                            <h3>MAC Addresses:</h3>
                                            <p class="alert alert-info">Enter the addresses for your devices below.
                                                Only one is required, enter only the MAC's you wish to store readings
                                                from.</p>
                                            <div class="form-group">
                                                <label class="col-form-label" for="station-access-mac">Access:</label>
                                                <input type="text" class="form-control" name="station[access_mac]"
                                                       id="station-access-mac" placeholder="Access MAC"
                                                       maxlength="12"
                                                       value="<?= $config->station->access_mac; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label" for="station-hub-mac">smartHUB:</label>
                                                <input type="text" class="form-control" name="station[hub_mac]"
                                                       id="station-hub-mac" placeholder="smartHUB MAC"
                                                       maxlength="12"
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
                                                    <p><strong>Barometer Source</strong></p>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[baro_source]" id="station-baro-source-0"
                                                               value="0"
                                                            <?= ($config->station->baro_source === 0) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-success"
                                                               for="station-baro-source-0">Default</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[baro_source]" id="station-baro-source-1"
                                                               value="1"
                                                            <?= ($config->station->baro_source === 1) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-warning"
                                                               for="station-baro-source-1">Hub</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[baro_source]" id="station-baro-source-2"
                                                               value="2"
                                                            <?= ($config->station->baro_source === 2) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-warning"
                                                               for="station-baro-source-2">Access</label>
                                                    </div>
                                                    <small id="station-baro-source-help" class="form-text text-muted">Which
                                                        device will report barometer readings? Default will save
                                                        readings
                                                        from all devices. Using multiple devices can result in skewed
                                                        readings!
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-12 mx-auto">
                                                <div class="form-group">
                                                    <p><strong>Barometer Offset</strong></p>
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
                                                               id="station-towers-0" value="1"
                                                            <?= ($config->station->towers === true) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-success"
                                                               for="station-towers-0">Enabled</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[towers]"
                                                               id="station-towers-1" value="0"
                                                            <?= ($config->station->towers === false) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-danger"
                                                               for="station-towers-1">Disabled</label>
                                                    </div>
                                                    <small id="station-towers-help"
                                                           class="form-text text-muted">Enable Tower Sensors?
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-12 mx-auto">
                                            <hr>
                                            <div class="col-md-8 col-12 mx-auto">
                                                <div class="form-group">
                                                    <p><strong>Lightning Source</strong></p>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[lightning_source]"
                                                               id="station-lightning-source-0"
                                                               value="0"
                                                            <?= ($config->station->lightning_source === 0) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-success"
                                                               for="station-lightning-source-0">None</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[lightning_source]"
                                                               id="station-lightning-source-1"
                                                               value="1"
                                                            <?= ($config->station->lightning_source === 1) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-warning"
                                                               for="station-lightning-source-1">Atlas</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="station[lightning_source]"
                                                               id="station-lightning-source-2"
                                                               value="2"
                                                            <?= ($config->station->lightning_source === 2) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-warning"
                                                               for="station-lightning-source-2">Tower</label>
                                                    </div>
                                                    <small id="station-lightning-source-help"
                                                           class="form-text text-muted">Which
                                                        device will report lightning readings?
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END: Sensor Settings -->

                        <!-- Feature Settings -->
                        <div class="tab-pane fade" id="nav-features" role="tabpanel"
                             aria-labelledby="nav-features-tab">
                            <div class="row">
                                <div class="col">
                                    <h2 class="panel-heading">Additional Pages</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-12 mx-auto">
                                    <div class="row">
                                        <div class="col border">
                                            <h3>Camera:</h3>
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="camera[enabled]"
                                                           id="camera-enabled-1" value="1"
                                                           onclick='document.getElementById("camera-text").disabled=false;'
                                                        <?= ($config->camera->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="camera-enabled-1">Enabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="camera[enabled]"
                                                           id="camera-enabled-0" value="0"
                                                           onclick='document.getElementById("camera-text").disabled=true;'
                                                        <?= ($config->camera->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="camera-enabled-0">Disabled</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <label class="col-form-label" for="camera-text">Image Text:</label>
                                                <div class="col form-group">
                                                    <input type="text" class="form-control" name="camera[text]"
                                                           id="camera-text"
                                                        <?= ($config->camera->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                           placeholder="Image updated every XX minutes."
                                                           value="<?= $config->camera->text; ?>">
                                                    <small id="camera-text-help" class="form-text text-muted">Text
                                                        under
                                                        live camera image.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <h3>Archive:</h3>
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="archive[enabled]"
                                                           id="archive-enabled-1" value="1"
                                                        <?= ($config->archive->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="archive-enabled-1">Enabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="archive[enabled]"
                                                           id="archive-enabled-0" value="0"
                                                        <?= ($config->archive->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="archive-enabled-0">Disabled</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <h3>Contact:</h3>
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="contact[enabled]"
                                                           id="contact-enabled-1" value="1"
                                                        <?= ($config->archive->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="contact-enabled-1">Enabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="contact[enabled]"
                                                           id="contact-enabled-0" value="0"
                                                        <?= ($config->contact->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="contact-enabled-0">Disabled</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="hr-dotted">

                            <div class="row">
                                <div class="col-md-8 col-12 mx-auto">
                                    <div class="row">
                                        <div class="col">
                                            <h2 class="panel-heading">Outage Alerts</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="outage_alert[enabled]"
                                                           id="outage-alert-enabled-1" value="1"
                                                           onclick='document.getElementById("outage-alert-offline-for").disabled=false;document.getElementById("outage-alert-interval").disabled=false;'
                                                        <?= ($config->outage_alert->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="outage-alert-enabled-1">Enabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="outage_alert[enabled]"
                                                           id="outage-alert-enabled-0" value="0"
                                                           onclick='document.getElementById("outage-alert-offline-for").disabled=true;document.getElementById("outage-alert-interval").disabled=true;'
                                                        <?= ($config->outage_alert->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="outage-alert-enabled-0">Disabled</label>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col form-group">
                                                    <label class="col-form-label" for="outage-alert-offline-for">Offline
                                                        For:</label>
                                                    <select name="outage_alert[offline_for]"
                                                            id="outage-alert-offline-for"
                                                        <?= ($config->outage_alert->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                            class="form-control">
                                                        <?php
                                                        foreach ($config->intervals as $interval) { ?>
                                                            <option value="<?= $interval; ?>" <?= ($config->outage_alert->offline_for === $interval) ? 'selected="selected"' : false; ?>><?= $interval; ?></option>
                                                            <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="col form-group">
                                                    <label class="col-form-label" for="outage-alert-interval">Send
                                                        Interval:</label>
                                                    <select name="outage_alert[interval]" id="outage-alert-interval"
                                                            class="form-control">
                                                        <?php
                                                        foreach ($config->intervals as $interval) { ?>
                                                            <option value="<?= $interval; ?>" <?= ($config->outage_alert->interval === $interval) ? 'selected="selected"' : false; ?>><?= $interval; ?></option>
                                                            <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="hr-dotted">
                            <div class="row">
                                <div class="col-md-8 col-12 mx-auto">
                                    <div class="row">
                                        <div class="col">
                                            <h2 class="panel-heading">Google Settings</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-12 border">
                                            <h3 class="panel-heading">Invisible reCAPTCHA</h3>
                                            <div class="form-group">
                                                <h4>Status:</h4>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="google[recaptcha][enabled]"
                                                           id="recaptcha-enabled-0" value="0"
                                                           onclick='document.getElementById("recaptcha-secret").disabled=true;document.getElementById("recaptcha-sitekey").disabled=true;'
                                                        <?= ($config->google->recaptcha->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="recaptcha-enabled-0">Disabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="google[recaptcha][enabled]"
                                                           id="recaptcha-enabled-1" value="1"
                                                           onclick='document.getElementById("recaptcha-secret").disabled=false;document.getElementById("recaptcha-sitekey").disabled=false;'
                                                        <?= ($config->google->recaptcha->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="recaptcha-enabled-1">Enabled</label>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <label class="col-form-label" for="recaptcha-secret">Secret:</label>
                                                <div class="col form-group">
                                                    <input type="text" class="form-control"
                                                           name="google[recaptcha][secret]"
                                                           id="recaptcha-secret"
                                                           placeholder="Secret Key"
                                                        <?= ($config->google->recaptcha->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                           value="<?= $config->google->recaptcha->secret; ?>">
                                                    <small id="recaptcha-secret-help" class="form-text text-muted">
                                                        Your
                                                        <a href="https://www.google.com/recaptcha/admin">reCAPTCHA
                                                            API</a> Secret Key
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <label class="col-form-label" for="recaptcha-sitekey">Site
                                                    Key:</label>
                                                <div class="col form-group">
                                                    <input type="text" class="form-control"
                                                           name="google[recaptcha][sitekey]"
                                                           id="recaptcha-sitekey"
                                                           placeholder="Site Key"
                                                        <?= ($config->google->recaptcha->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                           value="<?= $config->google->recaptcha->sitekey; ?>">
                                                    <small id="recaptcha-sitekey-help" class="form-text text-muted">
                                                        Your
                                                        <a href="https://www.google.com/recaptcha/admin">reCAPTCHA
                                                            API</a> Site Key
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12 border">
                                            <h3 class="panel-heading">Analytics</h3>
                                            <div class="form-group">
                                                <h4>Status:</h4>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="google[analytics][enabled]"
                                                           onclick='document.getElementById("analytics-id").disabled=true;'
                                                           id="analytics-enabled-0" value="0"
                                                        <?= ($config->google->analytics->enabled === false) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-danger"
                                                           for="analytics-enabled-0">Disabled</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="google[analytics][enabled]"
                                                           onclick='document.getElementById("analytics-id").disabled=false;'
                                                           id="analytics-enabled-1" value="1"
                                                        <?= ($config->google->analytics->enabled === true) ? 'checked="checked"' : false; ?>>
                                                    <label class="form-check-label alert alert-success"
                                                           for="analytics-enabled-1">Enabled</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <label class="col-form-label" for="analytics-id">ID:</label>
                                                <div class="col form-group">
                                                    <input type="text" class="form-control"
                                                           name="google[analytics][id]"
                                                           id="analytics-id"
                                                           placeholder="Analytics ID"
                                                        <?= ($config->google->analytics->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                           value="<?= $config->google->analytics->id ?>">
                                                    <small id="analytics-id-help" class="form-text text-muted">
                                                        Your
                                                        <a href="https://analytics.google.com/analytics/web/">Google
                                                            Analytics</a> tracking ID.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="hr-dotted">

                            <div class="row">
                                <div class="col-md-8 col-12 mx-auto">
                                    <div class="row">
                                        <div class="col">
                                            <h2 class="panel-heading">Debug Logging</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="debug[logging]"
                                                               id="debug-logging-enabled-1" value="1"
                                                            <?= ($config->debug->logging === true) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-success"
                                                               for="debug-logging-enabled-1">Enabled</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="debug[logging]"
                                                               id="debug-logging-enabled-0" value="0"
                                                            <?= ($config->debug->logging === false) ? 'checked="checked"' : false; ?>>
                                                        <label class="form-check-label alert alert-danger"
                                                               for="debug-logging-enabled-0">Disabled</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Settings -->
                        <div class="tab-pane fade" id="nav-upload" role="tabpanel"
                             aria-labelledby="nav-upload-tab">
                            <div class="row">
                                <div class="col">
                                    <h2 class="panel-heading">Upload Settings</h2>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Master Temp Sensor -->
                                <div class="col-md-6 col-12 border alert alert-secondary">
                                    <h3 class="panel-heading">Master Temp/Humidity Sensor</h3>
                                    <p>Choose the main sensor used when uploading Temp/Humidity data to
                                        3rd party sites. This does not affect the main dashboard.</p>
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[sensor][external]"
                                                   onclick='document.getElementById("station-updates-sensor-id").disabled=true;document.getElementById("station-updates-sensor-archive-0").disabled=true;document.getElementById("station-updates-sensor-archive-1").disabled=true;'
                                                   id="station-updates-sensor-0" value="default"
                                                <?= ($config->upload->sensor->external === 'default') ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="station-updates-sensor-0">Primary</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[sensor][external]"
                                                   onclick='document.getElementById("station-updates-sensor-id").disabled=false;document.getElementById("station-updates-sensor-archive-0").disabled=false;document.getElementById("station-updates-sensor-archive-1").disabled=false;'
                                                   id="station-updates-sensor-1" value="tower"
                                                <?= ($config->upload->sensor->external === 'tower') ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-warning"
                                                   for="station-updates-sensor-1">Tower</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="station-updates-sensor-id">Tower ID:</label>
                                        <div class="col form-group">
                                            <select name="upload[sensor][id]"
                                                    id="station-updates-sensor-id"
                                                <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                                    class="form-control">
                                                <option value="">&nbsp;</option>
                                                <?php
                                                $result = mysqli_query($conn,
                                                    "SELECT * FROM `towers` ORDER BY `arrange` ASC");
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                    <option value="<?= $row['sensor']; ?>" <?= ($config->upload->sensor->id === $row['sensor']) ? 'selected="selected"' : false; ?>><?= $row['sensor'] . ' - ' . $row['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h4>Use for Archive?</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[sensor][archive]"
                                                <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                                   id="station-updates-sensor-archive-0" value="0"
                                                <?= ($config->upload->sensor->archive === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="station-updates-sensor-archive-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="upload[sensor][archive]"
                                                    <?= ($config->upload->sensor->external === 'default') ? 'disabled="disabled"' : false; ?>
                                                       id="station-updates-sensor-archive-1" value="1"
                                                    <?= ($config->upload->sensor->archive === true) ? 'checked="checked"' : false; ?>>
                                                <label class="form-check-label alert alert-success"
                                                       for="station-updates-sensor-archive-1">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- MyAcuRite -->
                                <div class="col-md-6 col-12 border alert">
                                    <h3 class="panel-heading">MyAcuRite</h3>
                                    <div class="form-group">
                                        <h4>Access Upload:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[myacurite][access_enabled]"
                                                   id="myacurite-access-enabled-1" value="1"
                                                   onclick='document.getElementById("myacurite-access-url").disabled=false;'
                                                <?= ($config->upload->myacurite->access_enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="myacurite-access-enabled-1">Enabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[myacurite][access_enabled]"
                                                   id="myacurite-access-enabled-0" value="0"
                                                   onclick='document.getElementById("myacurite-access-url").disabled=true;'
                                                <?= ($config->upload->myacurite->access_enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="myacurite-access-enabled-0">Disabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h4>Unknown Sensors:</h4>
                                        <p>Send unknown sensor data?<br>
                                            <span class="small text-danger">Can include neighbours/noise and is generally not recommend.</span>
                                        </p>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[myacurite][pass_unknown]"
                                                   id="myacurite-pass-unknown-0" value="0"
                                                   onclick='document.getElementById("myacurite-pass-unknown").disabled=true;'
                                                <?= ($config->upload->myacurite->pass_unknown === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="myacurite-pass-unknown-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[myacurite][pass_unknown]"
                                                   id="myacurite-pass-unknown-1" value="1"
                                                   onclick='document.getElementById("myacurite-pass-unknown").disabled=false;'
                                                <?= ($config->upload->myacurite->pass_unknown === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="myacurite-pass-unknown-1">Enabled</label>
                                        </div>
                                    </div>
                                    <hr class="hr-dashed">
                                    <h4>Upload URL:</h4>
                                    <div class="row">
                                        <div class="col">
                                            <p class="alert-info">If installed on the same network as your device,
                                                use secondary. See <code>docs/DNS.md</code></p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <select name="upload[myacurite][access_url]"
                                                    id="myacurite-access-url"
                                                    class="form-control">
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

                            <hr class="hr">
                            <div class="row">
                                <div class="col">
                                    <h2 class="panel-heading">External Providers</h2>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Weather Underground -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">Weather Underground</h3>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[wu][enabled]"
                                                   id="wu-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("wu-updates-id").disabled=true;document.getElementById("wu-updates-password").disabled=true;'
                                                <?= ($config->upload->wu->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="wu-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[wu][enabled]"
                                                   id="wu-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("wu-updates-id").disabled=false;document.getElementById("wu-updates-password").disabled=false;'
                                                <?= ($config->upload->wu->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="wu-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="wu-updates-id">Station ID:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label"
                                               for="wu-updates-password">Password:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label" for="wu-updates-url">URL:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[wu][url]"
                                                   id="wu-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->wu->url; ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- PWS Weather -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">PWS Weather</h3>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[pws][enabled]"
                                                   id="pws-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("pws-updates-id").disabled=true;document.getElementById("pws-updates-password").disabled=true;'
                                                <?= ($config->upload->pws->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="pws-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[pws][enabled]"
                                                   id="pws-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("pws-updates-id").disabled=false;document.getElementById("pws-updates-password").disabled=false;'
                                                <?= ($config->upload->pws->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="pws-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="pws-updates-id">Station ID:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][id]"
                                                   id="pws-updates-id"
                                                   maxlength="15"
                                                   placeholder="PWS Station ID"
                                                <?= ($config->upload->pws->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->pws->id; ?>">
                                            <small id="pws-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://www.pwsweather.com">PWS</a>
                                                Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label"
                                               for="pws-updates-password">Password:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][password]"
                                                   id="pws-updates-password"
                                                   placeholder="PWS Password"
                                                   maxlength="35"
                                                <?= ($config->upload->pws->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->pws->password; ?>">
                                            <small id="pws-updates-password-help" class="form-text text-muted">
                                                Your <a
                                                        href="https://www.pwsweather.com">PWS</a>
                                                Password
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="pws-updates-url">URL:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[pws][url]"
                                                   id="pws-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->pws->url; ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- CWOP -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">CWOP</h3>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[cwop][enabled]"
                                                   id="cwop-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("cwop-updates-id").disabled=true;document.getElementById("cwop-updates-interval").disabled=true;document.getElementById("cwop-updates-location").disabled=true;'
                                                <?= ($config->upload->cwop->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="cwop-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[cwop][enabled]"
                                                   id="cwop-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("cwop-updates-id").disabled=false;document.getElementById("cwop-updates-interval").disabled=false;document.getElementById("cwop-updates-location").disabled=false;'
                                                <?= ($config->upload->cwop->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="cwop-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="cwop-updates-id">Station ID:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[cwop][id]"
                                                   id="cwop-updates-id"
                                                   maxlength="15"
                                                   placeholder="CWOP Station ID"
                                                <?= ($config->upload->cwop->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->cwop->id; ?>">
                                            <small id="cwop-updates-id-help" class="form-text text-muted">Your <a
                                                        href="http://www.wxqa.com/SIGN-UP.html">CWOP</a>
                                                Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <label class="col-form-label form-check-label"
                                                   for="cwop-updates-interval">Interval:</label>
                                            <select name="upload[cwop][interval]"
                                                    id="cwop-updates-interval"
                                                <?= ($config->upload->cwop->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                    class="form-control">
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
                                    <div class="form-row">
                                        <label class="col-form-label"
                                               for="cwop-updates-location">Location:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label" for="cwop-updates-url">URL:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[cwop][url]"
                                                   id="cwop-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->cwop->url; ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- Weathercloud -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">Weathercloud</h3>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[wc][enabled]"
                                                   id="wc-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("wc-updates-id").disabled=true;document.getElementById("wc-updates-key").disabled=true;document.getElementById("wc-updates-device").disabled=true;'
                                                <?= ($config->upload->wc->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="wc-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[wc][enabled]"
                                                   id="wc-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("wc-updates-id").disabled=false;document.getElementById("wc-updates-key").disabled=false;document.getElementById("wc-updates-device").disabled=false;'
                                                <?= ($config->upload->wc->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="wc-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="wc-updates-id">ID:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label"
                                               for="wc-updates-key">Key:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label" for="wc-updates-device">Device:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label" for="wc-updates-url">URL:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[wc][url]"
                                                   id="wc-updates-url"
                                                   readonly
                                                   value="<?= $config->upload->wc->url; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="hr">

                            <div class="row">
                                <!-- Windy Upload -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">Windy</h3>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[windy][enabled]"
                                                   id="windy-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("windy-updates-id").disabled=true;document.getElementById("windy-updates-key").disabled=true;'
                                                <?= ($config->upload->windy->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="windy-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[windy][enabled]"
                                                   id="windy-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("windy-updates-id").disabled=false;document.getElementById("windy-updates-key").disabled=false;'
                                                <?= ($config->upload->windy->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="windy-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="windy-updates-id">ID:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[windy][id]"
                                                   id="windy-updates-id"
                                                   maxlength="35"
                                                   placeholder="ID"
                                                <?= ($config->upload->windy->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->windy->id; ?>">
                                            <small id="wc-updates-id-help" class="form-text text-muted">Your <a
                                                        href="https://stations.windy.com/stations">Windy</a>
                                                Station ID
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="windy-updates-key">API Key:</label>
                                        <div class="col form-group">
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
                                </div>

                                <!-- Generic Upload -->
                                <div class="col-md-6 col-12 border">
                                    <h3 class="panel-heading">Generic Update Server</h3>
                                    <p>Sends data in wunderground format to any compatible provider.</p>
                                    <div class="form-group">
                                        <h4>Status:</h4>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[generic][enabled]"
                                                   id="generic-updates-enabled-0" value="0"
                                                   onclick='document.getElementById("generic-updates-id").disabled=true;document.getElementById("generic-updates-password").disabled=true;'
                                                <?= ($config->upload->generic->enabled === false) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="generic-updates-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="upload[generic][enabled]"
                                                   id="generic-updates-enabled-1" value="1"
                                                   onclick='document.getElementById("generic-updates-id").disabled=false;document.getElementById("generic-updates-password").disabled=false;'
                                                <?= ($config->upload->generic->enabled === true) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="generic-updates-enabled-1">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="generic-updates-id">Station ID:</label>
                                        <div class="col form-group">
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
                                    <div class="form-row">
                                        <label class="col-form-label"
                                               for="generic-updates-password">Password:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[generic][password]"
                                                   id="generic-updates-password"
                                                   placeholder="Password"
                                                   maxlength="35"
                                                <?= ($config->upload->generic->enabled === false) ? 'disabled="disabled"' : false; ?>
                                                   value="<?= $config->upload->generic->password; ?>">
                                            <small id="generic-updates-password-help" class="form-text text-muted">Your
                                                Password, if required.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="generic-updates-url">URL:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="upload[generic][url]"
                                                   id="generic-updates-url"
                                                   value="<?= $config->upload->generic->url; ?>">
                                        </div>
                                    </div>
                                    <p><strong>Supported Servers:</strong></p>
                                    <ul>
                                        <li>
                                            <a href="https://docs.acuparse.com/external/generic/WeatherPoly">WeatherPoly</a>:
                                            http(s)://{IP/HOSTNAME}:8080/acuparse
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Database Settings -->
                        <div class="tab-pane fade" id="nav-database" role="tabpanel"
                             aria-labelledby="nav-database-tab">
                            <div class="row">
                                <div class="col">
                                    <h2 class="panel-heading">Database Settings</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-12 mx-auto">
                                    <div class="form-row">
                                        <label class="col-form-label" for="mysql-host">Hostname:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="mysql[host]"
                                                   id="mysql-host"
                                                   placeholder="localhost"
                                                   maxlength="35"
                                                   value="<?= $config->mysql->host; ?>">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="mysql-database">Database:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="mysql[database]"
                                                   id="mysql-database"
                                                   placeholder="acuparse"
                                                   maxlength="35"
                                                   value="<?= $config->mysql->database; ?>">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="mysql-username">Username:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="mysql[username]"
                                                   id="mysql-username"
                                                   placeholder="acuparse.dbadmin"
                                                   maxlength="35"
                                                   value="<?= $config->mysql->username; ?>">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="col-form-label" for="mysql-password">Password:</label>
                                        <div class="col form-group">
                                            <input type="text" class="form-control"
                                                   name="mysql[password]"
                                                   id="mysql-password"
                                                   placeholder="Password"
                                                   maxlength="32"
                                                   value="<?= $config->mysql->password; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <p><strong>Database Trimming?</strong></p>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="mysql[trim]"
                                                   id="mysql-trim-enabled-0" value="0"
                                                <?= ($config->mysql->trim === 0) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-danger"
                                                   for="mysql-trim-enabled-0">Disabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="mysql[trim]"
                                                   id="mysql-trim-enabled-1" value="1"
                                                <?= ($config->mysql->trim === 1) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-success"
                                                   for="mysql-trim-enabled-1">Enabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="mysql[trim]"
                                                   id="mysql-trim-enabled-2" value="2"
                                                <?= ($config->mysql->trim === 2) ? 'checked="checked"' : false; ?>>
                                            <label class="form-check-label alert alert-warning"
                                                   for="mysql-trim-enabled-2">Enabled, <strong>EXCEPT</strong>
                                                Towers</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($config->debug->server->show === true) { ?>
                            <!-- Debug Server Settings -->
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
                        <?php } ?>
                    </div>

                    <hr class="hr-dotted">

                    <div class="row">
                        <div class="col">
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <?php
        include(APP_BASE_PATH . '/inc/footer.php');
    }
} // Not logged in or user is not an admin
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
