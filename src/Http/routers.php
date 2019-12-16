<?php

    Route::pattern('tree_name', '[a-z0-9-_]+');
    Route::pattern('any', '[a-z0-9-_/\]+');

    if (config('builder.admin.not_cms_urls')) {
        $noCmsUrl = config('builder.admin.not_cms_urls');

        Route::pattern('page_admin', '^(?!'.implode('|', $noCmsUrl).').*$');
    }

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
                    'Vis\Builder\TableAdminController@showTree'
                );
                Route::any(
                    '/handle/tree',
                    'Vis\Builder\TableAdminController@handleTree'
                );

                Route::any(
                    '/{tree_name}_tree',
                    'Vis\Builder\TableAdminController@showTreeOther'
                );
                Route::any(
                    '/handle/{tree_name}_tree',
                    'Vis\Builder\TableAdminController@handleTreeOther'
                );

                Route::post(
                    '/show_all_tree/{tree_name}',
                    'Vis\Builder\TableAdminController@showTreeAll'
                );

                Route::post(
                    '/handle/{page_admin}',
                    'Vis\Builder\TableAdminController@handlePage'
                );

                Route::post(
                    '/handle/{page_admin}/fast-edit',
                    'Vis\Builder\TableAdminController@fastEditText'
                );

                Route::post(
                    '/insert-new-record-for-many-to-many',
                    'Vis\Builder\TableAdminController@insertRecordForManyToMany'
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

                Route::post('change-relation-field', 'Vis\Builder\TableAdminController@doChangeRelationField');

                //router for pages builder
                Route::get(
                    '/{page_admin}',
                    'Vis\Builder\TableAdminController@showPage'
                )->middleware('permission');
                if (Request::ajax()) {
                    Route::get(
                        '/{page_admin}',
                        'Vis\Builder\TableAdminController@showPagePost'
                    );
                }
            }
        );
    });
