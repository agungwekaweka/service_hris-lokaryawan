<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\Izin\IzinModel;

use Carbon\Carbon;
use DateTime;

class HODController extends Controller
{
    // hod melakukan action terkait request cuti
    public function actionRequestCuti(Request $request) 
    {  
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
    public function actionRequestOvertime(Request $request) 
    {  
        $idOvertime = $request->id_overtime;
        $status = $request->status;
        $note = $request->note;
        $idKaryawanApprove = $request->id_karyawan_approve;
        $jamLembur = $request->jam_lembur;
        try {
            $tglApprove = Carbon::now()->format('Y-m-d H:i:s');
            $req = $this->actionRequestOvertime_($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove,$jamLembur);
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
    public function getNotifApprove(Request $request) 
    {  
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
               
                $requestModel=[];
                $requestModel['id_karyawan'] = $idkaryawan;
                $c_ModelIzin = New IzinModel();
                $reqIzin = $c_ModelIzin->getNotifApprove($requestModel);

                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Notif Approve Successfuly',
                    'dataCuti' => $reqCuti,
                    'dataLembur' => $reqOvertime,
                    'dataIzin' => $reqIzin
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

    // hod melakukan request overtime terhadap list karyawan
    public function requestListOvertime(Request $request)
    {
        $idRequestOvertime ='';
        if (isset($request['id_request_overtime'])) {
            $idRequestOvertime = $request['id_request_overtime'];
        }
        $tglLembur = $request->tgl_lembur;
        $jamMulai = $request->jam_mulai;
        $jamAkhir = $request->jam_akhir;
        $jamLembur = $request->jam_lembur;
        $keterangan = $request->keterangan;
        $pic = $request->pic;
        $listIdKaryawan = $request->list_id_karyawan;
        try
        {
            DB::beginTransaction();
            
                $tahun = Carbon::now()->format('Y');
                $tglPengajuan = Carbon::now()->format('Y-m-d H:i:s');
                // 0=pending; 1=approve; 2=reject
                $status = '0';
                $listIdKaryawan = json_decode($listIdKaryawan);

                // request baru
                if($idRequestOvertime=='')
                {
                    $c_generateID = new GenerateIDController();
                    $idRequestOvertime = $c_generateID->getIDRequestOvertimeMst($pic);
                }    
            
                foreach($listIdKaryawan as $v)
                {
                    $idKaryawan = $v->id_karyawan;

                    $c_generateID = new GenerateIDController();
                    $idOvertime = $c_generateID->getIDOvertimeMst($idKaryawan);

                    // insert ke table master overtime
                    $request=[];
                    $request['id_request_overtime'] = $idRequestOvertime;
                    $request['id_overtime'] = $idOvertime;
                    $request['tgl_pengajuan'] = $tglPengajuan;
                    $request['id_karyawan'] = $idKaryawan;
                    $request['tgl_lembur'] = $tglLembur;
                    $request['jam_lembur'] = $jamLembur;
                    $request['jam_mulai'] = $jamMulai;
                    $request['jam_akhir'] = $jamAkhir;
                    $request['keterangan'] = $keterangan;
                    $request['status'] = $status;
                    $request['pic'] = $pic;

                    $result_ = $this->insertReqeustOvertime($request);
                }

            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Notif Approve Successfuly',
                'result' =>$result_
            ]);
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get list karyawan yg akan ditambahkan ke Ovetime
    public function getListKaryawanOvertime(Request $request)
    {
        $isSubDepartemen ='';
        $idKaryawan='';
        // declare variable
        if (isset($request['is_sub_departemen'])) {
            $isSubDepartemen = $request['is_sub_departemen'];
        }
        $idKaryawan = $request->id_karyawan;
      
        try
        {
            $request['is_sub_departemen']=$isSubDepartemen;
            $request['id_karyawan']=$idKaryawan;

            $overtimeController = new OvertimeController();
            $result = $overtimeController->getListKaryawanOvertime($request);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get list request overtime grouping
    public function getRequestOvertimeGrouping(Request $request)
    {
        $pic = $request->pic;
        try
        {
            // get data overtime by ID Karyawan
            $request=[];
            $request['pic'] = $pic;
            $c_overtimeController = new OvertimeController();
            $result_['get_dataOvertimeByPIC'] = $c_overtimeController->getOvertimeRequestGroup($request);
            if($result_ !=null)
            {
                $result = response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Lembur Request Successfuly',
                    'data' => $result_
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Data Cuti Tidak Ditemukan',
                ]);
            }
            return $result;

        } catch (\Exception $ex) {
            return $ex;
        }
    }
    
    // get list request overtime grouping Karyawan
    public function getRequestOvertimeGroupingKaryawan(Request $request)
    {
            $idRequestOvertime = $request->id_request_overtime;
            try
            {
                // get data overtime by ID Karyawan
                $request=[];
                $request['id_request_overtime'] = $idRequestOvertime;
                $c_overtimeController = new OvertimeController();
                $result_['get_dataOvertime'] = $c_overtimeController->getOvertimeRequestGroup($request);
                $c_overtimeController = new OvertimeController();
                $result_['get_dataOvertimeKaryawan'] = $c_overtimeController->getOvertimeRequestGroupKaryawan($request);
                if($result_ !=null)
                {
                    $result = response()->json([
                        'status' => 'success',
                        'message' => 'Get Data Lembur Request Successfuly',
                        'data' => $result_
                    ]);
                }
                else
                {
                    $result=response()->json([
                        'status' => 'failed',
                        'message' => 'Data Cuti Tidak Ditemukan',
                    ]);
                }
                return $result;
    
            } catch (\Exception $ex) {
                return $ex;
            }
    }

    // ----------------------------------------------------------------------------------------------------------------
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

    private function actionRequestOvertime_($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove,$jamLembur_)
    {
        // get data overtime by ID Trn
        $request=[];
        $request['id_overtime'] = $idOvertime;
        $request['id_karyawan'] = '';
        $c_overtimeController = new OvertimeController();
        $result_['get_dataOvertimeByID'] = $c_overtimeController->getOvertimeRequest($request);
      
        $telephone = $result_['get_dataOvertimeByID'][0]->no_telephone;
        $nik = $result_['get_dataOvertimeByID'][0]->nik;
        $idKaryawan = $result_['get_dataOvertimeByID'][0]->id_karyawan;
        $tanggalLembur =  $result_['get_dataOvertimeByID'][0]->tgl_lembur;
        $jmLembur = $result_['get_dataOvertimeByID'][0]->jam_lembur;
        $keterangan = $result_['get_dataOvertimeByID'][0]->keterangan;

        // update cuti_approve_history
        $c_overtimeController = new OvertimeController();
        $result_['update_actionCuti'] = $c_overtimeController->updateApproveHistory($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove);

        // jika aprove ditolak langsung update master Overtime
        if($status=='2')
        {
            // update master overtime
            $request=[];
            $request['id_overtime'] = $idOvertime;
            $request['status'] = $status;
            $request['note'] = $note;
            $request['keterangan'] = $keterangan;
            $request['jam_lembur'] =$jmLembur;

            $c_overtimeController = new OvertimeController();
            // status 0=pending, 1=approve, 2=reject
            $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($request);

            // sent whatsapp message
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappOvertimeDiTolak($idOvertime,$telephone,$idKaryawan,$tanggalLembur,$jmLembur,$note);
        }
        else
        {
            // convert Jam lembur
            $c_toolsConvert = new ToolsConvert();
            $jamLembur = $c_toolsConvert->convertInputTypeDecimalOrInterger($jmLembur);

            $request = [];
            $request['id_overtime'] = $idOvertime;
            $request['status'] = $status;
            $request['note'] = $note;
            $request['telephone'] = $telephone;
            $request['id_karyawan'] = $idKaryawan;
            $request['tanggal_lembur'] = $tanggalLembur;
            $request['jam_lembur'] = $jamLembur;
            $request['keterangan'] = $keterangan;
            
            // cek apakah masih ada history approve yg belum di setujui
            $c_overtimeController =new OvertimeController();
            //  0=pending, 1=approve, 2=reject
            $result_['get_approve_history'] = $c_overtimeController->getApproveHistory(0,$idOvertime);
       
            if($result_['get_approve_history']==null)
            {
                if($jamLembur!=$jamLembur_)
                {
                    $request['jam_lembur'] = $jamLembur_;
                }
                $result_ = $this->OvertimeApproveAll($request);
            }
            else
            {
                $request['telephone'] = $result_['get_approve_history'][0]->telephone;
                $request['jam_lembur'] = $jamLembur_;
                $result_ = $this->OvertimeApproveOutstanding($request);
                
                // cek apakah masih ada history approve yg belum di setujui
                $c_overtimeController =new OvertimeController();
                //  0=pending, 1=approve, 2=reject
                $result_['get_approve_history'] = $c_overtimeController->getApproveHistory(0,$idOvertime);
           
                if($result_['get_approve_history']==null)
                {
                    $result_ = $this->OvertimeApproveAll($request);
                }
            }
        }
        return $result_;
    }

    private function OvertimeApproveAll($request)
    {
        // data sudah fix di setujui semua HOD
        // update master overtime
        $request['status'] = '1';
    
        $c_overtimeController = new OvertimeController();
        // status 0=pending, 1=approve, 2=reject
        $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($request);
                
        // sent whatsapp message
        $c_sentWaController = new SentWhatsappController();
        $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappOvertimeDiterima($request);
        return $result_;
    }

    private function OvertimeApproveOutstanding($request)
    {
        $request['status'] = '0'; // status 0=pending

        // update master overtime jika hod mengubah jam Lembur
        $c_overtimeController = new OvertimeController();
        $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($request);
        
        // sent whatsapp message
        $c_sentWaController = new SentWhatsappController();
        $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveOvertime($request);
        return $result_;
    }

    private function insertReqeustOvertime($request)
    {
        try
        {
            $c_overtimeController = new OvertimeController();
            $result_['insert_OvertimeMst'] = $c_overtimeController->insertOvertimeMst($request);

            // get data overtime by ID Trn
            $requestOvertime=[];
            $requestOvertime['id_overtime'] = $request['id_overtime'];
            $requestOvertime['id_karyawan'] = '';

            $c_overtimeController = new OvertimeController();
            $result_['get_dataOvertimeByID'] = $c_overtimeController->getOvertimeRequest($requestOvertime);   
            $telephone = $result_['get_dataOvertimeByID'][0]->no_telephone;
     
            // sent whatsapp message
            $request['telephone'] = $telephone;
         
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveOvertimeKaryawan($request);
      
            $idOvertime = $request['id_overtime'];
            $status = $request['status'];
            $telephone = $request['telephone'];
            $idKaryawanApprove = $request['id_karyawan'];
            $tglApprove = '0000-00-00';
            $note = '-';
       
            // insert list Approve up Level
            $c_overtimeController = new OvertimeController();
            $result_['insert_OvertimeHistory'] = $c_overtimeController->insertOvertimeHistory($idOvertime,$status,$telephone,$idKaryawanApprove,$tglApprove,$note);

            return $result_;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

}
