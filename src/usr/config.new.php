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
        'trim' => 1, // Database trim level
    ),

    // Sensor specific config variables
    'station' => (object)array(
        'device' => null, // Device to get readings from
        'access_mac' => null, // Access MAC Address
        'hub_mac' => null, // smartHUB MAC Address
        'primary_sensor' => null, // Primary Sensor
        'sensor_atlas' => null, // Atlas Sensor ID (8 Digits including leading 0's)
        'sensor_iris' => null, // Iris Sensor ID (8 Digits including leading 0's)
        'towers' => false, // Tower Sensors Active? True/False
        'towers_additional' => false, // Show additional High/Low values
        'baro_offset' => 0, // inHg. Adjust this as required to match the offset for your elevation
        'lightning_source' => 0, // Lightning (0-none/1-atlas/2-tower/3-both)
        'filter_access' => true, // Filter out of range Access readings
    ),

    // Site specific config variables
    'site' => (object)array(
        'name' => 'Acuparse Weather Station', // Name of the weather station
        'desc' => 'A Simple Weather Station Script', // Station Description
        'location' => 'Somewhere, Earth', // Station Location
        'hostname' => 'localhost', // Station Domain or IP Address
        'email' => 'noreply@localhost', // System email address.
        'timezone' => 'Etc/UCT', // Station Timezone - http://php.net/manual/en/timezones.php
        'display_date' => 'l, j F Y G:i:s T', // Live Date Format - https://www.php.net/manual/en/datetime.format.php
        'dashboard_display_time' => 'H:i', // Dashboard Time Format
        'dashboard_display_date' => 'j M @ H:i', // Dashboard Date Format
        'dashboard_display_date_full' => 'j M Y @ H:i', // Full Dashboard Date Format
        'date_api_json' => 'c', // Header Date Format
        'lat' => 0, // Station Latitude - Decimal Format
        'long' => 0, // Station Longitude - Decimal Format
        'imperial' => false, // Use imperial measurements by default? True/False
        'hide_alternate' => 'false', // Hide alternate measurements?
        'theme' => 'clean', // The CSS theme to use
        'updates' => true, // Check for updates?
    ),

    // IP camera variables
    'camera' => (object)array(
        'enabled' => false, // Web Camera Enabled, True/False
        'text' => 'Image updated every X minutes.', // Text displayed camera under image
        'sort' => (object)array(
            'today' => 'ascending', // Today's Sort Order
            'archive' => 'ascending', // Archive Sort Order
        ),
    ),

    // Archive variables
    'archive' => (object)array(
        'enabled' => true, // Archive Enabled, True/False
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
            'sitekey' => '', // Recaptcha Sitekey
        ),

        // Analytics
        'analytics' => (object)array(
            'enabled' => false, //true or false
            'id' => '', // Google Analytics ID
        ),
    ),

    // Mailgun
    'mailgun' => (object)array(
        'enabled' => false, // Use Mailgun? True/False
        'secret' => '', // Mailgun Secret
        'domain' => '', // Mailgun Sitekey
    ),

    // External Updater Settings
    'upload' => (object)array(
        // Master Sensor Settings
        'sensor' => (object)array(
            'external' => 'default', // default or tower
            'id' => '', // Tower ID
            'archive' => false, // true or false
        ),

        // Weather Underground Settings
        'wu' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php', // Server URL
        ),

        // PWS Weather Settings
        'pws' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => 'http://www.pwsweather.com/pwsupdate/pwsupdate.php', // Server URL
        ),

        // CWOP Settings
        'cwop' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'location' => '', // CWOP Coordinates in format "ddmm.hhN/dddmm.hhW"
            'interval' => '10 minutes', // Update Frequency. Should be at least 5 minutes; 10 is good.
            'url' => 'cwop.aprs.net', // CWOP Server to send updates to
        ),

        // Weathercloud Settings
        'wc' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // ID
            'key' => '', // Station Key
            'device' => '', // Device ID
            'url' => 'http://api.weathercloud.net/v01/set', // Weathercloud API path
        ),

        // Windy Settings
        'windy' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // ID
            'key' => '', // Station API Key
            'station' => '0', // Station ID
            'url' => 'http://stations.windy.com/pws/update', // Windy API path
        ),

        // Windguru Settings
        'windguru' => (object)array(
            'enabled' => false, // true or false
            'uid' => '', // User ID
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => 'http://www.windguru.cz/upload/api.php', // Server URL
        ),

        // OpenWeather Settings
        'openweather' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'key' => '', // Password
            'url' => 'http://api.openweathermap.org/data/3.0/measurements', // Server URL
        ),

        // Generic Settings
        'generic' => (object)array(
            'enabled' => false, // true or false
            'id' => '', // Station ID
            'password' => '', // Password
            'url' => '', // Server URL
        ),

        // MyAcuRite
        'myacurite' => (object)array(
            'access_enabled' => true, // true or false
            'access_url' => 'https://atlasapi.myacurite.com', // MyAcuRite API
            'pass_unknown' => false, // Pass unknown sensors to MyAcuRite?
        ),
    ),

    // Email Outage Alerts
    'outage_alert' => (object)array(
        'enabled' => false, // true or false
        'offline_for' => '10 minutes', // Time the station is offline before sending emails.
        'interval' => '1 hour', // How often to email admin about outages.
    ),

    // Debug Settings
    'debug' => (object)array(
        'logging' => true, // Debug logging to Syslog. True/False
        'server' => (object)array(
            'show' => false, // Show the debug server tab
            'enabled' => false, //true or false
            'url' => null, // The IP for your development system. eg. 127.0.0.1
        ),
    ),

    // Default Intervals
    'intervals' => (object)array(
        0 => '5 minutes',
        1 => '10 minutes',
        2 => '15 minutes',
        3 => '25 minutes',
        4 => '30 minutes',
        5 => '45 minutes',
        6 => '1 hour',
    ),

    // Application/Database Version
    'version' => (object)array(
        'app' => '3.3.1',
        'schema' => '3.3',
        'installHash' => null,
    ),
);
