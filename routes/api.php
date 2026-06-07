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

use App\Http\Controllers\Service_RoleApprove;
use App\Http\Controllers\Service_Cuti;
use App\Http\Controllers\Service_Komplemen;
use App\Http\Controllers\Service_Overtime;
use App\Http\Controllers\Service_Users;
use App\Http\Controllers\Service_KomplementGuest;
use App\Http\Controllers\Service_Izin;
use App\Http\Controllers\Serivce_Sppd;
use App\Http\Controllers\SentWhatsappController;


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

// * jika middleware aktif memerlukan session untuk mengakses route controller
// Route::middleware([CheckStatus::class])->group(function(){
    Route::controller(KaryawanController::class)->group(function () {
        // CUTI
        // add data master cuti & kompliment karyawan from lokaHR
        Route::get('insert_karyawan', 'insertKaryawan'); 
        
        // request cuti karyawan
        Route::post('request_cuti', 'requestCuti'); 
        Route::post('request_cuti_khusus', 'requestCutiKhusus'); 
        Route::post('update_request_cuti_khusus', 'updateRequestCutiKhusus'); 

        // get data sisa cuti karyawan
        Route::get('get_cuti', 'getCutiKaryawan'); 
        // update akses approve
        Route::post('akses_approve', 'updateAksesApprove'); 
        // jika type approve custom
        // insert cutom approve
        Route::post('insert_custom_approve', 'insertCustomApprove'); 
        Route::post('delete_custom_approve', 'deleteCustomApprove'); 

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
        // get list karyawan overtime 
        Route::get('get_list_karyawan_overtime', 'getListKaryawanOvertime'); 
        // request Overtime Karyawan from HOD
        Route::post('request_list_overtime', 'requestListOvertime'); 
        // get 
        Route::get('get_request_overtime_grouping', 'getRequestOvertimeGrouping'); 
        Route::get('get_request_overtime_grouping_karyawan', 'getRequestOvertimeGroupingKaryawan'); 
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
    // get data master cuti karyawan
    Route::get('get_request_cuti_karyawan_approve_not_available', 'getListRequestCutiApproveNotAvailable'); 
    // import Data Master Cuti
    Route::post('import_data_master_cuti', 'importDataMasterCuti');

    // CRON
    // Update Master Cuti Tahunan == Cron update:cutiTahunan
    Route::get('update_master_cutiTahunan', 'updateMasterCutiTahunan'); 
});

Route::controller(Service_Komplemen::class)->group(function () {
    // get data komplement Coming Soon 
    Route::get('get_request_komplement_comingSoon', 'getRequestKomplementComingSoon'); 
    
    // get data master list Price Komplemen
    Route::get('get_list_price_master_komplement', 'getListPriceMasterKomplemenByTanggal'); 

    // get data master komplemen karyawan
    Route::get('get_master_komplemen_karyawan', 'getListMasterKomplemen'); 

    // get data komplement by ID
    Route::get('get_request_komplement_ByID', 'getRequestKomplemen'); 

    // get data request komplement
    Route::get('get_request_komplement_karyawan', 'getRequestKomplemen'); 

    // To Reservation Ticket
    Route::post('update_reservation_ticket', 'updateReservationTicket');
    
    // import Data Master Komplement
    Route::post('import_data_master_komplement', 'importDataMasterKomplement');

    // CRON
    // Update Master Komplement == Cron update:komplement
    Route::get('update_master_komplement', 'updateMasterKomplement'); 
});

Route::controller(Service_RoleApprove::class)->group(function () {
    Route::get('cek_role_approve_karyawan','cekRoleApproveKaryawan');
    Route::get('get_list_role_approve','getListRoleApprove');

    Route::post('create_role_approve','createRoleApprove');
    Route::post('create_role_approve_all','createRoleApproveAll');
    
    Route::post('update_pic_approve','updatePicApprove');

    // import update Role Approve By Departemen / sub
    Route::post('import_update_role_approve', 'importUpdateRoleApprove');
});

Route::controller(Service_Overtime::class)->group(function () {
    Route::get('get_request_overtime_karyawan','getRequestOvertime');
    
     // export
    Route::get('export_request_overtime_karyawan', 'exportRequestOvertime');
});


Route::controller(Service_Users::class)->group(function () {
  Route::get('export_users', 'exportUsers');
});

Route::controller(Service_KomplementGuest::class)->group(function () {
    // get Users Guest
    Route::get('get_users_guest', 'getUsersGuest');
    Route::post('update_users_guest', 'updateUsersGuest');

    Route::post('import_users_guest', 'importUsersGuest');
    
    // request komplement users guest 
    Route::post('request_komplement_guest', 'requestKomplementUserGuest');

    // get Komplement Guest
    Route::get('get_komplement_guest', 'getKomplementUserGuest');
});

Route::controller(Service_Izin::class)->group(function () {
    // get Izin
    Route::get('get_izin', 'getIzin');

    // get total izin 
    Route::get('get_count_permission', 'getCountPermission');

    // created izin
    Route::post('created_izin','createdIzin');

    // updated izin
    Route::post('updated_izin','updatedIzin');
});

Route::controller(Serivce_Sppd::class)->group(function () {
    // get Sppd
    Route::get('get_sppd', 'getSppd');

    // created Sppd
    Route::post('created_sppd','createdSppd');

    // updated Sppd
    Route::post('updated_sppd','updatedSppd');
});

// CronJob
// update masa berlaku Cuti
Route::controller(CutiController::class)->group(function () {
    // get data master cuti
    Route::get('cron_updateMasaBerlakuCuti', 'disableCutiTrnValidatePeriodeExpied'); 
});

Route::controller(SentWhatsappController::class)->group(function () {
    Route::post('blast_all_karyawan', 'sentBlastWAAllKaryawan');
});




