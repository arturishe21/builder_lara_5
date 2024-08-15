<?php

use Vis\Builder\Http\Controllers\LoginController;
use Vis\Builder\Http\Controllers\TreeAdminController;
use Vis\Builder\Http\Controllers\LogViewerController;
use Vis\Builder\Http\Controllers\TableAdminController;
use Vis\Builder\Http\Controllers\EditorController;
use Vis\Builder\Http\Controllers\QuickEditController;
use Vis\Builder\Http\Controllers\TBController;
use Vis\Builder\Http\Controllers\ChangeRangeController;
use Vis\Builder\Http\ControllersNew\EditContentOnSiteController;
use Vis\Builder\Http\Controllers\ImagesManagementController;
use Vis\Builder\Http\Controllers\FilesManagementController;
use Vis\Builder\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

    Route::pattern('tree', '[a-z0-9-_]+');
    Route::pattern('any', '[a-z0-9-_/\]+');

    Route::group(['middleware' => ['web']], function () {
        Route::get('login', [LoginController::class, 'index'])->name('cms.login.index');
        Route::post('login', [LoginController::class, 'store'])->name('cms.login.store');
    });

    Route::group(['middleware' => ['web']], function () {
        Route::group(
            ['prefix' => 'admin', 'middleware' => 'auth.admin'],
            function () {
                Route::post('change-range-card', [ChangeRangeController::class, 'changeValue']);
                Route::post('change-range-trend', [ChangeRangeController::class, 'changeValue']);

                Route::post('/save_edit_on_site', [EditContentOnSiteController::class, 'index']);

                Route::get('logout',  [LoginController::class, 'logout'])->name('cms.logout');

                Route::get('/logs', [LogViewerController::class, 'index']);
                Route::any('/tree', [TreeAdminController::class, 'index']);
                Route::any('/actions/tree', [TreeAdminController::class, 'handle']);
                Route::post('/show-all-tree', [TreeAdminController::class, 'showAll']);

                Route::post('/photo/upload', [ImagesManagementController::class, 'upload']);
                Route::any('/photo/select_photos', [ImagesManagementController::class, 'selectPhotos']);

                Route::post('/file/upload', [FilesManagementController::class, 'upload']);
                Route::post('/file/select_files', [FilesManagementController::class, 'selectFiles']);

                Route::post('/actions/{page_admin}', [TableAdminController::class, 'actionsPage']);

                Route::get('/actions/{page_admin}/export', [ExportController::class, 'download']);

                Route::get('/', [TBController::class, 'showDashboard']);

                Route::post('upload_image', [EditorController::class, 'uploadImage']);
                Route::post('upload_file', [EditorController::class, 'uploadFile']);
                Route::get('load_image', [EditorController::class, 'getUploadedImages']);

                Route::post('change_skin', [TBController::class, 'changeSkin']);
                Route::get('change_lang', [TBController::class, 'changeLanguage'])->name('change_lang');

                Route::post('save_croped_img', [TBController::class, 'saveCropImg']);

                //router for pages builder
                Route::get('/{page_admin}', [TableAdminController::class, 'showPage']);
                if (Request::ajax()) {
                    Route::get('/{page_admin}', [TableAdminController::class, 'showPagePost']);
                }

                Route::post('/{page_admin}', [TableAdminController::class, 'actionsPage']);
                Route::post('/{page_admin}/fast-save/{id}', [TableAdminController::class, 'fastEdit']);
            }
        );
    });
