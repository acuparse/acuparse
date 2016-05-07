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
    $rain = $result['rain'];
    $rain_total = $result['total_rain'];

    ?>

    <p><strong>Reported:</strong> <?php echo $time, date(' T'); ?></p>
    <p><strong>Temp:</strong> <?php echo $tempC; ?>&#8451; (<?php echo $tempF; ?>&#8457;) | <strong>Wind:</strong> <?php echo $windD, ' @ ', $windSkm , ' km/h (', $windSmph, ' mph)';?></p>
    <p><strong>Dew Point: </strong><?php echo $dewptC; ?>&#8451; (<?php echo $dewptF; ?>&#8457;) | <strong>Relative Humidity: </strong><?php echo $relH, '%'; ?> | <strong>Pressure: </strong><?php echo $pressurehPa, ' hPa (', $pressureinHg, ' inHg)'; ?>
    <p><strong>Rain: </strong><?php echo $rain; ?> | <strong>Total Rain: </strong><?php echo $rain_total ?>
    <?php
}