<?php
/**
 * File: weather.php
 */

function get_current_weather() {
    require '/var/www/html/inc/config.php';

    // Master Weather Station
    $sql = "SELECT * FROM `weather` ORDER BY `reported` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));

    $time = $result['reported'];
    $time = strtotime($time);
    $time = date('j M Y @ H:i:s T', $time);

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
    $rainin = $result['rain'] / 2540;
    $rainmm = $result['rain'] / 1000;
    $rain_totalin = $result['total_rain'] * 0.039370;
    $rain_totalmm = $result['total_rain'];

    // Wind Direction
    // Convert wind direction into text
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
            $windD = 'South-East';
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
            $windD = 'South-West';
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
            $windD = 'North-West';
            break;
        case 'NNW':
            $windD = 'North-Northwest';
            break;
    }

    // Peak Windspeed
    $sql = "SELECT `timestamp`, `speedms` FROM `windspeed` WHERE `speedms` = (SELECT MAX(speedms) FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $max_wind_recorded = $result['timestamp'];
    $max_wind_recorded = strtotime($max_wind_recorded);
    $max_wind_recorded = date('H:i:s T', $max_wind_recorded);

    $max_windSms = $result['speedms'];
    $max_windSkmh = round($max_windSms * 3.6, 2);
    $max_windSmph = round($max_windSms * 2.23694, 2);

    // Average Windspeed
    $sql = "SELECT AVG(speedms) AS `avg_windsms` FROM `windspeed` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $avg_windSms = $result['avg_windsms'];
    $avg_windSkmh = round($avg_windSms * 3.6, 2);
    $avg_windSmph = round($avg_windSms * 2.23694, 2);

    // High Temp
    $sql = "SELECT `timestamp`, `tempc` FROM `temperature` WHERE `tempc` = (SELECT MAX(tempc) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $high_temp_recorded = $result['timestamp'];
    $high_temp_recorded = strtotime($high_temp_recorded);
    $high_temp_recorded = date('H:i:s T', $high_temp_recorded);

    $tempC_high = $result['tempc'];
    $tempC_high = round($tempC_high, 2);
    $tempF_high = round($tempC_high * 9/5 + 32, 2);

    // Low Temp
    $sql = "SELECT `timestamp`, `tempc` FROM `temperature` WHERE `tempc` = (SELECT MIN(tempc) FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()) AND DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $low_temp_recorded = $result['timestamp'];
    $low_temp_recorded = strtotime($low_temp_recorded);
    $low_temp_recorded = date('H:i:s T', $low_temp_recorded);

    $tempC_low = $result['tempc'];
    $tempC_low = round($tempC_low, 2);
    $tempF_low = $tempC_low * 9/5 + 32;

    // Average Temp
    $sql = "SELECT AVG(tempc) AS `avg_tempc` FROM `temperature` WHERE DATE(`timestamp`) = CURDATE()";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $tempC_avg = round($result['avg_tempc'], 2);
    $tempF_avg = $tempC_avg * 9/5 + 32;

    // Wind Chill
    if ($tempC <= 10 && $windSkmh >= 4.8){
        $feelsC = 13.12 + (0.6215 * $tempC) - (11.37 * ($windSkmh**0.16)) + ((0.3965 * $tempC) * ($windSkmh**0.16));
        $feelsC = round($feelsC, 2);
        $feelsF = round($feelsC * 9/5 + 32, 2);
    }

    elseif ($tempF >= 80 && $relH >= 40) {
        // Heat Index
        $feelsF = -42.379 + (2.04901523 * $tempF) + (10.14333127 * $relH) - (0.22475541 * $tempF * $relH) - (6.83783 * (10**-3) * ($tempF**2)) - (5.481717 * (10**-2) * ($relH**2)) + (1.22874 * (10**-3) * ($tempF**2) * $relH) + (8.5282 * (10**-4) * $tempF * ($relH**2)) - (1.99 * (10**-6) * ($tempF**2) * ($relH**2));
        $feelsF = round($feelsF, 2);
        $feelsC = round(($feelsF - 32) / 1.8, 2);
    }

?>

    <div class="row">
        <div class="col-lg-6">
            <h2>Weather Station:</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong>Reported at:</strong></td>
                            <td><?php echo $time; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Current Temperature:</strong></td>
                            <td><?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;)</td>
                        </tr>
                        <?php if (isset($feelsC)){echo '<tr>
                            <td><strong>Feels Like:</strong></td>
                            <td><?php echo $feelsC; ?>&#8451; (<?php echo $feelsF; ?>&#8457;)</td>
                        </tr>';} ?>
                        <tr>
                            <td><strong>Highest Temperature:</strong></td>
                            <td><?php echo $tempC_high; ?>&#8451; (<?php echo $tempF_high; ?>&#8457;) @ <?php echo $high_temp_recorded; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lowest Temperature:</strong></td>
                            <td><?php echo $tempC_low, '&#8451; (', $tempF_low, '&#8457;) @ ', $low_temp_recorded; ?></td>
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
                            <td><?php echo $windSkmh , ' km/h (', $windSmph, ' mph)';?></td>
                        </tr>
                        <tr>
                            <td><strong>Peak Wind Speed:</strong></td>
                            <td><?php echo $max_windSkmh, ' km/h (', $max_windSmph, ' mph)', ' @ ', $max_wind_recorded;?></td>
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
<?php
/*
    // Inside Temp Sensor
    $sql = "SELECT * FROM `under_trailer` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));

    $time = $result['timestamp'];
    $time = strtotime($time);
    $time = date('j M Y @ H:i:s T', $time);
    $tempC = $result['tempC'];
    $tempF = $tempC * 9/5 + 32;
    $relH = $result['relH'];

?>

        <div class="col-lg-6">
            <h2>Inside Trailer:</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><strong>Reported at:</strong></td>
                        <td><?php echo $time; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Current Temperature:</strong></td>
                        <td><?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;)</td>
                    </tr>
                    <tr>
                        <td><strong>Relative Humidity:</strong></td>
                        <td><?php echo $relH, '%'; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

<?php

        // Under Tubby Sensor
        $sql = "SELECT * FROM `under_tubby` ORDER BY `timestamp` DESC LIMIT 1";
        $result = mysqli_fetch_array(mysqli_query($conn, $sql));

        $time = $result['timestamp'];
        $time = strtotime($time);
        $time = date('H:i:s T', $time);
        $tempC = $result['tempC'];
        $tempF = $tempC * 9/5 + 32;
        $relH = $result['relH'];

?>

        <div class="col-lg-6">
            <h2>Under Tubby:</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><strong>Reported at:</strong></td>
                        <td><?php echo $time; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Current Temperature:</strong></td>
                        <td><?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;)</td>
                    </tr>
                    <tr>
                        <td><strong>Relative Humidity:</strong></td>
                        <td><?php echo $relH, '%'; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

<?php
*/
    // Under Trailer Sensor
    $sql = "SELECT * FROM `under_trailer` ORDER BY `timestamp` DESC LIMIT 1";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));

    $time = $result['timestamp'];
    $time = strtotime($time);
    $time = date('j M Y @ H:i:s T', $time);
    $tempC = $result['tempC'];
    $tempF = $tempC * 9/5 + 32;
    $relH = $result['relH'];

?>

        <div class="col-lg-6">
            <h2>Under Trailer:</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong>Reported at:</strong></td>
                            <td><?php echo $time; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Current Temperature:</strong></td>
                            <td><?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;)</td>
                        </tr>
                        <tr>
                            <td><strong>Relative Humidity:</strong></td>
                            <td><?php echo $relH, '%'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

<?php

}
