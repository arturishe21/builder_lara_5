<?php

Route::get('/js/translate_phrases_{lang}.js', 'Vis\Builder\Http\Controllers\TranslateController@getJs')->name('translate_js');
Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('translations/phrases', [
                    'as'   => 'phrases_all',
                    'uses' => 'Vis\Builder\Http\Controllers\TranslateController@fetchIndex', ]
            );

            if (Request::ajax()) {
                Route::post('translations/create_pop', [
                        'as'   => 'create_pop',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@fetchCreate', ]
                );
                Route::post('translations/translate', [
                        'as'   => 'translate',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@doTranslate', ]
                );
                Route::post('translations/add_record', [
                        'as'   => 'add_record',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@doSaveTranslate', ]
                );
                Route::post('translations/change_text_lang', [
                        'as'   => 'change_text_lang',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@doSavePhrase', ]
                );
                Route::post('translations/del_record', [
                        'as'   => 'del_record',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@doDelelePhrase', ]
                );
                Route::post('translations/create_js_file', [
                        'as'   => 'create_js_file',
                        'uses' => 'Vis\Builder\Http\Controllers\TranslateController@createdJsFile', ]
                );
            }
        });
});

Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => 'web'],
    function () {
        Route::post('auto_translate', 'Vis\Builder\Http\Controllers\TranslateController@doTranslatePhraseInJs')
            ->name('auto_translate');
    });
