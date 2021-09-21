<?php

use App\Http\Controllers\Admin\MicrosoftController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\JobController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/sign-in',[MicrosoftController::class,'msLogin'])->name('ms_login');
Route::get('/callback',[MicrosoftController::class,'callback'])->name('show_sign_in_view');
Route::get('/approve/{request_id}/{hash}/{choice}',[ApproveController::class,'accept']);
