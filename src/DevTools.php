<?php

namespace src;


use Illuminate\Support\Facades\Response;

class DevTools
{
    public static function generatePin()
    {
        $pin = mt_rand(1000000, 2000000);
        return $pin;
    }

    public static function getPersianDate()
    {
        date_default_timezone_set('Iran');
        $pd = new PersianDate();
        return $pd->mds_date("Y/m/d", "now", 0);
    }

    public static function getPersianTime()
    {
        date_default_timezone_set('Iran');
        return date('H:i');
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

}