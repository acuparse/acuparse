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
 * File: src/fcn/account/delete.php
 * Delete a User
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 */

$user = (int)mysqli_real_escape_string($conn,
    filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_STRING));

// Don't delete the logged in user
if ($user === $_SESSION['uid']) {
    // Log it
    syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: User $user cannot delete themselves");
    // Display message
    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Cannot delete your own account!</div>';
} else {
    $result = mysqli_query($conn, "DELETE FROM `users` WHERE `uid` = '$user'");
    // If the insert Query was successful.
    if (mysqli_affected_rows($conn) === 1) {
        // Log it
        syslog(LOG_INFO, "(SYSTEM){USER}: User $user deleted successfully");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>User Deleted Successfully!</div>';
    } else {
        // Log it
        syslog(LOG_ERR, "(SYSTEM){USER}[ERROR]: Deleting user $user failed!");
        // Display message
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Oops, something went wrong deleting the user!</div>';
    }
}
// Redirect to view accounts
header("Location: /admin/account?view");
exit();
