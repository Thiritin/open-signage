<?php

use App\Http\Controllers\ScreenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

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
Route::get('screens/{slug}', ScreenController::class)->name('screen');
Route::get('screens', ScreenController::class)->name('kiosk');
Route::get('cch/{slug}', ScreenController::class)->name('cch');
Route::get('config', \App\Http\Controllers\ConfigController::class)->name('config');
Route::get('browser/{browser}/preferences', \App\Http\Controllers\BrowserPreferencesController::class)->name('browser.preferences');
Route::get('/', function () {
    return redirect('/admin');
})->name('login');
