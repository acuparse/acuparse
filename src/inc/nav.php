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
 * File: src/inc/nav.php
 * Build the navigation bar for the main site
 */
?>
<header id="header_display" class="header_display">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><?= $config->site->name; ?><br>
                    <small><?= $config->site->location; ?></small>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li <?php if ($_SERVER['PHP_SELF'] === '/index.php' && empty($_GET)) {
                        echo 'class="active"';
                    } ?>>
                        <a href="/"><i class="wi wi-thermometer" aria-hidden="true"></i> Live</a>
                    </li>
                    <?php if ($config->archive->enabled === true) { ?>
                        <li <?php if ($_SERVER['PHP_SELF'] === '/archive.php') {
                            echo 'class="active"';
                        } ?>>
                            <a href="/archive"><i class="fa fa-archive" aria-hidden="true"></i> Archive</a>
                        </li>

                    <?php }

                    // Weather Camera
                    if ($config->camera->enabled === true) { ?>
                        <li class="dropdown <?php if ($_SERVER['PHP_SELF'] === '/camera.php') {
                            echo 'active';
                        } ?>">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-camera"
                                                                                         aria-hidden="true"></i>
                                Camera
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li <?php if (($_SERVER['PHP_SELF'] === '/camera.php') && (!isset($_GET['archive']))) {
                                    echo 'class="active"';
                                } ?>><a href="/camera"><i class="fa fa-binoculars" aria-hidden="true"></i> Live View</a>
                                </li>
                                <li <?php if (($_SERVER['PHP_SELF'] === '/camera.php') && (isset($_GET['archive']))) {
                                    echo 'class="active"';
                                } ?>><a href="/camera?archive"><i class="fa fa-archive" aria-hidden="true"></i> Archive</a>
                                </li>
                            </ul>
                        </li>
                    <?php }
                    // External Weather Sites
                    ?>

                    <?php if ($config->upload->wu->enabled === true || $config->upload->pws->enabled === true || $config->upload->cwop->enabled === true) { ?>
                        <li class="dropdown">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="fa fa-external-link-square" aria-hidden="true"></i>
                                External
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <?php if ($config->upload->wu->enabled === true) { ?>
                                    <li>
                                        <a href="//www.wunderground.com/personal-weather-station/dashboard?ID=<?= $config->upload->wu->id; ?>"
                                           target="_blank"><i
                                                    class="fa fa-external-link-square" aria-hidden="true"></i> WU</a>
                                    </li> <?php } ?>
                                <?php if ($config->upload->pws->enabled === true) { ?>
                                    <li>
                                        <a href="//www.pwsweather.com/obs/<?= $config->upload->pws->id; ?>.html"
                                           target="_blank"><i
                                                    class="fa fa-external-link-square" aria-hidden="true"></i> PWS</a>
                                    </li> <?php } ?>
                                <?php if ($config->upload->cwop->enabled === true) { ?>
                                    <li>
                                        <a href="//www.findu.com/cgi-bin/wxpage.cgi?call=<?= $config->upload->cwop->id; ?>"
                                           target="_blank"><i
                                                    class="fa fa-external-link-square" aria-hidden="true"></i> CWOP</a>
                                    </li> <?php } ?>
                            </ul>
                        </li>
                    <?php }

                    // Contact
                    if ($config->contact->enabled === true) {
                        ?>

                        <li <?php if ($_SERVER['PHP_SELF'] === '/contact.php') {
                            echo 'class="active"';
                        } ?>>
                            <a href="/contact"><i class="fa fa-envelope" aria-hidden="true"></i> Contact</a>
                        </li>

                        <?php
                    }

                    // Member Account Functions
                    if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true) {
                        ?>
                        <li class="dropdown <?php if (($_SERVER['PHP_SELF'] === '/admin/account.php') || ($_SERVER['PHP_SELF'] === '/admin/index.php')) {
                            echo 'active';
                        } ?>">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown"><span class="loggedin-user"><i
                                            class="fa fa-user"
                                            aria-hidden="true"></i> <?= $_SESSION['Username']; ?> </span><b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <?php if (isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin'] === true) { ?>
                                    <li <?php if ($_SERVER['PHP_SELF'] === '/admin/index.php' || $_SERVER['PHP_SELF'] === '/admin/tower.php' || ($_SERVER['PHP_SELF'] === '/admin/account.php' && ((isset($_GET['edit'])) && ((isset($_GET['uid'])) && $_GET['uid'] !== $_SESSION['UserID'])) || ((isset($_GET['password'])) && ((isset($_GET['uid'])) && $_GET['uid'] !== $_SESSION['UserID'])) || (isset($_GET['add_user'])) || (isset($_GET['view'])))) {
                                        echo 'class="active"';
                                    } ?>>
                                        <a href="/admin"><i class="fa fa-cog" aria-hidden="true"></i> Admin</a>
                                    </li>
                                <?php } ?>
                                <li <?php if (($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['edit'])) && (!isset($_GET['uid']))) {
                                    echo 'class="active"';
                                } ?>>
                                    <a href="/admin/account?edit"><i class="fa fa-user-circle" aria-hidden="true"></i>
                                        Edit Account</a>
                                </li>
                                <li <?php if (($_SERVER['PHP_SELF'] === '/admin/account.php') && (isset($_GET['password'])) && (!isset($_GET['uid']))) {
                                    echo 'class="active"';
                                } ?>>
                                    <a href="/admin/account?password"><i class="fa fa-user-secret"
                                                                         aria-hidden="true"></i> Change Password</a>
                                </li>
                                <li>
                                    <a href="/admin/account?logout"><i class="fa fa-sign-out" aria-hidden="true"></i>
                                        Logout</a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }

                    // If not already logged in, show the login button
                    if (!isset($_SESSION['UserLoggedIn'])) {
                        ?>
                        <li <?php if ($_SERVER['PHP_SELF'] === '/account.php') {
                            echo 'class="active"';
                        } ?>>
                            <a href="/admin/account"><i class="fa fa-unlock" aria-hidden="true"></i> Login</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
