<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



Route::prefix('subtitle-split')->group(function () {
  Route::post('/cue', 'SplitByCue@SplitByCue');

  Route::post('/time', 'SplitByTime@splitByTime');

  Route::get('/get-parts/{url}', 'SplitByCue@getParts');

  Route::get('/storage/{filename}', function ($filename) {
      if (!Storage::exists('4subbers/' . $filename)) {
          abort(404);
      }

      return Storage::download('4subbers/' . $filename);
  });
});

Route::prefix('subtitle-statistics')->group(function () {
  Route::post('/statistics', 'SubtitleStatistics@subtitleStatistics');
})->middleware('cors');
