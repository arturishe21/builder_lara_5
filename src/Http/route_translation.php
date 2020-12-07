<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('translations_cms/phrases', 'Vis\TranslationsCMS\TranslateController@index');

            if (Request::ajax()) {
                Route::post('translations_cms/create', 'Vis\TranslationsCMS\TranslateController@create');
                Route::post('translations_cms/translate', 'Vis\TranslationsCMS\TranslateController@doTranslate');
                Route::post('translations_cms/add-record', 'Vis\TranslationsCMS\TranslateController@addTraslate');
                Route::post('translations_cms/change-text-lang', 'Vis\TranslationsCMS\TranslateController@changeTranslate');
                Route::delete('translations_cms/{trans}', 'Vis\TranslationsCMS\TranslateController@destroy');
            }
        }
    );
});
