<?php
/**
 * File: index.php
 */

require(__DIR__ . '/inc/config.php');

// System Time
if (isset($_GET['time'])) {
    $date = date('l, j F Y G:i:s T');
    echo "<p><i class=\"fa fa-calendar\"></i> $date</p>";
    die();
}
// End system time

// Environment
if (isset($_GET['environment'])) {
    require(__DIR__ . '/fcn/environment.php');
    get_current_environment();
    die();
}
// End weather

// Weather
if (isset($_GET['weather'])) {
    require(__DIR__ . '/fcn/weather.php');
    get_current_weather();
    die();
}
// End weather

include 'inc/header.php';


?>
<script type="text/javascript">
    $(document).ready(function() {
        function update() {
            $.ajax({
                url: '?weather',
                success: function(data) {
                    $("#weather").html(data);
                    window.setTimeout(update, 30000);
                }
            });
        }
        update();
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        function update() {
            $.ajax({
                url: '?environment',
                success: function(data) {
                    $("#environment").html(data);
                    window.setTimeout(update, 43200000);
                }
            });
        }
        update();
    });
</script>
<div class="container">
    <div class="row weather_row">
        <div id="environment"></div>
        <div id="weather"></div>
    </div>
</div>

<?php
include 'inc/footer.php';