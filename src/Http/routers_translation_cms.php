<?php

use Illuminate\Support\Facades\Route;
use Vis\Builder\Http\Controllers\TranslateCmsController;

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin/translations_cms', 'middleware' => 'auth.admin'],
        function () {
            Route::any('phrases', [TranslateCmsController::class, 'index']);
            Route::post('create', [TranslateCmsController::class, 'create']);
            Route::post('translate', [TranslateCmsController::class, 'doTranslate']);
            Route::post('add_record', [TranslateCmsController::class, 'saveTranslate']);
            Route::post('change-text-lang', [TranslateCmsController::class, 'changeTranslate']);
            Route::post('remove/{id}', [TranslateCmsController::class, 'destroy']);
        }
    );
});
