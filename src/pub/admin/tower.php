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
 * File: src/pub/admin/tower.php
 * Admin Tower Sensors
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

if (isset($_SESSION['UserLoggedIn']) && $_SESSION['IsAdmin'] === true) {
    $page_title = 'Tower Admin Functions | ' . $config->site->name;
    include(APP_BASE_PATH . '/inc/header.php');

    // Tower Sensors

    // Add Tower
    if (isset($_GET['add']) && $config->station->towers = true) {

        // Insert the new tower into the database
        if (isset($_GET['do'])) {
            $tower_name = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'tower_name', FILTER_SANITIZE_STRING));
            $tower_sensor_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'tower_sensor_id', FILTER_SANITIZE_STRING));
            $tower_sensor_id = sprintf('%08d', $tower_sensor_id); // Tower ID should be 8 digits
            $display = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'display', FILTER_SANITIZE_STRING));
            $private = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'private', FILTER_SANITIZE_STRING));
            $result = mysqli_query($conn,
                "INSERT INTO `towers` (`name`, `sensor`, `arrange`, `private`) VALUES ('$tower_name', '$tower_sensor_id', '$display', '$private')");
            if (!$result) {
                syslog(LOG_ERR, "Adding tower $tower_sensor_id - $tower_name failed: " . mysqli_error($conn));
            }

            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Added Successfully!</div>';
                syslog(LOG_INFO, "Tower $tower_sensor_id - $tower_name added successfully");
            } else {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong creating new tower!</div>';
                syslog(LOG_ERR, "Adding tower $tower_sensor_id - $tower_name failed");
            }
            header("Location: /admin");
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
            <section id="add_tower">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">Add Tower Sensor</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4 ">
                        <div id="add_admin_user">
                            <p>Enter the details for the new tower:</p>
                            <form class="form" role="form" action="/admin/tower?add&do" method="POST">
                                <div class="form-group">
                                    <label for="tower_sensor_id">Tower Sensor ID:</label>
                                    <input type="text" class="form-control" name="tower_sensor_id" id="tower_sensor_id"
                                           placeholder="Sensor ID" maxlength="8" required>
                                </div>
                                <div class="form-group">
                                    <label for="tower_name">Tower Display Name:</label>
                                    <input type="text" class="form-control" name="tower_name" id="tower_name"
                                           placeholder="Tower Name" maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <strong>Private Sensor?</strong><br>
                                    <label class="radio-inline"><input type="radio" name="private" id="private"
                                                                       value="1" required>Yes</label>
                                    <label class="radio-inline"><input type="radio" name="private" id="private"
                                                                       value="0" checked="checked">No</label>
                                </div>
                                <input type="hidden" name="display" id="display" value="<?= $arrange; ?>">
                                <button type="submit" id="submit" value="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <?php
        }
    } // Delete Tower Sensor
    elseif (isset($_GET['delete'])) {
        $tower_sensor_id = mysqli_real_escape_string($conn,
            filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
        $result = mysqli_query($conn, "DELETE FROM `towers` WHERE `sensor` = '$tower_sensor_id'");
        // If the insert Query was successful.
        if (mysqli_affected_rows($conn) === 1) {
            $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Deleted Successfully!</div>';
            syslog(LOG_INFO, "Tower $tower_sensor_id deleted successfully");
        } else {
            $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the tower!</div>';
            syslog(LOG_ERR, "Deleting tower $tower_sensor_id failed!");
        }
        // Redirect to admin
        header("Location: /admin/tower?view");
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
        <section id="view_tower">
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4">
                    <h2 class="page-header">View Tower Sensors</h2>
                </div>
            </div>
            <div class="row">
                <p>Drag to reorder, click to edit/delete</p>
                <div class="col-lg-4 col-lg-offset-4">
                    <div id="tower_sensors">
                        <table class="table table-responsive tower_sensor_display" id="sortTable">
                            <thead>
                            <tr>
                                <td><strong>Tower ID</strong></td>
                                <td><strong>Tower Name</strong></td>
                                <td><strong>Is Private?</strong></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM `towers` ORDER BY `arrange` ASC");
                            while ($row = mysqli_fetch_assoc($result)) {
                                $private = 'No';
                                if ($row['private'] === 1) {
                                    $private = 'Yes';
                                }
                                ?>
                                <tr id="arrange_<?= $row['sensor']; ?>">
                                    <td><a href="/admin/tower?edit&sensor_id=<?= ltrim($row['sensor'],
                                            '0'); ?>"><?= ltrim($row['sensor'], '0'); ?></td>
                                    <td>
                                        <strong><a href="/admin/tower?edit&sensor_id=<?= $row['sensor']; ?>"><?= $row['name']; ?>
                                                <span class="ui-icon ui-icon-arrowthick-2-n-s"></span></a></strong></td>
                                    <td><?= $private; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="archive" class="btn btn-primary center-block"
                            onclick="location.href = '/admin'"><i class="fa fa-chevron-left" aria-hidden="true"></i>
                        Done
                    </button>
                </div>
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
                        $("#sortTable tbody").sortable({
                            update: function () {
                                var serial = $(\'#sortTable tbody\').sortable(\'serialize\');
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
            $tower_name = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'tower_name', FILTER_SANITIZE_STRING));
            $tower_sensor_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'tower_sensor_id', FILTER_SANITIZE_STRING));
            $private = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'private', FILTER_SANITIZE_STRING));
            $original_tower_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_POST, 'original_tower_id', FILTER_SANITIZE_STRING));
            if ($tower_sensor_id === $original_tower_id) {
                $result = mysqli_query($conn,
                    "UPDATE `towers` SET `name` = '$tower_name', `private` = '$private' WHERE `sensor` = '$tower_sensor_id'");
            } else {
                $result = mysqli_query($conn,
                    "UPDATE `towers` SET `name` = '$tower_name', `sensor` = '$tower_sensor_id' , `private` = '$private' WHERE `sensor` = '$original_tower_id'");
            }
            // If the insert Query was successful.
            if (mysqli_affected_rows($conn) === 1) {
                $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Updated Successfully!</div>';
                syslog(LOG_INFO, "Tower $tower_sensor_id - $tower_name updated successfully");
            } else {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong updating the tower!</div>';
                syslog(LOG_ERR, "Updating tower $tower_sensor_id - $tower_name failed!");
            }
            header("Location: /admin/?menu");
        } // Show the edit form
        else {
            $tower_sensor_id = mysqli_real_escape_string($conn,
                filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
            $row = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT * FROM `towers` WHERE `sensor` = $tower_sensor_id"));
            ?>
            <section id="edit_tower">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">Edit Tower Sensor</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <p>Enter the new details for the tower:</p>
                        <form class="form" role="form" action="/admin/tower?edit&do" method="POST">
                            <div class="form-group">
                                <label for="tower_sensor_id">Tower Sensor ID:</label>
                                <input type="text" class="form-control" name="tower_sensor_id"
                                       id="tower_sensor_id"
                                       placeholder="Sensor ID" maxlength="8"
                                       value="<?= $row['sensor']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="tower_name">Tower Display Name:</label>
                                <input type="text" class="form-control" name="tower_name" id="tower_name"
                                       placeholder="Tower Name" maxlength="255"
                                       value="<?= $row['name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <strong>Private Sensor?</strong><br>
                                <label class="radio-inline"><input type="radio" name="private" id="private"
                                                                   value="1" <?php if ((bool)$row['private'] === true) {
                                        echo 'checked="checked"';
                                    } ?> required>Yes</label>
                                <label class="radio-inline"><input type="radio" name="private" id="private"
                                                                   value="0" <?php if ((bool)$row['private'] === false) {
                                        echo 'checked="checked"';
                                    } ?>>No</label>
                            </div>
                            <input type="hidden" value="<?= $row['sensor']; ?>" name="original_tower_id">
                            <button type="submit" id="submit" value="submit" class="btn btn-primary"><i
                                        class="fa fa fa-save" aria-hidden="true"></i> Save
                            </button>
                        </form>
                    </div>
                </div>
                <hr class="hr-dotted">
                <div class="row">
                    <h2 class="">Delete Tower?</h2>
                    <div class="col-lg-6 col-lg-offset-3  alert-danger"
                    <p>Click below to remove <?= $row['name']; ?>.</p>
                    <button type="button" id="delete" class="btn btn-danger center-block"
                            onClick="confirmDelete('/admin/tower?delete&sensor_id=<?= $row['sensor']; ?>')"><i
                                class="fa fa fa-remove" aria-hidden="true"></i> Delete Tower
                    </button>
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
}
