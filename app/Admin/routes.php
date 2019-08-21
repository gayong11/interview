<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    // 题目列表
    $router->get('topics', 'TopicController@index')->name('admin.topics.index');
    // 创建题目
    $router->get('topics/create', 'TopicController@create')->name('admin.topics.create');
    // 保存题目
    $router->post('topics', 'TopicController@store')->name('admin.topics.store');
    // 编辑题目
    $router->get('topics/{topic}/edit', 'TopicController@edit')->name('admin.topics.edit');
    // 更新题目
    $router->put('topics/{topic}', 'TopicController@update')->name('admin.topics.update');
    // 题目详情
    $router->get('topics/{topic}', 'TopicController@show')->name('admin.topics.show');

});
