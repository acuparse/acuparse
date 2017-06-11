<?php
/**
 * Acuparse - AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2017 Maxwell Power
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
 * File: src/usr/config.new.php
 * Default Site Configuration Variables
 */

return (object)array(

    // Database config variables
    'mysql' => (object)array(
        'host' => 'localhost', // Database Host
        'database' => 'acuparse', // Database to use
        'username' => 'acuparse', // Database User
        'password' => '', // Database Password
        'trim' => 0 // Database trim level
    ),

    // Sensor specific config variables
    'station' => (object)array(
        'hub_mac' => '000000000000', // smartHUB MAC Address
        'sensor_5n1' => '00000000', // 5n1 Sensor ID (8 Digits including leading 0's)
        'towers' => false, // Tower Sensors Active? True/False
        'baro_offset' => 0 // inHg. Adjust this as required to match the offset for your elevation
    ),

    // Site specific config variables
    'site' => (object)array(
        'name' => 'Acuparse Weather Station', // Name of the weather station
        'desc' => 'A Simple Weather Station Script', // Station Description
        'location' => 'Somewhere, Earth', // Station Location
        'hostname' => 'example.com', // Station Domain or IP Address
        'email' => 'weatherstation@example.com', // System email address.
        'timezone' => 'UTC', // Station Timezone - http://php.net/manual/en/timezones.php
        'display_date' => 'l, j F Y G:i:s T', // Header Date Format
        'lat' => 0, // Station Latitude - Decimal Format
        'long' => 0, // Station Longitude - Decimal Format
        'imperial' => false, // Use imperial measurements by default? True/False
        'theme' => 'clean', // The CSS theme to use
        'updates' => true // Check for updates?
    ),

    // IP camera variables
    'camera' => (object)array(
        'enabled' => false, // Web Camera Enabled, True/False
        'text' => 'Image updated every 15 minutes.' // Text displayed camera under image
    ),

    // Archive variables
    'archive' => (object)array(
        'enabled' => false, // Archive Enabled, True/False
    ),

    // Contact variables
    'contact' => (object)array(
        'enabled' => false, // Archive Enabled, True/False
    ),

    // Google Settings
    'google' => (object)array(

        // Recaptcha
        'recaptcha' => (object)array(
            'enabled' => false, // Use recaptcha? True/False
            'secret' => '', // Recaptcha Secret
            'sitekey' => '' // Recaptcha Sitekey
        ),

        // Analytics
        'analytics' => (object)array(
            'enabled' => false, //true or false
            'id' => '' // Google Analytics ID
        ),
    ),

    // External Updater Settings
    'upload' => (object)array(

        // Weather Underground Settings
        'wu' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php' // Server URL
        ),

        // PWS Weather Settings
        'pws' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => 'http://www.pwsweather.com/pwsupdate/pwsupdate.php' // Server URL
        ),

        // CWOP Settings
        'cwop' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'location' => '', // CWOP Coordinates in format "ddmm.hhN/dddmm.hhW"
            'interval' => '10 minutes', // Update Frequency. Should be at least 5 minutes; 10 is good.
            'url' => 'cwop.aprs.net' // CWOP Server to send updates to
        ),

        // MyAcurite
        'myacurite' => (object)array(
            'enabled' => true, // true or false
            'url' => 'http://hubapi.myacurite.com'
        ),
    ),

    // Email Outage Alerts
    'outage_alert' => (object)array(
        'enabled' => false, // true or false
        'offline_for' => '5 minutes', // Time the station is offline before sending emails.
        'interval' => '1 hour' // How often to email admin about outages.
    ),

    // Debug Settings
    'debug' => (object)array(
        'logging' => true, // Debug logging to Syslog. True/False
        'server' => (object)array(
            'enabled' => false, //true or false
            'url' => 'http://' // The url for your development system. eg. http://127.0.0.1
        ),
    ),

    'version' => (object)array(
        'app' => '2.1.1',
        'schema' => '2.1'
    ),

    'intervals' => (object)array(
        0 => '5 minutes',
        1 => '10 minutes',
        2 => '15 minutes',
        3 => '25 minutes',
        4 => '30 minutes',
        5 => '45 minutes',
        6 => '1 hour',
    ),
);
