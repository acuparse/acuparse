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
 * File: src/fcn/towers/add.php
 * Add a Tower
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 */

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
            "(SYSTEM){TOWER}[ERROR]: Adding tower $towerSensorID - $towerName failed: " . mysqli_error($conn));
    }

    // If the insert Query was successful.
    if (mysqli_affected_rows($conn) === 1) {
        $_SESSION['messages'] = '<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Tower Added Successfully!</div>';
        syslog(LOG_INFO, "(SYSTEM){TOWER}: Tower $towerSensorID - $towerName added successfully");
    } else {
        $_SESSION['messages'] = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Oops, something went wrong creating new tower!</div>';
        syslog(LOG_ERR, "(SYSTEM){TOWER}[ERROR]: Adding tower $towerSensorID - $towerName failed");
    }
    header("Location: /admin");
    exit();
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
    <section id="add-tower" class="add-tower">
        <div class="row">
            <div class="col">
                <h1 class="page-header">Add New Tower</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8 col-12 mx-auto alert alert-secondary">
                <form class="form" action="/admin/tower?add&do" method="POST">
                    <div class="row">
                        <div class="col-3">
                            <label class="col-form-label" for="tower-id">Tower ID</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="id" id="tower-id"
                                   placeholder="00000000" maxlength="8" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-3">
                            <label class="col-form-label" for="tower-name">Display Name</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="name" id="tower-name"
                                   placeholder="Ground Level" maxlength="32" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <strong>Private Sensor?</strong><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-1"
                                       value="1">
                                <label class="form-check-label btn btn-primary" for="private-1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="private" id="private-0"
                                       value="0" checked="checked">
                                <label class="form-check-label btn btn-primary" for="private-0">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <input type="hidden" name="display" id="display" value="<?= $arrange; ?>">
                            <button type="button" class="btn btn-danger" onclick="location.href = '/admin'"><i
                                        class="fas fa-ban" aria-hidden="true"></i> Cancel
                            </button>
                            <button type="submit" id="submit" value="submit" class="btn btn-success"><i
                                        class="fas fa-save" aria-hidden="true"></i> Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php
}
