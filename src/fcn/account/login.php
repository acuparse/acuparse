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
 * File: src/fcn/account/login.php
 * User Login Form
 */

/**
 * @var object $config Global Config
 */

$pageTitle = 'Sign In';
$welcome = date("H");
$welcome = ($welcome < '12') ? 'Morning' : (($welcome >= '12' && $welcome < '17') ? 'Afternoon' : 'Evening');
include(APP_BASE_PATH . '/inc/header.php');
?>
    <section id="user-authentication" class="user-authentication">
        <div class="row mt-5">
            <div class="col-md-8 col-12 mx-auto alert alert-secondary">
                <div class="row">
                    <div class="col">
                        <h1 class="page-header alert alert-heading alert-dark">Good <?= $welcome; ?>!</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <form id="recaptcha-form" action="/admin/account?auth" method="POST">
                            <div class="row">
                                <div class="col-4">
                                    <label class="col-form-label" for="username">Username/Email</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="username" id="username" class="form-control border-primary"
                                           placeholder="username@example.com" aria-describedby="user-help" required
                                           autofocus>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-4">
                                    <label class="col-form-label" for="password">Password</label>
                                </div>
                                <div class="col">
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="Password" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php
                                    if ($config->google->recaptcha->enabled === true) { ?>
                                        <button class="mt-3 mb-3 btn btn-lg btn-success g-recaptcha"
                                                data-sitekey="<?= $config->google->recaptcha->sitekey; ?>"
                                                data-callback="onSubmit">
                                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
                                        </button>
                                        <?php
                                    } else { ?>
                                        <button class="mt-3 mb-3 btn btn-lg btn-success" type="submit"><i
                                                    class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
                                        </button>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col mx-auto mt-2">
                <div><a href="/recover">Forgot your password?</a></div>
            </div>
        </div>
    </section>
<?php
// Get app footer
include(APP_BASE_PATH . '/inc/footer.php');
