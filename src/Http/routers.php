<?php

    Route::pattern('tree_name', '[a-z0-9-_]+');
    Route::pattern('any', '[a-z0-9-_/\]+');

    Route::group(['middleware' => ['web']], function () {
        Route::get('login', 'Vis\Builder\LoginController@showLogin')->name('login_show');
        Route::post('login', 'Vis\Builder\LoginController@postLogin')->name('login');
    });

    Route::group(['middleware' => ['web']], function () {
        Route::group(
            ['prefix' => 'admin', 'middleware' => 'auth.admin'],
            function () {
                Route::post('change-range-card', 'Vis\Builder\ChangeRangeController@doChangeValue');
                Route::post('change-range-trend', 'Vis\Builder\ChangeRangeController@doChangeTrend');

                Route::get('logout', 'Vis\Builder\LoginController@doLogout')->name('logout');

                Route::get('/logs', 'Vis\Builder\LogViewerController@index');

                Route::any(
                    '/tree',
                    'Vis\Builder\TableAdminController@showTreeNew'
                );
                Route::any(
                    '/actions/tree',
                    'Vis\Builder\TableAdminController@handleTreeNew'
                );

                Route::post(
                    '/actions/{page_admin}',
                    'Vis\Builder\TableAdminController@actionsPage'
                );

                // view showDashboard
                Route::get('/', 'Vis\Builder\TBController@showDashboard');

                //routes for froala editor
                Route::post('upload_file', 'Vis\Builder\EditorController@uploadFile');
                Route::get('load_image', 'Vis\Builder\EditorController@loadImages');
                Route::post('delete_image', 'Vis\Builder\EditorController@deleteImages');
                Route::post('quick_edit', 'Vis\Builder\EditorController@doQuickEdit');
                Route::post('change_skin', 'Vis\Builder\TBController@doChangeSkin');
                Route::get('change_lang', 'Vis\Builder\TBController@doChangeLangAdmin')->name('change_lang');
                Route::post('upload_image', 'Vis\Builder\EditorController@uploadFoto');
                Route::post('save_croped_img', 'Vis\Builder\TBController@doSaveCropImg');

                //router for pages builder
                Route::get(
                    '/{page_admin}',
                    'Vis\Builder\TableAdminController@showPage'
                );
                if (Request::ajax()) {
                    Route::get(
                        '/{page_admin}',
                        'Vis\Builder\TableAdminController@showPagePost'
                    );
                }
            }
        );
    });
