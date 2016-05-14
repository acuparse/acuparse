<?php
/**
 * Attempt to hijack data from the acu-rite bridge.
 * File: index.php
 */

if (!empty($_POST)) {

    require '/var/www/html/inc/config.php';

    if (isset($_POST['id']) && $_POST['id'] == $MACADDRESS) {

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
            } else if ($D2 < $C5) {
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

            // Save to DB
            $sql = "INSERT INTO `pressure` (`raw_hpa`) VALUES ('$raw_pressure_hPa')";
            $result = mysqli_query($conn, $sql);
        }

        if ($_POST['mt'] == '5N1x31') {
            // Wind Speed, Wind Direction, and Rainfall

            // Wind Speed
            sscanf($_POST['windspeed'], "A0%02d%d", $a, $b);
            $windS_ms = $a . "." . $b;

            // Insert into DB
            $sql = "INSERT INTO `windspeed` (`speedms`) VALUES ('$windS_ms')";
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
            // Insert into DB
            $sql = "INSERT INTO `winddirection` (`degrees`) VALUES ('$windDEG')";
            $result = mysqli_query($conn, $sql);

            // Rainfall
            sscanf($_POST['rainfall'], "A0%02d%d", $a, $b);
            $rain = $a . "." . $b;

            // Insert into DB
            $sql = "INSERT INTO `rainfall` (`raw`) VALUES ('$rain')";
            $result = mysqli_query($conn, $sql);
        }

        else if ($_POST['mt'] == '5N1x38') {
            // Wind Speed, Temperature, Humidity

            // Wind Speed
            sscanf($_POST['windspeed'], "A0%02d%d", $a, $b);
            $windS_ms = $a . "." . $b;
            // Insert into DB
            $sql = "INSERT INTO `windspeed` (`speedms`) VALUES ('$windS_ms')";
            $result = mysqli_query($conn, $sql);

            // Temperature
            sscanf($_POST['temperature'], "A0%02d%d", $a, $b);
            $tempC = $a . "." . $b;
            // Insert into DB
            $sql = "INSERT INTO `temperature` (`tempc`) VALUES ('$tempC')";
            $result = mysqli_query($conn, $sql);

            // Humidity
            sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
            $humidity = $a . "." . $b;
            // Insert into DB
            $sql = "INSERT INTO `humidity` (`humidity`) VALUES ('$humidity')";
            $result = mysqli_query($conn, $sql);
        }

        else if ($_POST['mt'] == 'tower') {

            // Under Trailer Sensor
            if ($_POST['sensor'] == '11638') {
                // Temperature
                sscanf($_POST['temperature'], "A0%02d%d", $a, $b);
                $tempC = $a . "." . $b;

                // Humidity
                sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
                $humidity = $a . "." . $b;

                // Insert into DB
                $sql = "INSERT INTO `under_trailer` (`tempC`, `relH`) VALUES ('$tempC', '$humidity')";
                $result = mysqli_query($conn, $sql);
            }

            // Under Tubby Sensor
            /*else if ($_POST['sensor'] == '') {
                // Temperature
                sscanf($_POST['temperature'], "A0%02d%d", $a, $b);
                $tempC = $a . "." . $b;

                // Humidity
                sscanf($_POST['humidity'], "A0%02d%d", $a, $b);
                $humidity = $a . "." . $b;

                // Insert into DB
                $sql = "INSERT INTO `under_tubby` (`tempC`, `relH`) VALUES ('$tempC', '$humidity')";
                $result = mysqli_query($conn, $sql);
            }*/
        }
    }

    // Repost the data to MBW
    $url = 'http://acu-link.com/messages';
    $post = file_get_contents('php://input');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);

}
else    {
    die('No Data!');
}