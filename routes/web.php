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

Route::middleware(\App\Http\Middleware\EnsureSharedSecretIsSetMiddleware::class)->group(function () {
    Route::get('screens/{slug}', ScreenController::class)->name('screen');
    Route::get('screens', ScreenController::class)->name('kiosk');
    Route::get('cch/{slug}', ScreenController::class)->name('cch');
    Route::get('config', \App\Http\Controllers\ConfigController::class)->name('config');
    Route::get('browser/{browser}/preferences', \App\Http\Controllers\BrowserPreferencesController::class)->name('browser.preferences');
    Route::get('screens/{screen:hostname}/restart', \App\Http\Controllers\Screens\RestartController::class)->name('screens.restart');
    Route::post('screens/{screen}/ping', \App\Http\Controllers\Screens\PingController::class)->name('screens.ping');
});

Route::get('/', function () {
    return redirect('/admin');
})->name('login');

Route::get('timetable', \App\Http\Controllers\TimetableController::class)->name('timetable');

Route::get('efsched', \App\Http\Controllers\EurofurenceScheduleController::class)->name('efsched');
