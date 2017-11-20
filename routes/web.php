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

Route::get('/', 'MainController@index');

// Items
Route::get('items/success', 'ItemController@success');
Route::any('items/moderate/{unique_id}/{admin_hash}', 'ItemController@moderate');
Route::resource('items', 'ItemController');
Route::get('items/moderate/{id}/{unique_id}/{admin_hash}/{action?}', 'ItemController@moderate');

// Images
Route::any('images/upload', 'ImageController@upload');
Route::post('images/reorder', 'ImageController@reorder');
Route::get('images/delete/{id}', 'ImageController@delete');

// Notifications
Route::post('notifications/save', 'NotificationController@store');
Route::get('notifications/success', 'NotificationController@success');

// Locations
Route::post('locations/save-location-cookie','LocationController@saveLocationCookie');

