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
 * File: src/fcn/towers/view.php
 * View Towers
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */

if (isset($_GET['arrange'])) {
    $arrange = $_POST['arrange'];
    for ($i = 0; $i < count($arrange); $i++) {
        mysqli_query($conn,
            "UPDATE `towers` SET `arrange`= '$i' WHERE `sensor`= '$arrange[$i]'");
    }
    exit();
} ?>
    <section id="view-tower" class="view-tower">
        <div class="row">
            <div class="col">
                <h1 class="page-header">View Tower Sensors</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <p>Drag to reorder, click to edit/delete</p>
                <table class="table <?= ($config->site->theme === 'twilight') ? 'table-dark' : 'table-light'; ?> table-hover table-responsive-sm"
                       id="tower-table">
                    <thead>
                    <tr>
                        <th scope="col"><strong>ID</strong></th>
                        <th scope="col"><strong>Name</strong></th>
                        <th scope="col"><strong>Private?</strong></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange` ASC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $private = 'No';
                        if ($row['private'] === '1') {
                            $private = 'Yes';
                        }
                        ?>
                        <tr id="arrange_<?= $row['sensor']; ?>">
                            <th scope="row"><a href="/admin/tower?edit&sensor_id=<?= ltrim($row['sensor'],
                                    '0'); ?>"><?= ltrim($row['sensor'], '0'); ?></a></th>
                            <td>
                                <strong><a href="/admin/tower?edit&sensor_id=<?= $row['sensor']; ?>"><?= $row['name']; ?>
                                        <span class="ui-icon ui-icon-arrowthick-2-n-s"></span></a></strong></td>
                            <td><?= $private; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" onclick="location.href = '/admin'"><i
                            class="fas fa-check-circle" aria-hidden="true"></i> Done
                </button>
            </div>
        </div>
    </section>
<?php
// Load jquery ui and touch punch to allow the dragging of towers.
$page_footer = '
            <script src="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
            <script src="/lib/mit/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
            <script>
                $(document).ready(
                    function () {
                        $("#tower-table tbody").sortable({
                            update: function () {
                                let serial = $(\'#tower-table tbody\').sortable(\'serialize\');
                                $.ajax({
                                    url: "/admin/tower?view&arrange",
                                    type: "post",
                                    data: serial
                                });
                            }
                        });
                    });
            </script>';
