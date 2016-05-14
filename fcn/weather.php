<?php
/**
 * File: weather.php
 */

function get_current_weather() {
    require '/var/www/html/inc/config.php';

    $sql = "SELECT * FROM `weather` ORDER BY `reported` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));

    $time = $result['reported'];
    $time = strtotime($time);
    $time = date('j M Y @ H:i', $time);

    $tempC = $result['tempC'];
    $tempF = $result['tempF'];
    #$feelsC = $result['feelsC'];
    #$feelsF = $result['feelsF'];
    $windSms = $result['windSms'];
    $windSmph = $result['windSmph'];
    $windSkm = $result['windSkmh'];
    $windD = $result['windD'];
    $relH = $result['relH'];
    $pressurehPa = $result['pressurehPa'];
    $pressureinHg = $result['pressureinHg'];
    $dewptC = $result['dewptC'];
    $dewptF = $result['dewptF'];
    $rainin = $result['rain'] / 2540;
    $rainmm = $result['rain'] / 1000;
    $rain_totalin = $result['total_rain'] * 0.039370;
    $rain_totalmm = $result['total_rain'];

    // Wind Direction
    // Convert wind direction into degrees
    switch ($windD) {
        case 'N':
            $windD = 'North';
            break;
        case 'NNE':
            $windD = 'North-Northeast';
            break;
        case 'NE':
            $windD = 'North East';
            break;
        case 'ENE':
            $windD = 'East-Northeast';
            break;
        case 'E':
            $windD = 'East';
            break;
        case 'ESE':
            $windD = 'East-Southeast';
            break;
        case 'SE':
            $windD = 'South East';
            break;
        case 'SSE':
            $windD = 'South-Southeast';
            break;
        case 'S':
            $windD = 'South';
            break;
        case 'SSW':
            $windD = 'South-Southwest';
            break;
        case 'SW':
            $windD = 'South West';
            break;
        case 'WSW':
            $windD = 'West-Southwest';
            break;
        case 'W':
            $windD = 'West';
            break;
        case 'WNW':
            $windD = 'West-Northwest';
            break;
        case 'NW':
            $windD = 'North West';
            break;
        case 'NNW':
            $windD = 'North-Northwest';
            break;
    }

    // Peak Windspeed
    $sql = "SELECT MAX(speedms) AS `max_windsms` FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $max_windSms = $result['max_windsms'];
    $max_windSkmh = round($max_windSms * 3.6, 2);
    $max_windSmph = round($max_windSms * 2.23694, 2);

    // Average Windspeed
    $sql = "SELECT AVG(speedms) AS `avg_windsms` FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $avg_windSms = $result['avg_windsms'];
    $avg_windSkmh = round($avg_windSms * 3.6, 2);
    $avg_windSmph = round($avg_windSms * 2.23694, 2);

    // High Temp
    $sql = "SELECT MAX(tempc) AS `max_tempc`, `timestamp` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_high = $result['max_tempc'];
    $tempF_high = $tempC_high * 9/5 + 32;

    // Low Temp
    $sql = "SELECT MIN(tempc) AS `min_tempc`, `timestamp` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_low = $result['min_tempc'];
    $tempF_low = $tempC_low * 9/5 + 32;

    // Average Temp
    $sql = "SELECT AVG(tempc) AS `avg_tempc` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_avg = round($result['avg_tempc'], 2);
    $tempF_avg = $tempC_avg * 9/5 + 32;


    // Feels Like
    //$feelsF= .3634 + (.9986 * $tempF) + (4.7711 * $relH) - (.1140 * $tempF * $relH) - (.0009 * ($tempF**2)) - (.0207 * ($relH**2)) + (.0007 * $relH * ($tempF**2)) + (.0003 * $tempF * ($relH**2));
    //$feelsC = ($feelsF - 32) / 1.8;
    ?>

    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong>Reported at:</strong></td>
                            <td><?php echo $time, date('T'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Current Temperature:</strong></td>
                            <td><?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Highest Temperature:</strong></td>
                            <td><?php echo $tempC_high; ?>&#8451; (<?php echo $tempF_high; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Lowest Temperature:</strong></td>
                            <td><?php echo $tempC_low; ?>&#8451; (<?php echo $tempF_low; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Average Temperature:</strong></td>
                            <td><?php echo $tempC_avg; ?>&#8451; (<?php echo $tempF_avg; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Wind Direction:</strong></td>
                            <td><?php echo $windD;?></td>
                        </tr>
                        <tr>
                            <td><strong>Wind Speed:</strong></td>
                            <td><?php echo $windSkm , ' km/h (', $windSmph, ' mph)';?></td>
                        </tr>
                        <tr>
                            <td><strong>Peak Wind Speed:</strong></td>
                            <td><?php echo $max_windSkmh, ' km/h (', $max_windSmph, ' mph)';?></td>
                        </tr>
                        <tr>
                            <td><strong>Average Wind Speed:</strong></td>
                            <td><?php echo $avg_windSkmh, ' km/h (', $avg_windSmph, ' mph)';?></td>
                        </tr>
                        <tr>
                            <td><strong>Dew Point:</strong></td>
                            <td><?php echo $dewptC; ?>&#8451; (<?php echo $dewptF; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Relative Humidity:</strong></td>
                            <td><?php echo $relH, '%'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Pressure:</strong></td>
                            <td><?php echo $pressurehPa, ' hPa (', $pressureinHg, ' inHg)'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Rainfall Rate:</strong></td>
                            <td><?php echo $rainmm, ' mm/hr (', $rainin, ' in/hr)'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Daily Rain:</strong></td>
                            <td><?php echo $rain_totalmm, ' mm (', $rain_totalin, ' in)'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php

}