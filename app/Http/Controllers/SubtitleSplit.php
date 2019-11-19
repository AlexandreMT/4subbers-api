<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Part;
use App\API\APIError;
use App\Utilities\Helpers;
use Captioning\Format\SubripFile;

class SubtitleSplit extends Controller
{
    public function getParts($url) {
        return Project::with('parts')->where('url', $url)->get();
    }

    public function splitSubtitle(Request $request){
        try {
            $projectName = $request->name;
            $subtitle = new SubripFile($request->subtitle);
            $totalLines = $subtitle->getCuesCount(); // Pega o total de linhas da legenda
            $partsToSplit = $request->parts;
            $totalLinesPerPart = floor($totalLines / $partsToSplit); // Divide a quantidade de linhas por parte
            $lastPart = $totalLines - ($totalLinesPerPart * ($partsToSplit - 1)); // Define a quantidade de linhas da última parte
            $currentLines = 1;
            $sumParts = $totalLinesPerPart - 1;

            $project = $this->newProject($projectName);

            for ($i = 0; $i < $partsToSplit; $i++) {
                if ($i == 0) {
                    $newPart = new SubripFile();
                    for ($l = $currentLines - 1; $l <= $sumParts; $l++) {
                        $newPart->addCue($subtitle->getCue($l));
                    }
                    $newPart->build();
                    $this->newPart($newPart, $projectName, $i, $project);

                    $sumParts += $totalLinesPerPart;
                    $currentLines += $totalLinesPerPart - 1;
                } else {
                    if ($i + 1 == $partsToSplit) { // Ultima parte da legenda
                        $newPart = new SubripFile();

                        for ($l = $currentLines; $l <= ($lastPart + $currentLines) - 1; $l++) {
                            $newPart->addCue($subtitle->getCue($l));
                        }
                        $newPart->build();
                        $this->newPart($newPart, $projectName, $i, $project);
                    } else {
                        $newPart = new SubripFile();

                        for ($l = $currentLines; $l <= $sumParts; $l++) {
                            $newPart->addCue($subtitle->getCue($l));
                        }
                        $newPart->build();
                        $this->newPart($newPart, $projectName, $i, $project);

                        $sumParts += $totalLinesPerPart;
                        $currentLines += $totalLinesPerPart;
                    }
                }
            }

            if ($project) {
                return response()->json($this->getParts($project->url), 201);
            }
            return response()->json('Erro ao dividir legenda', 400);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json(ApiError::errorMessage($e->getMessage(), 1010));
            }
            return response()->json('Erro ao realizar a operação de salvar!', 1010);
        }
    }

    public function newProject($projectName) {
        $project = new Project;
        $project->url = Helpers::generateURL();
        $project->name = $projectName;
        $project->save();

        return $project;
    }

    public function newPart(SubripFile $newPart, $projectName, $i, $project) {
        $codePart = Helpers::generateURL();

        $newPart->save(env('LOCAL_SAVE_PARTS') . str_replace(' ', '_', $projectName) . '_[Part'. ($i + 1) . ']' . $codePart . '.srt');

        $part = new Part;
        $part->id = $project->id;
        $part->file_name = str_replace(' ', '_', $projectName) . '_[Part'. ($i + 1) . ']' . $codePart . '.srt';
        $part->save();
    }
}
