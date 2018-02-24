<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2018 Maxwell Power
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

if (isset($_SESSION['UserLoggedIn']) && $_SESSION['IsAdmin'] === true) {

    // Process the changes
    if (isset($_GET['do'])) {

        // Do some input filtering
        $raw = $_POST;
        array_walk_recursive($raw, function (&$i) {
            $i = filter_var(trim($i), FILTER_SANITIZE_STRING);
        });
        $_POST = $raw;

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
        $config->station->hub_mac = $_POST['station']['hub_mac'];
        $config->station->access_mac = $_POST['station']['access_mac'];
        $config->station->sensor_5n1 = sprintf('%08d', $_POST['station']['sensor_5n1']);
        $config->station->baro_offset = (float)$_POST['station']['baro_offset'];
        $config->station->towers = (bool)$_POST['station']['towers'];

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
        $config->camera->text = $_POST['camera']['text'];

        // Archive
        $config->archive->enabled = (bool)$_POST['archive']['enabled'];

        // Contact
        $config->contact->enabled = (bool)$_POST['contact']['enabled'];

        // Google
        // recaptcha
        $config->google->recaptcha->enabled = (bool)$_POST['google']['recaptcha']['enabled'];
        $config->google->recaptcha->secret = $_POST['google']['recaptcha']['secret'];
        $config->google->recaptcha->sitekey = $_POST['google']['recaptcha']['sitekey'];
        //analytics
        $config->google->analytics->enabled = (bool)$_POST['google']['analytics']['enabled'];
        $config->google->analytics->id = $_POST['google']['analytics']['id'];

        // Uploader
        // WU
        $config->upload->wu->enabled = (bool)$_POST['upload']['wu']['enabled'];
        $config->upload->wu->id = $_POST['upload']['wu']['id'];
        $config->upload->wu->password = $_POST['upload']['wu']['password'];
        $config->upload->wu->url = $_POST['upload']['wu']['url'];
        // PWS
        $config->upload->pws->enabled = (bool)$_POST['upload']['pws']['enabled'];
        $config->upload->pws->id = $_POST['upload']['pws']['id'];
        $config->upload->pws->password = $_POST['upload']['pws']['password'];
        $config->upload->pws->url = $_POST['upload']['pws']['url'];
        // CWOP
        $config->upload->cwop->enabled = (bool)$_POST['upload']['cwop']['enabled'];
        $config->upload->cwop->id = $_POST['upload']['cwop']['id'];
        $config->upload->cwop->location = $_POST['upload']['cwop']['location'];
        $config->upload->cwop->interval = $_POST['upload']['cwop']['interval'];
        $config->upload->cwop->url = $_POST['upload']['cwop']['url'];
        // MyAcurite
        $config->upload->myacurite->hub_enabled = (bool)$_POST['upload']['myacurite']['hub_enabled'];
        $config->upload->myacurite->hub_url = $_POST['upload']['myacurite']['hub_url'];
        $config->upload->myacurite->access_enabled = (bool)$_POST['upload']['myacurite']['access_enabled'];
        $config->upload->myacurite->access_url = $_POST['upload']['myacurite']['access_url'];

        // Email Outage Alerts
        $config->outage_alert->enabled = (bool)$_POST['outage_alert']['enabled'];
        $config->outage_alert->offline_for = $_POST['outage_alert']['offline_for'];
        $config->outage_alert->interval = $_POST['outage_alert']['interval'];

        // Debug
        $config->debug->logging = (bool)$_POST['debug']['logging']['enabled'];
        $config->debug->server->enabled = (bool)$_POST['debug']['server']['enabled'];
        $config->debug->server->url = $_POST['debug']['server']['url'];

        // Save the config file
        $export = var_export($config, true);
        $export = str_ireplace('stdClass::__set_state', '(object)', $export);
        $save = file_put_contents(APP_BASE_PATH . '/usr/config.php', '<?php return ' . $export . ';');
        if ($save !== false) {
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Site configuration saved successfully");
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Configuration saved successfully!</div>';
            header("Location: /admin");
        } else {
            // Log it
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Saving configuration failed");
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving configuration failed!</div>';
            header("Location: /admin");
        }
    } // Show the change form
    else {
        $page_title = 'Modify Configuration | ' . $config->site->name;
        include(APP_BASE_PATH . '/inc/header.php');
        ?>

        <section id="modify_settings" class="modify_settings_display">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Modify System Settings</h2>
                </div>
            </div>
            <form class="form" role="form" action="/admin/settings?do" method="POST">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h2 class="panel-heading">Database Settings:</h2>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_host">Hostname:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="mysql[host]" id="mysql_host"
                                       placeholder="MySQL Hostname" maxlength="32"
                                       value="<?= $config->mysql->host; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_database">Database:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="mysql[database]" id="mysql_database"
                                       placeholder="MySQL Database" maxlength="32"
                                       value="<?= $config->mysql->database; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_username">Username:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="mysql[username]" id="mysql_username"
                                       placeholder="MySQL Username" maxlength="32"
                                       value="<?= $config->mysql->username; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_password">Password:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="mysql[password]" id="mysql_password"
                                       placeholder="MySQL Password" maxlength="32"
                                       value="<?= $config->mysql->password; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="mysql_trim">Database
                                Trimming?</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-danger"><input type="radio" id="mysql_trim"
                                                                             name="mysql[trim]"
                                                                             value="0" <?php if ($config->mysql->trim === 0) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                                <label class="radio-inline bg-success"><input type="radio" id="mysql_trim"
                                                                              name="mysql[trim]"
                                                                              value="1" <?php if ($config->mysql->trim === 1) {
                                        echo 'checked="checked"';
                                    } ?>>Trim All</label>
                                <label class="radio-inline bg-success"><input type="radio" id="mysql_trim"
                                                                              name="mysql[trim]"
                                                                              value="2" <?php if ($config->mysql->trim === 2) {
                                        echo 'checked="checked"';
                                    } ?>>Trim all but towers</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h2 class="panel-heading">Sensor Settings:</h2>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_access_mac">Access
                                MAC:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="station[access_mac]"
                                       id="station_access_mac"
                                       placeholder="Access MAC" maxlength="12"
                                       value="<?= $config->station->access_mac; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_hub_mac">smartHUB
                                MAC:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="station[hub_mac]" id="station_hub_mac"
                                       placeholder="smartHUB MAC" maxlength="12"
                                       value="<?= $config->station->hub_mac; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_sensor_5n1">5N1 Sensor
                                ID:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="station[sensor_5n1]"
                                       id="station_sensor_5n1"
                                       placeholder="5n1 Sensor ID" maxlength="8"
                                       value="<?= $config->station->sensor_5n1; ?>">
                                <p class="bg-info">8 Digits including leading 0's</p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="baro_offset">Barometer
                                Offset:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="number" class="form-control" name="station[baro_offset]"
                                       id="baro_offset" step=".01" placeholder="Barometer Offset"
                                       value="<?= $config->station->baro_offset; ?>">
                                <p class="bg-info">inHg. Adjust this as required to match the offset for your
                                    elevation</p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_towers">Towers
                                Sensors?</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="station_towers"
                                                                              name="station[towers]"
                                                                              value="1" <?php if ($config->station->towers === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="station_towers"
                                                                             name="station[towers]"
                                                                             value="0" <?php if ($config->station->towers === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-lg-block visible-md-block visible-sm-block"></div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h2 class="panel-heading">Site Settings:</h2>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_name">Name:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[name]" id="site_name"
                                       placeholder="Station Name" maxlength="32"
                                       value="<?= $config->site->name; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_desc">Description:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[desc]" id="site_desc"
                                       placeholder="Station Description" maxlength="100"
                                       value="<?= $config->site->desc; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_location">Location:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[location]" id="site_location"
                                       placeholder="Station Location" maxlength="32"
                                       value="<?= $config->site->location; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_hostname">Hostname:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[hostname]" id="site_hostname"
                                       placeholder="Station Hostname" maxlength="32"
                                       value="<?= $config->site->hostname; ?>" required>
                                <p class="bg-info">Station Domain/IP Address</p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_email">Email:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[email]" id="site_email"
                                       placeholder="Station Email" maxlength="32"
                                       value="<?= $config->site->email; ?>" required>
                                <p class="bg-info">System Email Address (mail from)</p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_timezone">Timezone:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <select name="site[timezone]" id="site_timezone" class="form-control" required>
                                    <?php
                                    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                    foreach ($tzlist as $tz) { ?>
                                        <option value="<?= $tz; ?>" <?php if ($config->site->timezone === $tz) {
                                            echo 'selected="selected"';
                                        } ?>><?= $tz; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_display_date">Display
                                Date:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="site[display_date]" id="site_display_date"
                                       placeholder="Header Date Format" maxlength="32"
                                       value="<?= $config->site->display_date; ?>" required>
                                <p class="bg-info"><a href="http://php.net/manual/en/function.date.php">PHP Date</a></p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_lat">Latitude:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="number" step=".001" class="form-control" name="site[lat]" id="site_lat"
                                       placeholder="Station Latitude" max="90" min="-90"
                                       value="<?= $config->site->lat; ?>" required>
                                <p class="bg-info">Decimal Format</p>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_long">Longitude:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="number" step=".001" class="form-control" name="site[long]" id="site_long"
                                       placeholder="Station Longitude" max="180" min="-180"
                                       value="<?= $config->site->long; ?>" required>
                                <p class="bg-info">Decimal Format</p>
                            </div>
                        </div>
                        <?php
                        $themes = scandir(APP_BASE_PATH . '/pub/themes/');
                        $ignore = Array(".", "..");
                        ?>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_theme">Theme:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <select name="site[theme]" id="site_theme" class="form-control" required>
                                    <?php
                                    foreach ($themes as $theme) {
                                        if (!in_array($theme, $ignore)) {
                                            $theme_name = str_replace('.css', '', $theme);
                                            ?>
                                            <option value="<?= $theme_name; ?>" <?php if ($config->site->theme === $theme_name) {
                                                echo 'selected="selected"';
                                            } ?>><?= $theme_name; ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_imperial">Display
                                Format:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-info"><input type="radio" id="station_imperial"
                                                                           name="site[imperial]"
                                                                           value="0" <?php if ($config->site->imperial === false) {
                                        echo 'checked="checked"';
                                    } ?>>Metric</label>
                                <label class="radio-inline bg-info"><input type="radio" id="station_imperial"
                                                                           name="site[imperial]"
                                                                           value="1" <?php if ($config->site->imperial === true) {
                                        echo 'checked="checked"';
                                    } ?>>Imperial</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="station_hide_alternate">Hide
                                Alternate Measurements?</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-danger"><input type="radio" id="station_hide_alternate"
                                                                             name="site[hide_alternate]"
                                                                             value="false" <?php if ($config->site->hide_alternate === 'false') {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                                <label class="radio-inline bg-success"><input type="radio" id="station_hide_alternate"
                                                                              name="site[hide_alternate]"
                                                                              value="true" <?php if ($config->site->hide_alternate === 'true') {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-warning"><input type="radio" id="station_hide_alternate"
                                                                              name="site[hide_alternate]"
                                                                              value="live" <?php if ($config->site->hide_alternate === 'live') {
                                        echo 'checked="checked"';
                                    } ?>>Live</label>
                                <label class="radio-inline bg-warning"><input type="radio" id="station_hide_alternate"
                                                                              name="site[hide_alternate]"
                                                                              value="archive" <?php if ($config->site->hide_alternate === 'archive') {
                                        echo 'checked="checked"';
                                    } ?>>Archive</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="site_updates">Check for
                                updates?</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="site_updates"
                                                                              name="site[updates]"
                                                                              value="1" <?php if ($config->site->updates === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="site_updates"
                                                                             name="site[updates]"
                                                                             value="0" <?php if ($config->site->updates === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h2 class="panel-heading">App Settings:</h2>
                        <h3>Camera Settings:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="camera_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="camera_enabled"
                                                                              name="camera[enabled]"
                                                                              value="1" <?php if ($config->camera->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="camera_enabled"
                                                                             name="camera[enabled]"
                                                                             value="0" <?php if ($config->camera->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="camera_text">Image Text:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="camera[text]" id="camera_text"
                                       placeholder="Display Text" maxlength="32"
                                       value="<?= $config->camera->text; ?>" required>
                            </div>
                        </div>
                        <h3>Archive Settings:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="archive_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="archive_enabled"
                                                                              name="archive[enabled]"
                                                                              value="1" <?php if ($config->archive->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="archive_enabled"
                                                                             name="archive[enabled]"
                                                                             value="0" <?php if ($config->archive->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <h3>Contact Settings:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="contact_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="contact_enabled"
                                                                              name="contact[enabled]"
                                                                              value="1" <?php if ($config->contact->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="contact_enabled"
                                                                             name="contact[enabled]"
                                                                             value="0" <?php if ($config->contact->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <h3>Log Settings:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                   for="debug_logging_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="debug_logging_enabled"
                                                                              name="debug[logging][enabled]"
                                                                              value="1" <?php if ($config->debug->logging === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="debug_logging_enabled"
                                                                             name="debug[logging][enabled]"
                                                                             value="0" <?php if ($config->debug->logging === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                    </div>

                    <!–– Outage Alerts -->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h2 class="panel-heading">Outage Alerts:</h2>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                   for="outage_alert_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="outage_alert_enabled"
                                                                              name="outage_alert[enabled]"
                                                                              value="1" <?php if ($config->outage_alert->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="outage_alert_enabled"
                                                                             name="outage_alert[enabled]"
                                                                             value="0" <?php if ($config->outage_alert->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="outage_alert_offline_for">Offline
                                For:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <select name="outage_alert[offline_for]" id="outage_alert_offline_for"
                                        class="form-control" required>
                                    <?php
                                    foreach ($config->intervals as $interval) { ?>
                                        <option value="<?= $interval; ?>" <?php if ($config->outage_alert->offline_for === $interval) {
                                            echo 'selected="selected"';
                                        } ?>><?= $interval; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="outage_alert_interval">Send
                                Interval:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <select name="outage_alert[interval]" id="outage_alert_interval" class="form-control"
                                        required>
                                    <?php
                                    foreach ($config->intervals as $interval) { ?>
                                        <option value="<?= $interval; ?>" <?php if ($config->outage_alert->interval === $interval) {
                                            echo 'selected="selected"';
                                        } ?>><?= $interval; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!–– Google Settings -->
                <div class="row">
                    <h2 class="panel-heading">Google Settings:</h2>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h3 class="panel-heading">Invisible reCAPTCHA:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="recaptcha_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="recaptcha_enabled"
                                                                              name="google[recaptcha][enabled]"
                                                                              value="1" <?php if ($config->google->recaptcha->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="recaptcha_enabled"
                                                                             name="google[recaptcha][enabled]"
                                                                             value="0" <?php if ($config->google->recaptcha->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="recaptcha_secret">Secret:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="google[recaptcha][secret]"
                                       id="recaptcha_secret"
                                       placeholder="Secret" maxlength="32"
                                       value="<?= $config->google->recaptcha->secret; ?>">
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="recaptcha_sitekey">Sitekey:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="google[recaptcha][sitekey]"
                                       id="recaptcha_sitekey"
                                       placeholder="Sitekey" maxlength="32"
                                       value="<?= $config->google->recaptcha->sitekey; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <h3 class="panel-heading">Analytics:</h3>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="analytics_enabled">Status:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <label class="radio-inline bg-success"><input type="radio" id="analytics_enabled"
                                                                              name="google[analytics][enabled]"
                                                                              value="1" <?php if ($config->google->analytics->enabled === true) {
                                        echo 'checked="checked"';
                                    } ?>>Enabled</label>
                                <label class="radio-inline bg-danger"><input type="radio" id="analytics_enabled"
                                                                             name="google[analytics][enabled]"
                                                                             value="0" <?php if ($config->google->analytics->enabled === false) {
                                        echo 'checked="checked"';
                                    } ?>>Disabled</label>
                            </div>
                        </div>
                        <div class="form-group row margin-bottom-05">
                            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="analytics_id">ID:</label>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <input type="text" class="form-control" name="google[analytics][id]" id="analytics_id"
                                       placeholder="ID" maxlength="32"
                                       value="<?= $config->google->analytics->id; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!–– Upload Settings -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h2 class="panel-heading">Upload Settings:</h2>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h3 class="panel-heading">Weather Underground:</h3>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="wu_station_updates">Upload:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="radio-inline bg-success"><input type="radio" id="wu_station_updates"
                                                                                  name="upload[wu][enabled]"
                                                                                  value="1" <?php if ($config->upload->wu->enabled === true) {
                                            echo 'checked="checked"';
                                        } ?>>Enabled</label>
                                    <label class="radio-inline bg-danger"><input type="radio" id="wu_station_updates"
                                                                                 name="upload[wu][enabled]"
                                                                                 value="0" <?php if ($config->upload->wu->enabled === false) {
                                            echo 'checked="checked"';
                                        } ?>>Disabled</label>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="wu_station_id">Station
                                    ID:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[wu][id]" id="wu_station_id"
                                           placeholder="Station ID" maxlength="15"
                                           value="<?= $config->upload->wu->id; ?>">
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="wu_station_password">Station
                                    Password:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[wu][password]"
                                           id="wu_station_password"
                                           placeholder="Station Password" maxlength="35"
                                           value="<?= $config->upload->wu->password; ?>">
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="wu_update_url">Upload
                                    URL:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[wu][url]"
                                           id="wu_update_url"
                                           placeholder="Update URL" value="<?= $config->upload->wu->url; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h3 class="panel-heading">PWS Weather:</h3>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="pws_station_updates">Upload:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="radio-inline bg-success"><input type="radio" id="pws_station_updates"
                                                                                  name="upload[pws][enabled]"
                                                                                  value="1" <?php if ($config->upload->pws->enabled === true) {
                                            echo 'checked="checked"';
                                        } ?>>Enabled</label>
                                    <label class="radio-inline bg-danger"><input type="radio" id="pws_station_updates"
                                                                                 name="upload[pws][enabled]"
                                                                                 value="0" <?php if ($config->upload->pws->enabled === false) {
                                            echo 'checked="checked"';
                                        } ?>>Disabled</label>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="pws_station_id">Station
                                    ID:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[pws][id]"
                                           id="pws_station_id"
                                           placeholder="Station ID" maxlength="15"
                                           value="<?= $config->upload->pws->id; ?>">
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="pws_station_password">Station
                                    Password:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[pws][password]"
                                           id="pws_station_password"
                                           placeholder="Station Password" maxlength="35"
                                           value="<?= $config->upload->pws->password; ?>">
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="pws_update_url">Upload
                                    URL:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[pws][url]"
                                           id="pws_update_url"
                                           placeholder="Update URL" value="<?= $config->upload->pws->url; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix visible-lg-block visible-md-block visible-sm-block"></div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h3 class="panel-heading">CWOP:</h3>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="cwop_station_updates">Upload:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="radio-inline bg-success"><input type="radio" id="cwop_station_updates"
                                                                                  name="upload[cwop][enabled]"
                                                                                  value="1" <?php if ($config->upload->cwop->enabled === true) {
                                            echo 'checked="checked"';
                                        } ?>>Enabled</label>
                                    <label class="radio-inline bg-danger"><input type="radio" id="cwop_station_updates"
                                                                                 name="upload[cwop][enabled]"
                                                                                 value="0" <?php if ($config->upload->cwop->enabled === false) {
                                            echo 'checked="checked"';
                                        } ?>>Disabled</label>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="cwop_station_id">Station
                                    ID:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[cwop][id]"
                                           id="cwop_station_id"
                                           placeholder="Station ID" maxlength="15"
                                           value="<?= $config->upload->cwop->id; ?>">
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="cwop_station_location">Location</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[cwop][location]"
                                           id="cwop_station_location"
                                           placeholder="Station Location" maxlength="35"
                                           value="<?= $config->upload->cwop->location; ?>">
                                    <p class="bg-info">in format <code>ddmm.hhN/dddmm.hhW</code>.<br>See <a href="http://boulter.com/gps">Degrees, Minutes & Seconds</a></p>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="cwop_station_interval">Update
                                    Interval</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <select name="upload[cwop][interval]" id="cwop_station_interval"
                                            class="form-control">
                                        <?php
                                        foreach ($config->intervals as $interval) { ?>
                                            <option value="<?= $interval; ?>" <?php if ($config->upload->cwop->interval === $interval) {
                                                echo 'selected="selected"';
                                            } ?>><?= $interval; ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                    <p class="bg-info">Should be at least 5 minutes; 10 is good.</p>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="cwop_update_url">Upload
                                    URL:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="upload[cwop][url]"
                                           id="cwop_update_url"
                                           placeholder="Update URL" value="<?= $config->upload->cwop->url; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!–– MyAcuRite -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h3 class="panel-heading">MyAcuRite:</h3>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="myacurite_hub_update">smartHub Upload:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="radio-inline bg-success"><input type="radio" id="myacurite_hub_update"
                                                                                  name="upload[myacurite][hub_enabled]"
                                                                                  value="1" <?php if ($config->upload->myacurite->hub_enabled === true) {
                                            echo 'checked="checked"';
                                        } ?>>Enabled</label>
                                    <label class="radio-inline bg-danger"><input type="radio" id="myacurite_hub_update"
                                                                                 name="upload[myacurite][hub_enabled]"
                                                                                 value="0" <?php if ($config->upload->myacurite->hub_enabled === false) {
                                            echo 'checked="checked"';
                                        } ?>>Disabled</label>
                                </div>
                            </div>
                            <div class="form-group row margin-bottom-05">
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                       for="myacurite_access_update">Access Upload:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="radio-inline bg-success"><input type="radio"
                                                                                  id="myacurite_access_update"
                                                                                  name="upload[myacurite][access_enabled]"
                                                                                  value="1" <?php if ($config->upload->myacurite->access_enabled === true) {
                                            echo 'checked="checked"';
                                        } ?>>Enabled</label>
                                    <label class="radio-inline bg-danger"><input type="radio"
                                                                                 id="myacurite_access_update"
                                                                                 name="upload[myacurite][access_enabled]"
                                                                                 value="0" <?php if ($config->upload->myacurite->access_enabled === false) {
                                            echo 'checked="checked"';
                                        } ?>>Disabled</label>
                                </div>
                            </div>
                            <div class="row margin-bottom-05">
                                <h4>Upload URL's:</h4>
                                <div class="row">
                                    <div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 bg-info">
                                        <p><strong>If installed on the same network as your device, use secondary.<br>See
                                                <code>docs/DNS.md</code></strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group row margin-bottom-05 margin-top-05">
                                    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                           for="myacurite_hub_update_url">smartHub:</label>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                        <select name="upload[myacurite][hub_url]" id="myacurite_hub_update_url"
                                                class="form-control">
                                            <option value="http://hubapi.myacurite.com" <?php if ($config->upload->myacurite->hub_url === "http://hubapi.myacurite.com") {
                                                echo 'selected="selected"';
                                            } ?>>myacurite.com (official)
                                            </option>
                                            <option value="http://hubapi.acuparse.com" <?php if ($config->upload->myacurite->hub_url === "http://hubapi.acuparse.com") {
                                                echo 'selected="selected"';
                                            } ?>>acuparse.com (secondary)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row margin-bottom-05">
                                    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                           for="myacurite_access_update_url">Access:</label>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                        <select name="upload[myacurite][access_url]" id="myacurite_access_update_url"
                                                class="form-control">
                                            <option value="https://atlasapi.acuparse.com" <?php if ($config->upload->myacurite->access_url === "https://atlasapi.myacurite.com") {
                                                echo 'selected="selected"';
                                            } ?>>myacurite.com (official)
                                            </option>
                                            <option value="https://atlasapi.acuparse.com" <?php if ($config->upload->myacurite->access_url === "https://atlasapi.acuparse.com") {
                                                echo 'selected="selected"';
                                            } ?>>acuparse.com (secondary)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!–– Debug Server -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h3 class="panel-heading">Debug Update Server:</h3>
                            <p>Sends MyAcuRite data to an additional debug server</p>
                            <div class="form-group row margin-bottom-05">
                                <div class="form-group row margin-bottom-05">
                                    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                           for="debug_server_enabled">Upload:</label>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                        <label class="radio-inline bg-success"><input type="radio"
                                                                                      id="debug_server_enabled"
                                                                                      name="debug[server][enabled]"
                                                                                      value="1" <?php if ($config->debug->server->enabled === true) {
                                                echo 'checked="checked"';
                                            } ?>>Enabled</label>
                                        <label class="radio-inline bg-danger"><input type="radio"
                                                                                     id="debug_server_enabled"
                                                                                     name="debug[server][enabled]"
                                                                                     value="0" <?php if ($config->debug->server->enabled === false) {
                                                echo 'checked="checked"';
                                            } ?>>Disabled</label>
                                    </div>
                                </div>
                                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4" for="debug_server_url">URL:</label>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="text" class="form-control" name="debug[server][url]"
                                           id="debug_server_url"
                                           placeholder="Server URL" value="<?= $config->debug->server->url; ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4 col-md-4  col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-4 col-xs-offset-4">
                        <button type="submit" id="submit" value="submit" class="btn btn-primary btn-block"><i
                                    class="fa fa-check"
                                    aria-hidden="true"></i>
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <?php
        include(APP_BASE_PATH . '/inc/footer.php');
    }
} else {
    header("Location: /admin/account");
}
