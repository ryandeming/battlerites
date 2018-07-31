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

Route::get('/', 'PagesController@index');

Route::get('/about', 'PagesController@about');

Route::get('/player', 'PagesController@player');

Route::get('/player/{playerName}', 'PlayersController@retrieve');

//Route::get('/lookup/{playerName}', 'PagesController@lookup');

Route::get('/services', 'PagesController@services');

Route::resource('posts', 'PostsController');

    Route::resource('builds', 'BuildsController');

    Route::get('/builds', 'BuildsController@index');

    Route::get('/builds/view/{id}', 'BuildsController@view');

    Route::get('/builds/create', 'BuildsController@create');

    Route::get('/builds/create/{hero}', 'BuildsController@create');

    //Route::get('/builds/edit/{id}', 'BuildsController@edit');

    Route::get('/builds/{hero}', 'BuildsController@show');

    Route::get('/streamupdate', 'StreamsController@store');

    Route::get('/match/{matchId}', 'PlayerMatchesController@retrieve');

    Route::get('/playerpopulate', 'PlayersController@populate');

    Route::get('auth/steam', 'AuthController@redirectToSteam')->name('auth.steam');
    Route::get('auth/steam/handle', 'AuthController@handle')->name('auth.steam.handle');

    Route::get('/rate/{buildId}/{rate}', 'RatingsController@rate');

Auth::routes();