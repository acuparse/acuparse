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
 * File: src/pub/archive.php
 * View station archive data
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

/**
 * @return array
 * @var object $config Global Config
 */

if ($config->archive->enabled === true) {
    $pageTitle = 'Weather Archive';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>

    <div class="row">
        <div class="col">
            <h1 class="page-header">Weather Station Archive</h1>
        </div>
    </div>

    <hr>

    <section id="weather-archive">
        <div class="row">
            <div class="col mx-auto text-center">
                <img src="/img/loading/<?= ($config->site->theme === 'twilight') ? 'archive-dark' : 'archive'; ?>.gif"
                     alt="Loading Data">
                <h3>Going back through time ...</h3>
                <p>This can take a while</p>
            </div>
        </div>
    </section>

    <?php
// Set the footer to include scripts required for this page
    $page_footer = '
    <script>
        $(document).ready(function () {
            async function updateArchive() {
                $.ajax({
                    url: \'/api/v1/html/archive/\',
                    success: function (data) {
                        $("#weather-archive").html(data);
                    },
                    error: function (request) {
                        console.log("Archive Data Error:\n" + request.responseText);
                    }
                });
            }
            updateArchive();
        });
    </script>';

    include(APP_BASE_PATH . '/inc/footer.php');

} // Archive not enabled, go home.
else {
    header("Location: /");
}
