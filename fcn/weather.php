<?php
/**
 * File: fcn/weather.php
 * Get current weather conditions from database for display on main site
 */

function get_current_weather() {
    require(dirname(__DIR__) . '/inc/config.php');

    // 5n1 Sensor Data
    $time = date('j M Y @ H:i:s');
    $offset = date('Z') / 3600;

    // Process Wind Speed
    $sql = "SELECT `speedMS` FROM `windspeed` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $windS_ms = $result['speedMS'];
    $windS_kmh = round($windS_ms * 3.6, 2);
    $windS_mph = round($windS_ms * 2.23694, 2);

    // Process Wind Direction
    $sql = "SELECT `degrees` FROM `winddirection` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $windDEG = $result['degrees'];
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

    // Today's Peak Windspeed
    $sql = "SELECT `timestamp`, `speedMS` FROM `windspeed` WHERE `speedMS` = (SELECT MAX(speedMS) FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $max_wind_recorded = $result['timestamp'];
    $max_wind_recorded = strtotime($max_wind_recorded);
    $max_wind_recorded = date('H:i:s', $max_wind_recorded);

    $max_windSms = $result['speedMS'];
    $max_windSkmh = round($max_windSms * 3.6, 2);
    $max_windSmph = round($max_windSms * 2.23694, 2);

    // 10 Min Average Windspeed
    $sql = "SELECT AVG(speedMS) AS `avg_speedMS` FROM `windspeed` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $avg_windSms = $result['avg_speedMS'];
    $avg_windSkmh = round($avg_windSms * 3.6, 2);
    $avg_windSmph = round($avg_windSms * 2.23694, 2);

    // Process Pressure

    $sql = "SELECT `raw_hpa` FROM `pressure` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $raw_pressure_hPa = $result['raw_hpa'];
    $pressure_hPa = round(($raw_pressure_hPa + $PRESSURE_OFFSET), 2);
    $pressure_inHg = round($pressure_hPa / 33.8638866667, 2);

    // Process Temp
    $sql = "SELECT `tempC` FROM `temperature` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC = $result['tempC'];
    $tempC = round($tempC, 2);
    $tempF = round($tempC * 9/5 + 32, 2);

    // High Temp
    $sql = "SELECT `timestamp`, `tempC` FROM `temperature` WHERE `tempC` = (SELECT MAX(tempC) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $high_temp_recorded = $result['timestamp'];
    $high_temp_recorded = strtotime($high_temp_recorded);
    $high_temp_recorded = date('H:i:s', $high_temp_recorded);

    $tempC_high = $result['tempC'];
    $tempF_high = round($tempC_high * 9/5 + 32, 2);
    $tempC_high = round($tempC_high, 2);

    // Low Temp
    $sql = "SELECT `timestamp`, `tempC` FROM `temperature` WHERE `tempC` = (SELECT MIN(tempC) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $low_temp_recorded = $result['timestamp'];
    $low_temp_recorded = strtotime($low_temp_recorded);
    $low_temp_recorded = date('H:i:s', $low_temp_recorded);

    $tempC_low = $result['tempC'];
    $tempF_low = round($tempC_low * 9/5 + 32, 2);
    $tempC_low = round($tempC_low, 2);

    // Average Temp
    $sql = "SELECT AVG(tempC) AS `avg_tempC` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_avg = $result['avg_tempC'];
    $tempF_avg = round($tempC_avg * 9/5 + 32, 2);
    $tempC_avg = round($tempC_avg, 2);

    // Temp Trending
    $sql = "SELECT AVG(tempC) AS `trend_tempC` FROM `temperature` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_trend_1 = $result['trend_tempC'];

    $sql = "SELECT AVG(tempC) AS `trend_tempC` FROM `temperature` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_trend_2 = $result['trend_tempC'];
    $tempC_trend = $tempC_trend_1 - $tempC_trend_2;
    if ($tempC_trend >= 1) {
        $tempC_trend = ' <i class="wi wi-direction-up"></i>';
    }
    elseif ($tempC_trend <= -1) {
        $tempC_trend = ' <i class="wi wi-direction-down"></i>';
    }
    else {
        $tempC_trend = ' <i class="wi wi-direction-right"></i>';
    }

    // Process Humidity
    $sql = "SELECT `relH` FROM `humidity` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $relH = $result['relH'];

    // Calculate Dew Point
    $dewptC = round(((pow(($relH / 100), 0.125)) * (112 + 0.9 * $tempC) + (0.1 * $tempC) - 112), 2);
    $dewptF = round(($dewptC * 9/5) + 32, 2);

    // Process Rainfall
    $sql = "SELECT SUM(`raw`) AS `rainfall` FROM `rainfall` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $rain = $result['rainfall'];
    $rainin = round($rain * 0.0393701, 2);
    $rainmm = round($rain, 2);

    $sql = "SELECT SUM(`raw`) AS `rainfall_day_total` FROM `rainfall` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $total_rainfall_day = $result['rainfall_day_total'];
    $rain_totalin_day = round($total_rainfall_day * 0.0393701, 2);
    $rain_totalmm_day = round($total_rainfall_day, 2);

    // Weekly Rainfall
    $sql = "SELECT SUM(`raw`) AS `rainfall_week_total` FROM rainfall WHERE WEEKOFYEAR(`timestamp`) = WEEKOFYEAR(NOW());";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $rain_totalmm_month = $result['rainfall_week_total'];
    $rain_totalin_week = round($rain_totalmm_month * 0.0393701, 2);
    $rain_totalmm_week = round($rain_totalmm_month, 2);

    // Monthly Rainfall
    $sql = "SELECT SUM(`raw`) AS `rainfall_month_total` FROM rainfall WHERE YEAR(`timestamp`) = YEAR(CURDATE()) AND MONTH(`timestamp`) = MONTH(CURDATE())";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $rain_totalmm_month = $result['rainfall_month_total'];
    $rain_totalin_month = round($rain_totalmm_month * 0.0393701, 2);
    $rain_totalmm_month = round($rain_totalmm_month, 2);

    // Pressure Trending
    $sql = "SELECT AVG(raw_hpa) AS `hpa_trend` FROM `pressure` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $hpa_trend_1 = $result['hpa_trend'];

    $sql = "SELECT AVG(raw_hpa) AS `hpa_trend` FROM `pressure` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $hpa_trend_2 = $result['hpa_trend'];
    $hpa_trend = $hpa_trend_1 - $hpa_trend_2;
    if ($hpa_trend >= 1) {
        $hpa_trend = ' <i class="wi wi-direction-up"></i>';
    }
    elseif ($hpa_trend <= -1) {
        $hpa_trend = ' <i class="wi wi-direction-down"></i>';
    }
    else {
        $hpa_trend = ' <i class="wi wi-direction-right"></i>';
    }

    // Humidity Trending
    $sql = "SELECT AVG(relH) AS `relH_trend` FROM `humidity` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $relH_trend_1 = $result['relH_trend'];

    $sql = "SELECT AVG(relH) AS `relH_trend` FROM `humidity` WHERE `timestamp` BETWEEN DATE_SUB(NOW(), INTERVAL 6 HOUR) AND DATE_SUB(NOW(), INTERVAL 3 HOUR)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $relH_trend_2 = $result['relH_trend'];
    $relH_trend = $relH_trend_1 - $relH_trend_2;
    if ($relH_trend >= 1) {
        $relH_trend = ' <i class="wi wi-direction-up"></i>';
    }
    elseif ($relH_trend <= -1) {
        $relH_trend = ' <i class="wi wi-direction-down"></i>';
    }
    else {
        $relH_trend = ' <i class="wi wi-direction-right"></i>';
    }

    // Wind Chill
    if ($tempC <= 10 && $windS_kmh >= 4.8){
        $feelsC = 13.12 + (0.6215 * $tempC) - (11.37 * ($windS_kmh**0.16)) + ((0.3965 * $tempC) * ($windS_kmh**0.16));
        $feelsC = round($feelsC, 2);
        $feelsF = round($feelsC * 9/5 + 32, 2);
    }

    // Heat Index
    elseif ($tempF >= 80 && $relH >= 40) {

        $feelsF = -42.379 + (2.04901523 * $tempF) + (10.14333127 * $relH) - (0.22475541 * $tempF * $relH) - (6.83783 * (10**-3) * ($tempF**2)) - (5.481717 * (10**-2) * ($relH**2)) + (1.22874 * (10**-3) * ($tempF**2) * $relH) + (8.5282 * (10**-4) * $tempF * ($relH**2)) - (1.99 * (10**-6) * ($tempF**2) * ($relH**2));
        $feelsF = round($feelsF, 2);
        $feelsC = round(($feelsF - 32) / 1.8, 2);
    }

?>

    <div class="row weather_row">
        <div class="col-md-5 col-md-offset-1">
            <h2><?php echo $sensor_5n1_name; ?>:</h2>
            <p><strong>Reported:</strong> <?php echo $time; ?></p>
            <p><strong>Sunrise:</strong> <?php echo date_sunrise(time(), SUNFUNCS_RET_STRING, $lat, $long, $zenith, $offset); ?> / <strong>Sunset:</strong> <?php echo date_sunset(time(), SUNFUNCS_RET_STRING, $lat, $long, $zenith, $offset); ?></p>
            <hr>
            <p><strong>Temp:</strong> <?php echo $tempC, '&#8451; (', $tempF, '&#8457;)', $tempC_trend; ?></p>
            <p><?php if (isset($feelsC)){ echo '<p><strong>Feels Like:</strong> ', $feelsC, '&#8451; (', $feelsF, '&#8457;)</p>';} ?>
            <p><strong>High:</strong> <?php echo $tempC_high, '&#8451; (', $tempF_high, '&#8457;) @ ', $high_temp_recorded; ?></p>
            <p><strong>Low:</strong> <?php echo $tempC_low, '&#8451; (', $tempF_low, '&#8457;) @ ', $low_temp_recorded; ?></p>
            <p><strong>Average:</strong> <?php echo $tempC_avg, '&#8451; (', $tempF_avg,'&#8457;)'; ?></p>
            <p><strong>Dew Point:</strong> <?php echo $dewptC, '&#8451; (', $dewptF, '&#8457;)'; ?></p>
            <hr>
            <p><strong>Wind:</strong> <?php if ($windS_kmh >= 25 ){ echo ' <i class="wi wi-strong-wind"></i>';} elseif ($windS_kmh < 25){ if ($windS_kmh >= 10) {echo ' <i class="wi wi-windy"></i>';}} ?> <i class="wi wi-wind wi-from-<?php echo strtolower($windD); ?>"></i> <?php echo $windD, ' @ ', $windS_kmh , ' km/h (', $windS_mph, ' mph)'; ?></p>
            <p><strong>Peak:</strong> <?php echo $max_windSkmh, ' km/h (', $max_windSmph, ' mph)', ' @ ', $max_wind_recorded;?></p>
            <p><strong>Average:</strong> <?php echo $avg_windSkmh, ' km/h (', $avg_windSmph, ' mph)';?></p>
            <hr>
            <p><strong>Humidity:</strong> <?php echo $relH, '%', $relH_trend; ?></p>
            <hr>
            <p><strong>Pressure:</strong> <?php echo $pressure_hPa, ' hPa (', $pressure_inHg, ' inHg)', $hpa_trend; ?></p>
            <hr>
            <p><strong>Rain Rate:</strong> <?php echo $rainmm, ' mm/hr (', $rainin, ' in/hr)'; ?></p>
            <p><strong>Daily Rain:</strong> <?php echo $rain_totalmm_day, ' mm (', $rain_totalin_day, ' in)'; ?></p>
            <p><strong>Weekly Rain:</strong> <?php echo $rain_totalmm_week, ' mm (', $rain_totalin_week, ' in)'; ?></p>
            <p><strong><?php echo $time = date('M'); ?> Rain:</strong> <?php echo $rain_totalmm_month, ' mm (', $rain_totalin_month, ' in)'; ?></p>
        </div>
        
<?php
    // Output Tower Sensors

    if ($tower_sensors_active == 1) {
    
        // Tower 1

        if (isset($sensor_tower1_id)) {
            $sql = "SELECT * FROM `tower1` ORDER BY `timestamp` DESC LIMIT 1";
            $result = mysqli_fetch_array(mysqli_query($conn, $sql));

            $time = $result['timestamp'];
            $time = strtotime($time);
            $time = date('j M Y @ H:i:s T', $time);
            $tempC = $result['tempC'];
            $tempF = $tempC * 9 / 5 + 32;
            $relH = $result['relH'];

?>
        <div class="col-md-6">
            <h2><?php echo $sensor_tower1_name; ?>:</h2>
            <p><strong>Reported:</strong> <?php echo $time; ?></p>
            <p><strong>Temperature:</strong> <?php echo $tempC, '&#8451; (', $tempF, '&#8457;)'; ?></p>
            <p><strong>Humidity:</strong> <?php echo $relH, '%'; ?></p>
        </div>

<?php
        }
    
        // Tower 2

        elseif (isset($sensor_tower2_id)) {
            $sql = "SELECT * FROM `tower2` ORDER BY `timestamp` DESC LIMIT 1";
            $result = mysqli_fetch_array(mysqli_query($conn, $sql));

            $time = $result['timestamp'];
            $time = strtotime($time);
            $time = date('H:i:s T', $time);
            $tempC = $result['tempC'];
            $tempF = $tempC * 9 / 5 + 32;
            $relH = $result['relH'];

?>

            <div class="col-md-6">
                <h2><?php echo $sensor_tower2_name; ?>:</h2>
                <p><strong>Reported:</strong> <?php echo $time; ?></p>
                <p><strong>Temperature:</strong> <?php echo $tempC, '&#8451; (', $tempF, '&#8457;)'; ?></p>
                <p><strong>Humidity:</strong> <?php echo $relH, '%'; ?></p>
            </div>

<?php
        }
        // Tower 3

        elseif (isset($sensor_tower3_id)) {
            $sql = "SELECT * FROM `tower3` ORDER BY `timestamp` DESC LIMIT 1";
            $result = mysqli_fetch_array(mysqli_query($conn, $sql));

            $time = $result['timestamp'];
            $time = strtotime($time);
            $time = date('j M Y @ H:i:s T', $time);
            $tempC = $result['tempC'];
            $tempF = $tempC * 9 / 5 + 32;
            $relH = $result['relH'];

?>

            <div class="col-md-6">
                <h2><?php echo $sensor_tower3_name; ?>:</h2>
                <p><strong>Reported:</strong> <?php echo $time; ?></p>
                <p><strong>Temperature:</strong> <?php echo $tempC, '&#8451; (', $tempF, '&#8457;)'; ?></p>
                <p><strong>Humidity:</strong> <?php echo $relH, '%'; ?></p>
            </div>
    </div>

<?php
        }
    }
}
