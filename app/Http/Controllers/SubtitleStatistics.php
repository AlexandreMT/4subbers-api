<?php

namespace App\Http\Controllers;

use App\API\APIError;
use Captioning\Format\SubripFile;
use Illuminate\Http\Request;

class SubtitleStatistics extends Controller
{
    public static function subtitleStatistics(Request $request) {
        try {
            $subtitle = new SubripFile($request->subtitle);

            $maxCps = 0;
            $minCps = 1000;

            $maxCueLength = 0;
            $minCueLength = 1000;

            $maxSingleLineLength = 0;
            $minSingleLineLength = 1000;

            $subtitleStatistics = array();

            for ($i = 0; $i <= $subtitle->getCuesCount() - 1; $i++) {
                array_push($subtitleStatistics, [
                    'cue' => $i + 1,
                    'cueText' => $subtitle->getCue($i)->getText(),
                    'textFirstLine' => $subtitle->getCue($i)->getTextLine(0),
                    'textSecondeLine' => $subtitle->getCue($i)->getTextLine(1),
                    'totalCharsFirstLine' => strlen($subtitle->getCue($i)->getTextLine(0)),
                    'totalCharsSecondeLine' => strlen($subtitle->getCue($i)->getTextLine(1)),
                    'totalCueChars' => $subtitle->getCue($i)->strlen(),
                    'start' => $subtitle->getCue($i)->getStart(),
                    'stop' => $subtitle->getCue($i)->getStop(),
                    'duration' => $subtitle->getCue($i)->getDuration(),
                    'cps' => $subtitle->getCue($i)->getCPS()
                ]);

                // Check max CPS
                if ($subtitle->getCue($i)->getCPS() > $maxCps) {
                    $maxCps = $subtitle->getCue($i)->getCPS();
                }

                // Check min CPS
                if ($subtitle->getCue($i)->getCPS() < $minCps) {
                    $minCps = $subtitle->getCue($i)->getCPS();
                }

                // Check max simple CPL
                if (strlen($subtitle->getCue($i)->getTextLine(0)) > $maxSingleLineLength) {
                    $maxSingleLineLength = strlen($subtitle->getCue($i)->getTextLine(0));
                }

                // Check min simple CPL
                if (strlen($subtitle->getCue($i)->getTextLine(0)) < $minSingleLineLength) {
                    $minSingleLineLength = strlen($subtitle->getCue($i)->getTextLine(0));
                }

                // Check max cue length
                if ($subtitle->getCue($i)->strlen() > $maxCueLength) {
                    $maxCueLength = $subtitle->getCue($i)->strlen();
                }

                // Check min cue length
                if ($subtitle->getCue($i)->strlen() < $minCueLength) {
                    $minCueLength = $subtitle->getCue($i)->strlen();
                }
            }

            return response()->json([
                'subtitleStatistics' => [
                    'totalCues' => $subtitle->getCuesCount(),
                    'maxCPS' => $maxCps,
                    'minCPS' => $minCps,
                    'maxCueLength' => $maxCueLength,
                    'minCueLength' => $minCueLength,
                    'maxSingleLineLength' => $maxSingleLineLength,
                    'minSingleLineLength' => $minSingleLineLength
                ],
                'cuesStatistics' => $subtitleStatistics,
            ], 200);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json(ApiError::errorMessage($e->getMessage(), 1010));
            }
            return response()->json('Erro na leitura da legenda!', 1010);
        }
    }
}
