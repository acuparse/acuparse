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

/**
 * File: src/fcn/towers/edit.php
 * Edit a Tower
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 */

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
        $_SESSION['messages'] = '<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Tower Updated Successfully!</div>';
        syslog(LOG_INFO, "(SYSTEM){TOWER}: Tower $towerSensorID - $towerName updated successfully");
    } else {
        $_SESSION['messages'] = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Oops, something went wrong updating the tower!</div>';
        syslog(LOG_ERR, "(SYSTEM){TOWER}[ERROR]: Updating tower $towerSensorID - $towerName failed!");
    }
    header("Location: /admin/?menu");
    exit();
} // Show the edit form
else {
    $towerSensorID = mysqli_real_escape_string($conn,
        filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM `towers` WHERE `sensor` = $towerSensorID"));
    ?>
    <section id="edit-tower" class="edit-tower">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Editing Tower Details</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto alert alert-secondary">
                <form class="form" action="/admin/tower?edit&do" method="POST">
                    <div class="row">
                        <div class="col-3">

                            <label class="col-form-label" for="tower-id">Tower ID</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="id" id="tower-id"
                                   value="<?= $row['sensor']; ?>" maxlength="8" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                            <label class="col-form-label" for="tower-name">Display Name</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="name" id="tower-name"
                                   value="<?= $row['name']; ?>" maxlength="32" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <strong>Private Sensor?</strong><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-1"
                                       value="1" <?= ((bool)$row['private'] === true) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary" for="private-1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-0"
                                       value="0" <?= ((bool)$row['private'] === false) ? 'checked="checked"' : false; ?>>
                                <label class="form-check-label btn btn-primary" for="private-0">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                        class="fas fa-ban" aria-hidden="true"></i> Cancel
                            </button>
                            <input type="hidden" value="<?= $row['sensor']; ?>" name="original-id">
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr class="hr-dotted">

        <div class="row">
            <div class="col">
                <h2 class="page-header">Delete Sensor</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <div class="alert alert-danger">
                    <p>Click below to remove this Tower Sensor.</p>
                    <button type="button" id="delete" class="btn btn-danger"
                            onClick="confirmDelete('/admin/tower?delete&sensor_id=<?= $row['sensor']; ?>')"><i
                                class="far fa-trash-alt" aria-hidden="true"></i> Delete Tower
                    </button>
                </div>
            </div>
        </div>
    </section>
    <script>
        function confirmDelete(url) {
            if (confirm("Are you sure you want to delete this tower?")) {
                window.open(url, "_self");
            }
        }
    </script>
    <?php
}
