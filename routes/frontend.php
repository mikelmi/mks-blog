<?php
/**
 * User: mike
 * Date: 29.11.17
 * Time: 10:41
 */

Route::group([
    'prefix' => 'blog',
    'namespace' => 'Modules\\Blog\\Http\\Controllers',
    'as' => 'blog.'
], function (\Illuminate\Routing\Router $router) {
    $router->get('/', 'BlogController@index')->name('index');
    $router->get('/post/{id}/{slug?}', 'BlogController@post')->name('post');
    $router->get('/tag/{tag}', 'BlogController@tag')->name('tag');
    $router->get('/section/{id}/{slug?}', 'BlogController@section')->name('section');
    $router->get('/{id}/{slug?}', 'BlogController@category')->name('category');
});