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
 * File: src/fcn/install/database.php
 * Load the Database and config file
 */

/**
 * @return array
 * @var object $config Global Config
 */

set_time_limit(0);
// Do some input filtering
$raw = $_POST;
array_walk_recursive($raw, function (&$i) {
    $i = filter_var(trim($i), FILTER_SANITIZE_STRING);
});
$_POST = $raw;

// Configure the database details
$config->mysql->host = $_POST['mysql']['host'];
$config->mysql->database = $_POST['mysql']['database'];
$config->mysql->username = $_POST['mysql']['username'];
$config->mysql->password = $_POST['mysql']['password'];
$config->mysql->trim = (int)$_POST['mysql']['trim'];

// Specific settings used when running tests
if (isset($_GET['ci'])) {
    $config->station->device = 0;
    $config->station->primary_sensor = 0;
    $config->station->access_mac = $_POST['station']['access_mac'];
    $config->station->sensor_atlas = sprintf('%08d', $_POST['station']['sensor_atlas']);
    $config->upload->myacurite->access_enabled = false;
    $config->site->updates = false;
    $config->site->imperial = true;
}

// Set the default timezone
$systemTimezone = getenv('TZ');
$config->site->timezone = (!empty($systemTimezone)) ? $systemTimezone : 'Etc/UTC';

// Generate the install hash
$installHash1 = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    mt_rand(1, 10))), 0,
    32);
$installHash2 = (string)substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    mt_rand(1, 10))), 0,
    8);
$installHash = "$installHash1-$installHash2";
$config->version->installHash = $installHash;

// Save the users config file
$configFilePath = APP_BASE_PATH . '/usr/config.php';
$export = var_export($config, true);
$export = str_ireplace('stdClass::__set_state', '(object)', $export);
$saveConfig = file_put_contents($configFilePath, '<?php return ' . $export . ';');
if ($saveConfig) {
    $sqlPath = dirname(dirname(dirname(__DIR__))) . '/sql';
    $testDB = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} --batch --skip-column-names -e \"SHOW DATABASES LIKE 'acuparse'\" | grep acuparse";
    $testDB = exec($testDB, $testDBOutput, $testDBReturn);
    if ($testDBReturn === 0) {
        // Load the database with the default schema
        $schema = $sqlPath . '/master.sql';
        $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
        $schema = exec($schema, $schemaOutput, $schemaReturn);
        if ($schemaReturn !== 0) {
            if (!unlink($configFilePath)) {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error Writing to Database. Could not remove config file. Please remove and try again. Return: ' . $schemaReturn . ' ' . $schemaOutput . '</div>';
                header("Location: /admin/install");
                exit(syslog(LOG_INFO, "(SYSTEM){INSTALLER}[ERROR]: Error Writing to Database. Could not remove config file. Please remove and try again."));
            } else {
                $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error Writing to Database. Please try again. Return: ' . $schemaReturn . ' ' . $schemaOutput . '</div>';
                header("Location: /admin/install");
                exit(syslog(LOG_INFO, "(SYSTEM){INSTALLER}[ERROR]: Error Writing to Database. Please try again."));
            }
        } else {
            if ($config->mysql->trim !== 0) {
                if ($config->mysql->trim === 1) {
                    $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable.sql';
                } elseif ($config->mysql->trim === 2) {
                    $schema = dirname(dirname(dirname(__DIR__))) . '/sql/trim/enable_xtower.sql';
                }
                $schema = "mysql -h{$config->mysql->host} -u{$config->mysql->username} -p{$config->mysql->password} {$config->mysql->database} < {$schema}";
                $schema = exec($schema, $schemaOutput, $schemaReturn);
                if ($schemaReturn !== 0) {
                    syslog(LOG_WARNING, "(SYSTEM){TRIM}[WARNING]: Failed Enabling Database Trimming | Result: $schemaReturn  | SQL: " . printf($schemaOutput));
                } else {
                    syslog(LOG_INFO, "(SYSTEM){TRIM}: Successfully Enabled Database Trimming");
                }
            }
        }
    } else {
        $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Error Connecting to Database!</div>';
        unlink(APP_BASE_PATH . '/usr/config.php');
        header("Location: /admin/install");
        exit(syslog(LOG_ERR, "(SYSTEM){INSTALLER}[ERROR]: Database Ping Failed"));
    }
    // Log it
    $_SESSION['messages'] = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Database Configuration Saved Successfully!</div>';
    header("Location: /admin/install/?account");
    exit(syslog(LOG_INFO, "(SYSTEM){INSTALLER}: Database configuration saved successfully"));
} else {
    // Log it
    $_SESSION['messages'] = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a>Saving Config File Failed!</div>';
    unlink(APP_BASE_PATH . '/usr/config.php');
    header("Location: /admin/install");
    exit(syslog(LOG_ERR, "(SYSTEM){INSTALLER}: Saving Config File Failed"));
}
