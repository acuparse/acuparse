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
 * File: src/fcn/settings/features.php
 * Feature Settings
 */

/**
 * @var object $config Global Config
 */
?>
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
                    <h3>Camera</h3>
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
                    <div class="form-row">
                        <label class="col-form-label" for="camera_sort_today">Today Sort Order:</label>
                        <div class="col form-group">
                            <div class="form-group">
                                <input type="hidden" name="archive">
                                <select class="form-control form-cam-sort" name="camera[sort][today]"
                                        id="camera_sort_today">
                                    <option value="ascending" <?= ($config->camera->sort->today === 'ascending') ? 'selected="selected"' : false; ?>>
                                        Ascending
                                    </option>
                                    <option value="descending" <?= ($config->camera->sort->today === 'descending') ? 'selected="selected"' : false; ?>>
                                        Descending
                                    </option>
                                </select>
                            </div>
                            <small id="camera-text-help" class="form-text text-muted">Sort order for today's
                                images</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-form-label" for="camera_sort_archive">Archive Sort Order:</label>
                        <div class="col form-group">
                            <div class="form-group">
                                <input type="hidden" name="archive">
                                <select class="form-control form-cam-sort" name="camera[sort][archive]"
                                        id="camera_sort_archive">
                                    <option value="ascending" <?= ($config->camera->sort->archive === 'ascending') ? 'selected="selected"' : false; ?>>
                                        Ascending
                                    </option>
                                    <option value="descending" <?= ($config->camera->sort->archive === 'descending') ? 'selected="selected"' : false; ?>>
                                        Descending
                                    </option>
                                </select>
                            </div>
                            <small id="camera-text-help" class="form-text text-muted">Sort order for archive
                                images</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h3>Archive</h3>
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
                    <h3>Contact</h3>
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
                                For</label>
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
                                Interval</label>
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
                            Key</label>
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
            <h3 class="panel-heading">Mailgun</h3>
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="mailgun[enabled]"
                           id="mailgun-enabled-0" value="0"
                           onclick='document.getElementById("mailgun-secret").disabled=true;document.getElementById("mailgun-domain").disabled=true;'
                        <?= ($config->mailgun->enabled === false) ? 'checked="checked"' : false; ?>>
                    <label class="form-check-label alert alert-danger"
                           for="mailgun-enabled-0">Disabled</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="mailgun[enabled]"
                           id="mailgun-enabled-1" value="1"
                           onclick='document.getElementById("mailgun-secret").disabled=false;document.getElementById("mailgun-domain").disabled=false;'
                        <?= ($config->mailgun->enabled === true) ? 'checked="checked"' : false; ?>>
                    <label class="form-check-label alert alert-success"
                           for="mailgun-enabled-1">Enabled</label>
                </div>
            </div>

            <div class="form-row">
                <label class="col-form-label" for="mailgun-secret">Secret:</label>
                <div class="col form-group">
                    <input type="text" class="form-control"
                           name="mailgun[secret]"
                           id="mailgun-secret"
                           placeholder="API Key"
                        <?= ($config->mailgun->enabled === false) ? 'disabled="disabled"' : false; ?>
                           value="<?= $config->mailgun->secret; ?>">
                    <small id="recaptcha-secret-help" class="form-text text-muted">
                        Your Mailgun API Key (see https://app.mailgun.com/app/sending/domains/
                        {YOUR_DOMAIN_NAME}/sending-keys)
                    </small>
                </div>
            </div>
            <div class="form-row">
                <label class="col-form-label" for="mailgun-domain">Domain:</label>
                <div class="col form-group">
                    <input type="text" class="form-control"
                           name="mailgun[domain]"
                           id="mailgun-domain"
                           placeholder="example.com"
                        <?= ($config->mailgun->enabled === false) ? 'disabled="disabled"' : false; ?>
                           value="<?= $config->mailgun->domain; ?>">
                    <small id="mailgun-domain-help" class="form-text text-muted">Your Mailgun Domain</small>
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