<?php
/**
 * File: weather.php
 */

function get_current_weather() {
    require '/var/www/html/inc/config.php';

    // 5n1 Sensor Data
    $sql = "SELECT * FROM `weather` ORDER BY `reported` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));

    $time = $result['reported'];
    $time = strtotime($time);
    $time = date('j M Y @ H:i:s', $time);

    $tempC = $result['tempC'];
    $tempF = $result['tempF'];
    $windSmph = $result['windSmph'];
    $windSkmh = $result['windSkmh'];
    $windD = $result['windD'];
    $relH = $result['relH'];
    $pressurehPa = $result['pressurehPa'];
    $pressureinHg = $result['pressureinHg'];
    $dewptC = $result['dewptC'];
    $dewptF = $result['dewptF'];

    // Still needs tweaking!!
    $rainin = $result['rainim'];
    $rainmm = $result['rainmm'];
    $rain_totalin = $result['total_rainin'];
    $rain_totalmm = $result['total_rainmm'];

    // Peak Windspeed
    $sql = "SELECT `timestamp`, `speedMS` FROM `windspeed` WHERE `speedMS` = (SELECT MAX(speedMS) FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $max_wind_recorded = $result['timestamp'];
    $max_wind_recorded = strtotime($max_wind_recorded);
    $max_wind_recorded = date('H:i:s', $max_wind_recorded);

    $max_windSms = $result['speedMS'];
    $max_windSkmh = round($max_windSms * 3.6, 2);
    $max_windSmph = round($max_windSms * 2.23694, 2);

    // Average Windspeed
    $sql = "SELECT AVG(speedMS) AS `avg_speedMS` FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $avg_windSms = $result['avg_speedMS'];
    $avg_windSkmh = round($avg_windSms * 3.6, 2);
    $avg_windSmph = round($avg_windSms * 2.23694, 2);

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
    if ($tempC <= 10 && $windSkmh >= 4.8){
        $feelsC = 13.12 + (0.6215 * $tempC) - (11.37 * ($windSkmh**0.16)) + ((0.3965 * $tempC) * ($windSkmh**0.16));
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
            <p><strong>Temp:</strong> <?php echo $tempC, '&#8451; (', $tempF, '&#8457;)', $tempC_trend; ?></p>
            <p><?php if (isset($feelsC)){ echo '<p><strong>Feels Like:</strong> ', $feelsC, '&#8451; (', $feelsF, '&#8457;)</p>';} ?>
            <p><strong>High:</strong> <?php echo $tempC_high, '&#8451; (', $tempF_high, '&#8457;) @ ', $high_temp_recorded; ?></p>
            <p><strong>Low:</strong> <?php echo $tempC_low, '&#8451; (', $tempF_low, '&#8457;) @ ', $low_temp_recorded; ?></p>
            <p><strong>Average:</strong> <?php echo $tempC_avg, '&#8451; (', $tempF_avg,'&#8457;)'; ?></p>
            <p><strong>Wind: <?php if ($windSkmh >= 25 ){ echo ' <i class="wi wi-strong-wind"></i>';} elseif ($windSkmh < 25){ if ($windSkmh >= 10) {echo ' <i class="wi wi-windy"></i>';}} ?> <i class="wi wi-wind wi-from-<?php echo strtolower($windD); ?>"></i></strong> <?php echo $windD, ' @ ', $windSkmh , ' km/h (', $windSmph, ' mph)'; ?></p>
            <p><strong>Peak:</strong> <?php echo $max_windSkmh, ' km/h (', $max_windSmph, ' mph)', ' @ ', $max_wind_recorded;?></p>
            <p><strong>Average:</strong> <?php echo $avg_windSkmh, ' km/h (', $avg_windSmph, ' mph)';?></p>
            <p><strong>Dew Point:</strong> <?php echo $dewptC, '&#8451; (', $dewptF, '&#8457;)'; ?></p>
            <p><strong>Humidity:</strong> <?php echo $relH, '%', $relH_trend; ?></p>
            <p><strong>Pressure:</strong> <?php echo $pressurehPa, ' hPa (', $pressureinHg, ' inHg)', $hpa_trend; ?></p>
            <p><strong>Rain Rate:</strong> <?php echo $rainmm, ' mm/hr (', $rainin, ' in/hr)'; ?></p>
            <p><strong>Daily Rain:</strong> <?php echo $rain_totalmm, ' mm (', $rain_totalin, ' in)'; ?></p>
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
