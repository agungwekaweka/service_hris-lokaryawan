<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CutiMst;
use App\Models\CutiTrn;
use App\Models\CutiApproveHistory;
use Carbon\Carbon;
use DateTime;


class CutiController extends Controller
{
    // insert
    // menambahkan data cuti karyawan ke table master
    public function insertCutiMst($idMst_,$idCuti_, $tahun_,$idKaryawan_,$tipeCuti_,$cuti_,$jml_,$masaBerlaku_,$tglBerlaku_)
    {
        // declare variable
        $idMst = $idMst_;
        $idCuti = $idCuti_;
        $tahun = $tahun_;
        $idKaryawan = $idKaryawan_;
        $tipeCuti = $tipeCuti_;
        $cuti = $cuti_;
        $jml = $jml_;
        $masaBerlaku = $masaBerlaku_;
        $tglBerlaku = $tglBerlaku_;

        try {
            // cek data
            $dt = DB::table('cuti_mst')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('is_dell','1')
            ->where('id_cuti',$idCuti);
            if($dt->exists())
            {
                // data sudah ada
                return 'data sudah ada';
            }
            else
            {
                    // menambahkan toleransi masa berlaku cuti
                    // $toleransiIntervalCuti = $this->getToleransiMasterCuti();
                    $carbonFormatDate = new Carbon($tglBerlaku.' 00:00:00');
                    $tglExpied = $carbonFormatDate->addMonths($masaBerlaku);

                    $data = new CutiMst();
                    $data->id_cuti_mst = $idMst;
                    $data->id_cuti = $idCuti;
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_cuti = $tipeCuti; 
                    $data->cuti = $cuti; 
                    $data->jml_cuti = $jml; 
                    $data->sisa_cuti = $jml; 
                    $data->masa_berlaku = $masaBerlaku;
                    $data->date_start = $tglBerlaku;
                    $data->date_end = $tglExpied;
                    $data->is_dell = '1';
                    $data->save();
            }

            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // menambahkan data request cuti
    public function insetCutiTrn($idCutiMst_,$idCutiTrn_,$idCuti_, $idPeriode_,$tahun_,$idKaryawan_,$tipeCuti_,$cuti_,$tanggal_,$totalCuti_,$keterangan_,$tglPengajuan_,$status_)
    {
        // declare variable
        $idCutiMst = $idCutiMst_;
        $idCutiTrn = $idCutiTrn_;
        $idCuti = $idCuti_;
        $idPeriode = $idPeriode_;
        $tahun = $tahun_;
        $idKaryawan = $idKaryawan_;
        $tipeCuti = $tipeCuti_;
        $cuti = $cuti_;
        $tanggal = $tanggal_;
        $totalCuti = $totalCuti_;
        $keterangan = $keterangan_;
        $tglPengajuan = $tglPengajuan_;
        $status = $status_;
     
        try {
               // cek data
               $dt = DB::table('cuti_trn')
               ->select('id')
               ->where('id_karyawan',$idKaryawan)
               ->where('tahun',$tahun)
               ->where('id_cuti',$idCuti)
               ->where('tanggal',$tanggal);
               if($dt->exists())
               {
                   // data sudah ada
                   return 'data sudah ada';
               }
               else
               {
                    $data = new CutiTrn();
                    $data->id_cuti_mst = $idCutiMst;
                    $data->id_cuti_trn = $idCutiTrn;
                    $data->id_cuti = $idCuti;
                    $data->id_periode = $idPeriode; 
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_cuti = $tipeCuti; 
                    $data->cuti = $cuti; 
                    $data->tanggal = $tanggal; 
                    $data->total_cuti = $totalCuti; 
                    $data->keterangan = $keterangan; 
                    $data->tgl_pengajuan = $tglPengajuan; 
                    $data->status = $status; 
                    $data->is_dell = '1';
                    $data->save();
               }

            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // menambahkan history approve
    public function insertCutiApproveHistory($idCutiTrn_, $status_, $telephone_, $idKaryawanApprove_, $tglApprove_, $note_)
    {
        // declare variable
       $idCutiTrn = $idCutiTrn_;
       $status = $status_;
       $telephone = $telephone_;
       $idKaryawanApprove = $idKaryawanApprove_;
       $tglApprove = $tglApprove_;
       $note = $note_;

        try {
            // cek data
            $data = new CutiApproveHistory();
            $data->id_cuti_trn = $idCutiTrn; 
            $data->status = $status; 
            $data->telephone = $telephone; 
            $data->id_karyawan_approve = $idKaryawanApprove; 
            $data->tgl_approve = $tglApprove; 
            $data->note = $note; 
            $data->save();
            
            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    // end -- insert

    public function updateActionCutiTrn($id_,$status_,$note_,$reff_,$tglApprove_)
    {
        // declare variable
        $id = $id_;
        $note = $note_;
        $status = $status_;
        $reff = $reff_;
        $tglApprove = $tglApprove_;
        
        try {
            // cek data
            $dt = DB::table('cuti_trn')
            ->select('id','id_cuti','tipe_cuti','tahun','id_karyawan','cuti','tanggal','total_cuti','keterangan')
            ->where('id',$id);
            if($dt->exists())
            {
                $data = $dt->first();
                // status 0=pending, 1=acccept/setuju, 2=reject
                if($status =='1')
                {
                    // accept/setuju
                    // call service lokaHR
                    $idCuti = $data->id_cuti;
                    $tahun = $data->tahun;
                    $tanggal = $data->tanggal;
                    $tipeCuti = $data->tipe_cuti;
                    $idKaryawan = $data->id_karyawan;

                        $idPeriode = 'x';
                        DB::table('cuti_trn')
                        ->where('id','=',$id)
                        ->update([
                            'id_periode'=> $idPeriode,
                            'note'=>$note,
                            'status' => $status,
                            'reff_1' => $reff,
                            'tgl_aprove1'=> $tglApprove
                        ]);
                        // update table master trn sisa cuti
                        $this->updateMasterCutiKaryawan($idKaryawan,$tahun,$idCuti);
                   
                        $result='Update Cuti Successfuly';
                }
                elseif($status=='2')
                {
                    // request reject
                    DB::table('cuti_trn')
                    ->where('id','=',$id)
                    ->update([
                        'note'=>$note,
                        'status' => $status,
                        'reff_1' => $reff,
                        'tgl_aprove1'=> $tglApprove
                    ]);

                    $result='Update Cuti Successfuly';
                }
            }
            else
            {
                $result='data tidak ditemukan';
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateApproveHistory($idCutiTrn,$status,$note,$idKaryawanApprove,$tglApprove)
    {
        try
        {
            DB::table('cuti_approve_history')
            ->where('id_cuti_trn','=',$idCutiTrn)
            ->where('id_karyawan_approve',$idKaryawanApprove)
            ->update([
                'status' => $status,
                'tgl_approve' => $tglApprove,
                'note'=>$note,
            ]);

            return 'Update Approve History Cuti Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateMasterCutiKaryawan($idKaryawan_,$tahun_,$idCuti_)
    {
        $idKaryawan = $idKaryawan_;
        $tahun = $tahun_;
        $idCuti = $idCuti_;
        try {
            $jmlCutiMst_ = 0;
            $jmlCutiTrn_ = 0;
            // Get Jml Cuti Master Periode karyawan
            $jmlCutiMst_ = DB::table('cuti_mst')
            ->select('jml_cuti')
            ->where('id_cuti',$idCuti)
            ->where('tahun',$tahun)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlCutiMst = $jmlCutiMst_->jml_cuti;
            
            // Get total request Cuti Trn Karyawan
            $jmlCutiTrn_ = DB::table('cuti_trn')
            ->select(DB::raw("sum(total_cuti) as total"))
            ->where('id_cuti',$idCuti)
            ->where('tahun',$tahun)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlCutiTrn=$jmlCutiTrn_->total;

            $sisaCuti = $jmlCutiMst - $jmlCutiTrn;
            DB::table('cuti_mst')
            ->where('id_cuti','=',$idCuti)
            ->where('tahun','=',$tahun)
            ->where('id_karyawan','=',$idKaryawan)
            ->update([
                'sisa_cuti'=> $sisaCuti
            ]);

            return 'Update Sisa Cuti Karyawan Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function updateServiceLokaHRCuti($tanggal_,$tipeCuti_,$idKaryawan_,$reff_)
    {
        try {
            // update service lokahr
            $typeService = 'update_request_cuti';
            $json_data = new API_Guzzle();
            $data_jsonDecode = $json_data->getServiceLokaHR($typeService);
            return $data_jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function disableCutiMst($idKaryawan)
    {
        try {
            // cek data
            $dt = DB::table('cuti_mst')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan);
            if($dt->exists())
            {
                DB::table('cuti_mst')
                ->where('id_karyawan','=',$idKaryawan)
                ->update([
                    'is_dell'=>'0'
                ]);
            }
            else
            {
                return 'data tidak ditemukan';
            }

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // mendisable cuti trn yg masa berlakunya sudah jatuh tempo
    public function disableCutiTrnValidatePeriodeExpied()
    {
        try
        {
            $tahun = Carbon::yesterday()->format('Y');
            $bulan = Carbon::yesterday()->format('m');
            $day = "01";
            $dateStartMonth = date($tahun.'-'.$bulan.'-'.$day);
            $yesterday = Carbon::yesterday();

            DB::table('cuti_mst')
            ->where('tahun',$tahun)
            ->whereBetween('date_end', array($dateStartMonth." 00:00:00", $yesterday." 23:59:59"))
            ->update([
                'is_dell'=> '0'
            ]);

            $dataExpied= DB::table('cuti_mst')
            ->select('users.departemen','users.name','cuti_mst.id_karyawan')
            ->join('users','users.id_karyawan','cuti_mst.id_karyawan')
            ->whereBetween('cuti_mst.date_end', array($dateStartMonth." 00:00:00", $yesterday." 23:59:59"))
            ->where('cuti_mst.updated_at','like', '%'. Carbon::now()->format('Y-m-d').'%')
            ->where('cuti_mst.is_dell','0')
            ->get();
            
            $result_['callback'] ='disable Master Cuti validated period expied success';
            $result_['data']=json_encode($dataExpied);

            return $result_;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // public function disableCutiTrn($idKaryawan)
    // {
    //     try {
    //         // cek data
    //         $dt = DB::table('cuti_trn')
    //         ->select('id_karyawan')
    //         ->where('id_karyawan',$idKaryawan);
    //         if($dt->exists())
    //         {
    //             DB::table('cuti_trn')
    //             ->where('id_karyawan','=',$idKaryawan)
    //             ->update([
    //                 'is_dell'=>'0'
    //             ]);
    //         }
    //         else
    //         {
    //             return 'data tidak ditemukan';
    //         }

    //         return 'success';
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    // mengambil master cuti tahunan
    public function getTypeMasterCuti()
    {
        $data = DB::table('master_cuti')
        ->select('id_cuti','tipe_cuti','cuti','jml_hari','masa_berlaku')
        ->where('tipe_cuti','CT')
        ->where('is_dell','1')
        ->get();
        return $data;
    }

    // mengambil master toleransi cuti
    // public function getToleransiMasterCuti()
    // {
    //     $toleransi_ = DB::table('cuti_validity_period')
    //     ->select('toleransi_masa_berlaku')
    //     ->where('id','1')
    //     ->first();
    //     $data = $toleransi_->toleransi_masa_berlaku;
    //     return $data;
    // }

    // mengambil data history approve yg belum di setujui
    public function getApproveHistory($typeHistory_,$idCutiTrn_)
    {
        // 0= pending, 1=approve, 2=reject, else all data
        $typeHistory = $typeHistory_;
        $idCutiTrn = $idCutiTrn_;

        $data_ = DB::table('cuti_approve_history')
        ->select('id_cuti_trn','status','telephone','id_karyawan_approve','tgl_approve','note')
        ->where('id_cuti_trn',$idCutiTrn);
        if($data_->exists())
        {
            // get pending
            if($typeHistory!==null)
            {
                $data_->where('status',$typeHistory);
            }
            $data = $data_->get();
        }
        else
        {
            $data='Karyawan Tidak Mempunyai Hisotry Approve';
        }

        return $data;
    }

    // megambil data sisa cuti karyawan
    public function getCutiKaryawan($idKaryawan_, $tahun_,$idCuti_)
    {
        $idKaryawan = $idKaryawan_;
        $tahun = $tahun_;
        $idCuti = $idCuti_;
        try
        {
            $data_ = DB::table('cuti_mst')
            ->select('id_cuti_mst','id_cuti','tahun','id_karyawan','tipe_cuti','cuti','sisa_cuti','date_start','date_end')
            ->where('is_dell','1')
            ->where('id_karyawan',$idKaryawan)
            ->where('tahun',$tahun)
            ->where('id_cuti','like','%'.$idCuti.'%')
            ->orderBy('id_cuti','asc');
            if($data_->exists())
            {
                $result = $data_->first();
            }
            else
            {
                $result = 'ID Karyawan Tidak Ditemukan';
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    
    // get request cuti pending 
    public function getNotifApprove($idKaryawan_,$typeApprove_,$roleApprove_)
    {
        $idKaryawan = $idKaryawan_;
        $typeApprove = $typeApprove_;
        $roleApprove = $roleApprove_;
        try
        {            
            // data user login
            $c_users = new UsersController();
            $dtUsers = $c_users->getData($idKaryawan);

            $idDepartemen = $dtUsers->id_departemen;
            $idSubDepartemen = $dtUsers->id_sub_departemen;
            $idGrade = $dtUsers->id_grade;
            $grade = $dtUsers->grade;

            $approveLvUp = $dtUsers->approve_level_up;
            $approveLvDown = $dtUsers->approve_level_down;

            $c_gradeController = new GradeController();
            $dtGradeAsc = $c_gradeController->getTypeMasterGrade('asc');
            $dtGradeDsc = $c_gradeController->getTypeMasterGrade('desc');

            $gradeDown = $c_gradeController->getLevelGrade($dtGradeAsc,$idGrade,$approveLvDown);
            // $gradeUp = $c_gradeController->getLevelGrade($dtGradeDsc,$idGrade,$approveLvUp);

            // return array
            $lstDtUsers = $c_gradeController->getKaryawanApproveByGrade($typeApprove,$roleApprove,$gradeDown,$idDepartemen,$idSubDepartemen);

            $data_ = DB::table('cuti_trn')
            ->select(
            'cuti_trn.id',
            'users.departemen',
            'users.sub_departemen',
            'users.grade',
            'users.name',
            'cuti_trn.id_cuti',
            'cuti_trn.tahun',
            'cuti_trn.id_karyawan',
            'cuti_trn.cuti',
            'cuti_trn.tanggal',
            'cuti_trn.keterangan',
            'cuti_trn.tgl_pengajuan')
            ->join('users','users.id_karyawan','cuti_trn.id_karyawan')
            ->where('cuti_trn.status','0')
            ->whereIn('cuti_trn.id_karyawan',$lstDtUsers)
            ->orderBy('cuti_trn.tgl_pengajuan','asc');
            if($data_->exists())
            {
                $result = $data_->get();
            }
            else
            {
                $result = 'Data Tidak Ditemukan';
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // REQUEST API ----------------------------------
    // mengambil master tipe cuti
    public function getMasterCuti(Request $request)
    {
        try
        {
            $data_ = DB::table('master_cuti')
            ->select('id_cuti','tipe_cuti','tipe_cuti','cuti','jml_hari','masa_berlaku')
            ->where('is_dell','1');
            if($data_->exists())
            {
                $data = $data_->get();
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Master Cuti Successfuly',
                    'data' => $data
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Get Data Master Cuti Not Successfuly',
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

}
