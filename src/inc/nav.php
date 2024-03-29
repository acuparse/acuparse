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
 * File: src/inc/nav.php
 * Build the navigation bar for the main site
 */

/**
 * @var object $config Global Config
 */

?>
<nav class="navbar navbar-expand-lg <?= ($config->site->theme === 'twilight') ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?> fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/"><?= $config->site->name; ?><br>
            <small><?= $config->site->location; ?></small>
        </a>
        <button id="navbar-nav-dropdown-button" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-nav-dropdown"
                aria-controls="navbar-nav-dropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar-nav-dropdown">
            <ul class="navbar-nav mx-auto">
                <li class="<?= ($_SERVER['PHP_SELF'] === '/index.php' && empty($_GET)) ? 'nav-item active' : 'nav-item'; ?>">
                    <a class="nav-link" href="/"><i class="fas fa-sun" aria-hidden="true"></i> Live</a>
                </li>
                <?php if ($config->archive->enabled === true) { ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/archive.php') ? 'nav-item active' : 'nav-item'; ?>">
                        <a class="nav-link" href="/archive"><i class="fas fa-archive" aria-hidden="true"></i>
                            Archive</a>
                    </li>

                <?php }

                // Weather Camera
                if ($config->camera->enabled === true) {
                    $liveCamActive = ($_SERVER['PHP_SELF'] === '/camera.php') && (!isset($_GET['archive']));
                    $camArchiveActive = ($_SERVER['PHP_SELF'] === '/camera.php') && (isset($_GET['archive']));
                    ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/camera.php') ? 'nav-item dropdown active' : 'nav-item dropdown'; ?>">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i class="fas fa-camera" aria-hidden="true"></i>
                            Camera</a>
                        <div class="<?= ($config->site->theme === 'twilight') ? 'dropdown-menu dropdown-menu-dark' : 'dropdown-menu'; ?>">
                            <a class="<?= ($liveCamActive === true) ? 'dropdown-item active' : 'dropdown-item' ?>"
                               href="/camera"><i class="far fa-eye" aria-hidden="true"></i> Live View</a>
                            <a class="<?= ($camArchiveActive === true) ? 'dropdown-item active' : 'dropdown-item' ?>"
                               href="/camera?archive" data-instant><i class="far fa-images" aria-hidden="true"></i>
                                Archive</a>
                        </div>
                    </li>
                <?php }
                // External Weather Sites
                ?>

                <?php if ($config->upload->wu->enabled === true || $config->upload->pws->enabled === true || $config->upload->cwop->enabled === true || $config->upload->wc->enabled === true || $config->upload->windy->enabled === true || $config->upload->windguru->enabled === true) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-external-link-square-alt" aria-hidden="true"></i> External</a>
                        <div class="<?= ($config->site->theme === 'twilight') ? 'dropdown-menu dropdown-menu-dark' : 'dropdown-menu'; ?>">
                            <?php if ($config->upload->wu->enabled === true) { ?>
                                <a class="dropdown-item pe-4"
                                   href="//www.wunderground.com/personal-weather-station/dashboard?ID=<?= $config->upload->wu->id; ?>"
                                   target="_blank"><img src="/img/external/wu.ico" width="16" height="16"
                                                        aria-hidden="true" alt="Wunderground Icon"> Weather Underground</a>
                            <?php } ?>
                            <?php if ($config->upload->wc->enabled === true) { ?>
                                <a class="dropdown-item"
                                   href="//app.weathercloud.net/<?= $config->upload->wc->device; ?>"
                                   target="_blank"><img src="/img/external/wc.ico" width="16" height="16"
                                                        aria-hidden="true" alt="WeatherCloud Icon"> Weathercloud</a>
                            <?php } ?>
                            <?php if ($config->upload->pws->enabled === true) { ?>
                                <a class="dropdown-item"
                                   href="//www.pwsweather.com/obs/<?= $config->upload->pws->id; ?>.html"
                                   target="_blank"><img src="/img/external/pws.ico" width="16" height="16"
                                                        aria-hidden="true" alt="PWS Icon"> PWS Weather</a>
                            <?php } ?>
                            <?php if ($config->upload->windy->enabled === true) { ?>
                                <a class="dropdown-item"
                                   href="//www.windy.com/station/pws-<?= $config->upload->windy->id; ?>"
                                   target="_blank"><img src="/img/external/windy.ico" width="16" height="16"
                                                        aria-hidden="true" alt="Windy Icon"> Windy</a>
                            <?php } ?>
                            <?php if ($config->upload->windguru->enabled === true) { ?>
                                <a class="dropdown-item"
                                   href="//www.windguru.cz/station/<?= $config->upload->windguru->id; ?>"
                                   target="_blank"><img src="/img/external/windguru.png" width="16" height="16"
                                                        aria-hidden="true" alt="Windguru Icon"> Windguru</a>
                            <?php } ?>
                            <?php if ($config->upload->cwop->enabled === true) { ?>
                                <a class="dropdown-item"
                                   href="http://www.findu.com/cgi-bin/wxpage.cgi?call=<?= $config->upload->cwop->id; ?>"
                                   target="_blank"><img src="/img/external/findu.ico" width="16" height="16"
                                                        aria-hidden="true" alt="CWOP Icon"> CWOP via findU</a>
                            <?php } ?>
                        </div>
                    </li>
                <?php }

                // Contact
                if ($config->contact->enabled === true) {
                    ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/contact.php') ? 'nav-item active' : 'nav-item'; ?>"><a
                                class="nav-link" href="/contact"><i class="fas fa-envelope" aria-hidden="true"></i>
                            Contact</a></li>
                    <?php
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php

                // Member Account Functions
                if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
                    $userActive = ($_SERVER['PHP_SELF'] === '/admin/account.php') || ($_SERVER['PHP_SELF'] === '/admin/index.php' || $_SERVER['PHP_SELF'] === '/admin/tower.php' || $_SERVER['PHP_SELF'] === '/admin/settings.php');
                    ?>
                    <li class="<?= ($userActive === true) ? 'nav-item dropdown active' : 'nav-item dropdown'; ?>">
                        <a id="user-menu-dropdown" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i
                                    class="fas <?= ($_SESSION['admin'] === true) ? 'fa-user-tie admin-authenticated' : 'fa-user user-authenticated'; ?>"
                                    aria-hidden="true"></i> <?= ($_SESSION['admin'] === true) ? '<span class="admin-authenticated">' . $_SESSION['username'] . '</span>' : '<span class="user-authenticated">' . $_SESSION['username'] . '</span>'; ?>
                        </a>
                        <div class="<?= ($config->site->theme === 'twilight') ? 'dropdown-menu dropdown-menu-dark' : 'dropdown-menu'; ?>">
                            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                                $adminActive = $_SERVER['PHP_SELF'] === '/admin/index.php' || $_SERVER['PHP_SELF'] === '/admin/tower.php' || $_SERVER['PHP_SELF'] === '/admin/access.php' || $_SERVER['PHP_SELF'] === '/admin/status.php' || $_SERVER['PHP_SELF'] === '/admin/settings.php' || ($_SERVER['PHP_SELF'] === '/admin/account.php' && ((isset($_GET['edit'])) && ((isset($_GET['uid'])) && (int)$_GET['uid'] !== $_SESSION['uid'])) || ((isset($_GET['password'])) && ((isset($_GET['uid'])) && (int)$_GET['uid'] !== $_SESSION['uid'])) || (isset($_GET['add'])) || (isset($_GET['view'])));
                                ?>
                                <a id="user-menu-admin" class="<?= ($adminActive === true) ? 'dropdown-item active' : 'dropdown-item'; ?>"
                                   href="/admin"><i
                                            class="<?= ($adminActive === true) ? 'fas fa-cog fa-spin' : 'fas fa-cog'; ?>"
                                            aria-hidden="true"></i>
                                    Admin</a>
                                <?php
                            }
                            $userEditActive = ($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['edit'])) && ((!isset($_GET['uid'])) || ((int)$_GET['uid'] === $_SESSION['uid']));
                            $userPasswordActive = ($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['password'])) && ((!isset($_GET['uid'])) || ((int)$_GET['uid'] === $_SESSION['uid']));
                            ?>
                            <a class="<?= ($userEditActive === true) ? 'dropdown-item active' : 'dropdown-item'; ?>"
                               href="/admin/account?edit" data-instant><i class="fas fa-user-edit"
                                                                          aria-hidden="true"></i> My Account</a>
                            <a class="<?= ($userPasswordActive) ? 'dropdown-item active' : 'dropdown-item'; ?>"
                               href="/admin/account?password" data-instant><i class="fas fa-user-secret"
                                                                              aria-hidden="true"></i> Change
                                Password</a>
                            <a class="dropdown-item" id="nav-sign-out" href="/admin/account?deauth" data-no-instant><i
                                        class="fas fa-sign-out-alt" aria-hidden="true"></i> Sign Out</a>
                        </div>
                    </li>
                    <?php
                }

                // Not authenticated, show sign in
                if (!isset($_SESSION['authenticated'])) {
                    ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/admin/account.php') ? 'nav-item active' : 'nav-item'; ?>">
                        <a class="nav-link" id="nav-sign-in" href="/admin/account"><i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            Sign In</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
