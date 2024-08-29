<?php

use Illuminate\Support\Facades\Route;


Route::post('login', 'Api\AuthController@signin');

Route::group(['prefix' => 'kindergartens'], function() {
    Route::get('', ['uses' => 'Api\KindergartenController@getAll']);
    Route::get('{id}', ['uses' => 'Api\KindergartenController@getById']);
});

Route::group(['prefix' => 'albums'], function() {
    Route::get      ('{albumId}', ['uses' => 'Api\AlbumController@getById']);
    Route::get      ('', ['uses' => 'Api\AlbumController@getAllByKindergartenId']);
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\AlbumController@create']);
    Route::post     ('{albumId}/multiple-upload', ['middleware' => 'auth:api', 'uses' => 'Api\AlbumController@multipleUpload']);
    Route::patch    ('{albumId}', ['middleware' => 'auth:api', 'uses' => 'Api\AlbumController@patch']);
    Route::delete   ('{albumId}', ['middleware' => 'auth:api', 'uses' => 'Api\AlbumController@delete']);
});

Route::group(['prefix' => 'documents'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\DocumentController@create']);
    Route::post     ('multiple-upload', ['middleware' => 'auth:api', 'uses' => 'Api\DocumentController@multipleUpload']);
    Route::get      ('', ['uses' => 'Api\DocumentController@get']);
    Route::delete   ('{documentId}', ['middleware' => 'auth:api', 'uses' => 'Api\DocumentController@delete']);
});

Route::group(['prefix' => 'documents-groups'], function() {
    Route::get      ('', ['uses' => 'Api\DocumentGroupController@getAll']);
});

Route::group(['prefix' => 'employees'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\EmployeeController@add']);
    Route::get      ('', ['uses' => 'Api\EmployeeController@getByKindergartenId']);
    Route::delete   ('{employeeId}', ['middleware' => 'auth:api', 'uses' => 'Api\EmployeeController@delete']);
    Route::patch    ('{employeeId}', ['middleware' => 'auth:api', 'uses' => 'Api\EmployeeController@patch']);
});

Route::group(['prefix' => 'kindergarten-groups'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\KindergartenGroupController@create']);
    Route::get      ('', ['uses' => 'Api\KindergartenGroupController@getByKindergartenId']);
    Route::get      ('{kindergartenGroupId}', ['uses' => 'Api\KindergartenGroupController@getById']);
    Route::delete   ('{kindergartenGroupId}', ['middleware' => 'auth:api', 'uses' => 'Api\KindergartenGroupController@delete']);
    Route::patch    ('{kindergartenGroupId}', ['middleware' => 'auth:api', 'uses' => 'Api\KindergartenGroupController@patch']);
});

Route::group(['prefix' => 'tt-groups'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\TtGroupController@create']);
});

Route::group(['prefix' => 'tts'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\TtController@create']);
    Route::get      ('', ['uses' => 'Api\TtController@getByKindergartenId']);
    Route::post     ('upload', ['middleware' => 'auth:api', 'uses' => 'Api\TtController@upload']);
    Route::get      ('template', ['uses' => 'Api\TtController@downloadTemplate']);
});


Route::group(['prefix' => 'news'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\NewsController@create']);
    Route::patch    ('{newsId}', ['middleware' => 'auth:api', 'uses' => 'Api\NewsController@patch']);
    Route::get      ('', ['uses' => 'Api\NewsController@getByKindergartenId']);
    Route::get      ('{newsId}', ['uses' => 'Api\NewsController@getById']);
    Route::delete   ('{newsId}', ['middleware' => 'auth:api', 'uses' => 'Api\NewsController@delete']);
});

Route::group(['prefix' => 'news-groups'], function() {
    Route::get      ('', ['uses' => 'Api\NewsGroupController@getAll']);
});

Route::group(['prefix' => 'welcome-blocks'], function() {
    Route::post     ('', ['middleware' => 'auth:api', 'uses' => 'Api\WelcomeBlockController@create']);
    Route::patch    ('{welcomeBlockId}', ['middleware' => 'auth:api', 'uses' => 'Api\WelcomeBlockController@patch']);
    Route::get      ('', ['uses' => 'Api\WelcomeBlockController@getByKindergartenId']);
});
