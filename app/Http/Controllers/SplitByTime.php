<?php

namespace App\Http\Controllers;

use App\API\APIError;
use App\Project;
use App\Utilities\Helpers;
use Captioning\Format\SubripFile;
use Illuminate\Http\Request;

class SplitByTime extends Controller
{
    public function getParts($url) {
        return Project::with('parts')->where('url', $url)->get();
    }

    public function splitByTime(Request $request) {
        try {
            $projectName = $request->name;
            $subtitle = new SubripFile($request->subtitle);
            $parts = $request->parts;
            $minutes = $request->minutes;

//            $project = $this->newProject($projectName, $subtitle);

            $subtitleParts = Helpers::calculateSlotsPartsByTime($minutes, $parts);
            $fromTo = array();
            for ($i = 0; $i < count($subtitleParts); $i++) {
                $partStartTimeMinutes = explode(' - ', $subtitleParts[$i])[0];
                $partStopTimeMinutes = explode(' - ', $subtitleParts[$i])[1];
                $partStartTime = Helpers::minutesToHours($partStartTimeMinutes);
                $partStopTime = Helpers::minutesToHours($partStopTimeMinutes);

                for ($c = 0; $c <= $subtitle->getCuesCount() - 1; $c++) {
                    $cueStartTime = Helpers::msToHours($subtitle->getCue($c)->getStartMS());
                    $cueStopTime = Helpers::msToHours($subtitle->getCue($c)->getStopMS());

                    if ($cueStartTime >= $partStartTime) {
                        if ($cueStopTime <= $partStopTime) {
                            array_push($fromTo, $c + 1);
                        }
                    }

                    if ($cueStartTime < $partStopTime && $cueStopTime > $partStopTime) {
                        array_push($fromTo, $c + 1);
                    }
                }
                print_r($fromTo[0] . ' --> ' . $fromTo[count($fromTo) - 1]);
                $fromTo = [];
                echo PHP_EOL . '------------' . PHP_EOL;
            }
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json(ApiError::errorMessage($e->getMessage(), 400));
            }
            return response()->json('Error on splitting subtitle.', 400);
        }
    }
}
