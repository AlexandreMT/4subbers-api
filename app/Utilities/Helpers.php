<?php


namespace App\Utilities;


class Helpers
{
    public static function generateURL() {
        return substr(md5(microtime()),rand(0,26),8);
    }
}
