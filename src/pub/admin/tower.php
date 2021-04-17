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
 * File: src/pub/admin/tower.php
 * Admin Tower Sensors
 */

// Get the loader
require(dirname(dirname(__DIR__)) . '/inc/loader.php');

/**
 * @var object $config Global Config
 */

if ((isset($_SESSION['authenticated'])) && ($_SESSION['admin'] === true)) {
    $pageTitle = 'Tower Admin Functions';
    include(APP_BASE_PATH . '/inc/header.php');

    // Tower Sensors

    // Add Tower
    if ((isset($_GET['add'])) && ($config->station->towers = true)) {

        require(APP_BASE_PATH . '/fcn/towers/add.php');

    } // Delete Tower Sensor
    elseif (isset($_GET['delete'])) {

        require(APP_BASE_PATH . '/fcn/towers/delete.php');

    } // View/Edit Towers
    elseif ((isset($_GET['view'])) && ($config->station->towers = true)) {

        require(APP_BASE_PATH . '/fcn/towers/view.php');

    } // Edit the tower sensor
    elseif (isset($_GET['edit'])) {

        require(APP_BASE_PATH . '/fcn/towers/edit.php');

    }

    // Get app footer
    include(APP_BASE_PATH . '/inc/footer.php');
} // Not logged in or user is not an admin
else {
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    header("Location: /");
}
