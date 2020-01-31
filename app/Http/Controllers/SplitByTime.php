<?php

namespace App\Http\Controllers;

use App\API\APIError;
use App\Part;
use App\Project;
use App\Utilities\Helpers;
use Captioning\Format\SubripFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            error_log($projectName);
            error_log($parts);
            error_log($minutes);
            $project = $this->newProject($projectName, $subtitle);

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
                            array_push($fromTo, $c);
                        }
                    }

                    if ($cueStartTime < $partStopTime && $cueStopTime > $partStopTime) {
                        array_push($fromTo, $c);
                    }
                }
                $newPart = $subtitle->buildPart($fromTo[0], $fromTo[count($fromTo) - 1]);

                $this->newPart(
                    $newPart,
                    $projectName,
                    str_replace(' - ', '_', $subtitleParts[$i]),
                    $project
                );

                $fromTo = [];
            }

            if ($project) {
                return response()->json($this->getParts($project->url), 201);
            }
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json(ApiError::errorMessage($e->getMessage(), 400));
            }
            return response()->json('Error on splitting subtitle.', 400);
        }
    }

    public function newProject($projectName, SubripFile $originalSubtitle) {
        $projectUrl = Helpers::generateURL();
        $fileName = str_replace(' ', '_', $projectName) . '_original_' . $projectUrl . '.srt';

        Storage::put('4subbers/' . $fileName, $originalSubtitle->getFileContent());

        $project = new Project;
        $project->url = $projectUrl;
        $project->name = $projectName;
        $project->originalSubtitle = $fileName;
        $project->save();

        return $project;
    }

    public function newPart(SubripFile $newPart, $projectName, $interval, $project) {
        $codePart = Helpers::generateURL();
        $fileName = str_replace(' ', '_', $projectName) . '_['. $interval . ']_' . $codePart . '.srt';

        Storage::put('4subbers/' . $fileName, $newPart->getFileContent());

        $part = new Part;
        $part->id = $project->id;
        $part->fileName = $fileName;
        $part->save();
    }
}
