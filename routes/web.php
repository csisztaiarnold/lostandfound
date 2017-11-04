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

Route::get('/', function () {
    return view('index');
});

// Items
Route::get('items/success', 'ItemController@success');
Route::any('items/moderate/{unique_id}/{admin_hash}', 'ItemController@moderate');

// Images
Route::any('images/upload', 'ImageController@upload');
Route::any('images/reorder', 'ImageController@reorder');

Route::resource('items', 'ItemController');

