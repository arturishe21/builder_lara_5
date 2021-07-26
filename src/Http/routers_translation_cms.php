<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('translations_cms/phrases', 'Vis\Builder\Http\Controllers\TranslateCmsController@index');

            if (Request::ajax()) {
                Route::post('translations_cms/create', 'Vis\Builder\Http\Controllers\TranslateCmsController@create');
                Route::post('translations_cms/translate', 'Vis\Builder\Http\Controllers\TranslateCmsController@doTranslate');
                Route::post('translations_cms/add_record', 'Vis\Builder\Http\Controllers\TranslateCmsController@saveTranslate');
                Route::post('translations_cms/change-text-lang', 'Vis\Builder\Http\Controllers\TranslateCmsController@changeTranslate');
                Route::post('translations_cms/remove/{id}', 'Vis\Builder\Http\Controllers\TranslateCmsController@destroy');
            }
        }
    );
});
