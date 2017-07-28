<?php
/**
 * Acuparse - AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2017 Maxwell Power
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
 * File: src/inc/header.php
 * Build the main site's header
 */
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta http-equiv="cleartype" content="on">
    <meta name="handheldfriendly" content="true">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.5">
    <meta name="description" content="<?= $config->site->desc; ?>">
    <meta name="keywords" content="weather">
    <title><?= $page_title ?></title>

    <!-- CSS -->
    <link href="/lib/mit/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/lib/mit/weather_icons/css/weather-icons.min.css" rel="stylesheet">
    <link href="/lib/mit/weather_icons/css/weather-icons-wind.min.css" rel="stylesheet">
    <link href="/lib/mit/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <?php if (($_SERVER['PHP_SELF'] === '/camera.php') && (isset($_GET['archive']))) { ?>
        <link href="/lib/mit/datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet">
        <link href="/lib/mit/lightbox/css/lightbox.min.css" rel="stylesheet">
    <?php }
    if (($_SERVER['PHP_SELF'] === '/admin/tower.php') && (isset($_GET['view']))) { ?>
        <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet">
        <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css" rel="stylesheet">
    <?php } ?>
    <!-- Site Theme -->
    <link href="/themes/<?= $config->site->theme; ?>.css" rel="stylesheet">
</head>
<body>
<!-- Page Header -->
<?php include 'nav.php'; ?>

<!-- Page Container -->
<div class="container">
    <?php
    // Messages
    if (isset($_SESSION['messages'])) {
        echo '<div class="row" id="messages"><br><div class="col-lg-12">', $_SESSION['messages'], '</div></div>';
        unset($_SESSION['messages']);
    }
    // Logged in admin
    if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true && $_SESSION['IsAdmin'] === true) {

        // Check Git for updates
        if ($config->site->updates === true) {
            if ($_SERVER['PHP_SELF'] !== '/admin/install/index.php') {
                $result = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT `value` FROM `system` WHERE `name`='schema'"));
                $schema = $result['value'];
                $repo = "$app_info->updater_url";
                $headers = get_headers($repo);

                if (($schema > $app_info->schema) || ($app_info->version > $config->version->app)) {
                    header("Location: /admin/install/?update");
                    die();
                } elseif (strpos($headers[0], "200")) {
                    $git_version = json_decode(file_get_contents($repo));
                    if ($git_version !== null) {
                        if ($app_info->version < $git_version->version) {
                            ?>
                            <div class="row" id="update_message"><br>
                                <div class="col-lg-12">
                                    <div class="alert alert-info alert-dismissable">
                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                                        <strong>Version <?= $git_version->version; ?> is available!</strong><br>
                                        Execute "git pull" or upload new source to update.<br>
                                        <a href="<?= $app_info->homepage; ?>">See docs for details.</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            }
        }
    }
    ?>
    <!-- Time Section -->
    <section id="current_time" class="current_time_display">
        <div class="row">
            <div id="time"></div>
        </div>
    </section>
