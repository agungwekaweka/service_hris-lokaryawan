<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use DateTime;

class HODController extends Controller
{
    // hod melakukan action terkait request cuti
    public function actionRequestCuti(Request $request) {  
        $idCutiTrn = $request->id_cuti_trn;
        $status = $request->status;
        $note = $request->note;
        $idKaryawanApprove = $request->id_karyawan_approve;
        try {
            $tglApprove = Carbon::now()->format('Y-m-d H:i:s');
            $req = $this->actionRequestCuti_($idCutiTrn,$status,$note,$idKaryawanApprove,$tglApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Action Cuti Successfuly',
                'data' => $req
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get notif approve 
    public function getNotifApprove(Request $request) {  
        $idkaryawan = $request->id_karyawan;
        try {
            $canApprove = $this->cekApprove($idkaryawan);
            if($canApprove->approve=='1')
            {
                $roleApprove = $this->roleApprove($idkaryawan);

                $c_cuti = new CutiController();
                // $c_komplement = new KomplementController();
                $reqCuti = $c_cuti->getNotifApprove($idkaryawan,$canApprove->type_approve,$roleApprove);
                // $reqKomplemen = $c_komplement->getNotifApprove($idkaryawan);

                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Notif Approve Successfuly',
                    'dataCuti' => $reqCuti,
                    // 'dataKomplemen' => $reqKomplemen
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'ID Karyawan tidak memiliki Access Approve',
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function cekApprove($idKaryawan_)
    {
        $idKaryawan = $idKaryawan_;
        try
        {
            $dt_ = DB::table('users')
            ->select('approve','type_approve')
            ->where('id_karyawan',$idKaryawan);
            if($dt_->exists())
            {
                $dt = $dt_->first();
            }
            else
            {
                $dt = false;
            }
            return $dt;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function roleApprove($idKaryawan_)
    {
        $idKaryawan=$idKaryawan_;
        try
        {
            $dt_ = DB::table('role_approve')
            ->select('id_karyawan','type_approve','id_approve')
            ->where('id_karyawan',$idKaryawan);
            if($dt_->exists())
            {
                $dt = $dt_->get();
            }
            else
            {
                $dt = false;
            }
            return $dt;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // -----------------------------------------------------------------------------
    private function actionRequestCuti_($idCutiTrn,$status,$note,$idKaryawanApprove,$tglApprove)
    {
        // update cuti_approve_history
        $c_cutiController = new CutiController();
        $result_['update_actionCuti'] = $c_cutiController->updateApproveHistory($idCutiTrn,$status,$note,$idKaryawanApprove,$tglApprove);
 
        // jika aprove ditolak langsung update master cuti TRN
        if($status=='2')
        {
            // update master cuti
            $c_cutiController = new CutiController();
            // status 0=pending, 1=approve, 2=reject
            $result_['update_actionCuti'] = $c_cutiController->updateActionCutiTrn($idCutiTrn,$status,$note);

             // get data cuti TRN by ID Trn
             $c_cutiController = new CutiController();
             $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);

             $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
             $name = $result_['get_dataCutiTRN']->name;
             $telephone = $result_['get_dataCutiTRN']->no_telephone;
             $idCuti = $result_['get_dataCutiTRN']->id_cuti;
             $tahun = $result_['get_dataCutiTRN']->tahun;
             $cuti = $result_['get_dataCutiTRN']->cuti;
             $tanggal =$result_['get_dataCutiTRN']->tanggal;
            //  $note = $result_['get_dataCutiTRN']->keterangan;
           
            // update data master cuti
            $c_cuti = new CutiController();
            $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$tahun,$idCuti);

            // sent whatsapp message
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCutiCancel($telephone,$idKaryawan,$cuti,$tanggal,$note);
        }
        else
        {
             // cek apakah masih ada history approve yg belum di setujui
             $c_cutiController =new CutiController();
             //  0=pending, 1=approve, 2=reject
             $result_['get_approve_history'] = $c_cutiController->getApproveHistory(0,$idCutiTrn);
             
             if($result_['get_approve_history']==null)
             {
               // selesai semua
               $c_cutiController = new CutiController();
               // status 0=pending, 1=approve, 2=reject
               $result_['update_actionCuti'] = $c_cutiController->updateActionCutiTrn($idCutiTrn,$status,$note);

               // update tanggal di aplikasi lokaHR (API)
               
             }
             else
             {
                // masih ada data yg pending
                // get data cuti TRN by ID Trn
                $c_cutiController = new CutiController();
                $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);
 
                $telephone = $result_['get_approve_history'][0]->telephone;
                $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
                $cuti = $result_['get_dataCutiTRN']->cuti;
                $tanggal =$result_['get_dataCutiTRN']->tanggal;
                $note = $result_['get_dataCutiTRN']->keterangan;

                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCuti($telephone,$idKaryawan,$cuti,$tanggal,$note);
             }
        }
       
        return $result_;
    }
}
