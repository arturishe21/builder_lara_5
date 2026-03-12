<?php

use Vis\Builder\Http\Controllers\TranslateController;

Route::get('/js/translate_phrases_{lang}.js', [TranslateController::class, 'getJs'])->name('translate_js');
Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('translations/phrases', [TranslateController::class, 'index'])->name('phrases_all');

            if (Request::ajax()) {
                Route::post('translations/phrases', [TranslateController::class, 'shopPopupForCreate'])
                    ->name('create_pop');

                Route::post('translations/add_record', [TranslateController::class, 'saveTranslate'])
                    ->name('add_record');

                Route::post('translations/change_text_lang', [TranslateController::class, 'savePhrase'])
                    ->name('change_text_lang');

                Route::post('translations/del_record', [TranslateController::class, 'remove'])
                    ->name('del_record');

                Route::post('translations/create_js_file', [TranslateController::class, 'createdJsFile'])
                    ->name('create_js_file');
            }
        });
});

Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => 'web'],
    function () {
        Route::post('auto_translate', [TranslateController::class, 'doTranslatePhraseInJs'])
            ->name('auto_translate');
    });
