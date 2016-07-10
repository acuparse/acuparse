<?php
/**
 * File: environment.php
 */

function get_current_environment() {

    require(dirname(__DIR__) . '/inc/config.php');

    // Get Moon Data
    require('moonphase.php');
    $moon = new Solaris\MoonPhase();
    $moon_age = round( $moon->age(), 1 );
    $moon_stage = $moon->phase_name();
    $next_new_moon = date( 'j M @ H:i:s', $moon->next_new_moon() );
    $next_full_moon = date( 'j M @ H:i:s', $moon->next_full_moon() );
    $last_new_moon = date( 'j M @ H:i:s', $moon->new_moon() );
    $last_full_moon = date( 'j M @ H:i:s', $moon->full_moon() );
    $moon_illumination = round($moon->illumination(), 2);
    function percent($number){
        return $number * 100 . '%';
    }
    $moon_illumination = percent($moon_illumination);
    // Get moon icon

    switch ($moon_stage) {
        case 'New Moon':
            $moon_icon = 'wi-moon-new';
            break;
        case 'Waxing Crescent':
            $moon_icon = 'wi-moon-waxing-crescent-6';
            break;
        case 'First Quarter':
            $moon_icon = 'wi-moon-first-quarter';
            break;
        case 'Waxing Gibbous':
            $moon_icon = 'wi-moon-waxing-gibbous-6';
            break;
        case 'Full Moon':
            $moon_icon = 'wi-moon-full';
            break;
        case 'Waning Gibbous':
            $moon_icon = 'wi-moon-waning-gibbous-6';
            break;
        case 'Last Quarter':
            $moon_icon = 'wi-moon-third-quarter';
            break;
        case 'Waning Crescent':
            $moon_icon = 'wi-moon-waning-crescent-1';
            break;
    }

    // Moon rise/set
    require('moontime.php');
    $moon_time = Moon::calculateMoonTimes($lat, $long);
    $moon_rise = gmdate('H:i', $moon_time->moonrise);
    $moon_set = gmdate('H:i', $moon_time->moonset);

    // Get Sun Data
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_STRING, $lat, $long, $zenith, $offset);
    $sunset = date_sunset(time(), SUNFUNCS_RET_STRING, $lat, $long, $zenith, $offset);

    // Get Forcast Data
    // Rainfall
    $sql = "SELECT SUM(`raw`) AS `rainfall` FROM `rainfall` WHERE `timestamp` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
    $result = mysqli_fetch_array(mysqli_query($conn, $sql));
    $rain = $result['rainfall'];
    if ($rain !=0) {$rain = 1;}
    else {
        $rain = 0;
    }

    // actual forcast
    ?>

    <script type="text/javascript">
        $(document).ready(function() {
            function update() {
                $.ajax({
                    url: '/?time',
                    success: function(data) {
                        $("#time").html(data);
                        window.setTimeout(update, 1000);
                    }
                });
            }
            update();
        });
    </script>

    <div class="col-md-4 <?php  if ($tower_sensors_active != 1) {echo 'col-md-offset-2';} ?>">
        <h2>Environment:</h2>
        <div id="time"></div>
        <?php if ($rain == 1) {echo '<p><strong>Currently Raining</strong></p>';} ?>
        <hr>
        <h3><i class="wi wi-day-sunny"></i> Sun:</h3>
        <ul class="list-unstyled">
            <li><i class="wi wi-sunrise"></i> <strong>Sunrise:</strong> <?php echo $sunrise; ?></li>
            <li><i class="wi wi-sunset"></i> <strong>Sunset:</strong> <?php echo $sunset; ?></li>
        </ul>
        <h3><i class="wi <?php echo $moon_icon; ?>"></i> Moon:</h3>
        <h4><?php echo "$moon_stage, $moon_age days. $moon_illumination visible" ?></h4>
        <ul class="list-unstyled">
            <li><i class="wi wi-moonrise"></i> <strong>Moonrise:</strong> <?php echo $moon_rise; ?></li>
            <li><i class="wi wi-moonset"></i> <strong>Moonset:</strong> <?php echo $moon_set; ?></li>
            <li><strong>Current New:</strong> <?php echo $last_new_moon; ?></li>
            <li><strong>Current Full:</strong> <?php echo $last_full_moon; ?></li>
            <li><strong>Upcoming New:</strong> <?php echo $next_new_moon; ?></li>
            <li><strong>Upcoming Full:</strong> <?php echo $next_full_moon; ?></li>
        </ul>
    </div>

<?php
    }