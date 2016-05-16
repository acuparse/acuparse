<?php
/**
 * File: cron.php
 */
require '/var/www/html/inc/config.php';

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
$windS_mph = $windS_ms * 2.23694;

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
$rainin = $rain / 2540;
$rainmm = $rain / 1000;

$sql = "SELECT SUM(`raw`) AS `rainfall_total` FROM rainfall WHERE DATE(`timestamp`) = CURDATE()";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$total_rainfall = $result['rainfall_total'];
$total_rainfallin = $total_rainfall / 2540;
$total_rainfallmm = $total_rainfall /1000;

// Calculate Dew Point
$dewptC = ((pow(($humidity / 100), 0.125)) * (112 + 0.9 * $tempC) + (0.1 * $tempC) - 112);
$dewptF = ($dewptC * 9/5) + 32;

// Convert wind direction into degrees
switch ($windDEG) {
    case '0.00':
        $windD = 'N';
        break;
    case '22.5':
        $windD = 'NNE';
        break;
    case '45':
        $windD = 'NE';
        break;
    case '67.5':
        $windD = 'ENE';
        break;
    case '90':
        $windD = 'E';
        break;
    case '112.5':
        $windD = 'ESE';
        break;
    case '135':
        $windD = 'SE';
        break;
    case '157.5':
        $windD = 'SSE';
        break;
    case '180':
        $windD = 'S';
        break;
    case '202.5':
        $windD = 'SSW';
        break;
    case '225':
        $windD = 'SW';
        break;
    case '247.5':
        $windD = 'WSW';
        break;
    case '270.0':
        $windD = 'W';
        break;
    case '292.5':
        $windD = 'WNW';
        break;
    case '315.0':
        $windD = 'NW';
        break;
    case '337.5':
        $windD = 'NNW';
        break;
}

// Change rain to 0 when sending to WU until fixed

$rainin_wu = 0;
$total_rainfallin_wu = 0;

// Send data to wunderground
$wu_query_url = 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?ID=' . $wu_id . '&PASSWORD=' . $wu_password;
$wu_query = '&tempf=' . $tempF . '&winddir=' . $windDEG . '&windspeedmph=' . $windS_mph . '&baromin=' . $pressure_inHg . '&humidity=' . $humidity . '&dewptf=' . $dewptF . '&rainin=' . $rainin_wu . '&dailyrainin=' . $total_rainfallin_wu;
$wu_query_static = '&dateutc=now&softwaretype=other&action=updateraw';
$wu_query_result = file_get_contents($wu_query_url . $wu_query . $wu_query_static);

// Save to DB
$sql = "INSERT INTO `weather` (`tempC`, `tempF`, `windSms`, `windSkmh`, `windSmph`, `windDEG`, `windD`, `relH`, `pressurehPa`, `pressureinHg`, `dewptC`, `dewptF`, `rain`, `total_rain`, `wu_query`,`wu_result`) VALUES ('$tempC', '$tempF', '$windS_ms', '$windS_kmh', '$windS_mph', '$windDEG', '$windD', '$humidity', '$pressure_hPa', '$pressure_inHg', '$dewptC', '$dewptF', '$rain', '$total_rainfall', '$wu_query', '$wu_query_result')";
$result = mysqli_query($conn, $sql);

// Log
syslog(LOG_DEBUG,"Message: $wu_query");