<?php


namespace App\Utilities;


class Helpers
{
    public static function generateURL() {
        return substr(md5(microtime()),rand(0,26),8);
    }

    public static function calculateSlotsPartsByLines($totalCues, $totalSlots) {
        $parts = array_fill(0, $totalSlots, '0');

        while ($totalCues > 0) {
            for ($i = 0; $i < $totalSlots; $i++) {
                if ($totalCues > 0) {
                    $parts[$i]++;
                    $totalCues--;
                }
            }
        }

        $formattedParts = array();

        for ($i = 0; $i < count($parts); $i++) {
            if ($i >= 1) {
                $parts[$i] += $parts[$i - 1];
                array_push($formattedParts, $parts[$i - 1] + 1 . ' - ' .  $parts[$i]);
            } else {
                array_push($formattedParts, '0 - ' . $parts[$i]);
            }
        }

        return $formattedParts;
    }
}
