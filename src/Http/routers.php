<?php

    Route::pattern('tree', '[a-z0-9-_]+');
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

                Route::post(
                    '/save_edit_on_site',
                    'Vis\Builder\ControllersNew\EditContentOnSiteController@index'
                );

                Route::get('logout', 'Vis\Builder\LoginController@doLogout')->name('logout');

                Route::get('/logs', 'Vis\Builder\LogViewerController@index');

                Route::any(
                    '/tree',
                    'Vis\Builder\TreeAdminController@index'
                );

                Route::any(
                    '/actions/tree',
                    'Vis\Builder\TreeAdminController@handle'
                );

                Route::post(
                    '/show-all-tree',
                    'Vis\Builder\TreeAdminController@showAll'
                );

                Route::post(
                    '/photo/upload',
                    'Vis\Builder\PhotoController@upload'
                );

                Route::post(
                    '/file/upload',
                    'Vis\Builder\PhotoController@upload'
                );

                Route::any(
                    '/photo/select_photos',
                    'Vis\Builder\PhotoController@selectPhotos'
                );

                Route::post(
                    '/actions/{page_admin}',
                    'Vis\Builder\TableAdminController@actionsPage'
                );

                Route::get(
                    '/actions/{page_admin}/export',
                    'Vis\Builder\ExportController@download'
                );

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

                Route::post(
                    '/{page_admin}',
                    'Vis\Builder\TableAdminController@actionsPage'
                );
            }
        );
    });
