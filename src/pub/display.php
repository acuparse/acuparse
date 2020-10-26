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
 * File: src/pub/display.php
 * Main Dashboard formatted for full screen display
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 */
/** @var string $installed */

if ($installed == false) {
    header("Location: /admin/install");
    exit();
} else {

    $pageTitle = 'Live Weather - Display Mode';

    // Force light theme
    if (isset($_GET['light'])) {
        $config->site->theme = 'clean';
    }
    // Force dark theme
    if (isset($_GET['dark'])) {
        $config->site->theme = 'twilight';
    }

    include(APP_BASE_PATH . '/inc/header.php');

// Get Forcast Data
    ?>

    <!-- Time Section -->
    <div id="local-time" class="row local-time">
        <div class="col-auto mx-auto">
            <div id="local-time-display"></div>
        </div>
    </div>

    <!-- Modify CSS for Display Mode -->
    <style>
        body {
            padding-top: unset;
            display: -webkit-flex;
            display: flex;
            -webkit-align-items: center;
            align-items: center;
            -webkit-justify-content: center;
            justify-content: center;
        }
    </style>

    <!-- Live Weather Section -->
    <section id="live-weather">
        <div class="row">
            <div class="col mx-auto text-center">
                <img src="/img/loading/<?= ($config->site->theme === 'twilight') ? 'live-dark' : 'live'; ?>.gif"
                     alt="Loading Data">
                <h3>Crunching numbers ...</h3>
            </div>
        </div>
    </section>
    <?php
// Set the footer to include scripts required for this page
    $page_footer = '
    <!-- Refresh Weather Data -->
    <script>
        $(document).ready(function () {
            function update() {
                $.ajax({
                    url: \'/?weather\',
                    success: function (data) {
                        $("#live-weather").html(data);
                        window.setTimeout(update, 39000);
                    }
                });
            }

            update();
        });
    </script>
    
    <!-- Refresh Server Time -->
    <script>
        $(document).ready(function () {
            function update() {
                $.ajax({
                    url: \'/?time\',
                    success: function (data) {
                        $("#local-time-display").html(data);
                        window.setTimeout(update, 1000);
                    }
                });
            }
    
            update();
        });
    </script>
';

    include(APP_BASE_PATH . '/inc/footer.php');
}
