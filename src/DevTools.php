<?php

namespace miladseifoori\DevTools;

use src\PersianDate;

class DevTools
{
    public static function generateUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
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
        $pd = new PersianDate();
        $date = $pd->mds_date("Y/m/d", "now", 0);
        $month = explode('/', $date)[1];
        return $pd->monthname($month);
    }

    public static function getPersianDate($isPersianNumber = false)
    {
        if ($isPersianNumber) {
            date_default_timezone_set('Iran');
            $pd = new PersianDate();
            return $pd->mds_date("Y/m/d", "now", 1);
        } else {
            date_default_timezone_set('Iran');
            $pd = new PersianDate();
            return $pd->mds_date("Y/m/d", "now", 0);
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

    public static function HttpGet($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public static function HttpPost($url, $header, $content)
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
}