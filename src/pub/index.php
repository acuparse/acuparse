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
 * File: src/pub/index.php
 * Main Index
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

// System Time
if (isset($_GET['time'])) {
    $date = date($config->site->display_date);
    echo "<p><strong>$date</strong></p>";
    die();
}
// End system time

// Get Weather HTML
if (isset($_GET['weather'])) {
    require(APP_BASE_PATH . '/fcn/weather/getCurrentHTML.php');
    getCurrentHTML();
    die();
}

// Get Archive Weather HTML
if (isset($_GET['archive'])) {
    require(APP_BASE_PATH . '/fcn/weather/getArchiveHTML.php');
    getArchiveHTML();
    die();
}

// Get Weather JSON
if (isset($_GET['json'])) {
    require(APP_BASE_PATH . '/fcn/weather/GetCurrentWeatherData.php');
    $GetData = new GetCurrentWeatherData();
    echo json_encode($GetData->getConditions());
    die();
}

// Get Camera Watermark
if (isset($_GET['cam'])) {
    require(APP_BASE_PATH . '/fcn/wmark.php');
    camWmark();
    die();
}

if ($installed === false) {
    header("Location: /admin/install");
} else {

    $page_title = $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');

// PHP Info
    if (isset($_GET['info']) && isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true) {
        phpinfo();
        die();
    }
// Get Forcast Data
    ?>
    <!-- Live Weather Section -->
    <section id="live_weather_display" class="live_weather_display">
        <div class="row">
            <div id="weather"><img src="/img/weather.gif">
                <h2>Crunching numbers ...</h2></div>
        </div>
    </section>
    <?php
// Set the footer to include scripts required for this page
    $page_footer =
        '<script>
        $(document).ready(function () {
            function update() {
                $.ajax({
                    url: \'/?weather\',
                    success: function (data) {
                        $("#weather").html(data);
                        window.setTimeout(update, 30000);
                    }
                });
            }

            update();
        });
    </script>';

    include(APP_BASE_PATH . '/inc/footer.php');
}