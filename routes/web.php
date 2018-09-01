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
Route::get('/datepicker', function () {
    return view('datepicker');
});
Route::get('/tabel', 'HomeController@index');
Route::get('datatable/{id_obat}/{tgl_dari}/{tgl_sampai}', 'HomeController@datatable');
Route::get('/upload', 'UploadController@index');
Route::get('/input', 'InputController@index');
Route::post('/input', 'InputController@input');
Route::post('/fileupload', 'UploadController@upload');
Route::get('/forecasting', 'ForcastingController@index');
Route::post('/forecasting', 'ForcastingController@hitung');
