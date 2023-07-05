<?php

/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
 * @author Maxwell Power <max@acuparse.com>
 * @link http://www.acuparse.com
 * @license AGPL-3.0+
 *
 * Original calculations and JavaScript code written by Keith Burnett <https://tinyurl.com/wn2tmp5a>
 * and initially ported into PHP by Matt Hackmann <dxprog.com>
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
class moonDate
{
    /**
     * Finds the parabola through three points (-1,ym), (0,yz), (1, yp) and returns the coordinates
     * of the max/min (if any) xe, ye, and the values of x, where the parabola crosses zero
     * (roots of the self::quadratic) and the number of roots (0, 1 or 2) within the interval [-1, 1]
     */
    private static function quad($ym, $yz, $yp): array
    {
        $nz = $z1 = $z2 = 0;
        $a = 0.5 * ($ym + $yp) - $yz;
        $b = 0.5 * ($yp - $ym);
        $c = $yz;
        $xe = -$b / (2 * $a);
        $ye = ($a * $xe + $b) * $xe + $c;
        $dis = $b * $b - 4 * $a * $c;

        if ($dis > 0) {
            $dx = 0.5 * sqrt($dis) / abs($a);
            $z1 = $xe - $dx;
            $z2 = $xe + $dx;
            $nz = abs($z1) < 1 ? $nz + 1 : $nz;
            $nz = abs($z2) < 1 ? $nz + 1 : $nz;
            $z1 = $z1 < -1 ? $z2 : $z1;
        }

        return [$nz, $z1, $z2, $xe, $ye];
    }

    // Returns the sine of the altitude of the moon
    private static function sinAlt($mjd, $hour, $glon, $cglat, $sglat)
    {
        $mjd += $hour / 24;
        $t = ($mjd - 51544.5) / 36525;
        $moonEQ = self::moonEQ($t);

        $ra = $moonEQ[1];
        $dec = $moonEQ[0];
        $tau = 15 * (self::lmst($mjd, $glon) - $ra);

        return $sglat * sin(deg2rad($dec)) + $cglat * cos(deg2rad($dec)) * cos(deg2rad($tau));
    }

    // Returns an angle in degrees in the range 0 to 360
    private static function degRange($x)
    {
        $b = $x / 360;
        $a = 360 * ($b - (int)$b);

        return $a < 0 ? $a + 360 : $a;
    }

    private static function lmst($mjd, $glon)
    {
        $d = $mjd - 51544.5;
        $t = $d / 36525;
        $lst = self::degRange(280.46061839 + 360.98564736629 * $d + 0.000387933 * $t * $t - $t * $t * $t / 38710000);

        return $lst / 15 + $glon / 15;
    }

    /**
     * Takes t and returns the geocentric ra and dec in an array moonEQ claimed good to 5' (angle) in ra and 1' in dec
     * tallies with another approximate method and with ICE for a couple of dates
     */
    private static function moonEQ($t): array
    {
        $p2 = 6.283185307;
        $arc = 206264.8062;
        $coseps = 0.91748;
        $sineps = 0.39778;

        $lo = self::frac(0.606433 + 1336.855225 * $t);
        $l = $p2 * self::frac(0.374897 + 1325.552410 * $t);
        $l2 = $l * 2;
        $ls = $p2 * self::frac(0.993133 + 99.997361 * $t);
        $d = $p2 * self::frac(0.827361 + 1236.853086 * $t);
        $d2 = $d * 2;
        $f = $p2 * self::frac(0.259086 + 1342.227825 * $t);
        $f2 = $f * 2;

        $sinls = sin($ls);
        $sinf2 = sin($f2);

        $dl = 22640 * sin($l);
        $dl += -4586 * sin($l - $d2);
        $dl += 2370 * sin($d2);
        $dl += 769 * sin($l2);
        $dl += -668 * $sinls;
        $dl += -412 * $sinf2;
        $dl += -212 * sin($l2 - $d2);
        $dl += -206 * sin($l + $ls - $d2);
        $dl += 192 * sin($l + $d2);
        $dl += -165 * sin($ls - $d2);
        $dl += -125 * sin($d);
        $dl += -110 * sin($l + $ls);
        $dl += 148 * sin($l - $ls);
        $dl += -55 * sin($f2 - $d2);

        $s = $f + ($dl + 412 * $sinf2 + 541 * $sinls) / $arc;
        $h = $f - $d2;
        $n = -526 * sin($h);
        $n += 44 * sin($l + $h);
        $n += -31 * sin(-$l + $h);
        $n += -23 * sin($ls + $h);
        $n += 11 * sin(-$ls + $h);
        $n += -25 * sin(-$l2 + $f);
        $n += 21 * sin(-$l + $f);

        $L_moon = $p2 * self::frac($lo + $dl / 1296000);
        $B_moon = (18520.0 * sin($s) + $n) / $arc;

        $cb = cos($B_moon);
        $x = $cb * cos($L_moon);
        $v = $cb * sin($L_moon);
        $w = sin($B_moon);
        $y = $coseps * $v - $sineps * $w;
        $z = $sineps * $v + $coseps * $w;
        $rho = sqrt(1 - $z * $z);
        $dec = (360 / $p2) * atan($z / $rho);
        $ra = (48 / $p2) * atan($y / ($x + $rho));
        $ra = $ra < 0 ? $ra + 24 : $ra;

        return [$dec, $ra];
    }

    // Returns the fractional part of x as used in self::moonEQ
    private static function frac($x): float
    {
        $x -= (int)$x;

        return $x < 0 ? $x + 1 : $x;
    }

    /**
     * Takes the day, month, year and hours in the day and returns the modified julian day number defined as
     * mjd = jd - 2400000.5 checked OK for Greg era dates - 26 Dec 2002
     */
    private static function modifiedJulianDate($month, $day, $year)
    {
        if ($month <= 2) {
            $month += 12;
            $year--;
        }

        $a = 10000 * $year + 100 * $month + $day;

        if ($a <= 15821004.1) {
            $b = -2 * (int)(($year + 4716) / 4) - 1179;
        } else {
            $b = (int)($year / 400) - (int)($year / 100) + (int)($year / 4);
        }

        $a = 365 * $year - 679004;

        return $a + $b + (int)(30.6001 * ($month + 1)) + $day;
    }

    // Converts an hour decimal to hours and minutes
    private static function convertTime($hours): array
    {
        $hrs = (int)($hours * 60 + 0.5) / 60.0;
        $h = (int)($hrs);
        $m = (int)(60 * ($hrs - $h) + 0.5);

        return ['hrs' => $h, 'min' => $m];
    }

    // Calculates the Moonrise and Moonset for a given Latitude and Longitude and Date.
    public static function moonRiseSet($month, $day, $year, $lat, $lon, $timezone): stdClass
    {
        $utcRise = $utcSet = $rise = $set = 0;
        $hour = 1;
        $date = self::modifiedJulianDate($month, $day, $year);
        $date -= (float)$timezone / 24;
        $sinho = 0.0023271056;
        $sglat = sin(deg2rad($lat));
        $cglat = cos(deg2rad($lat));
        $ym = self::sinAlt($date, $hour - 1.0, $lon, $cglat, $sglat) - $sinho;

        while ($hour < 25 && ($set === 0 || $rise === 0)) {

            $yz = self::sinAlt($date, $hour, $lon, $cglat, $sglat) - $sinho;
            $yp = self::sinAlt($date, $hour + 1, $lon, $cglat, $sglat) - $sinho;

            $quadOut = self::quad($ym, $yz, $yp);
            $nz = $quadOut[0];
            $z1 = $quadOut[1];
            $z2 = $quadOut[2];
            $ye = $quadOut[4];

            if ($nz == 1) {
                if ($ym < 0) {
                    $utcRise = $hour + $z1;
                    $rise = 1;
                } else {
                    $utcSet = $hour + $z1;
                    $set = 1;
                }
            }

            if ($nz == 2) {
                if ($ye < 0) {
                    $utcRise = $hour + $z2;
                    $utcSet = $hour + $z1;
                } else {
                    $utcRise = $hour + $z1;
                    $utcSet = $hour + $z2;
                }
            }

            $ym = $yp;
            $hour += 2.0;
        }

        // Convert to unix timestamps and return as an object
        $output = new stdClass();
        $utcRise = self::convertTime($utcRise);
        $utcSet = self::convertTime($utcSet);
        $output->rise = $rise ? mktime($utcRise['hrs'], $utcRise['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);
        $output->set = $set ? mktime($utcSet['hrs'], $utcSet['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);

        return $output;
    }
}
