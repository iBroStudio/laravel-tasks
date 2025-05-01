<?php

declare(strict_types=1);

use IBroStudio\Tasks\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/tasks/process/{process_id}', function (Request $request, int $process_id) {
    Process::resume($process_id);
    /*
    defer(function () use ($process_id) {
        Process::resume($process_id);
    });
    */

    return response()->json(['message' => 'ok']);
})
    ->name('tasks-process')
    ->middleware('signed');
