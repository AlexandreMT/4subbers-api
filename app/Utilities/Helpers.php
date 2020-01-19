<?php


namespace App\Utilities;


class Helpers
{
    public static function generateURL() {
        return substr(md5(microtime()),rand(0,26),8);
    }

    public static function calculateSlotsPartsByCues($totalCues, $totalSlots) {
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

    public static function calculateSlotsPartsByTime($totalMinutes, $totalSlots) {
        $parts = array_fill(0, $totalSlots, '0');

        while ($totalMinutes > 0) {
            for ($i = 0; $i < $totalSlots; $i++) {
                if ($totalMinutes > 0) {
                    $parts[$i]++;
                    $totalMinutes--;
                }
            }
        }

        $formattedParts = array();

        for ($i = 0; $i < count($parts); $i++) {
            if ($i >= 1) {
                $parts[$i] += $parts[$i - 1];
                array_push($formattedParts, $parts[$i - 1] . ' - ' .  $parts[$i]);
            } else {
                array_push($formattedParts, '0 - ' . $parts[$i]);
            }
        }

        return $formattedParts;
    }

    public static function msToHours($milliseconds) {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;

        $format = '%02d:%02d:%02d';
        $time = sprintf($format, $hours, $minutes, $seconds);

        return $time;
    }

    public static function minutesToHours($time) {
        $format = '%02d:%02d:%02d';

        if ($time < 1) {
            return '00:00:00';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf($format, $hours, $minutes, 0, 0);
    }
}
