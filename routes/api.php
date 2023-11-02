<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\KomplementController;
use App\Http\Controllers\HODController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(KaryawanController::class)->group(function () {

    // add data master cuti & kompliment karyawan from lokaHR
    Route::post('login', 'login'); 

    // add data master cuti & kompliment karyawan from lokaHR
    Route::get('insert_karyawan', 'insertKaryawan'); 
    // request cuti karyawan
    Route::post('request_cuti', 'requestCuti'); 
    // get data sisa cuti karyawan
    Route::get('get_cuti', 'getCutiKaryawan'); 
    // update akses approve
    Route::post('akses_approve', 'updateAksesApprove'); 
    // jika type approve custom
    // insert cutom approve
    Route::post('insert_custom_approve', 'insertCustomApprove'); 

});

Route::controller(HODController::class)->group(function () {
    // update action cuti HOD
    Route::post('update_action_cuti', 'actionRequestCuti'); 
    // get notif aprovel 
    Route::get('get_notif_approve', 'getNotifApprove'); 
});

Route::controller(CutiController::class)->group(function () {
    // get data master cuti
    Route::get('get_master_cuti', 'getMasterCuti'); 

    Route::post('updateMasterCutiKaryawan', 'updateMasterCutiKaryawan'); 
});

Route::controller(KomplementController::class)->group(function () {
    // get data komplement karyawan
    Route::get('get_komplemen', 'getKomplemen'); 
});

Route::middleware('auth:sanctum')->group( function () {
    // Cara 1
    // Route::resource('products', ProductController::class);

    // Cara 2
    // Route::controller(KaryawanController::class)->group(function () {
    //     Route::get('insert_karyawan', 'insertKaryawan'); 
    // });

});



