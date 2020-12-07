<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('/settings/settings_all', 'Vis\Builder\SettingsController@fetchIndex')
                    ->name('m.show_settings');

            if (Request::ajax()) {
                Route::post('/settings/create_pop', 'Vis\Builder\SettingsController@fetchCreate');
                Route::post('/settings/add_record', 'Vis\Builder\SettingsController@doSave');
                Route::post('/settings/delete', 'Vis\Builder\SettingsController@doDelete');
                Route::post('/settings/edit_record', 'Vis\Builder\SettingsController@fetchEdit');
                Route::post('/settings/del_select', 'Vis\Builder\SettingsController@doDeleteSettingSelect');
                Route::post('/settings/fast_edit', 'Vis\Builder\SettingsController@doFastSave');
            }
        }
    );
});
