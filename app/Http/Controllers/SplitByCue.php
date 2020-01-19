<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Part;
use App\API\APIError;
use App\Utilities\Helpers;
use Captioning\Format\SubripFile;

class SplitByCue extends Controller
{
    public function getParts($url) {
        return Project::with('parts')->where('url', $url)->get();
    }

    public function SplitByCue(Request $request){
        try {
            $projectName = $request->name;
            $subtitle = new SubripFile($request->subtitle);
            $partsToSplit = $request->parts;

            $totalCues = $subtitle->getCuesCount();

            $project = $this->newProject($projectName, $subtitle);

            $subtitleParts = Helpers::calculateSlotsPartsByCues($totalCues, $partsToSplit);

            for ($i = 0; $i < $partsToSplit; $i++) {
                $from = explode(' - ', $subtitleParts[$i])[0];
                $to = explode(' - ', $subtitleParts[$i])[1];

                $newPart = $subtitle->buildPart($from, $to);

                $this->newPart($newPart, $projectName, $i, $project);
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

        $originalSubtitle->save(
            env('LOCAL_SAVE_PARTS') .
            str_replace(' ', '_', $projectName) .
            '_original_' . $projectUrl . '.srt'
        );

        $project = new Project;
        $project->url = $projectUrl;
        $project->name = $projectName;
        $project->originalSubtitle = str_replace(' ', '_', $projectName) .
            '_original_' . $projectUrl . '.srt';
        $project->save();

        return $project;
    }

    public function newPart(SubripFile $newPart, $projectName, $i, $project) {
        $codePart = Helpers::generateURL();

        $newPart->save(
            env('LOCAL_SAVE_PARTS') .
            str_replace(' ', '_', $projectName) .
            '_[Part'. ($i + 1) . ']_' . $codePart . '.srt'
        );

        $part = new Part;
        $part->id = $project->id;
        $part->fileName =
            str_replace(' ', '_', $projectName) . '_[Part'. ($i + 1) . ']_' . $codePart . '.srt';
        $part->save();
    }
}
