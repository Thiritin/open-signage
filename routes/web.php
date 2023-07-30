<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScreenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/screens/{slug}',ScreenController::class)->name('screen');
Route::get('/cch/{slug}',ScreenController::class)->name('screen');
Route::get('/', function () {
    return redirect('/admin');
});
