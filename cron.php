<?php
/**
 * File: cron.php
 */

$PRESSURE_OFFSET = 127;

// DATABASE CONFIG:
$db_host="localhost";
$db_username="root";
$db_password="Summer01";
$db_name="weather";
// Create Connection
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process Pressure

$sql = "SELECT `raw_hpa` FROM `pressure` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$raw_pressure_hPa = $result['raw_hpa'];
$pressure_hPa = ($raw_pressure_hPa + $PRESSURE_OFFSET);
$pressure_inHg = $pressure_hPa / 33.8638866667;

// Process Wind Speed
$sql = "SELECT `speedms` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windS_ms = $result['speedms'];
$windS_kmh = $windS_ms * 3.6;
$windS_mph = 2.23694 * $windS_ms;

// Process Wind Direction
$sql = "SELECT `degrees` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windDEG = $result['degrees'];

// Process Temp
$sql = "SELECT `tempc` FROM `temperature` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$tempC = $result['tempc'];
$tempF = $tempC * 9/5 + 32;

// Process Humidity
$sql = "SELECT `humidity` FROM `humidity` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$humidity = $result['humidity'];

// Process Rainfall
$sql = "SELECT `raw` FROM `rainfall` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$rain = $result['raw'];

$sql = "SELECT SUM(`raw`) AS `rainfall_total` FROM rainfall WHERE DATE(`timestamp`) = CURDATE()";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$total_rainfall = $result['rainfall_total'];

// Calculate Dew Point
$dewptC = ((pow(($humidity / 100), 0.125)) * (112 + 0.9 * $tempC) + (0.1 * $tempC) - 112);
$dewptF = ($dewptC * 9/5) + 32;

// Send data to wunderground
$wu_id = 'IALBERTA517';
$wu_password = 'P0pc0rn';
$wu_query_url = 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?ID=' . $wu_id . '&PASSWORD=' . $wu_password;
$wu_query = '&tempf=' . $tempF . '&winddir=' . $windDEG . '&windspeedmph=' . $windS_mph . '&baromin=' . $pressure_inHg . '&humidity=' . $humidity . '&dewptf=' . $dewptF . '&rainin=' . $rain . '&dailyrainin=' . $total_rainfall;
$wu_query_static = '&dateutc=now&softwaretype=other&action=updateraw';
$wu_query_result = file_get_contents($wu_query_url . $wu_query . $wu_query_static);

// Save to DB
$sql = "INSERT INTO `weather` (`tempC`, `tempF`, `windSms`, `windSkmh`, `windSmph`, `windD`, `relH`, `pressurehPa`, `pressureinHg`, `dewptC`, `dewptF`, `rain`, `total_rain`, `wu_query`,`wu_result`) VALUES ('$tempC', '$tempF', '$windS_ms', '$windS_mph', '$windS_kmh', '$windDEG', '$humidity', '$pressure_hPa', '$pressure_inHg', '$dewptC', '$dewptF', '$rain', '$total_rainfall', '$wu_query', '$wu_query_result')";
$result = mysqli_query($conn, $sql);

// Log
syslog(LOG_DEBUG,"Message: $wu_query");