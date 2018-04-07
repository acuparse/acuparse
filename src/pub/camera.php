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
 * File: src/pub/camera.php
 * Shows the current camera image and archives
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

// Check that camera is enabled
if ($config->camera->enabled === true) {

    // Archive
    if (isset($_GET['archive'])) {
        if (isset($_GET['date'])) {
            $date = strtotime($_GET['date']);
            $forward_date = date('Y-m-d', strtotime('+1 day', $date));
            $backward_date = date('Y-m-d', strtotime('-1 day', $date));
            $date = date('Y-m-d', $date);
        } else {
            $date = date('Y-m-d');
            $forward_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
            $backward_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        }

        // Get the page header
        $page_title = 'Weather Camera Archive | ' . $config->site->name;
        include(APP_BASE_PATH . '/inc/header.php');

        // Check the cam directory for images
        $cam_dir = scandir('img/cam/');
        $last_dir = current(array_slice($cam_dir,
            -2)); // Latest directory should be the 2nd last value. Since latest.jpg should be the last.
        $cam_dir = $cam_dir[3]; // Oldest images should be the 3rd value. Since .=0 ..=1 and .gitignore=2 are first.
        // No images? make today the latest day.
        $cam_dir_has_images = true;
        if ($cam_dir === null) {
            $cam_dir = date('Y-m-d');
            $cam_dir_has_images = false;
        }

        // Today's Date
        $today = date('Y-m-d');
        ?>

        <section id="camera_archive" class="camera_archive_display">
        <div class="row">
            <h1 class="page-header">Weather Camera Archive</h1>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-xs-3 text-left">
                <?php if (($backward_date < $cam_dir) || $cam_dir_has_images === false) {
                    // Don't display backward button
                } elseif ($backward_date > $today) {
                    ?>
                    <a href="/camera?archive&date=<?= $last_dir; ?>"><h3><i class="fa fa-backward"
                                                                            aria-hidden="true"></i></h3></a>
                    <?php
                } else { ?>
                    <a href="/camera?archive&date=<?= $backward_date; ?>"><h3><i class="fa fa-backward"
                                                                                 aria-hidden="true"></i></h3></a>
                <?php } ?>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-6">
                <form role="form" class="form-horizontal form-inline" action="/camera" method="GET">
                    <div class="form-group">
                        <input type="hidden" name="archive">
                        <input class="form-control form-cam-archive-date" type="date" name="date" id="date"
                               onchange="this.form.submit()" value="<?= $date; ?>"
                               min="<?= $cam_dir; ?>" max="<?= date('Y-m-d'); ?>">
                    </div>
                </form>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-3 text-right">
                <?php if (($forward_date > $today) || $cam_dir_has_images === false) {
                    // Don't display forward button
                } else {
                    if ($forward_date < $cam_dir) {
                        ?>
                        <a href="/camera?archive&date=<?= $cam_dir; ?>"><h3><i class="fa fa-forward"
                                                                               aria-hidden="true"></i></h3>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="/camera?archive&date=<?= $forward_date; ?>"><h3><i class="fa fa-forward"
                                                                                    aria-hidden="true"></i></h3>
                        </a>
                        <?php
                    }
                } ?>
            </div>
        </div>
        <?php
        $dirname = "img/cam/$date/";
        $images = scandir($dirname);
        $ignore = Array(".", "..", "daily.gif");

        if ($images == 0) { ?>
            <div class="row margin-top-10">
                <div class="alert alert-warning"><strong>No images to display!</strong></div>
            </div><?php
        } else { ?>
            <div class="row margin-top-10">
                <div class="col-lg-6 col-lg-offset-3 thumb">
                    <a class="thumbnail" href="<?= $dirname . 'daily.gif'; ?>"
                       data-lightbox="timelapse">
                        <img class="img-responsive" src="<?= $dirname . 'daily.gif'; ?>" alt=""
                             title="Daily Timelapse">Daily Timelapse</a>
                </div>
            </div>
            <hr class="hr-dotted">
            <div class="row">
                <?php
                $counter = 0;
                foreach ($images as $curimg) {
                    if (!in_array($curimg, $ignore)) {
                        $image_time = str_replace('.jpg', '', $curimg);
                        $image_time = str_split($image_time, 2);
                        $image_time = implode(':', $image_time);

                        ?>
                        <div class="col-lg-2 col-md-4 col-xs-6 thumb">
                            <a class="thumbnail" href="<?= $dirname . $curimg; ?>"
                               data-lightbox="weather-cam">
                                <img class="img-responsive" src="<?= $dirname . $curimg; ?>" alt=""
                                     title="<?= "$image_time"; ?>"><?= "$image_time"; ?></a>
                        </div>
                        <?php

                        // Apply clearfixes to keep columns in place
                        $counter++;
                        if ($counter % 2 === 0) {
                            echo '<div class="clearfix visible-sm-block"></div>';
                        }
                        if ($counter % 3 === 0) {
                            echo '<div class="clearfix visible-md-block"></div>';
                        }
                        if ($counter % 6 === 0) {
                            echo '<div class="clearfix visible-lg-block"></div>';
                        }
                    }
                }
                ?>
            </div>
            </section>
            <?php
        }
    }
    // End archive

    // Display camera
    else {
        $page_title = 'Live Weather Camera | ' . $config->site->name;
        include(APP_BASE_PATH . '/inc/header.php');
        ?>
        <section id="live_camera" class="live_camera_display">
            <div class="row">
                <h1 class="page-header">Weather Camera</h1>
            </div>
            <?php
            if (file_exists('img/cam/latest.jpg')) {
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <img class="center-block" src="/img/cam/latest.jpg?<?= time(); ?>">
                    </div>
                </div>
                <div class="row"><strong><?= $config->camera->text; ?></strong></div>
                <div class="row margin-top-15">
                    <button type="button" id="archive" class="btn btn-default center-block"
                            onclick="location.href = '/camera?archive'"><i class="fa fa fa-archive"
                                                                           aria-hidden="true"></i> View Camera
                        Archive
                    </button>
                </div>
                <?php
            } else {
                ?>
                <div class="row">
                    <div class="col-lg-12 margin-top-10">
                        <div class=" alert alert-warning"><strong>Recent camera image not found!</strong>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </section>
        <?php
    }

    // Set the footer to include scripts required for this page
    $page_footer =
        '<script src="/lib/mit/lightbox/js/lightbox.min.js"></script>';

    include(APP_BASE_PATH . '/inc/footer.php');

} // Camera not enabled, go home.
else {
    header("Location: /");
}
