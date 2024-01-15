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

// Get the loader
require(dirname(__DIR__, 2) . '/inc/loader.php');

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $authenticatedUser = true;
    if ($_SESSION['admin'] === true) {
        $adminUser = true;
    }
} else {
    $authenticatedUser = false;
    $adminUser = false;
}

// Set the header for javascript
header('Content-Type: text/javascript; charset=UTF-8');
?>
$(document).ready(async function () {
    /**
     * @var weatherRefreshTime Weather Refresh Time
     */

    // Update the Dashboard readings based on weatherRefreshTime
    async function updateWeather() {
        $.ajax({
            url: '/api/v1/html/dashboard/', success: function (data) {
                $("#live-weather").html(data);
                setTimeout(updateWeather, weatherRefreshTime)
            }, error: function (request) {
                console.log("Weather Data Error:\n" + request.responseText);
            }
        });
    }

    await updateWeather();
});

// Ping the server and use the RTT to update the Dashboard time
$(document).ready(async function () {
    async function updateTime() {
        const start_time = new Date().getTime();

        try {
            await $.ajax({
                url: '/api/system/ping',
                start_time: start_time
            });

            const timeData = await $.ajax({
                url: '/api/system/time',
                start_time: start_time
            });

            $("#system-time-display").html(timeData);
        } catch (error) {
            console.error('Error updating time:', error);
        } finally {
            const rtt = new Date().getTime() - start_time;
            setTimeout(updateTime, 1000 - rtt);
        }
    }

    await updateTime();
});

<?php if ($authenticatedUser === true) { ?>

// Update the footer with the last updated timestamp
async function lastUpdate() {
    $.ajax({
        url: '/api/system/health', success: function (data) {
            /**
             * @var authenticated
             */
            if (data.authenticated === true) {
                /**
                 * @var realtime
                 */
                if (data.realtime === 'online') {
                    /**
                     * @var updated
                     */
                    $("#last-updated-timestamp").html('Updated ' + data.updated + ' (Realtime)');
                } else {
                    $("#last-updated-timestamp").html('Updated ' + data.updated);
                }
                setTimeout(lastUpdate, weatherRefreshTime)
            }
        }, error: function (request) {
            console.log("Health Data Error:\n" + request.responseText);
        }
    });
}

lastUpdate();
<?php } ?>
