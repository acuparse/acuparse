<?php
/**
 * File: messages/index.php
 * Hijack data from the acu-rite bridge, process and store in database.
 */

if (isset($_POST['id']) && $_POST['mt']) {

    require(dirname(__DIR__) . '/inc/config.php');

    if ($_POST['id'] == $MACADDRESS) {

        // Get Pressure reading from the Bridge

        if ($_POST['mt'] == 'pressure') {
            $C1 = hexdec($_POST['C1']);
            $C2 = hexdec($_POST['C2']);
            $C3 = hexdec($_POST['C3']);
            $C4 = hexdec($_POST['C4']);
            $C5 = hexdec($_POST['C5']);
            $C6 = hexdec($_POST['C6']);
            $C7 = hexdec($_POST['C7']);
            $A = hexdec($_POST['A']);
            $B = hexdec($_POST['B']);
            $C = hexdec($_POST['C']);
            $D = hexdec($_POST['D']);
            $D1 = hexdec($_POST['PR']);
            $D2 = hexdec($_POST['TR']);

            //Step 1: (get temperature value)
            if ($D2 >= $C5) {
                $dUT = $D2 - $C5 - (($D2 - $C5) / 2 ** 7) * (($D2 - $C5) / 2 ** 7) * $A / 2 ** $C;
            } elseif ($D2 < $C5) {
                $dUT = $D2 - $C5 - (($D2 - $C5) / 2 ** 7) * (($D2 - $C5) / 2 ** 7) * $B / 2 ** $C;
            }

            //Step 2: (calculate offset, sensitivity and final pressure value)
            $OFF = ($C2 + ($C4 - 1024) * $dUT / 2 ** 14) * 4;
            $SENS = $C1 + $C3 * $dUT / 2 ** 10;
            $X = $SENS * ($D1 - 7168) / 2 ** 14 - $OFF;
            $P = $X * 10 / 2 ** 5 + $C7;

            //Step 3: (calculate temperature)
            $T = 250 + $dUT * $C6 / 2 ** 16 - $dUT / 2 ** $D;

            $raw_pressure_hPa = $P / 10;

            syslog(LOG_DEBUG, "Bridge Pressure: $raw_pressure_hPa");

            // Save to DB
            $sql = "INSERT INTO `pressure` (`raw_hpa`) VALUES ('$raw_pressure_hPa')";
            $result = mysqli_query($conn, $sql);
        }

        // Process Wind Speed, Wind Direction, and Rainfall
        if ($_POST['mt'] == '5N1x31' && $_POST['sensor'] == $sensor_5n1_id) {

            // Wind Speed
            sscanf($_POST['windspeed'], "A0%02d%d", $a, $b);
            $windS_ms = $a . "." . $b;
            syslog(LOG_DEBUG, "5n1 Windspeed: $a.$b");

            // Insert into DB
            $sql = "INSERT INTO `windspeed` (`speedMS`) VALUES ('$windS_ms')";
            $result = mysqli_query($conn, $sql);

            // Wind Direction
            // Convert wind direction into degrees
            switch ($_POST['winddir']) {
                case '5':
                    $windDEG = '0.00';
                    break;
                case '7':
                    $windDEG = '22.5';
                    break;
                case '3':
                    $windDEG = '45.0';
                    break;
                case '1':
                    $windDEG = '67.5';
                    break;
                case '9':
                    $windDEG = '90';
                    break;
                case 'B':
                    $windDEG = '112.5';
                    break;
                case 'F':
                    $windDEG = '135.0';
                    break;
                case 'D':
                    $windDEG = '157.5';
                    break;
                case 'C':
                    $windDEG = '180';
                    break;
                case 'E':
                    $windDEG = '202.5';
                    break;
                case 'A':
                    $windDEG = '225';
                    break;
                case '8':
                    $windDEG = '247.5';
                    break;
                case '0':
                    $windDEG = '270.0';
                    break;
                case '2':
                    $windDEG = '292.5';
                    break;
                case '6':
                    $windDEG = '315.0';
                    break;
                case '4':
                    $windDEG = '337.5';
                    break;
            }

            syslog(LOG_DEBUG, "5n1 WindDirection: $windDEG");
            // Insert into DB
            $sql = "INSERT INTO `winddirection` (`degrees`) VALUES ('$windDEG')";
            $result = mysqli_query($conn, $sql);

            // Rainfall
            sscanf($_POST['rainfall'], "A0%02d%d", $a, $b);
            $rain = $a . "." . $b;
            syslog(LOG_DEBUG, "5n1 Rainfall: $a.$b");

            // Insert into DB
            $sql = "INSERT INTO `rainfall` (`raw`) VALUES ('$rain')";
            $result = mysqli_query($conn, $sql);
        }

        // Process Wind Speed, Temperature, Humidity
        elseif ($_POST['mt'] == '5N1x38') {

            // Wind Speed
            sscanf($_POST['windspeed'], "A0%02d%d", $a, $b);
            $windS_ms = $a . "." . $b;
            syslog(LOG_DEBUG, "5n1 Windspeed: $a.$b");
            // Insert into DB
            $sql = "INSERT INTO `windspeed` (`speedMS`) VALUES ('$windS_ms')";
            $result = mysqli_query($conn, $sql);

            // Temperature
            sscanf($_POST['temperature'], "A%01s%02d%d", $operator, $a, $b);
            syslog(LOG_DEBUG, "5n1 tempC: $operator$a.$b");
            if ($operator == 0) {
                $tempC = $a . "." . $b;
            } else {
                $tempC = "-" . $a . "." . $b;
            }

            // Insert into DB
            $sql = "INSERT INTO `temperature` (`tempC`) VALUES ('$tempC')";
            $result = mysqli_query($conn, $sql);

            // Humidity
            sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
            $humidity = $a;
            syslog(LOG_DEBUG, "5n1 relH: $a");
            // Insert into DB
            $sql = "INSERT INTO `humidity` (`relH`) VALUES ('$humidity')";
            $result = mysqli_query($conn, $sql);
        }

        // Process Tower Sensors
        elseif ($tower_sensors_active == 1 && $_POST['mt'] == 'tower') {

            // Tower Sensor 1
            if (isset($sensor_tower1_id) && $_POST['sensor'] == $sensor_tower1_id) {
                // Temperature
                sscanf($_POST['temperature'], "A%01s%02d%d", $operator, $a, $b);
                syslog(LOG_DEBUG, "$sensor_tower1_name tempC: $operator$a.$b");
                if ($operator == 0) {
                    $tempC = $a . "." . $b;
                } else {
                    $tempC = "-" . $a . "." . $b;
                }

                // Humidity
                sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
                $humidity = $a;
                syslog(LOG_DEBUG, "$sensor_tower1_name relH: $a");

                // Insert into DB
                $sql = "INSERT INTO `tower1` (`tempC`, `relH`) VALUES ('$tempC', '$humidity')";
                $result = mysqli_query($conn, $sql);
            }

            // Tower Sensor 2
            elseif (isset($sensor_tower2_id) && $_POST['sensor'] == $sensor_tower2_id) {
                // Temperature
                sscanf($_POST['temperature'], "A%01s%02d%d", $operator, $a, $b);
                syslog(LOG_DEBUG, "$sensor_tower2_name tempC: $operator$a.$b");
                if ($operator == 0) {
                    $tempC = $a . "." . $b;
                } else {
                    $tempC = "-" . $a . "." . $b;
                }

                // Humidity
                sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
                $humidity = $a;
                syslog(LOG_DEBUG, "$sensor_tower2_name relH: $a");

                // Insert into DB
                $sql = "INSERT INTO `tower2` (`tempC`, `relH`) VALUES ('$tempC', '$humidity')";
                $result = mysqli_query($conn, $sql);
            }

            // Tower Sensor 3
            elseif (isset($sensor_tower3_id) && $_POST['sensor'] == $sensor_tower3_id) {
                // Temperature
                sscanf($_POST['temperature'], "A%01s%02d%d", $operator, $a, $b);
                syslog(LOG_DEBUG, "$sensor_tower3_name tempC: $operator$a.$b");
                if ($operator == 0) {
                    $tempC = $a . "." . $b;
                } else {
                    $tempC = "-" . $a . "." . $b;
                }

                // Humidity
                sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
                $humidity = $a;
                syslog(LOG_DEBUG, "$sensor_tower3_name relH: $a");

                // Insert into DB
                $sql = "INSERT INTO `tower3` (`tempC`, `relH`) VALUES ('$tempC', '$humidity')";
                $result = mysqli_query($conn, $sql);
            }
        }


        // Repost the data to MBW and send the response back to the bridge
        $url = 'http://acu-link.com/messages';
        $post = file_get_contents('php://input');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);

        // Log the raw data to syslog
        syslog(LOG_DEBUG, "Raw Data: $post");
    }
}

// No usable data, repost the request to MBW and send the response back to the bridge
else {

    $url = 'http://acu-link.com/messages';
    $post = file_get_contents('php://input');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);

    // Log the raw data to syslog
    syslog(LOG_ERR, "Data not parsed, check config! Raw Data: $post");
}
