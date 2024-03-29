<?php

namespace MiladSeifoori\Tools;

use http\Env\Response;
use ArrayAccess;

class DevTools
{

    public static function addArray($array, $items)
    {
        foreach ($items as $key => $value) {
            $array = array_add($items, $key, $value);
        }

        return $array;
    }

    public static function generateUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        $generated = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        return strtolower($generated);
    }

    public static function generatePIN()
    {
        $pin = mt_rand(1000000, 2000000);
        return $pin;
    }

    public static function getPersianTime()
    {
        date_default_timezone_set('Iran');
        return date('H:i');
    }

    public static function getPersianMonth()
    {
        date_default_timezone_set('Iran');
        $date = self::mds_date("Y/m/d", "now", 0);
        $month = explode('/', $date)[1];
        return self::monthname($month);
    }

    public static function showResponse($status, $message, $code, $hasModel, $modelKey, $modelData)
    {
        if ($hasModel) {
            return Response::json([$modelKey => json_decode($modelData, true)], 200, [], JSON_PRETTY_PRINT);
        } else {
            $response = [
                'status' => $status,
                'message' => $message,
            ];
            return response($response, $code)
                ->header('Content-Type', 'application/json');
        }

    }

    public static function getPersianDate($isPersianNumber = false)
    {
        if ($isPersianNumber) {
            date_default_timezone_set('Iran');
            return self::mds_date("Y/m/d", "now", 1);
        } else {
            date_default_timezone_set('Iran');
            return self::mds_date("Y/m/d", "now", 0);
        }
    }

    public static function getUrlContent($url)
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $content = file_get_contents($url, false, stream_context_create($arrContextOptions));
        return $content;
    }

    public static function httpGet($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public static function httpPost($url, $header, $content)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_HTTPHEADER => $header));

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public static function hasContains($content, $needles)
    {
        if (strpos($content, $needles) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function mds_date($format, $when = "now", $persianNumber = 0)
    {
        ///chosse your timezone
        $TZhours = 0;
        $TZminute = 0;
        $need = "";
        $result1 = "";
        $result = "";
        if ($when == "now") {
            $year = date("Y");
            $month = date("m");
            $day = date("d");
            list($Dyear, $Dmonth, $Dday) = self::gregorian_to_mds($year, $month, $day);
            $when = mktime(date("H") + $TZhours, date("i") + $TZminute, date("s"), date("m"), date("d"), date("Y"));
        } else {
            //$when=0;
            $when += $TZhours * 3600 + $TZminute * 60;
            $date = date("Y-m-d", $when);
            list($year, $month, $day) = preg_split('/-/', $date);

            list($Dyear, $Dmonth, $Dday) = self::gregorian_to_mds($year, $month, $day);
        }

        $need = $when;
        $year = date("Y", $need);
        $month = date("m", $need);
        $day = date("d", $need);
        $i = 0;
        $subtype = "";
        $subtypetemp = "";
        list($Dyear, $Dmonth, $Dday) = self::gregorian_to_mds($year, $month, $day);
        while ($i < strlen($format)) {
            $subtype = substr($format, $i, 1);
            if ($subtypetemp == "\\") {
                $result .= $subtype;
                $i++;
                continue;
            }

            switch ($subtype) {

                case "A":
                    $result1 = date("a", $need);
                    if ($result1 == "pm") $result .= "&#1576;&#1593;&#1583;&#1575;&#1586;&#1592;&#1607;&#1585;";
                    else $result .= "&#1602;&#1576;&#1604;&#8207;&#1575;&#1586;&#1592;&#1607;&#1585;";
                    break;

                case "a":
                    $result1 = date("a", $need);
                    if ($result1 == "pm") $result .= "&#1576;&#46;&#1592;";
                    else $result .= "&#1602;&#46;&#1592;";
                    break;
                case "d":
                    if ($Dday < 10) $result1 = "0" . $Dday;
                    else    $result1 = $Dday;
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "D":
                    $result1 = date("D", $need);
                    if ($result1 == "Thu") $result1 = "&#1662;";
                    else if ($result1 == "Sat") $result1 = "&#1588;";
                    else if ($result1 == "Sun") $result1 = "&#1609;";
                    else if ($result1 == "Mon") $result1 = "&#1583;";
                    else if ($result1 == "Tue") $result1 = "&#1587;";
                    else if ($result1 == "Wed") $result1 = "&#1670;";
                    else if ($result1 == "Thu") $result1 = "&#1662;";
                    else if ($result1 == "Fri") $result1 = "&#1580;";
                    $result .= $result1;
                    break;
                case"F":
                    $result .= monthname($Dmonth);
                    break;
                case "g":
                    $result1 = date("g", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "G":
                    $result1 = date("G", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "h":
                    $result1 = date("h", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "H":
                    $result1 = date("H", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "i":
                    $result1 = date("i", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "j":
                    $result1 = $Dday;
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "l":
                    $result1 = date("l", $need);
                    if ($result1 == "Saturday") $result1 = "&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Sunday") $result1 = "&#1610;&#1603;&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Monday") $result1 = "&#1583;&#1608;&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Tuesday") $result1 = "&#1587;&#1607;&#32;&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Wednesday") $result1 = "&#1670;&#1607;&#1575;&#1585;&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Thursday") $result1 = "&#1662;&#1606;&#1580;&#1588;&#1606;&#1576;&#1607;";
                    else if ($result1 == "Friday") $result1 = "&#1580;&#1605;&#1593;&#1607;";
                    $result .= $result1;
                    break;
                case "m":
                    if ($Dmonth < 10) $result1 = "0" . $Dmonth;
                    else    $result1 = $Dmonth;
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "M":
                    $result .= short_monthname($Dmonth);
                    break;
                case "n":
                    $result1 = $Dmonth;
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "s":
                    $result1 = date("s", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "S":
                    $result .= "&#1575;&#1605;";
                    break;
                case "t":
                    $result .= lastday($month, $day, $year);
                    break;
                case "w":
                    $result1 = date("w", $need);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "y":
                    $result1 = substr($Dyear, 2, 4);
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "Y":
                    $result1 = $Dyear;
                    if ($persianNumber == 1) $result .= self::Convertnumber2farsi($result1);
                    else $result .= $result1;
                    break;
                case "U" :
                    $result .= mktime();
                    break;
                case "Z" :
                    $result .= days_of_year($Dmonth, $Dday, $Dyear);
                    break;
                case "L" :
                    list($tmp_year, $tmp_month, $tmp_day) = self::mds_to_gregorian(1384, 12, 1);
                    echo $tmp_day;
                    /*if(lastday($tmp_month,$tmp_day,$tmp_year)=="31")
                        $result.="1";
                    else
                        $result.="0";
                        */
                    break;
                default:
                    $result .= $subtype;
            }
            $subtypetemp = substr($format, $i, 1);
            $i++;
        }
        return $result;
    }

    public static function make_time($hour = "", $minute = "", $second = "", $Dmonth = "", $Dday = "", $Dyear = "")
    {
        if (!$hour && !$minute && !$second && !$Dmonth && !$Dmonth && !$Dday && !$Dyear)
            return mktime();
        if ($Dmonth > 11) die("Incorrect month number");
        list($year, $month, $day) = self::mds_to_gregorian($Dyear, $Dmonth, $Dday);
        $i = mktime($hour, $minute, $second, $month, $day, $year);
        return $i;
    }

    public static function mstart($month, $day, $year)
    {
        list($Dyear, $Dmonth, $Dday) = self::gregorian_to_mds($year, $month, $day);
        list($year, $month, $day) = mds_to_gregorian($Dyear, $Dmonth, "1");
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return date("w", $timestamp);
    }

    public static function lastday($month, $day, $year)
    {
        $Dday2 = "";
        $jdate2 = "";
        $lastdayen = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
        list($Dyear, $Dmonth, $Dday) = self::gregorian_to_mds($year, $month, $day);
        $lastdatep = $Dday;
        $Dday = $Dday2;
        while ($Dday2 != "1") {
            if ($day < $lastdayen) {
                $day++;
                list($Dyear, $Dmonth, $Dday2) = self::gregorian_to_mds($year, $month, $day);
                if ($jdate2 == "1") break;
                if ($jdate2 != "1") $lastdatep++;
            } else {
                $day = 0;
                $month++;
                if ($month == 13) {
                    $month = "1";
                    $year++;
                }
            }

        }
        return $lastdatep - 1;
    }

    public static function days_of_year($Dmonth, $Dday, $Dyear)
    {
        $year = "";
        $month = "";
        $year = "";
        $result = "";
        if ($Dmonth == "01")
            return $Dday;
        for ($i = 1; $i < $Dmonth || $i == 12; $i++) {
            list($year, $month, $day) = mds_to_gregorian($Dyear, $i, "1");
            $result += lastday($month, $day, $year);
        }
        return $result + $Dday;
    }

    public static function monthname($month)
    {

        if ($month == "01" || $month == "1") return "&#1601;&#1585;&#1608;&#1585;&#1583;&#1610;&#1606;";

        if ($month == "02" || $month == "2") return "&#1575;&#1585;&#1583;&#1610;&#1576;&#1607;&#1588;&#1578;";

        if ($month == "03" || $month == "3") return "&#1582;&#1585;&#1583;&#1575;&#1583;";

        if ($month == "04" || $month == "4") return "&#1578;&#1610;&#1585;";

        if ($month == "05" || $month == "5") return "&#1605;&#1585;&#1583;&#1575;&#1583;";

        if ($month == "06" || $month == "6") return "&#1588;&#1607;&#1585;&#1610;&#1608;&#1585;";

        if ($month == "07" || $month == "7") return "&#1605;&#1607;&#1585;";

        if ($month == "08" || $month == "8") return "&#1570;&#1576;&#1575;&#1606;";

        if ($month == "09" || $month == "9") return "&#1570;&#1584;&#1585;";

        if ($month == "10") return "&#1583;&#1610;";

        if ($month == "11") return "&#1576;&#1607;&#1605;&#1606;";

        if ($month == "12") return "&#1575;&#1587;&#1601;&#1606;&#1583;";
    }

    public static function short_monthname($month)
    {

        if ($month == "01") return "&#1601;&#1585;&#1608;";

        if ($month == "02") return "&#1575;&#1585;&#1583;";

        if ($month == "03") return "&#1582;&#1585;&#1583;";

        if ($month == "04") return "&#1578;&#1610;&#1585;";

        if ($month == "05") return "&#1605;&#1585;&#1583;";

        if ($month == "06") return "&#1588;&#1607;&#1585;";

        if ($month == "07") return "&#1605;&#1607;&#1585;";

        if ($month == "08") return "&#1570;&#1576;&#1575;";

        if ($month == "09") return "&#1570;&#1584;&#1585;";

        if ($month == "10") return "&#1583;&#1610;";

        if ($month == "11") return "&#1576;&#1607;&#1605;";

        if ($month == "12") return "&#1575;&#1587;&#1601; ";
    }

    public static function Convertnumber2farsi($srting)
    {
        $num0 = "&#1776;";
        $num1 = "&#1777;";
        $num2 = "&#1778;";
        $num3 = "&#1779;";
        $num4 = "&#1780;";
        $num5 = "&#1781;";
        $num6 = "&#1782;";
        $num7 = "&#1783;";
        $num8 = "&#1784;";
        $num9 = "&#1785;";

        $stringtemp = "";
        $len = strlen($srting);
        for ($sub = 0; $sub < $len; $sub++) {
            if (substr($srting, $sub, 1) == "0") $stringtemp .= $num0;
            elseif (substr($srting, $sub, 1) == "1") $stringtemp .= $num1;
            elseif (substr($srting, $sub, 1) == "2") $stringtemp .= $num2;
            elseif (substr($srting, $sub, 1) == "3") $stringtemp .= $num3;
            elseif (substr($srting, $sub, 1) == "4") $stringtemp .= $num4;
            elseif (substr($srting, $sub, 1) == "5") $stringtemp .= $num5;
            elseif (substr($srting, $sub, 1) == "6") $stringtemp .= $num6;
            elseif (substr($srting, $sub, 1) == "7") $stringtemp .= $num7;
            elseif (substr($srting, $sub, 1) == "8") $stringtemp .= $num8;
            elseif (substr($srting, $sub, 1) == "9") $stringtemp .= $num9;
            else $stringtemp .= substr($srting, $sub, 1);
        }
        return $stringtemp;

    }

    public static function is_kabise($year)
    {
        if ($year % 4 == 0 && $year % 100 != 0)
            return true;
        return false;
    }

    public static function mcheckdate($month, $day, $year)
    {
        $m_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
        if ($month <= 12 && $month > 0) {
            if ($m_days_in_month[$month - 1] >= $day && $day > 0)
                return 1;
            if (is_kabise($year))
                echo "Asdsd";
            if (is_kabise($year) && $m_days_in_month[$month - 1] == 31)
                return 1;
        }

        return 0;

    }

    public static function mtime()
    {
        return mktime();
    }

    public static function mgetdate($timestamp = "")
    {
        if ($timestamp == "")
            $timestamp = mktime();

        return array(
            0 => $timestamp,
            "seconds" => mds_date("s", $timestamp),
            "minutes" => mds_date("i", $timestamp),
            "hours" => mds_date("G", $timestamp),
            "mday" => mds_date("j", $timestamp),
            "wday" => mds_date("w", $timestamp),
            "mon" => mds_date("n", $timestamp),
            "year" => mds_date("Y", $timestamp),
            "yday" => days_of_year(mds_date("m", $timestamp), mds_date("d", $timestamp), mds_date("Y", $timestamp)),
            "weekday" => mds_date("l", $timestamp),
            "month" => mds_date("F", $timestamp),
        );
    }

    public static function gregorian_to_mds($g_y, $g_m, $g_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $m_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);


        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 399, 400);


        for ($i = 0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)))
            /* leap and after Feb */
            $g_day_no++;
        $g_day_no += $gd;

        $m_day_no = $g_day_no - 79;

        $j_np = self::div($m_day_no, 12053); /* 12053 = 365*33 + 32/4 */
        $m_day_no = $m_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * self::div($m_day_no, 1461); /* 1461 = 365*4 + 4/4 */

        $m_day_no %= 1461;

        if ($m_day_no >= 366) {
            $jy += self::div($m_day_no - 1, 365);
            $m_day_no = ($m_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $m_day_no >= $m_days_in_month[$i]; ++$i)
            $m_day_no -= $m_days_in_month[$i];
        $jm = $i + 1;
        $jd = $m_day_no + 1;

        return array($jy, $jm, $jd);
    }

    public static function mds_to_gregorian($m_y, $j_m, $m_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $m_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);


        $jy = $m_y - 979;
        $jm = $j_m - 1;
        $jd = $m_d - 1;

        $m_day_no = 365 * $jy + self::div($jy, 33) * 8 + self::div($jy % 33 + 3, 4);
        for ($i = 0; $i < $jm; ++$i)
            $m_day_no += $m_days_in_month[$i];

        $m_day_no += $jd;

        $g_day_no = $m_day_no + 79;

        $gy = 1600 + 400 * self::div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */ {
            $g_day_no--;
            $gy += 100 * self::div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4 * self::div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return array($gy, $gm, $gd);
    }

    public static function div($a, $b)
    {
        return (int)($a / $b);

    }
}