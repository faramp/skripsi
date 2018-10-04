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
Route::post('/logincustom','AksesController@loginAction');

Route::get('/tabel', 'LaporanController@index');
Route::get('/dashboard', function(){
	return view('home');
});
Route::get('/upload', 'UploadController@index');
Route::get('/input', 'InputController@index');
Route::get('datatable/{id_obat}/{tgl_dari}/{tgl_sampai}', 'LaporanController@datatableLaporan');
Route::get('/datatable/edit/{id_penjualan}','LaporanController@edit');
Route::post('/datatable/edit','LaporanController@editAction');
Route::get('/datatable/delete/{id_penjualan}','LaporanController@delete');
Route::post('/input', 'InputController@input');
Route::post('/grafik', 'ForecastingController@grafik');
Route::post('/grafikDetail', 'ForecastingController@grafikDetail');
Route::post('/upload', 'UploadController@upload');
Route::get('/forecasting', 'ForecastingController@index');
Route::post('/forecasting', 'ForecastingController@hitung');
Route::get('/logoutcustom', 'AksesController@logout');	

Route::group(['middleware' => ['auth']], function(){
});

