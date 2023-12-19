<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CutiMst;
use App\Models\CutiTrn;
use App\Models\CutiApproveHistory;
use App\Models\CutiLampiran;
use Carbon\Carbon;
use DateTime;


class CutiController extends Controller
{
    // insert
    // menambahkan data cuti karyawan ke table master
    public function insertCutiMst($idMst_,$idCuti_, $tahun_,$idKaryawan_,$tipeCuti_,$cuti_,$jml_,$tipeMasaBerlaku_,$masaBerlaku_,$tglBerlaku_)
    {
        // declare variable
        $idMst = $idMst_;
        $idCuti = $idCuti_;
        $tahun = $tahun_;
        $idKaryawan = $idKaryawan_;
        $tipeCuti = $tipeCuti_;
        $cuti = $cuti_;
        $jml = $jml_;
        $tipeMasaBerlaku = $tipeMasaBerlaku_;
        $masaBerlaku = $masaBerlaku_;
        $tglBerlaku = $tglBerlaku_;
    
        try {
            // cek data
            $dt = DB::table('cuti_mst')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('tipe_cuti','CT')
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
                    // get tglExpied
                    $tglExpied = $this->getTglExpied($masaBerlaku,$tipeMasaBerlaku,$tglBerlaku);
                    // jika tipe cuti khusus maka jml hari menggunakan interval tanggal
                    if($tipeCuti=='CK')
                    {
                        $jml = $this->getIntervalHariFromMonth($tglBerlaku,$tglExpied);
                    }
               
             
                    $data = new CutiMst();
                    $data->id_cuti_mst = $idMst;
                    $data->id_cuti = $idCuti;
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_cuti = $tipeCuti; 
                    $data->cuti = $cuti; 
                    $data->jml_cuti = $jml; 
                    $data->sisa_cuti = $jml; 
                    $data->tipe_masa_berlaku = $tipeMasaBerlaku;
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
    
    private function getTglExpied($masaBerlaku,$tipeMasaBerlaku,$tglBerlaku)
    {
        $carbonFormatDate = new Carbon($tglBerlaku.' 00:00:00');
        // cek tipe masa Berlaku
        if($tipeMasaBerlaku=='year')
        {
            $tglExpied = $carbonFormatDate->addYears($masaBerlaku);
        }
        elseif($tipeMasaBerlaku=='month')
        {
            $tglExpied = $carbonFormatDate->addMonths($masaBerlaku);
        }
        elseif($tipeMasaBerlaku=='day')
        {
            $tglExpied = $carbonFormatDate->addDays($masaBerlaku);
        }
        return $tglExpied;
    }

    private function getIntervalHariFromMonth($startDate,$endDate)
    {
        $startDate = Carbon::parse($startDate);

        $endDate = Carbon::parse($endDate);

        $countDay = $startDate->diffInDays($endDate);
        return $countDay;
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
               ->where('tanggal',$tanggal)
               ->where('tipe_cuti','CT');
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

    public function insertCutiLampiran($idCutiMst_,$idCutiTrn_,$tahun_,$url_)
    {
        // declare variable
       $idCutiMst = $idCutiMst_;
       $idCutiTrn = $idCutiTrn_;
       $tahun = $tahun_;
       $url = $url_;

        try {
            // cek data
            $data = new CutiLampiran();
            $data->id_cuti_mst = $idCutiMst; 
            $data->id_cuti_trn = $idCutiTrn; 
            $data->tahun = $tahun; 
            $data->url = $url; 
            $data->save();
            
            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    // end -- insert

    public function updateActionCutiTrn($idCutiTrn_,$status_,$note_)
    {
        // declare variable
        $idCutiTrn = $idCutiTrn_;
        $status = $status_;
        $note = $note_;

        try {
            // cek data
            $dt = DB::table('cuti_trn')
            ->select('id','id_cuti','tipe_cuti','tahun','id_karyawan','cuti','tanggal','total_cuti','keterangan')
            ->where('id_cuti_trn',$idCutiTrn);
            if($dt->exists())
            {
                $data = $dt->first();

                DB::table('cuti_trn')
                ->where('id_cuti_trn','=',$idCutiTrn)
                ->update([
                    'note'=>$note,
                    'status' => $status,
                ]);
                
                if($status=='2')
                {
                    DB::table('cuti_trn')
                    ->where('id_cuti_trn','=',$idCutiTrn)
                    ->update([
                        'is_dell' => '0',
                    ]);
                }

                $result = 'update Cuti Trn karyawan success';
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

    public function updateMasterCutiKaryawan($idKaryawan_,$idMst_,$idTrn_,$idCuti_)
    {
        $idKaryawan = $idKaryawan_;
        $idMst = $idMst_;
        $idTrn = $idTrn_;
        $idCuti = $idCuti_;
        try {
            $jmlCutiMst_ = 0;
            $jmlCutiTrn_ = 0;
            // Get Jml Cuti Master Periode karyawan
            $jmlCutiMst_ = DB::table('cuti_mst')
            ->select('jml_cuti')
            ->where('is_dell','1')
            ->where('sisa_cuti','<>',0)
            ->where('id_cuti',$idCuti)
            ->where('id_cuti_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlCutiMst = $jmlCutiMst_->jml_cuti;
          
            // Get total request Cuti Trn Karyawan
            $jmlCutiTrn_ = DB::table('cuti_trn')
            ->select(DB::raw("sum(total_cuti) as total"))
            ->where('is_dell','1')
            ->where('id_cuti',$idCuti)
            ->where('id_cuti_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();

            $jmlCutiTrn=$jmlCutiTrn_->total;
            $sisaCuti = $jmlCutiMst - $jmlCutiTrn;
            
            DB::table('cuti_mst')
            ->where('is_dell','1')
            ->where('sisa_cuti','<>',0)
            ->where('id_cuti','=',$idCuti)
            ->where('id_cuti_mst','=',$idMst)
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
        ->select('id_cuti','tipe_cuti','cuti','jml_hari','tipe_masa_berlaku','masa_berlaku')
        ->where('tipe_cuti','CT')
        ->where('is_dell','1')
        ->get();
        return $data;
    }

    // mengambil master cuti tahunan
    public function getTypeMasterCutiByID($idCuti)
    {
        $data = DB::table('master_cuti')
        ->select('id_cuti','tipe_cuti','cuti','jml_hari','tipe_masa_berlaku','masa_berlaku')
        ->where('id_cuti',$idCuti)
        ->where('is_dell','1')
        ->first();
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

    // mengambil data history approve
    public function getApproveHistory($typeHistory_,$idCutiTrn_)
    {
        // 0= pending, 1=approve, 2=reject, else '' all data
        $typeHistory = $typeHistory_;
        $idCutiTrn = $idCutiTrn_;
      
        $data_ = DB::table('cuti_approve_history')
        ->select('cuti_approve_history.id_cuti_trn',
        'cuti_approve_history.status',
        'cuti_approve_history.telephone',
        'cuti_approve_history.id_karyawan_approve',
        'users.name',
        'users.grade',
        'cuti_approve_history.tgl_approve',
        'cuti_approve_history.note')
        ->join('users','users.id_karyawan','cuti_approve_history.id_karyawan_approve')
        ->where('id_cuti_trn',$idCutiTrn)
        ->orderBy('cuti_approve_history.id','asc');
        if($data_->exists())
        {
            if($typeHistory!='')
            {
                $data_->where('status',$typeHistory);
            }
            if($data_->count()==0)
            {
                $data=null;
                return $data;
            }

            $data = $data_->get();
        }
        else
        {
            $data=null;
        }
        return $data;
    }

    // mengambil data lampiran
    public function getDataLampiran($idCutiTrn_)
    {
        $idCutiTrn = $idCutiTrn_;

        $c_Server = new API_Guzzle();
        $urlServer=$c_Server->urlLokaryawan();
      
        $data_ = DB::table('cuti_lampiran')
        ->select('id_cuti_mst',
        'id_cuti_trn','tahun',
        DB::raw("CONCAT('".$urlServer."storage/',url) as url"))
        ->where('id_cuti_trn',$idCutiTrn);
        if($data_->exists())
        {
            $data = $data_->get();
        }
        else
        {
            $data=null;
        }
        return $data;
    }

    // mengambil data cuti Trn
    public function getCutiTrn($idCutiTrn_)
    { 
        $data_ = DB::table('cuti_trn')
        ->select('cuti_trn.id_cuti_mst',
        'cuti_trn.id_cuti_trn',
        'users.name',
        'users.nik',
        'users.no_telephone',
        'cuti_trn.id_cuti',
        'cuti_trn.id_periode',
        'cuti_trn.tahun',
        'cuti_trn.id_karyawan',
        'cuti_trn.tipe_cuti',
        'cuti_trn.cuti',
        'cuti_trn.tanggal',
        'cuti_trn.total_cuti',
        'cuti_trn.keterangan',
        'cuti_trn.tgl_pengajuan',
        'cuti_trn.status',
        'cuti_trn.note')
        ->join('users','users.id_karyawan','cuti_trn.id_karyawan')
        ->where('cuti_trn.id_cuti_trn',$idCutiTrn_);
        if($data_->exists())
        {
            $data = $data_->first();
        }
        else
        {
            $data=null;
        }
        return $data;
    }
    
    // mengambil data request cuti
    public function getCutiRequest($idKaryawan_,$status_,$tipe_)
    { 
        try
        {
        $data_ = DB::table('cuti_trn')
        ->select('cuti_trn.id_cuti_mst',
        'cuti_trn.id_cuti_trn',
        'cuti_trn.id_cuti',
        'cuti_trn.id_periode',
        'cuti_trn.tahun',
        'cuti_trn.id_karyawan',
        'cuti_trn.tipe_cuti',
        'cuti_trn.cuti',
        'cuti_trn.tanggal',
        'cuti_trn.total_cuti',
        'cuti_trn.keterangan',
        'cuti_trn.tgl_pengajuan',
        'cuti_trn.status',
        'cuti_trn.note')
        ->where('cuti_trn.id_karyawan',$idKaryawan_);
        if($data_->exists())
        {
            // cek apakah get by status);
            if($status_!=null)
            {
                $data_->where('cuti_trn.status',$status_);
            }
            if($tipe_!=null)
            {
                $data_->where('cuti_trn.tipe_cuti',$tipe_);
            }
            $data = $data_->get();
        }
        else
        {
            $data=null;
        }
        return $data;
        }
        catch (\Exception $ex) {
            return $ex;
        }
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
            ->where('sisa_cuti','<>',0)
            ->where('id_karyawan',$idKaryawan)
            ->where('id_cuti','like','%'.$idCuti.'%')
            ->orderBy('id','asc');
            if($data_->exists())
            {
                $result = $data_->get();
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
            'cuti_trn.id_cuti_trn')
            ->where('cuti_trn.status','0')
            ->whereIn('cuti_trn.id_karyawan',$lstDtUsers)
            ->orderBy('cuti_trn.tgl_pengajuan','asc');
    
            if($data_->exists())
            {
                $result = $data_->get();
            
                $lstIDTrnOutstanding=null;
                $countLstOutstanding=0;
                
                foreach($result as $x)
                {
                
                    $idCutiTrn = $x->id_cuti_trn;
                    
                    // cek apakah masih ada history approve yg belum di setujui
                    //  0=pending, 1=approve, 2=reject
                    $result_ = $this->getApproveHistory(0,$idCutiTrn);
                 
                    if($result_==null)
                    {
                    // selesai semua
                        
                    }
                    else
                    {
                        // masih ada data yg pending
                        if($result_[0]->id_karyawan_approve == $idKaryawan)
                        {
                            $lstIDTrnOutstanding[$countLstOutstanding] = $result_[0]->id_cuti_trn;
                            $countLstOutstanding++;
                        }
                    }
                }
              
                // cek apakah ada data
                if($lstIDTrnOutstanding!=null)
                {
                    $data_ = DB::table('cuti_trn')
                    ->select(
                    'cuti_trn.id',
                    'cuti_trn.id_cuti_trn',
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
                    ->whereIn('cuti_trn.id_cuti_trn',$lstIDTrnOutstanding)
                    ->orderBy('cuti_trn.tgl_pengajuan','asc')
                    ->get();
                    $result = $data_;
                }
                else
                {
                    $result = 'Data Tidak Ditemukan'; 
                }
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

    public function getRequestCutiByID(Request $request)
    {
        $idCutiTrn = $request->id_cuti_trn;
        try
        {
            $data = $this->getCutiTrn($idCutiTrn);
            $dataHistory = $this->getApproveHistory('',$idCutiTrn);
            $dataLampiran = $this->getDataLampiran($idCutiTrn);
          
            if($data !=null)
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Cuti By ID Successfuly',
                    'data' => $data,
                    'history_approve' =>$dataHistory,
                    'data_lampiran' =>$dataLampiran
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Data Cuti By ID Tidak Ditemukan',
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getRequestCuti(Request $request)
    {
        $idCutiTrn = $request->id_karyawan;
        $status = $request->status;
        $type = $request->type;

        try
        {
            $data = $this->getCutiRequest($idCutiTrn,$status,$type);
            if($data !=null)
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Cuti Request Successfuly',
                    'data' => $data
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

}
