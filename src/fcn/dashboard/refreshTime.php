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
 * File: src/fcn/dashboard/refreshTime.php
 * Return the dashboard refresh rate
 */

function dashboardRefreshScripts(): string
{
    require(dirname(__DIR__, 2) . '/inc/loader.php');
    /**
     * @var object $config Global Config
     */

    if ($config->station->realtime === true) {
        $weatherRefreshTime = 20000;
    } elseif ($config->station->primary_sensor === 0) {
        $weatherRefreshTime = 150000;
    } else {
        $weatherRefreshTime = 80000;
    }

    return '<script>let weatherRefreshTime = ' . $weatherRefreshTime . ';</script><script defer src="/js/dashboardRefresh.js.php""></script>';
}
