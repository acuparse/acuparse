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
 * File: src/fcn/towers/delete.php
 * Delete a Tower
 */

/** @var mysqli $conn Global MYSQL Connection */

$towerSensorID = mysqli_real_escape_string($conn,
    filter_input(INPUT_GET, 'sensor_id', FILTER_SANITIZE_STRING));
$result = mysqli_query($conn, "DELETE FROM `towers` WHERE `sensor` = '$towerSensorID'");
// If the insert Query was successful.
if (mysqli_affected_rows($conn) === 1) {
    $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Tower Deleted Successfully!</div>';
    syslog(LOG_INFO, "(SYSTEM){TOWER}: Tower $towerSensorID deleted successfully");
} else {
    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the tower!</div>';
    syslog(LOG_ERR, "(SYSTEM){TOWER}[ERROR]: Deleting tower $towerSensorID failed!");
}
// Redirect to admin
header("Location: /admin/tower?view");
exit();
