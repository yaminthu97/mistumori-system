<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 管理側
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');

    // ログイン認証
    Auth::routes([
        'register' => false,
        'reset'    => false,
        'verify'   => false,
        'confirm'  => false,
    ]);

    // パスワード再発行画面
    Route::match(['get', 'post'], 'password/reset/index', 'Auth\ResetPasswordController@index')->name('passwordReset.index');
    // パスワード再発行フォーム入力
    Route::match(['get', 'post'], 'password/reset/create', 'Auth\ResetPasswordController@create')->name('passwordReset.create');
    // パスワード再発行完了画面
    Route::match(['get', 'post'], 'password/reset/finish', 'Auth\ResetPasswordController@finish')->name('passwordReset.finish');

    // ログイン後の画面はこの中に
    Route::middleware('auth:admin')->group(function () {
        //【MK02020】ダッシュボード画面
        Route::get('/top', 'TopController@index')->name('top.index');

        // ログアウト
        Route::get('/logout', 'Auth\LoginController@logout');

        // アカウント一覧画面
        Route::match(['get', 'post'], '/account', 'AccountController@index')->name('account.index');
        Route::post('/account/delete-selected', 'AccountController@deleteSelected')->name('account.deleteSelected');
        Route::post('/account/delete', 'AccountController@delete')->name('account.delete');
        Route::match(['get', 'post'], '/account/save/{account_id?}', 'AccountController@save')->name('account.save');

        // アカウントCSVのダウンロード
        Route::post('/account/csv/download/api', 'AccountController@accountDownloadCSV')->name('account.downloadApi');
        // アカウントCSVアップロード
        Route::post('/account/csv/upload', 'AccountController@csvUpload')->name('account.csvUpload');

        // コメント
        Route::post('/comment/create', 'CommentController@create')->name('comment.create');
        Route::post('/comment/edit', 'CommentController@edit')->name('comment.edit');
        Route::post('/comment/delete', 'CommentController@delete')->name('comment.delete');

        // 見積内容
        Route::post('/estimate/create', 'EstimateController@create')->name('estimate.create');
        Route::post('/estimate/edit', 'EstimateController@edit')->name('estimate.edit');
        Route::post('/estimate/delete', 'EstimateController@delete')->name('estimate.delete');
        Route::get('/estimate/{id?}/download/{filepath?}', 'EstimateController@estimateFileDownload')->name('estimateFile.download');
        Route::get('/estimate/get-estimate-data/{estimate_id}', 'EstimateController@getEstimationData')->name('estimate.getEstimationData');

        // お問い合わせ管理
        Route::get('/inquiry', 'InquiryController@index')->name('inquiry.index');
        Route::match(['get', 'post'], 'inquiry/save/{inquiry_id?}', 'InquiryController@save')->name('inquiry.save');
        Route::get('/inquiry/detail/{inquiry_id}', 'InquiryController@showCreatedInquiry')->name('inquiry.detail');

        // お問い合わせからの回答
        Route::post('/answer/save', 'AnswerController@save')->name('answer.save');
        Route::post('/answer/delete', 'AnswerController@delete')->name('answer.delete');
        Route::get('/answer/get-answer-data/{answer_id}', 'AnswerController@getAnswerData')->name('answer.getAnswerData');

        // プロジェクト
        Route::get('/project', 'ProjectController@index')->name('project.index');
        Route::match(['get', 'post'], '/project/save/{id?}', 'ProjectController@save')->name('project.save');
        Route::get('/project/detail/{project_id}', 'ProjectController@detail')->name('project.detail');
        Route::get('/project/detail/{project_id}/download/{filepath?}', 'ProjectController@systemContentDownload')->name('systemContent.download');

        // 顧客リスト
        Route::get('/customer', 'CustomerController@index')->name('customer.index');
        // お客様登録と編集画面
        Route::match(['get', 'post'], '/customer/save/{customer_id?}', 'CustomerController@save')->name('customer.save');

        // 通知
        Route::get('/notification', 'NotificationsController@getNotifications')->name('notification.notificationList');
        Route::get('/markAsRead/{id}', 'NotificationsController@markAsRead')->name('markAsRead');

        // 言語設定
        Route::get('lang/{lang}', 'LanguageController@switchLang')->name('lang.switch');
        // 言語変更
        Route::get('/language/setting', 'LanguageController@changeLanguage')->name('change.language');

        // Wiki
        Route::get('/wiki', 'WikiController@index')->name('wiki.index');
        Route::get('/wiki/detail/{id}', 'WikiController@detail')->name('wiki.detail');
        Route::match(['get', 'post'], '/wiki/save/{id?}', 'WikiController@save')->name('wiki.save');
        Route::post('/wiki/delete', 'WikiController@delete')->name('wiki.delete');
        Route::get('/wiki/{id}/download/{filepath}', 'WikiController@wikiDownload')->name('wiki.download');

        // オンボーディング
        Route::post('/onboarding/{user_id}', 'OnboardController@changeOnboardStatus')->name('changeOnboard');
    });
});
