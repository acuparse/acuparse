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
 * File: fcn/api/json/getTowers.php
 * Include for Tower Data
 */

/**
 * @var string $initString
 * @var mysqli_result $result
 * @var string $towerCount
 */

$i = 1;
require(APP_BASE_PATH . '/fcn/weather/getCurrentTowerData.php');
echo $initString;
while ($row = mysqli_fetch_assoc($result)) {
    $sensor = $row['sensor'];
    echo "\"$sensor\": ";
    $getTowerData = new getCurrentTowerData($sensor);
    echo json_encode($getTowerData->getJSONConditions());
    if ($i < $towerCount) {
        echo ",";
    }
    $i++;
}
