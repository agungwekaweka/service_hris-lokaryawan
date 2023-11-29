<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;
use App\Http\Controllers\ClassUploadImageClass;

class KaryawanController extends Controller
{
    // memasukkan semua data karyawan dari lokahr ke lokaryawan (master cuti & master komplemen)
    public function insertKaryawan() {        
        try {
            // get service API
            $typeService = 'get_all_karyawan';
            $json_data = new API_Guzzle();
            $data_jsonDecode = $json_data->getServiceLokaHR($typeService);
            // dd($data_jsonDecode);
            // cek status API
            if($data_jsonDecode->status=='success')
            {
                // karyawan Acvie
                $lstEmpActive = $data_jsonDecode->karyawanActive;
                foreach($lstEmpActive as $v)
                {
                  
                    $idDepartemen = $v->id_departemen;
                    $departemen = $v->departemen;
                    $idSubDepartemen = $v->id_departemen_sub;
                    $subDepartemen = $v->sub_departemen;
                    $idGrade = $v->id_grade;
                    $grade = $v->grade;
                    $username = $v->username;
                    $name = $v->name;
                    $noTelephone = $v->no_hp;
                    $password = $v->password;
                    $idAbsen = $v->id_absen;
                    $isDell = $v->status;
                    $doj = $v->doj;
                    $dob = $v->dob;

                    // cek tanggal join sudah memenuhi atau belum
                    $toDate = Carbon::now();
                    $fromDate = Carbon::parse($doj);
                    $months = $toDate->diffInMonths($fromDate);
                    
                    // if($months < 11)
                    // {
                    //     continue;
                    // }

                    // insert table user
                    $result_['karyawn_active'][0] = $this->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password, $isDell,$doj,$dob);
              
                    $tahun = Carbon::now()->format('Y');
                    // get tipe Cuti
                    $c_cuti = new CutiController();
                    $lstMstCuti = $c_cuti->getTypeMasterCuti();
                    foreach($lstMstCuti as $v)
                    {
                        $idCuti = $v->id_cuti;
                        $tipeCuti = $v->tipe_cuti;
                        $cuti = $v->cuti;
                        $jmlHari = $v->jml_hari;
                        $masaBerlaku = $v->masa_berlaku;
                        $tipeMasaBerlaku = $v->tipe_masa_berlaku;
                     
                        // insert Master Cuti
                       $result_['karyawn_active'][1]= $this->insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$doj);
                    }
                    
                    // get master komplement
                    $c_komplement = new KomplementController();
                    $lstMstKomplemen = $c_komplement->getTypeMasterKomplemen();
                    foreach($lstMstKomplemen as $v)
                    {
                        $idKomplement = $v->id_komplement;
                        $tipeKomplement = $v->komplement;
                        $qty = $v->qty;

                        // insert Master Komplemen
                        $result_['karyawn_active'][2] = $this->insertKomplement($idKomplement, $tahun,$idAbsen,$tipeKomplement,$qty);
                    }     
                }

                // karyawan Non Acvie
                $lstEmpNonActive = $data_jsonDecode->karyawanNonActive;
                foreach($lstEmpNonActive as $v)
                {
                    $username = $v->username;
                    $idAbsen = $v->id_absen;
                    $name = $v->name;
                    $password = $v->password;
                 
                    // disable Master Cuti
                    $result_['karyawn_nonActive'] = $this->disableCuti($idAbsen);
                }
                
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Insert Data Karyawan Successfuly',
                    'callback' => $result_
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'API Error'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // karyawan melakukan request cuti
    public function requestCuti(Request $request) {  
        $idKaryawan = $request->id_karyawan;
        $idCuti = $request->id_cuti;
        $tipeCuti = $request->tipe_cuti;
        $cuti = $request->cuti;
        $tanggal= $request->tanggal;
        $totalCuti = $request->total_cuti;
        $keterangan = $request->keterangan;
    
        try {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $idPeriode = '-';
            $tglPengajuan = Carbon::now()->format('Y-m-d h:m:s');
            // status 0 = pending HOD, 1= approve, 2=reject
            $status = '0';
          
            // $tanggal = json_encode($tanggal_);
            $note = '-';
           
            $sisaCuti =0;
            // cek sisa cuti in Master
            $c_cuti = new CutiController();
            $dt = $c_cuti->getCutiKaryawan($idKaryawan, $tahun,$idCuti);

            foreach($dt as $x)
            {
        
                $idMst = $x->id_cuti_mst;
                $sisaCutiDB = $x->sisa_cuti;
                break;
            }
            // $c_toolsDateClass = new ToolsDateCLass();
            // $isRangeDate = $c_toolsDateClass->checkDateRange($tanggal,$dateStart,$dateEnd);
         
            if($sisaCutiDB >= $totalCuti)
            {  
                // insert cuti
                $c_generateID = new GenerateIDController();
                $idTrn = $c_generateID->getIDCutiTrn($idCuti);

                $req = $this->insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status);
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Request Cuti Karyawan Successfuly',
                    'callback' => $req
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Sisa Cuti Anda sudah Tidak Mencukupi, Cuti Tersisa : '.$sisaCutiDB,
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    
    // get sisa cuti karyawan
    public function getCutiKaryawan(Request $request) {  
        $idKaryawan = $request->id_karyawan;
        $tahun = $request->tahun;
        $idCuti = $request->id_cuti;
        try {
            $c_cuti = new CutiController();
            $req = $c_cuti->getCutiKaryawan($idKaryawan,$tahun,$idCuti);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Cuti Karyawan Successfuly',
                'data' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // update akses approve karyawan 1=can aprove, 0=can't
    // type approve 0=departemen, 1=sub departemen, 9=custom
    public function updateAksesApprove(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $approve = $request->approve;
        $typeApprove = $request->type_approve;
        try {
            $req = $this->updateAksesRoleApprove($idKaryawan,$approve,$typeApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Akses Aprove Karyawan Successfuly',
                'callback' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // if type aprove = 9
    // type approve (0=by departemen, 1=by sub departemen,2=by id karyawan)
    // id approve (id departemen, id sub departemen, id karyawan)
    public function insertCustomApprove(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $typeApprove = $request->type_approve;
        $idApprove = $request->id_approve;
        try {
            $req = $this->insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Menambahkan Custom Akses Aprove Karyawan Successfuly',
                'data' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // -----------------------------------------------------------------------------
    private function insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password, $isDell,$doj,$dob)
    {
        $c_user = new UsersController();
        $result_['insert_userDB'] = $c_user->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password,$isDell,$doj,$dob);
        return $result_;
    }

    public function insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku)
    {
        // generate ID
        $c_generateID = new GenerateIDController();
        $idMst = $c_generateID->getIdCutiMst($tipeCuti);

        $c_cuti = new CutiController();
        $result_['insert_masterCutiDB'] = $c_cuti->insertCutiMst($idMst,$idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku);
        return $result_;
    }

    private function insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status)
    {  
        // insert data
        $c_cuti = new CutiController();
        $result_['insert_cutiDB'] = $c_cuti->insetCutiTrn($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status);

        // update data master cuti
        $c_cuti = new CutiController();
        $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$idMst,$idTrn,$idCuti);
     
        // get list approve up Level
        $c_grade = new GradeController();
        $lstApproveGradeUp = $c_grade->getGradeLvUp($idKaryawan);
     
        if($lstApproveGradeUp!=null)
        {
            foreach($lstApproveGradeUp as $v)
            {
                // 0=pending, 1=approve, 2=reject
                $status = '0';
                $telephone = $v->no_telephone;
                $idKaryawanApprove = $v->id_karyawan;
                $tglApprove = '0000-00-00';
                $note = '-';
                //insert list Approve up Level
                $c_cuti = new CutiController();
                $result_['insert_approveHistory'] = $c_cuti->insertCutiApproveHistory($idTrn, $status, $telephone, $idKaryawanApprove, $tglApprove, $note);
            }

            // sent whatsapp message
            $c_sentWaController = new SentWhatsappController();
            $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCuti($idTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
        }
        else
        {
            $result_['insert_approveHistory'] = 'ID Karyawan : '. $idKaryawan.' Tidak memiliki Atasan untuk Approve';
        }
        
        return $result_;
    }

    private function updateRequestCuti($id,$status,$note,$reff,$tglApprove)
    {
        $c_cuti = new CutiController();
        $result_ = $c_cuti->updateActionCutiTrn($id,$status,$note,$reff,$tglApprove);
        return $result_;
    }

    private function disableCuti($idAbsen)
    {
        $c_cuti = new CutiController();
        $result_['disable_MasterCutiDB'] = $c_cuti->disableCutiMst($idAbsen);
        return $result_;
    }

    private function insertKomplement($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement)
    {
        $c_komplement = new KomplementController();
        $result_['insert_KomplementDB'] = $c_komplement->insertKomplemenMst($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement);
        return $result_;
    }
    
    private function disableKomplement($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement)
    {
        $c_komplement = new KomplementController();
        $result_ = $c_komplement->insertKomplemenMst($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement);
        return $result_;
    }

    private function updateAksesRoleApprove($idKaryawan,$approve,$typeApprove)
    {
        $c_users = new UsersController();
        $result_['update_aksesApprove'] = $c_users->updateAksesApprove($idKaryawan,$approve,$typeApprove);
        return $result_;
    }
    private function insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove)
    {
        $c_roleApprove = new RoleApproveController();
        $result_['insert_customRoleApprove'] = $c_roleApprove->insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove);
        return $result_;
    }
    // --------------------------------------------------------------------------------------------

    // karyawan mengajukan Cuti Khusus
    public function requestCutiKhusus(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $idCuti = $request->id_cuti;
        $tipeCuti = $request->tipe_cuti;
        $cuti = $request->cuti;
        $tanggal = $request->tanggal;
        $totalCuti = $request->total_cuti;
        $keterangan = $request->keterangan;
        $lampiranFile = $request->lampiran_file;

        try
        {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $idPeriode = '-';
            $tglPengajuan = Carbon::now()->format('Y-m-d h:m:s');
            // status 0 = pending HOD, 1= approve, 2=reject
            $status = '0';

            // get tipe Cuti
            $c_cuti = new CutiController();
            $dataCuti = $c_cuti->getTypeMasterCutiByID($idCuti);

            $masaBerlaku = $dataCuti->masa_berlaku;
            $jmlHari = 0;
            $tipeMasaBerlaku = $dataCuti->tipe_masa_berlaku;

            $tanggalDecode = json_decode($tanggal);
            // get tanggal first json Decode
            $tanggalAwal = $tanggalDecode[0];
         
            // insert cuti mst
            $result_['insert_cutiMST'] =  $this->insertCuti($idCuti,$tahun,$idKaryawan,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$tanggalAwal);
          
            $sisaCuti =0;
            // cek sisa cuti
            $c_cuti = new CutiController();
            $dt = $c_cuti->getCutiKaryawan($idKaryawan,$tahun,$idCuti);
            foreach($dt as $x)
            {
                $idMst = $x->id_cuti_mst;
                $sisaCuti = $x->sisa_cuti;
                break;
            }
            
            $keterangan = 'Cuti Khusus';
          
            if($sisaCuti > 0)
            {  
                // insert cuti
                // generate ID
                $c_generateID = new GenerateIDController();
                $idTrn = $c_generateID->getIDCutiTrn($idCuti);
                $result_['insert_cutiTRN'] = $this->insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$sisaCuti,$keterangan,$tglPengajuan,$status);
           
                // insert Image
                if($lampiranFile!='') {
                    // call class upload Image
                    $c_uploadImage = new ClassUploadImageClass();
                    $urlPathImgLampiran = $c_uploadImage->processImageLampiran($request->file('lampiran_file'),$idKaryawan,$idMst,$idTrn);
         
                    // insert 
                } 
            }
            else
            {
                $result_['insert_cutiTRN'] ='Sisa Cuti Anda sudah Habis';
            }
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Request Cuti Karyawan Successfuly',
                'callback' => $result_
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 
}
