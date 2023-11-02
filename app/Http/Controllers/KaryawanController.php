<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use DateTime;

class KaryawanController extends Controller
{
    // public function login(Request $request) {
    //     header('Access-Control-Allow-Origin: *');
    //     header('Content-Type: application/json');
    //     $username = $request->username;
    //     $password = $request->password;
       
    //     try {
    //         $dtUser = DB::table('users')
    //             ->select('password')
    //             ->where('id_karyawan','=',$username)
    //             ->orderBy('id','desc');

    //         if ($dtUser->exists()) {
    //             $user = $dtUser->first();
    //             if (Crypt::decryptString($user->password) == $password) {
    //                 $user = DB::table('users')
    //                 ->select('id_karyawan','name')
    //                 ->where('id_karyawan','=',$username)
    //                 ->orderBy('id','desc')
    //                 ->first();

    //                 $result=response()->json([
    //                     'status' => 'success',
    //                     'message' => 'Get Data User Successfuly',
    //                     'user' => $user
    //                 ]);
    //             } else {
    //                 $result = 'Password salah.';
    //             }
    //         } else {
    //             $result = 'ID Karyawan tidak terdaftar';
    //         }
    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    // memasukkan semua data karyawan dari lokahr ke lokaryawan (master cuti & master komplemen)
    public function insertKaryawan(Request $request) {        
        try {
            // get service API
            $typeService = 'get_all_karyawan';
            $json_data = new API_Guzzle();
            $data_jsonDecode = $json_data->getServiceLokaHR($typeService);
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
                    $grade = $v->grade;
                    $username = $v->username;
                    $name = $v->name;
                    $password = $v->password;
                    $idAbsen = $v->id_absen;
                    $isDell = $v->status;
                    
                    // insert table user
                    $x = $this->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen, $grade, $name, $idAbsen, $username, $password, $isDell);
              
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
                     
                        // insert Master Cuti
                        $this->insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$jmlHari,$masaBerlaku);
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
                        $this->insertKomplement($idKomplement, $tahun,$idAbsen,$tipeKomplement,$qty);
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
                    $this->disableCuti($idAbsen);
                }
                
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Insert Data Karyawan Successfuly'
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
        $tipeCuti = $request->tipeCuti;
        $cuti = $request->cuti;
        $tanggal = $request->tanggal;
        $totalCuti = $request->total_cuti;
        $keterangan = $request->keterangan;

        try {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $idPeriode = '-';
            $tglPengajuan = Carbon::now()->format('Y-m-d');
            // status 0 = pending HOD, 1= approve, 2=reject
            $status = '0';
            $note = '-';
            $reff1 = '-';
            $reff2 = '-';
            $reff3 = '-';

            $sisaCuti =0;
            // cek sisa cuti
            $c_cuti = new CutiController();
            $dt = $c_cuti->getCutiKaryawan($idKaryawan, $tahun,$idCuti);
            $sisaCuti = $dt[0]->sisa_cuti;
            if($sisaCuti > 0)
            {
                // insert cuti
                $req = $this->insertCutiTRN($idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status,$note,$reff1,$reff2,$reff3);
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
                    'message' => 'Sisa Cuti Anda sudah Habis',
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
                'data' => $req
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
    private function insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen, $grade, $name, $idAbsen, $username, $password, $isDell)
    {
        $c_user = new UsersController();
        $result_ = $c_user->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen, $grade, $name, $idAbsen, $username, $password, $isDell);
        return $result_;
    }

    private function insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$masaBerlaku)
    {
        $c_cuti = new CutiController();
        $result_ = $c_cuti->insertCutiMst($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$masaBerlaku);
        return $result_;
    }

    private function insertCutiTRN($idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status,$note,$reff1,$reff2,$reff3)
    {
        $c_cuti = new CutiController();
        $result_ = $c_cuti->insetCutiTrn($idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status,$note,$reff1,$reff2,$reff3);
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
        $result_ = $c_cuti->disableCutiMst($idAbsen);
        return $result_;
    }

    private function insertKomplement($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement)
    {
        $c_komplement = new KomplementController();
        $result_ = $c_komplement->insertKomplemenMst($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$sisaKomplement);
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
        $result_ = $c_users->updateAksesApprove($idKaryawan,$approve,$typeApprove);
        return $result_;
    }
    private function insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove)
    {
        $c_roleApprove = new RoleApproveController();
        $result_ = $c_roleApprove->insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove);
        return $result_;
    }
    // --------------------------------------------------------------------------------------------


}
