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
 * File: src/pub/admin/status.php
 * Admin Sensor Status
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

function rssiConvert($rssi)
{
    $result = array();
    switch ($rssi) {
        case 1:
            $signal = 'Poor';
            $colour = 'indianred';
            break;
        case 2:
            $signal = 'Fair';
            $colour = 'yellowgreen';
            break;
        case 3:
            $signal = 'Good';
            $colour = 'greenyellow';
            break;
        case 4:
            $signal = 'Excellent';
            $colour = 'green';
            break;
        default:
            $signal = $rssi;
            $colour = null;
    }
    array_push($result, $signal, $colour);
    return $result;
}

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {
    $pageTitle = 'Sensor Status';
    include(APP_BASE_PATH . '/inc/header.php');
    ?>
    <section id="sensor-status" class="sensor-status">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Sensor Status</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <table class="table table-light table-hover table-responsive-sm" id="sensor-table">
                    <thead>
                    <tr>
                        <th scope="col"><strong>ID</strong></th>
                        <th scope="col"><strong>Name</strong></th>
                        <th scope="col"><strong>Battery</strong></th>
                        <th scope="col"><strong>Signal</strong></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($config->station->access_mac != 0) {
                        $result = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT `battery`, `rssi` FROM `access_status` ORDER BY `last_update` DESC LIMIT 1"));
                        ?>
                        <tr id="<?= $config->station->access_mac; ?>">
                            <th scope="row"><?= ltrim($config->station->access_mac, '0'); ?></th>
                            <td>Access</td>
                            <td style="background-color: <?= ($result['battery'] = 'normal') ? 'lightgreen' : 'orangered'; ?>"><?= ucfirst($result['battery']); ?></td>
                            <td>N/A</td>
                        </tr>
                    <?php }
                    if ($config->station->sensor_atlas != 0) {
                        $result = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT `battery`, `rssi` FROM `atlas_status` ORDER BY `last_update` DESC LIMIT 1"));
                        ?>
                        <tr id="<?= $config->station->sensor_atlas; ?>">
                            <th scope="row"><?= ltrim($config->station->sensor_atlas, '0'); ?></th>
                            <td>Atlas</td>
                            <td style="background-color: <?= ($result['battery'] = 'normal') ? 'lightgreen' : 'orangered'; ?>"><?= ucfirst($result['battery']); ?></td>
                            <?php
                            $rssi = rssiConvert($result['rssi']);
                            ?>
                            <td style="background-color: <?= $rssi[1]; ?>;"><?= $rssi[0]; ?></td>
                        </tr>
                    <?php }
                    if ($config->station->sensor_5n1 != 0) {
                        $result = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT `battery`, `rssi` FROM `5n1_status` ORDER BY `last_update` DESC LIMIT 1"));
                        ?>
                        <tr id="<?= $config->station->sensor_5n1; ?>">
                            <th scope="row"><?= ltrim($config->station->sensor_5n1, '0'); ?></th>
                            <td>5N1</td>
                            <td style="background-color: <?= ($result['battery'] = 'normal') ? 'lightgreen' : 'orangered'; ?>"><?= ucfirst($result['battery']); ?></td>
                            <?php
                            $rssi = rssiConvert($result['rssi']);
                            ?>
                            <td style="background-color: <?= $rssi[1]; ?>;"><?= $rssi[0]; ?></td>
                        </tr>
                    <?php } ?>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange` ASC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $name = $row['name'];
                        $sensor = $row['sensor'];
                        ?>
                        <tr id="<?= $sensor; ?>">
                            <th scope="row"><?= ltrim($sensor, '0'); ?></th>
                            <td><?= $name; ?></td>
                            <?php
                            $result2 = mysqli_fetch_assoc(mysqli_query($conn,
                                "SELECT `battery`, `rssi` FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
                            ?>
                            <td style="background-color: <?= ($result2['battery'] = 'normal') ? 'lightgreen' : 'orangered'; ?>"><?= ucfirst($result2['battery']); ?></td>
                            <?php
                            $rssi = rssiConvert($result2['rssi']);
                            ?>
                            <td style="background-color: <?= $rssi[1]; ?>;"><?= $rssi[0]; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col text-center">
                        <button type="button" class="btn btn-success" onclick="location.href = '/admin'"><i
                                    class="fas fa-check-circle" aria-hidden="true"></i> Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
// Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in or user is not an admin
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
