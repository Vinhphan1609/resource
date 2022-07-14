<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/roles', [App\Http\Controllers\PermissionController::class,'Permission']);

Route::get('login', [App\Http\Controllers\Auth\AuthController::class, 'index'])->name('login');
Route::post('doLogin', [App\Http\Controllers\Auth\AuthController::class, 'doLogin']); 
Route::get('registration', [App\Http\Controllers\Auth\AuthController::class, 'registration'])->name('register');
Route::get('post-registration', [App\Http\Controllers\Auth\AuthController::class, 'postRegistration'])->name('register.post'); 
Route::get('dashboard', [App\Http\Controllers\Auth\AuthController::class, 'dashboard']); 
Route::get('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');


Route::get('/staff','App\Http\Controllers\StaffsController@index');
Route::get('/export-excel', 'App\Http\Controllers\StaffsController@exportExcel');
