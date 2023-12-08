<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\CheckStatus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\KomplementController;
use App\Http\Controllers\HODController;
use App\Http\Controllers\OvertimeController;

use App\Http\Controllers\Service_Cuti;
use App\Http\Controllers\Service_Komplemen;

use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::controller(KaryawanController::class)->group(function () {
//     Route::post('login', 'login'); 
// });

// * jika middleware aktif memerlukan session untuk mengakses route controller
// Route::middleware([CheckStatus::class])->group(function(){
    Route::controller(KaryawanController::class)->group(function () {
        // CUTI
        // add data master cuti & kompliment karyawan from lokaHR
        Route::get('insert_karyawan', 'insertKaryawan'); 
        // request cuti karyawan
        Route::post('request_cuti', 'requestCuti'); 
        Route::post('request_cuti_khusus', 'requestCutiKhusus'); 
        // get data sisa cuti karyawan
        Route::get('get_cuti', 'getCutiKaryawan'); 
        // update akses approve
        Route::post('akses_approve', 'updateAksesApprove'); 
        // jika type approve custom
        // insert cutom approve
        Route::post('insert_custom_approve', 'insertCustomApprove'); 

        // KOMPLEMEN
        // get data sisa komplemen karyawan
        Route::get('get_komplemen', 'getKomplemenKaryawan'); 
        // request komplemen
        Route::post('request_komplemen', 'requestKomplemen'); 

        // OVERTIME
        // request overtime
        Route::post('request_overtime', 'requestOvertime'); 
    });

    Route::controller(HODController::class)->group(function () {
        // update action cuti HOD
        Route::post('update_action_cuti', 'actionRequestCuti'); 
        // update action overtime HOD
        Route::post('update_action_overtime', 'actionRequestOvertime'); 
        // get notif aprovel 
        Route::get('get_notif_approve', 'getNotifApprove'); 
    });

    Route::controller(CutiController::class)->group(function () {
        // get data master cuti
        Route::get('get_master_cuti', 'getMasterCuti'); 
        // get data cuti by ID
        Route::get('get_request_cuti_ByID', 'getRequestCutiByID'); 
        // get data request cuti
        Route::get('get_request_cuti', 'getRequestCuti'); 
    });

    Route::controller(OvertimeController::class)->group(function () {
        // get data overtime by ID
        Route::get('get_request_overtime_ByID', 'getRequestOvertimeByID'); 
        // get data request overtime
        Route::get('get_request_overtime', 'getRequestOvertime'); 
    });
// });

// Service LokaHR
Route::controller(Service_Cuti::class)->group(function () {
    // get data master cuti karyawan
    Route::get('get_master_cuti_karyawan', 'getListMasterCuti'); 
    // get data master cuti karyawan
    Route::get('get_request_cuti_karyawan', 'getListRequestCuti'); 
});

Route::controller(Service_Komplemen::class)->group(function () {
    // get data master list Price Komplemen
    Route::get('get_list_price_master_komplement', 'getListPriceMasterKomplemenByTanggal'); 

    // get data master komplemen karyawan
    Route::get('get_master_komplemen_karyawan', 'getListMasterKomplemen'); 
});

// CronJob
// update masa berlaku Cuti
Route::controller(CutiController::class)->group(function () {
    // get data master cuti
    Route::get('cron_updateMasaBerlakuCuti', 'disableCutiTrnValidatePeriodeExpied'); 
});




