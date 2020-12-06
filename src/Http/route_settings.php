<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('/settings/settings_all', 'Vis\Builder\SettingsController@fetchIndex')->name('m.show_settings');

            if (Request::ajax()) {
                Route::post('/settings/create', 'Vis\Builder\SettingsController@create');
                Route::post('/settings/{setting}/update', 'Vis\Builder\SettingsController@update');
                Route::post('/settings/store', 'Vis\Builder\SettingsController@update');
                Route::post('/settings/{setting}/edit', 'Vis\Builder\SettingsController@edit');
                Route::post('/settings/fast-save/{setting}', 'Vis\Builder\SettingsController@fastSave');
            }
        }
    );
});
