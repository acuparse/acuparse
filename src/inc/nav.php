<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2019 Maxwell Power
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
?>

<nav class="navbar navbar-expand-lg <?= ($config->site->theme === 'twilight') ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?> fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/"><?= $config->site->name; ?><br>
            <small><?= $config->site->location; ?></small>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-nav-dropdown"
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
                    $liveCamActive = (($_SERVER['PHP_SELF'] === '/camera.php') && (!isset($_GET['archive']))) ? true : false;
                    $camArchiveActive = (($_SERVER['PHP_SELF'] === '/camera.php') && (isset($_GET['archive']))) ? true : false;
                    ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/camera.php') ? 'nav-item dropdown active' : 'nav-item dropdown'; ?>">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i class="fas fa-camera" aria-hidden="true"></i>
                            Camera</a>
                        <div class="dropdown-menu">
                            <a class="<?= ($liveCamActive === true) ? 'dropdown-item active' : 'dropdown-item' ?>"
                               href="/camera"><i class="far fa-eye" aria-hidden="true"></i> Live View</a>
                            <a class="<?= ($camArchiveActive === true) ? 'dropdown-item active' : 'dropdown-item' ?>"
                               href="/camera?archive"><i class="far fa-images" aria-hidden="true"></i> Archive</a>
                        </div>
                    </li>
                <?php }
                // External Weather Sites
                ?>

                <?php if ($config->upload->wu->enabled === true || $config->upload->pws->enabled === true || $config->upload->cwop->enabled === true) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i
                                    class="fas fa-external-link-square-alt" aria-hidden="true"></i> External</a>
                        <div class="dropdown-menu">
                            <?php if ($config->upload->wu->enabled === true) { ?>
                                <a class="dropdown-item"
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
            <ul class="navbar-nav ml-auto">
                <?php

                // Member Account Functions
                if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
                    $userActive = (($_SERVER['PHP_SELF'] === '/admin/account.php') || ($_SERVER['PHP_SELF'] === '/admin/index.php' || $_SERVER['PHP_SELF'] === '/admin/tower.php' || $_SERVER['PHP_SELF'] === '/admin/settings.php')) ? true : false;
                    ?>
                    <li class="<?= ($userActive === true) ? 'nav-item dropdown active' : 'nav-item dropdown'; ?>">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false"><i
                                    class="fas <?= ($_SESSION['admin'] === true) ? 'fa-user-tie admin-authenticated' : 'fa-user user-authenticated'; ?>"
                                    aria-hidden="true"></i> <?= ($_SESSION['admin'] === true) ? '<span class="admin-authenticated">' . $_SESSION['username'] . '</span>' : '<span class="user-authenticated">' . $_SESSION['username'] . '</span>'; ?>
                        </a>
                        <div class="dropdown-menu">
                            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                                $adminActive = ($_SERVER['PHP_SELF'] === '/admin/index.php' || $_SERVER['PHP_SELF'] === '/admin/tower.php' || $_SERVER['PHP_SELF'] === '/admin/settings.php' || ($_SERVER['PHP_SELF'] === '/admin/account.php' && ((isset($_GET['edit'])) && ((isset($_GET['uid'])) && (int)$_GET['uid'] !== $_SESSION['uid'])) || ((isset($_GET['password'])) && ((isset($_GET['uid'])) && (int)$_GET['uid'] !== $_SESSION['uid'])) || (isset($_GET['add'])) || (isset($_GET['view'])))) ? true : false;
                                ?>
                                <a class="<?= ($adminActive === true) ? 'dropdown-item alert-danger active' : 'dropdown-item alert-danger'; ?>"
                                   href="/admin"><i
                                            class="<?= ($adminActive === true) ? 'fas fa-cog fa-spin' : 'fas fa-cog'; ?>"
                                            aria-hidden="true"></i>
                                    Admin</a>
                                <?php
                            }
                            $userEditActive = (($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['edit'])) && ((!isset($_GET['uid'])) || ((isset($_GET['uid'])) && (int)$_GET['uid'] === $_SESSION['uid']))) ? true : false;
                            $userPasswordActive = (($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['password'])) && ((!isset($_GET['uid'])) || ((isset($_GET['uid'])) && (int)$_GET['uid'] === $_SESSION['uid']))) ? true : false;
                            ?>
                            <a class="<?= ($userEditActive === true) ? 'dropdown-item active' : 'dropdown-item'; ?>"
                               href="/admin/account?edit"><i class="fas fa-user-edit" aria-hidden="true"></i>
                                My Account</a>
                            <a class="<?= ($userPasswordActive) ? 'dropdown-item active' : 'dropdown-item'; ?>"
                               href="/admin/account?password"><i class="fas fa-user-secret"
                                                                 aria-hidden="true"></i> Change Password</a>
                            <a class="dropdown-item" href="/admin/account?deauth"><i class="fas fa-sign-out-alt"
                                                                                     aria-hidden="true"></i>
                                Sign Out</a>
                        </div>
                    </li>
                    <?php
                }

                // Not authenticated, show sign in
                if (!isset($_SESSION['authenticated'])) {
                    ?>
                    <li class="<?= ($_SERVER['PHP_SELF'] === '/admin/account.php') ? 'nav-item active' : 'nav-item'; ?>">
                        <a class="nav-link" href="/admin/account"><i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            Sign In</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
