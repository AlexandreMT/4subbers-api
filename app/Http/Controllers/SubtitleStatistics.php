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

            $subtitleStatistics = array();

            for ($i = 0; $i <= $subtitle->getCuesCount() - 1; $i++) {
                array_push($subtitleStatistics, [
                    'cue' => $i + 1,
                    'textFirstLine' => $subtitle->getCue($i)->getTextLine(0),
                    'textSecondeLine' => $subtitle->getCue($i)->getTextLine(1),
                    'totalCharsFirstLine' => strlen($subtitle->getCue($i)->getTextLine(0)),
                    'totalCharsSecondeLine' => strlen($subtitle->getCue($i)->getTextLine(1)),
                    'totalCueChars' => strlen($subtitle->getCue($i)->getTextLine(0)) + strlen($subtitle->getCue($i)->getTextLine(1)),
                    'start' => $subtitle->getCue($i)->getStart(),
                    'stop' => $subtitle->getCue($i)->getStop(),
                    'duration' => $subtitle->getCue($i)->getDuration(),
                    'cps' => $subtitle->getCue($i)->getCPS()
                ]);
            }

            return response()->json([
                'cuesStatistics' => $subtitleStatistics,
                'subtitleStatistics' => [
                    'totalCues' => $subtitle->getCuesCount()
                ]
            ], 200);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json(ApiError::errorMessage($e->getMessage(), 1010));
            }
            return response()->json('Erro na leitura da legenda!', 1010);
        }
    }
}
