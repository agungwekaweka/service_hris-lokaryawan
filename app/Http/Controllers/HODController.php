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

    // hod melakukan action terkait request overtime
    public function actionRequestOvertime(Request $request) {  
        $idOvertime = $request->id_overtime;
        $status = $request->status;
        $note = $request->note;
        $idKaryawanApprove = $request->id_karyawan_approve;
        try {
            $tglApprove = Carbon::now()->format('Y-m-d H:i:s');
            $req = $this->actionRequestOvertime_($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove);
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
                $reqCuti = $c_cuti->getNotifApprove($idkaryawan,$canApprove->type_approve,$roleApprove);
            
                $c_overtimeController = new OvertimeController();
                $reqOvertime = $c_overtimeController->getNotifApprove($idkaryawan,$canApprove->type_approve,$roleApprove);

                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Notif Approve Successfuly',
                    'dataCuti' => $reqCuti,
                    'dataLembur' => $reqOvertime
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

    // ---------------------------------------------------------------------
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
 
        // get data cuti TRN by ID Trn
        $c_cutiController = new CutiController();
        $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);
     
        $idMst = $result_['get_dataCutiTRN']->id_cuti_mst;
        $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
        $nip = $result_['get_dataCutiTRN']->nik;
        $name = $result_['get_dataCutiTRN']->name;
        $telephone = $result_['get_dataCutiTRN']->no_telephone;
        $idCuti = $result_['get_dataCutiTRN']->id_cuti;
        $tahun = $result_['get_dataCutiTRN']->tahun;
        $cuti = $result_['get_dataCutiTRN']->cuti;
        $tanggal =$result_['get_dataCutiTRN']->tanggal;
        $keterangan = $result_['get_dataCutiTRN']->keterangan;

        // jika aprove ditolak langsung update master cuti TRN
        if($status=='2')
        {
            // update master cuti
            $c_cutiController = new CutiController();
            // status 0=pending, 1=approve, 2=reject
            $result_['update_actionCuti'] = $c_cutiController->updateActionCutiTrn($idCutiTrn,$status,$note);
           
            // update data master cuti
            $c_cuti = new CutiController();
            $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$idMst,$idCutiTrn,$idCuti);

            // sent whatsapp message
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCutiCancel($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
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
               $c_apiGuzzle = new API_Guzzle();
               $var = 'update_jadwal_karyawan';
               // cuti tahunan = 6, cuti khusus = 66
               $valueKehadiran = '6';
               $result_['update_lokaHR_jadwal_karyawan'] = $c_apiGuzzle->postServiceLokaHR($var,$nip,$tanggal,$keterangan,$valueKehadiran);
              
                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCutiDiterima($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
                
            }
             else
             {
                // masih ada data yg pending
                $telephone = $result_['get_approve_history'][0]->telephone;

                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCuti($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$keterangan);
             }
        }
       
        return $result_;
    }

    private function actionRequestOvertime_($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove)
    {
        // update cuti_approve_history
        $c_overtimeController = new OvertimeController();
        $result_['update_actionCuti'] = $c_overtimeController->updateApproveHistory($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove);

        // get data overtime by ID Trn
        $c_overtimeController = new OvertimeController();
        $result_['get_dataOvertimeByID'] = $c_overtimeController->getOvertimeRequest($idOvertime,'');
   
        $telephone = $result_['get_dataOvertimeByID'][0]->no_telephone;
        $nik = $result_['get_dataOvertimeByID'][0]->nik;
        $idKaryawan = $result_['get_dataOvertimeByID'][0]->id_karyawan;
        $tanggalLembur =  $result_['get_dataOvertimeByID'][0]->tgl_lembur;
        $jamLembur_ = $result_['get_dataOvertimeByID'][0]->jam_lembur;
        $keterangan = $result_['get_dataOvertimeByID'][0]->keterangan;

        // convert Jam lembur
        $c_toolsConvert = new ToolsConvert();
        $jamLembur = $c_toolsConvert->convertInputTypeDecimalOrInterger($jamLembur_);

        // jika aprove ditolak langsung update master Overtime
        if($status=='2')
        {
            // update master overtime
            $c_overtimeController = new OvertimeController();
            // status 0=pending, 1=approve, 2=reject
            $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($idOvertime,$status,$note);

            // sent whatsapp message
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappOvertimeDiTolak($idOvertime,$telephone,$idKaryawan,$tanggalLembur,$jamLembur,$note);
        }
        else
        {
             // cek apakah masih ada history approve yg belum di setujui
             $c_overtimeController =new OvertimeController();
             //  0=pending, 1=approve, 2=reject
             $result_['get_approve_history'] = $c_overtimeController->getApproveHistory(0,$idOvertime);
           
             if($result_['get_approve_history']==null)
             {
               // update master overtime
                $c_overtimeController = new OvertimeController();
                // status 0=pending, 1=approve, 2=reject
                $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($idOvertime,$status,$note);

               // insert tanggal overtime di aplikasi Payroll
            //    $c_apiGuzzle = new API_Guzzle();
            //    $var = 'update_jadwal_karyawan';
            //    // cuti tahunan = 6, cuti khusus = 66
            //    $valueKehadiran = '6';
            //    $result_['update_lokaHR_jadwal_karyawan'] = $c_apiGuzzle->postServiceLokaHR($var,$nip,$tanggal,$keterangan,$valueKehadiran);
              
                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappOvertimeDiterima($idOvertime,$telephone,$idKaryawan,$tanggalLembur,$jamLembur,$keterangan);
                
            }
             else
             {
                // masih ada data yg pending
                $telephone = $result_['get_approve_history'][0]->telephone;
                
                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveOvertime($idOvertime,$telephone,$idKaryawan,$tanggalLembur,$jamLembur,$keterangan);
             }
        }
       
        return $result_;
    }
}
