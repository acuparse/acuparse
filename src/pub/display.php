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
 * File: src/pub/display.php
 * Main Dashboard formatted for full screen display
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

/**
 * @var object $config Global Config
 * @var string $installed
 */

if ($installed == false) {
    header("Location: /admin/install");
    exit();
} elseif ((empty($config->station->access_mac) && empty($config->station->hub_mac)) && (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true)) {
    $_SESSION['messages'] = '<div class="alert alert-warning alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Missing Access/Hub Mac. Please configure in sensor settings.</div>';
    header("Location: /admin/settings");
    exit();
} elseif (empty($config->station->access_mac) && empty($config->station->hub_mac)) {
    $_SESSION['messages'] = '<div class="alert alert-warning alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Setup Required! Please login as an admin to continue.</div>';
    header("Location: /admin/account");
    exit();
} else {
    $pageTitle = 'Live Weather';
    include(APP_BASE_PATH . '/inc/header.php');

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
    <!-- Modify CSS for Display Mode -->
    <style>
        body {
            position: absolute;
            margin: 0;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            padding-top: unset;
            display: flex;
            -webkit-align-items: center;
            align-items: center;
            -webkit-justify-content: center;
            justify-content: center;
            overflow: hidden;
        }
    </style>

    <!-- Time Section -->
    <section id="local-time" class="local-time">
        <div class="row">
            <div class="col-auto mx-auto">
                <div>
                    <p id="local-time-display"></p>
                </div>
            </div>
        </div>
    </section>
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
    if ($config->station->primary_sensor === 0) {
        $weatherRefreshTime = 150000;
    } else {
        $weatherRefreshTime = 80000;
    }
// Set the footer to include scripts required for this page
    $page_footer = '
    <!-- Refresh Weather Data -->
    <script>
        $(document).ready(function () {
            async function updateWeather() {
                $.ajax({
                    url: \'/api/v1/html/dashboard/\',
                    success: function (data) {
                        $("#live-weather").html(data);
                        setTimeout(updateWeather, ' . $weatherRefreshTime . ')
                    },
                    error: function (request) {
                        console.log("Weather Data Error:\n" + request.responseText);
                    }
                })
            }
            async function updateTime() {
                $.ajax({
                    url: \'/api/system/ping\',
                    startTime: new Date().getTime(),
                    success: async function(data) {
                        $.ajax({
                            url: \'/api/system/time\',
                            startTime: this.startTime,
                            success: async function (data) {
                                $("#local-time-display").html(data);
                                let rtt = new Date().getTime() - this.startTime;
                                setTimeout(updateTime, 1000 - rtt);
                            }
                        });
                    }
                });
            }
            updateWeather();
            updateTime();
        });
    </script>
';
    include(APP_BASE_PATH . '/inc/footer.php');
}
