<?php
/**
 * File: fcn/cron.php
 * Called by system Cron every minute to send data to Weather Underground and update database
 */

require(dirname(__DIR__) . '/inc/config.php');

// Process Pressure

$sql = "SELECT `raw_hpa` FROM `pressure` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$raw_pressure_hPa = $result['raw_hpa'];
$pressure_hPa = ($raw_pressure_hPa + $PRESSURE_OFFSET);
$pressure_inHg = $pressure_hPa / 33.8638866667;

// Process Wind Speed
$sql = "SELECT `speedMS` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windS_ms = $result['speedMS'];
$windS_kmh = $windS_ms * 3.6;
$windS_mph = $windS_ms * 2.23694;

// Process Average Wind Speed over the last 2 minutes
$sql = "SELECT AVG(speedMS) AS `avg_speedMS` FROM `windspeed` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windspdmph_avg2m = $result['avg_speedMS'];
$windspdmph_avg2m_mph = $windspdmph_avg2m * 2.23694;

// Process Wind Direction
$sql = "SELECT `degrees` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windDEG = $result['degrees'];

// Process Average Wind Direction over the last 2 minutes
$sql = "SELECT AVG(degrees) AS `avg_degrees` FROM `winddirection` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$windDEG_avg2m = $result['avg_degrees'];

// Process Temp
$sql = "SELECT `tempC` FROM `temperature` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$tempC = $result['tempC'];
$tempF = $tempC * 9/5 + 32;

// Process Humidity
$sql = "SELECT `relH` FROM `humidity` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$relH = $result['relH'];

// Process Rainfall
$sql = "SELECT SUM(`raw`) AS `rainfall` FROM `rainfall` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$rain = $result['rainfall'];
$rainin = $rain * 0.0393701;
$rainmm = $rain;

$sql = "SELECT SUM(`raw`) AS `rainfall_total` FROM `rainfall` WHERE DATE(`timestamp`) = CURDATE()";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$total_rainfall = $result['rainfall_total'];
$total_rainfallin = $total_rainfall * 0.0393701;
$total_rainfallmm = $total_rainfall;

// Calculate Dew Point
$dewptC = ((pow(($relH / 100), 0.125)) * (112 + 0.9 * $tempC) + (0.1 * $tempC) - 112);
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

// Send data to wunderground
$wu_query_url = 'http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?ID=' . $wu_id . '&PASSWORD=' . $wu_password;
$wu_query = '&tempf=' . $tempF . '&winddir=' . $windDEG . '&winddir_avg2m=' . $windDEG_avg2m . '&windspeedmph=' . $windS_mph . '&windspdmph_avg2m=' . $windspdmph_avg2m_mph . '&baromin=' . $pressure_inHg . '&humidity=' . $relH . '&dewptf=' . $dewptF . '&rainin=' . $rainin . '&dailyrainin=' . $total_rainfallin;
$wu_query_static = '&dateutc=now&softwaretype=other&action=updateraw';

// Make sure new data is being sent

$sql = "SELECT `wu_query` FROM `weather` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysqli_fetch_array(mysqli_query($conn, $sql));
$last_update = $result['wu_query'];

if ($last_update != $wu_query) {

    $wu_query_result = file_get_contents($wu_query_url . $wu_query . $wu_query_static);

    // Save to DB
    $sql = "INSERT INTO `weather` (`tempC`, `tempF`, `windSms`, `windSkmh`, `windSmph`, `windSmph_avg2m`, `windDEG`, `windD`, `windDEG_avg2m`, `relH`, `pressurehPa`, `pressureinHg`, `dewptC`, `dewptF`, `rainin`, `rainmm`, `total_rainin`, `total_rainmm`, `wu_query`,`wu_result`) VALUES ('$tempC', '$tempF', '$windS_ms', '$windS_kmh', '$windS_mph', '$windspdmph_avg2m_mph', '$windDEG', '$windD', '$windDEG_avg2m', '$relH', '$pressure_hPa', '$pressure_inHg', '$dewptC', '$dewptF', '$rainin', '$rainmm', '$total_rainfallin', '$total_rainfallmm', '$wu_query', '$wu_query_result')";
    $result = mysqli_query($conn, $sql);

    // Log
    syslog(LOG_DEBUG,"WU Query: $wu_query - $wu_query_result");
}

else {
    // Log
    syslog(LOG_DEBUG,"WU Query Failed: Repeat entry, station might be offline!");
}
