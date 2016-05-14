<?php
/**
 * File: config.php
 */

// Sitewide Variables
$site_name = 'Golden Heights Weather Station';
$site_desc = 'Live weather from Golden Heights, Sturgeon County, Alberta, Canada';
$site_url = 'http://weather.maxpower.co';
$date = date("Y-m-d H:i:s");

// Weather Underground Settings
$wu_id = 'IALBERTA517';
$wu_password = 'P0pc0rn';

// Bridge Config
$MACADDRESS = '24C86E0479FB'; //Acurite Bridge MAC Address

// Pressure Offset from Sea Level
$PRESSURE_OFFSET = 127;

// DATABASE CONFIG:
$db_host="localhost"; // DB Host
$db_name="weather"; // DB Name
$db_username="root"; // Username
$db_password="Summer01"; // Password
// Create Connection
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
