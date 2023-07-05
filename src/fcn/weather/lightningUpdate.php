<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
 * @author Maxwell Power <max@acuparse.com>
 * @link http://www.acuparse.com
 * @license AGPL-3.0+
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this code. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * File: src/fcn/weather/lightningUpdate.php
 * Updates lightning data in the DB
 */

/**
 * @param int $strikecount
 * @param int $interference
 * @param string|null $last_strike_ts
 * @param float|null $last_strike_distance
 * @param string $source
 */

function updateLightning(int $strikecount, int $interference, ?string $last_strike_ts, ?float $last_strike_distance, string $source)
{
    global $timestamp;
    global $config;
    global $conn;
    if ($source === 'A') {
        $dbsource = 'atlasLightning';
        $device = 'ATLAS';
    } elseif ($source === 'T') {
        $dbsource = 'towerLightning';
        $device = 'TOWER';
    } else {
        exit(syslog(LOG_EMERG,
            "(ACCESS){LIGHTNING}[ERROR]: Missing Source"));
    }
    $timestampDate = date('Y-m-d', strtotime($timestamp));

    if ($strikecount !== 0) {

        // Get the last update data
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `strikecount`, `last_strike_ts` FROM `lightningData` WHERE `source`='$source';"));
        @$lastStrikecount = (int)$result['strikecount'];
        @$lastStrikeTime = (string)$result['last_strike_ts'];

        // If there is no existing data, let's add this one
        if (empty($lastStrikeTime)) {
            $sql = "INSERT INTO `$dbsource` (`dailystrikes`, `currentstrikes`, `last_update`, `date`) VALUES(0, 0, '$timestamp', '$timestampDate') ON DUPLICATE KEY UPDATE `dailystrikes` = '$strikecount', `currentstrikes` = '$strikecount', `last_update` = '$timestamp';
            INSERT INTO `lightningData` (`strikecount`, `interference`, `last_strike_ts`, `last_strike_distance`, `source`) VALUES('$strikecount', '$interference', '$last_strike_ts', '$last_strike_distance', '$source') ON DUPLICATE KEY UPDATE `strikecount`='$strikecount', `interference`='$interference', `last_strike_ts`='$last_strike_ts', `last_strike_distance`='$last_strike_distance', `source`='$source';";
            mysqli_multi_query($conn, $sql) or syslog(LOG_ERR,
                "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Failed inserting! +SC, no LST. Details: " . mysqli_error($conn));
            while (mysqli_next_result($conn)) {
                NULL;
            }
            syslog(LOG_INFO,
                "(ACCESS){LIGHTNING}<$device>: Possible Lightning Detected | Count: $strikecount | Last: $lastStrikecount | Interference = $interference | Last Strike = $last_strike_ts | Distance = $last_strike_distance");
        } // Existing data, process an update
        else {
            // Is the strikecount different?
            if ($strikecount !== $lastStrikecount) {
                // Is the recent last strike time greater than existing last strike time?
                if (strtotime($last_strike_ts) > strtotime($lastStrikeTime)) {
                    // Count the current strikes and update the daily total
                    $currentStrikes = $strikecount - $lastStrikecount;
                    if ($currentStrikes === $strikecount) {
                        $currentStrikes = 0;
                    } // Negative currentStrikes means the count was reset. So we'll use the total strikeCount to create the currentStrikes.
                    elseif ($currentStrikes < 0) {
                        $currentStrikes = $strikecount;
                    }

                    // Get the dailyStrikesSoFar so we can update it
                    $result = mysqli_fetch_assoc(mysqli_query($conn,
                        "SELECT `dailystrikes` FROM `$dbsource` WHERE `date`='$timestampDate'")) or syslog(LOG_WARNING,
                        "(ACCESS){LIGHTNING}<$device>[WARNING]: Fetching dailyStrikesSoFar failed. If this is the first update today and details is empty, this is normal. SQL Details: " . mysqli_error($conn));
                    $dailyStrikesSoFar = (float)$result['dailystrikes'];

                    // If there is no dailyStrikesSoFar then we are starting a new day or sensor.
                    if (!$dailyStrikesSoFar) {
                        $dailyStrikes = $currentStrikes;
                    } else {
                        $dailyStrikes = $dailyStrikesSoFar + $currentStrikes;
                    }

                    // Insert lightning readings into DB
                    $sql = "INSERT INTO `$dbsource` (`dailystrikes`, `currentstrikes`, `last_update`, `date`) VALUES('$dailyStrikes', '$currentStrikes', '$timestamp', '$timestampDate') ON DUPLICATE KEY UPDATE `dailystrikes`='$dailyStrikes', `currentstrikes`='$currentStrikes', `last_update`='$timestamp';
                            UPDATE `lightningData` SET `strikecount`='$strikecount', `interference`='$interference', `last_strike_ts`='$last_strike_ts', `last_strike_distance`='$last_strike_distance' WHERE `source`='$source';";
                    mysqli_multi_query($conn, $sql) or syslog(LOG_ERR,
                        "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Failed to insert readings. Details: " . mysqli_error($conn));
                    while (mysqli_next_result($conn)) {
                        NULL;
                    }
                    syslog(LOG_INFO,
                        "(ACCESS){LIGHTNING}<$device>: Daily = $dailyStrikes | Current: $currentStrikes | Count: $strikecount | Last: $lastStrikecount | Last Daily: $dailyStrikesSoFar | Interference = $interference | Last Strike = $last_strike_ts | Distance = $last_strike_distance");
                } // strikeCount changed, date not greater, update the readings
                else {
                    $sql = "UPDATE `lightningData` SET `strikecount`='$strikecount', `interference`='$interference', `last_strike_ts`='$last_strike_ts', `last_strike_distance`='$last_strike_distance' WHERE `source`='$source'";
                    mysqli_query($conn, $sql) or syslog(LOG_ERR,
                        "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Count changed, date not greater - Insert Failed. Details: " . mysqli_error($conn));
                    syslog(LOG_INFO,
                        "(ACCESS){LIGHTNING}<$device>: Count and date mismatch | Count: $strikecount | Last: $lastStrikecount | Interference = $interference | Last Strike = $last_strike_ts | Distance = $last_strike_distance");
                }
            } // strikeCount is equal, update the last reading time
            else {
                // Insert lightning readings into DB
                $sql = "INSERT INTO `$dbsource` (`dailystrikes`, `currentstrikes`, `last_update`, `date`) VALUES(0, 0, '$timestamp', '$timestampDate') ON DUPLICATE KEY UPDATE `last_update`='$timestamp'";
                mysqli_query($conn, $sql) or syslog(LOG_ERR,
                    "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Equal Strike Count - Failed to insert readings. Details: " . mysqli_error($conn));
                syslog(LOG_INFO,
                    "(ACCESS){LIGHTNING}<$device>: No Lightning Detected | Count: $strikecount | Last: $lastStrikecount | Interference = $interference | Last Strike = $last_strike_ts | Distance = $last_strike_distance");
            }
        }
    } // Zero Strike Count
    else {
        // Get the last update date
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `last_strike_ts` FROM `lightningData` WHERE `source`='$source';"));
        @$lastStrikeTime = $result['last_strike_ts'];

        // If there is no existing data, let's start with this one
        if (empty($lastStrikeTime)) {
            $sql = "INSERT INTO `$dbsource` (`dailystrikes`, `currentstrikes`, `last_update`, `date`) VALUES(0, 0, '$timestamp', '$timestampDate') ON DUPLICATE KEY UPDATE `last_update` = '$timestamp';
            INSERT INTO `lightningData` (`strikecount`, `interference`, `last_strike_ts`, `last_strike_distance`, `source`) VALUES('$strikecount', '$interference', '$timestamp', '$last_strike_distance', '$source') ON DUPLICATE KEY UPDATE `strikecount`='$strikecount', `interference`='$interference', `last_strike_ts`='$last_strike_ts', `last_strike_distance`='$last_strike_distance', `source`='$source';";
            mysqli_multi_query($conn, $sql) or syslog(LOG_ERR,
                "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Failed inserting! Zero SC, no LST. Details: " . mysqli_error($conn));
            while (mysqli_next_result($conn)) {
                NULL;
            }
        } elseif ($lastStrikeTime < $timestampDate) {
            $sql = "INSERT INTO `$dbsource` (`dailystrikes`, `currentstrikes`, `last_update`, `date`) VALUES(0, 0, '$timestamp', '$timestampDate') ON DUPLICATE KEY UPDATE `last_update` = '$timestamp';
            INSERT INTO `lightningData` (`strikecount`, `interference`, `last_strike_ts`, `last_strike_distance`, `source`) VALUES('$strikecount', '$interference', '$timestamp', '$last_strike_distance', '$source') ON DUPLICATE KEY UPDATE `strikecount`='$strikecount', `interference`='$interference', `last_strike_ts`='$last_strike_ts', `last_strike_distance`='$last_strike_distance', `source`='$source';";
            mysqli_multi_query($conn, $sql) or syslog(LOG_ERR,
                "(ACCESS){LIGHTNING}<$device>[SQL ERROR]: Failed inserting Zero SC. Details: " . mysqli_error($conn));
            while (mysqli_next_result($conn)) {
                NULL;
            }
        }
        syslog(LOG_INFO,
            "(ACCESS){LIGHTNING}<$device>: No Lightning Detected | Count: $strikecount | Interference = $interference | Last Strike = $last_strike_ts | Distance = $last_strike_distance");
    }
}
