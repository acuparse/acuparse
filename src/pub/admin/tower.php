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
 * File: src/pub/admin/tower.php
 * Admin Tower Sensors
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

if (isset($_SESSION['authenticated']) && $_SESSION['admin'] === true) {
    $pageTitle = 'Tower Admin Functions';
    include(APP_BASE_PATH . '/inc/header.php');

    // Tower Sensors

    // Add Tower
    if (isset($_GET['add']) && $config->station->towers = true) {

        // Insert the new tower into the database
        if (isset($_GET['do'])) {
            $towerName = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
            $towerSensorID = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING));
            $towerSensorID = sprintf('%08d', $towerSensorID); // Tower ID should be 8 digits
            $display = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'display', FILTER_SANITIZE_STRING));
            $private = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'private', FILTER_SANITIZE_STRING));
            $result = mysqli_query($conn,
                "INSERT INTO `towers` (`name`, `sensor`, `arrange`, `private`) VALUES ('$towerName', '$towerSensorID', '$display', '$private')");
            if (!$result) {
                syslog(LOG_ERR,
                    "(SYSTEM)[ERROR]: Adding tower $towerSensorID - $towerName failed: " . mysqli_error($conn));
            }

            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Added Successfully!</div>';
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Tower $towerSensorID - $towerName added successfully");
            } else {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong creating new tower!</div>';
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Adding tower $towerSensorID - $towerName failed");
            }
            header("Location: /admin");
            die();
        } // Show the add new tower form
        else {
            // Get the next number in the arrangement order
            $result = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT MAX(arrange) AS `arrange` FROM `towers`"));
            $arrange = 1;
            if ($result['arrange'] >= 1) {
                $arrange = $result['arrange'];
                $arrange = ++$arrange;
            }
            ?>
            <div class="row">
                <div class="col">
                    <h1 class="page-header">Add New Tower</h1>
                </div>
            </div>
            <hr>
            <section id="add-tower" class="row add-tower">
                <div class="col-8 col-md-6 mx-auto">
                    <form class="form" role="form" action="/admin/tower?add&do" method="POST">
                        <div class="form-group">
                            <label for="tower-id">Tower ID:</label>
                            <input type="text" class="form-control" name="id" id="tower-id"
                                   placeholder="00000000" maxlength="8" required>
                        </div>
                        <div class="form-group">
                            <label for="tower-name">Display Name:</label>
                            <input type="text" class="form-control" name="name" id="tower-name"
                                   placeholder="Ground Level" maxlength="32" required>
                        </div>
                        <div class="form-group">
                            <strong>Private Sensor?</strong><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-1"
                                       value="1">
                                <label class="form-check-label" for="private-1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-0"
                                       value="0" checked="checked">
                                <label class="form-check-label" for="private-0">No</label>
                            </div>
                        </div>
                        <input type="hidden" name="display" id="display" value="<?= $arrange; ?>">
                        <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                    class="fas fa-save" aria-hidden="true"></i> Save
                        </button>
                        <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                    class="fas fa-ban" aria-hidden="true"></i> Cancel
                        </button>
                    </form>
                </div>
            </section>
            <?php
        }
    } // Delete Tower Sensor
    elseif (isset($_GET['delete'])) {
        $towerSensorID = mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
        $result = mysqli_query($conn, "DELETE FROM `towers` WHERE `sensor` = '$towerSensorID'");
        // If the insert Query was successful.
        if (mysqli_affected_rows($conn) === 1) {
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Deleted Successfully!</div>';
            syslog(LOG_INFO, "(SYSTEM)[INFO]: Tower $towerSensorID deleted successfully");
        } else {
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the tower!</div>';
            syslog(LOG_ERR, "(SYSTEM)[ERROR]: Deleting tower $towerSensorID failed!");
        }
        // Redirect to admin
        header("Location: /admin/tower?view");
        die();
    } // View/Edit Towers
    elseif (isset($_GET['view']) && $config->station->towers = true) {

        if (isset($_GET['arrange'])) {
            $arrange = $_POST['arrange'];
            for ($i = 0; $i < count($arrange); $i++) {
                mysqli_query($conn,
                    "UPDATE `towers` SET `arrange`= '$i' WHERE `sensor`= '$arrange[$i]'");
            }
            die;
        } ?>
        <div class="row">
            <div class="col">
                <h1 class="page-header">View Tower Sensors</h1>
            </div>
        </div>
        <hr>
        <section id="view-tower" class="row view-tower">

            <div class="col-md-8 col-12 mx-auto">
                <p>Drag to reorder, click to edit/delete</p>
                <table class="table table-light table-hover table-responsive-sm" id="tower-table">
                    <thead>
                    <tr>
                        <th scope="col"><strong>ID</strong></th>
                        <th scope="col"><strong>Name</strong></th>
                        <td scope="col"><strong>Private?</strong></td>
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
                                    '0'); ?>"><?= ltrim($row['sensor'], '0'); ?></th>
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
        </section>
        <?php
        // Load jquery ui and touch punch to allow the dragging of towers.
        $page_footer =
            '<script type="text/javascript" src="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
            <script type="text/javascript" src="/lib/mit/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
            <script>
                $(document).ready(
                    function () {
                        $("#tower-table tbody").sortable({
                            update: function () {
                                var serial = $(\'#tower-table tbody\').sortable(\'serialize\');
                                $.ajax({
                                    url: "/admin/tower?view&arrange",
                                    type: "post",
                                    data: serial
                                });
                            }
                        });
                    });
            </script>';
    } // Edit the tower sensor
    elseif (isset($_GET['edit'])) {

        // Process the update
        if (isset($_GET['do'])) {
            $towerName = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
            $towerSensorID = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING));
            $private = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'private', FILTER_SANITIZE_STRING));
            $originalTowerID = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'original-id', FILTER_SANITIZE_STRING));
            if ($towerSensorID === $originalTowerID) {
                $result = mysqli_query($conn,
                    "UPDATE `towers` SET `name` = '$towerName', `private` = '$private' WHERE `sensor` = '$towerSensorID'");
            } else {
                $result = mysqli_query($conn,
                    "UPDATE `towers` SET `name` = '$towerName', `sensor` = '$towerSensorID' , `private` = '$private' WHERE `sensor` = '$originalTowerID'");
            }
            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Updated Successfully!</div>';
                syslog(LOG_INFO, "(SYSTEM)[INFO]: Tower $towerSensorID - $towerName updated successfully");
            } else {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong updating the tower!</div>';
                syslog(LOG_ERR, "(SYSTEM)[ERROR]: Updating tower $towerSensorID - $towerName failed!");
            }
            header("Location: /admin/?menu");
            die();
        } // Show the edit form
        else {
            $towerSensorID = mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
            $row = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT * FROM `towers` WHERE `sensor` = $towerSensorID"));
            ?>
            <div class="row">
                <div class="col">
                    <h1 class="page-header">Editing Tower Details</h1>
                </div>
            </div>
            <hr>
            <section id="add-tower" class="add-tower">
                <div class="row">
                    <div class="col-8 col-md-6 mx-auto">
                        <form class="form" role="form" action="/admin/tower?edit&do" method="POST">
                            <div class="form-group">
                                <label for="tower-id">Tower ID:</label>
                                <input type="text" class="form-control" name="id" id="tower-id"
                                       value="<?= $row['sensor']; ?>" maxlength="8" required>
                            </div>
                            <div class="form-group">
                                <label for="tower-name">Display Name:</label>
                                <input type="text" class="form-control" name="name" id="tower-name"
                                       value="<?= $row['name']; ?>" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <strong>Private Sensor?</strong><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="private" id="private-1"
                                           value="1" <?= ((bool)$row['private'] === true) ? 'checked="checked"' : false; ?>>
                                    <label class="form-check-label" for="private-1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="private" id="private-0"
                                           value="0" <?= ((bool)$row['private'] === false) ? 'checked="checked"' : false; ?>>
                                    <label class="form-check-label" for="private-0">No</label>
                                </div>
                            </div>
                            <input type="hidden" value="<?= $row['sensor']; ?>" name="original-id">
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save
                            </button>
                            <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                        class="fas fa-ban" aria-hidden="true"></i> Cancel
                            </button>
                        </form>
                    </div>
                </div>
                <hr class="hr-dotted">

                <div class="row">
                    <div class="col">
                        <h2 class="page-header">Delete Tower?</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 col mx-auto">
                        <div class="alert alert-danger">
                            <p>Click below to remove <?= $row['name']; ?>.</p>
                            <button type="button" id="delete" class="btn btn-danger"
                                    onClick="confirmDelete('/admin/tower?delete&sensor_id=<?= $row['sensor']; ?>')"><i
                                        class="far fa-trash-alt" aria-hidden="true"></i> Delete Tower
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <?php
            // Set the page footer to include the delete warning dialogue.
            $page_footer = '
                    <script>    
                    function confirmDelete(url) {
                        if (confirm("Are you sure you want to delete this tower?")) {
                            window.open(url,"_self");
                        } else {
                            false;
                        }       
                    }
                    </script>';
        }
    }

    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in or user is not an admin
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
