<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/siswa', 'App\Http\Controllers\SiswaController@index');
Route::get('/siswa/export_excel', 'App\Http\Controllers\SiswaController@export_excel');
Route::post('/siswa/import_excel', 'App\Http\Controllers\SiswaController@import_excel');

Route::get('/product', 'App\Http\Controllers\ProductController@index');
Route::post('/product/import_excel', 'App\Http\Controllers\ProductController@import_excel');

Route::get('/user', 'App\Http\Controllers\UserController@index');
Route::post('/user/import_excel', 'App\Http\Controllers\UserController@import_excel');

Route::get('/productuser', 'App\Http\Controllers\ProductUserController@index');
Route::post('/productuser/import_excel', 'App\Http\Controllers\ProductUserController@import_excel');
