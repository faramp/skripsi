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
    return view('logincustom');
});
Route::post('/logincustom','AksesController@loginAction');
Route::group(['middleware' => ['auth']], function(){
	Route::get('/tabel', 'LaporanController@index');
	Route::get('/dashboard', function(){
		return view('home');
	});
	Route::get('/input', 'InputController@index');
	Route::get('datatable/{id_obat}/{tgl_dari}/{tgl_sampai}', 'LaporanController@datatable');
	Route::get('/upload', 'UploadController@index');
	Route::post('/input', 'InputController@input');
	Route::post('/fileupload', 'UploadController@upload');
	Route::get('/forecasting', 'ForcastingController@index');
	Route::post('/forecasting', 'ForcastingController@hitung');
	Route::get('/logoutcustom', 'AksesController@logout');
});

