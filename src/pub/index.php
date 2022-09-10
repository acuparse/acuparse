<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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
 * File: src/pub/index.php
 * Main Index
 */

// Get the loader
require(dirname(__DIR__) . '/inc/loader.php');

/**
 * @var object $config Global Config
 */

/** @var string $installed */
if (!$installed) {
    header("Location: /admin/install");
    exit();
} elseif ((empty($config->station->access_mac) && empty($config->station->hub_mac)) && (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true)) {
    $_SESSION['messages'] = '<div class="alert alert-warning alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Missing Access/Hub Mac. Please configure in sensor settings.</div>';
    header("Location: /admin/settings");
    exit();
} elseif (empty($config->station->access_mac) && empty($config->station->hub_mac)) {
    $_SESSION['messages'] = '<div class="alert alert-warning alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Setup Required! Please log in as an admin to continue.</div>';
    header("Location: /admin/account");
    exit();
} else {
    $pageTitle = 'Live Weather';
    include(APP_BASE_PATH . '/inc/header.php');
    require_once APP_BASE_PATH . '/fcn/dashboard/index.php';
    require_once APP_BASE_PATH . '/fcn/dashboard/refreshTime.php';

    // Set the footer to include scripts required for this page
    $page_footer = dashboardRefreshScripts();

    include(APP_BASE_PATH . '/inc/footer.php');
}
