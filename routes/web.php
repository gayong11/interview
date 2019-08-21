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


Auth::routes();

Route::group(['middleware' => 'auth'], function () {


    // 邮箱验证提示页面
    Route::get('/email_verify_notice', 'PageController@emailVerifyNotice')->name('email_verify_notice');
    // 邮箱验证
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    // 发送邮箱验证
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');


    Route::group(['middleware' => 'email_verified'], function () {
        Route::get('/', 'PageController@root')->name('root');
        // 重置答题
        Route::post('/topic/reset', 'PageController@reset')->name('topic.reset');
        // 下一题
        Route::get('/topic/next', 'PageController@next')->name('topic.next');

    });
});
