<?php
/**
 * File: index.php
 */

require '/var/www/html/inc/config.php';

// Weather
if (isset($_GET['weather'])) {
    include 'fcn/weather.php';
    get_current_weather();
    die();
}
// End weather

include 'inc/header.php';
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

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Local Weather from Golden Heights, Sturgeon County, Alberta</h2>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                function update() {
                    $.ajax({
                        url: '?weather',
                        success: function(data) {
                            $("#weather").html(data);
                            window.setTimeout(update, 60000);
                        }
                    });
                }
                update();
            });
        </script>
        <div class="col-lg-12">
            <div id="weather"></div>
        </div>
    </div>
</div>

<?php
include 'inc/footer.php';