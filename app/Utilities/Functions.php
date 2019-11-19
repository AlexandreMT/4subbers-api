<?php


namespace App\Utilities;


class Functions
{
    public static function generateURL() {
        return substr(md5(microtime()),rand(0,26),8);
    }
}