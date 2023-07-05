<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: fcn/api/auth/getToken.php
 * Load an API token
 */

if (isset($_GET['token'])) {
    include(APP_BASE_PATH . '/fcn/api/auth/token.php');
    /**
     * @var $authToken
     */
    $auth = checkToken($authToken);
    if ($auth === false) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
        echo 'Invalid Auth Token';
        exit();
    }
}
