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
 * File: src/pub/admin/settings.php
 * Modify Config Settings
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 */

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {

    // Process the changes
    if (isset($_GET['do'])) {

        function checkMAC($MAC)
        {
            $patterns = array();
            $patterns[0] = '/:/';
            $patterns[1] = '/-/';
            $patterns[1] = '/ /';
            $replacement = '';
            return preg_replace($patterns, $replacement, $MAC);
        }

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
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/disable.sql';
            } elseif ($_POST['mysql']['trim'] === '1') {
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable.sql';
            } elseif ($_POST['mysql']['trim'] === '2') {
                $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable_xtower.sql';
            }
            $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
            $schema = exec($schema, $schemaOutput, $schemaReturn);
            syslog(LOG_INFO, "(SYSTEM){CONFIG}: Database Trimming Updated");
            $config->mysql->trim = (int)$_POST['mysql']['trim'];
        }

        // Station
        $config->station->device = (int)$_POST['station']['device'];
        $config->station->hub_mac = (isset($_POST['station']['hub_mac'])) ? checkMAC($_POST['station']['hub_mac']) : null;
        $config->station->access_mac = (isset($_POST['station']['access_mac'])) ? checkMAC($_POST['station']['access_mac']) : null;
        $config->station->primary_sensor = (int)$_POST['station']['primary_sensor'];
        $config->station->sensor_iris = (isset($_POST['station']['sensor_iris'])) ? sprintf('%08d',
            $_POST['station']['sensor_iris']) : null;
        $config->station->sensor_atlas = (isset($_POST['station']['sensor_atlas'])) ? sprintf('%08d',
            $_POST['station']['sensor_atlas']) : null;
        $config->station->baro_offset = (isset($_POST['station']['baro_offset'])) ? (float)$_POST['station']['baro_offset'] : 0;
        $config->station->towers = (bool)$_POST['station']['towers'];
        $config->station->towers_additional = (isset($_POST['station']['towers_additional'])) ? (bool)$_POST['station']['towers_additional'] : false;
        $config->station->lightning_source = (int)$_POST['station']['lightning_source'];
        $config->station->filter_access = (isset($_POST['station']['filter_access'])) ? (bool)$_POST['station']['filter_access'] : false;

        // Site
        $config->site->name = $_POST['site']['name'];
        $config->site->desc = $_POST['site']['desc'];
        $config->site->location = $_POST['site']['location'];
        $config->site->hostname = $_POST['site']['hostname'];
        $config->site->email = $_POST['site']['email'];
        $config->site->timezone = $_POST['site']['timezone'];
        $config->site->display_date = $_POST['site']['display_date'];
        $config->site->dashboard_display_time = $_POST['site']['dashboard_display_time'];
        $config->site->dashboard_display_date = $_POST['site']['dashboard_display_date'];
        $config->site->dashboard_display_date_full = $_POST['site']['dashboard_display_date_full'];
        $config->site->date_api_json = $_POST['site']['date_api_json'];
        $config->site->lat = (float)$_POST['site']['lat'];
        $config->site->long = (float)$_POST['site']['long'];
        $config->site->imperial = (bool)$_POST['site']['imperial'];
        $config->site->hide_alternate = $_POST['site']['hide_alternate'];
        $config->site->theme = $_POST['site']['theme'];
        $config->site->updates = (bool)$_POST['site']['updates'];

        // Webcam
        $config->camera->enabled = (bool)$_POST['camera']['enabled'];
        $config->camera->text = (isset($_POST['camera']['text'])) ? $_POST['camera']['text'] : 'Image updated every X minutes.';
        $config->camera->sort->today = (isset($_POST['camera']['sort']['today'])) ? $_POST['camera']['sort']['today'] : 'ascending';
        $config->camera->sort->archive = (isset($_POST['camera']['sort']['archive'])) ? $_POST['camera']['sort']['archive'] : 'ascending';

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

        // Mailgun
        $config->mailgun->enabled = (bool)$_POST['mailgun']['enabled'];
        $config->mailgun->secret = (isset($_POST['mailgun']['secret'])) ? $_POST['mailgun']['secret'] : null;
        $config->mailgun->domain = (isset($_POST['mailgun']['domain'])) ? $_POST['mailgun']['domain'] : null;

        // Uploader

        // Master Sensor
        $config->upload->sensor->external = $_POST['upload']['sensor']['external'];
        $config->upload->sensor->id = (isset($_POST['upload']['sensor']['id'])) ? $_POST['upload']['sensor']['id'] : null;
        $config->upload->sensor->archive = (isset($_POST['upload']['sensor']['archive'])) ? (bool)$_POST['upload']['sensor']['archive'] : false;

        // WU
        $config->upload->wu->enabled = (bool)$_POST['upload']['wu']['enabled'];
        $config->upload->wu->id = (isset($_POST['upload']['wu']['id'])) ? $_POST['upload']['wu']['id'] : null;
        $config->upload->wu->password = (isset($_POST['upload']['wu']['password'])) ? $_POST['upload']['wu']['password'] : null;
        $config->upload->wu->url = (isset($_POST['upload']['wu']['url'])) ? $_POST['upload']['wu']['url'] : 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php';

        // PWS
        $config->upload->pws->enabled = (bool)$_POST['upload']['pws']['enabled'];
        $config->upload->pws->id = (isset($_POST['upload']['pws']['id'])) ? $_POST['upload']['pws']['id'] : null;
        $config->upload->pws->password = (isset($_POST['upload']['pws']['password'])) ? $_POST['upload']['pws']['password'] : null;
        $config->upload->pws->url = (isset($_POST['upload']['pws']['url'])) ? $_POST['upload']['pws']['url'] : 'http://www.pwsweather.com/pwsupdate/pwsupdate.php';

        // CWOP
        $config->upload->cwop->enabled = (bool)$_POST['upload']['cwop']['enabled'];
        $config->upload->cwop->id = (isset($_POST['upload']['cwop']['id'])) ? $_POST['upload']['cwop']['id'] : null;
        $config->upload->cwop->location = (isset($_POST['upload']['cwop']['location'])) ? $_POST['upload']['cwop']['location'] : null;
        $config->upload->cwop->interval = (isset($_POST['upload']['cwop']['interval'])) ? $_POST['upload']['cwop']['interval'] : '10 minutes';
        $config->upload->cwop->url = (isset($_POST['upload']['cwop']['url'])) ? $_POST['upload']['cwop']['url'] : 'cwop.aprs.net';

        // WC
        $config->upload->wc->enabled = (bool)$_POST['upload']['wc']['enabled'];
        $config->upload->wc->id = (isset($_POST['upload']['wc']['id'])) ? $_POST['upload']['wc']['id'] : null;
        $config->upload->wc->key = (isset($_POST['upload']['wc']['key'])) ? $_POST['upload']['wc']['key'] : null;
        $config->upload->wc->device = (isset($_POST['upload']['wc']['device'])) ? $_POST['upload']['wc']['device'] : null;
        $config->upload->wc->url = (isset($_POST['upload']['wc']['url'])) ? $_POST['upload']['wc']['url'] : 'http://api.weathercloud.net/v01/set';

        // Windy
        $config->upload->windy->enabled = (bool)$_POST['upload']['windy']['enabled'];
        $config->upload->windy->id = (isset($_POST['upload']['windy']['id'])) ? $_POST['upload']['windy']['id'] : null;
        $config->upload->windy->key = (isset($_POST['upload']['windy']['key'])) ? $_POST['upload']['windy']['key'] : null;
        $config->upload->windy->station = (isset($_POST['upload']['windy']['station'])) ? $_POST['upload']['windy']['station'] : '0';
        $config->upload->windguru->url = (isset($_POST['upload']['windy']['url'])) ? $_POST['upload']['windy']['url'] : 'http://stations.windy.com/pws/update';

        // Windguru
        $config->upload->windguru->enabled = (bool)$_POST['upload']['windguru']['enabled'];
        $config->upload->windguru->uid = (isset($_POST['upload']['windguru']['uid'])) ? $_POST['upload']['windguru']['uid'] : null;
        $config->upload->windguru->id = (isset($_POST['upload']['windguru']['id'])) ? $_POST['upload']['windguru']['id'] : null;
        $config->upload->windguru->password = (isset($_POST['upload']['windguru']['password'])) ? $_POST['upload']['windguru']['password'] : null;
        $config->upload->windguru->url = (isset($_POST['upload']['windguru']['url'])) ? $_POST['upload']['windguru']['url'] : 'http://www.windguru.cz/upload/api.php';

        // OpenWeather
        $config->upload->openweather->enabled = (bool)$_POST['upload']['openweather']['enabled'];
        $config->upload->openweather->id = (isset($_POST['upload']['openweather']['id'])) ? $_POST['upload']['openweather']['id'] : null;
        $config->upload->openweather->key = (isset($_POST['upload']['openweather']['key'])) ? $_POST['upload']['openweather']['key'] : null;
        $config->upload->openweather->url = (isset($_POST['upload']['openweather']['url'])) ? $_POST['upload']['openweather']['url'] : 'http://api.openweathermap.org/data/3.0/measurements';

        // Generic
        $config->upload->generic->enabled = (bool)$_POST['upload']['generic']['enabled'];
        $config->upload->generic->id = (isset($_POST['upload']['generic']['id'])) ? $_POST['upload']['generic']['id'] : null;
        $config->upload->generic->password = (isset($_POST['upload']['generic']['password'])) ? $_POST['upload']['generic']['password'] : null;
        $config->upload->generic->url = (isset($_POST['upload']['generic']['url'])) ? $_POST['upload']['generic']['url'] : null;

        // MyAcuRite
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
            syslog(LOG_INFO, "(SYSTEM){CONFIG}: Site configuration saved successfully");
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Configuration saved successfully!</div>';
            header("Location: /admin");
            exit();
        } else {
            // Log it
            syslog(LOG_ERR, "(SYSTEM){CONFIG}[ERROR]: Saving configuration failed");
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving configuration failed!</div>';
            header("Location: /admin");
            exit();
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
                        <?php if ($config->debug->server->show === true) { ?>
                            <a class="nav-item nav-link" id="nav-debug-tab" data-toggle="tab" href="#nav-debug"
                               role="tab" aria-controls="nav-debug" aria-selected="false">Debug</a>
                        <?php } ?>
                    </div>
                </nav>

                <form action="/admin/settings?do" method="POST">
                    <!-- Content Tabs-->
                    <div class="tab-content margin-top-15" id="nav-tabContent">

                        <!-- Site Settings -->
                        <?php require(APP_BASE_PATH . '/fcn/settings/site.php'); ?>
                        <!-- END: Site Settings -->

                        <!-- Sensor Settings -->
                        <?php require(APP_BASE_PATH . '/fcn/settings/sensor.php'); ?>
                        <!-- END: Sensor Settings -->

                        <!-- Feature Settings -->
                        <?php require(APP_BASE_PATH . '/fcn/settings/features.php'); ?>
                        <!-- END: Feature Settings -->

                        <!-- Upload Settings -->
                        <?php require(APP_BASE_PATH . '/fcn/settings/upload.php'); ?>
                        <!-- END: Upload Settings -->

                        <!-- Database Settings -->
                        <?php require(APP_BASE_PATH . '/fcn/settings/database.php'); ?>
                        <!-- END: Database Settings -->

                        <?php if ($config->debug->server->show === true) { ?>
                            <!-- Debug Server Settings -->
                            <?php require(APP_BASE_PATH . '/fcn/settings/debug.php'); ?>
                            <!-- END: Debug Settings -->
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
    header("Location: /admin/account");
}
