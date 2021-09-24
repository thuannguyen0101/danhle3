<?php

use App\Http\Controllers\Admin\MicrosoftController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\JobController;

use App\Http\Controllers\TimekeepingController;

use App\Models\Timekeeping;
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

Route::get('/',[TimekeepingController::class,'renderQrCode']);
Route::get('/api/{code}/{id}',[TimekeepingController::class,'handle']);
Route::get('/admin/sign-in',[MicrosoftController::class,'msLogin'])->name('ms_login');
Route::get('/callback',[MicrosoftController::class,'callback'])->name('show_sign_in_view');
Route::get('/approve/{request_id}/{hash}/{choice}',[ApproveController::class,'accept']);

Route::get('/test',function (){
    $all_user_late_attendance = Timekeeping::query()->where(['end_time'=>null,'late_attendance'=>0])->get();
    $all_user_late_attendance->late_attendance = 1;
    foreach ($all_user_late_attendance as $item){
        $item->late_attendance = 1;
        $item->save();
    }
    return $all_user_late_attendance;
});
