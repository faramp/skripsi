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
    return view('home');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/show', 'UploadController@index');
Route::post('/fileupload', 'UploadController@upload');
Route::get('/forecasting', 'Forcasting2Controller@index');
Route::post('/forecasting', 'Forcasting2Controller@hitung');
Route::get('/graph', function () {
    return view('graph');
});
