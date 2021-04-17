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
 * File: src/fcn/cron/towerData.php
 * Use Tower Readings
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $data Weather Data
 */

$sensor = $config->upload->sensor->id;
$result = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM `tower_data` WHERE `sensor` = '$sensor' ORDER BY `timestamp` DESC LIMIT 1"));
$data->tempF = round($result['tempF'], 1);
$data->tempC = round(($result['tempF'] - 32) * 5 / 9, 1);
$data->relH = $result['relH'];
$data->dewptC = ((pow(($data->relH / 100), 0.125)) * (112 + 0.9 * $data->tempC) + (0.1 * $data->tempC) - 112);
$data->dewptF = ($data->dewptC * 9 / 5) + 32;
