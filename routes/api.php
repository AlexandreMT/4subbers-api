<?php

use Illuminate\Http\Request;

Route::middleware(['cors'])->group(function () {
    Route::prefix('subtitle-split')->group(function () {
        Route::post('/cue', 'SplitByCue@SplitByCue');

        Route::get('/get-parts/{url}', 'SplitByCue@getParts');

        Route::get('/storage/{filename}', function ($filename) {
            $path = env('LOCAL_SAVE_PARTS') . $filename;

            if (!\Illuminate\Support\Facades\File::exists($path)) {
                abort(404);
            }
            return response()->download(env('LOCAL_SAVE_PARTS') . $filename);
        });
    });

    Route::prefix('subtitle-statistics')->group(function () {
        Route::post('/statistics', 'SubtitleStatistics@subtitleStatistics');
    });
});
