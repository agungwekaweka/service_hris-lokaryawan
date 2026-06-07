<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Overtime;
use App\Models\OvertimeHistory;
use Carbon\Carbon;
use DateTime;

class OvertimeController extends Controller
{
    // insert
    // menambahkan data lembur karyawan ke table master
    public function insertOvertimeMst($request)
    {
        // declare variable
        if (isset($request['id_request_overtime'])) {
            $idRequestOvertime = $request['id_request_overtime'];
        }
        $nip = '-';
        $totalJam = 0;
        $idOvertime = $request['id_overtime'];
        $idKaryawan = $request['id_karyawan'];
        $tglPengajuan = $request['tgl_pengajuan'];
        $tglLembur = $request['tgl_lembur'];
        $jamLembur = $request['jam_lembur'];
        $jamMulai = $request['jam_mulai'];
        $jamAkhir = $request['jam_akhir'];
        $keterangan = $request['keterangan'];
        $pic = $request['pic'];
        
        try {
            // cek data
            $dt = DB::table('overtime')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('tgl_lembur',$tglLembur)
            ->where('jam_awal',$jamMulai)
            ->where('jam_akhir',$jamAkhir)
            ->where('status','<>','2');
            if($dt->exists())
            {
                // data sudah ada
                return 'data sudah ada';
            }
            else
            {
                    $data = new Overtime();
                    $data->id_request_overtime = $idRequestOvertime;
                    $data->pic = $pic;
                    $data->id_overtime = $idOvertime;
                    $data->id_karyawan = $idKaryawan;
                    $data->nip = $nip; 
                    $data->tgl_pengajuan = $tglPengajuan; 
                    $data->tgl_lembur = $tglLembur; 
                    $data->jam_lembur = $jamLembur; 
                    $data->jam_awal = $jamMulai;
                    $data->jam_akhir = $jamAkhir;
                    $data->total_jam = $totalJam; 
                    $data->keterangan = $keterangan;
                    // status 0=pending; 1=approve; 2=reject
                    $data->status = '0'; 
                    $data->is_dell = '1';
                    $data->save();
            }
            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertOvertimeHistory($idOvertime_,$status_,$telephone_,$idKaryawanApprove_,$tglApprove_,$note_)
    {
        // declare variable
        $idOvertime = $idOvertime_;
        $status = $status_;
        $telephone = $telephone_;
        $idKaryawanApprove = $idKaryawanApprove_;
        $tglApprove = $tglApprove_;
        $note = $note_;
    
        try {
            
            $data = new OvertimeHistory();
            $data->id_overtime = $idOvertime;
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

     // GET
     // mengambil data request Overtime
      public function getOvertimeRequest($request)
     { 
        $idDepartemen = ''; $idSubDepartemen = ''; $idOvertime = ''; $idKaryawan = ''; $status = ''; $tanggalAwal = ''; $tanggalAkhir = ''; $idRequestOvertime = ''; $pic = '';

        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['id_overtime']) && $request['id_overtime']!='' ) {$idOvertime = $request['id_overtime'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['tanggal_awal']) && $request['tanggal_awal']!='' ) {$tanggalAwal = $request['tanggal_awal'];}
        if (isset($request['tanggal_akhir']) && $request['tanggal_akhir']!='' ) {$tanggalAkhir = $request['tanggal_akhir'];}
        if (isset($request['id_request_overtime']) && $request['id_request_overtime']!='' ) {$idRequestOvertime = $request['id_request_overtime'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}

        try
        {
            $data_ = DB::table('overtime')
            ->select('overtime.id_overtime',
            'users.departemen',
            'users.sub_departemen',
            'users.grade',
            'users.name',
            'users.nik',
            'users.no_telephone',
            'overtime.id_karyawan',
            'overtime.nip',
            'overtime.tgl_pengajuan',
            'overtime.tgl_lembur',
            'overtime.jam_lembur',
            'overtime.jam_awal',
            'overtime.jam_akhir',
            'overtime.jam_jadwal_masuk',
            'overtime.jam_jadwal_pulang',
            'overtime.jam_absen_masuk',
            'overtime.jam_absen_pulang',
            'overtime.jam_absen_jsonObject',
            'overtime.jam_lembur',
            'overtime.total_jam',
            'overtime.status',
            'overtime.keterangan')
            ->join('users','users.id_karyawan','overtime.id_karyawan');
            if($data_->exists())
            { 
                if($idDepartemen!='')
                {
                    $data_->where('users.id_departemen',$idDepartemen);
                }
                if($idSubDepartemen!='')
                {
                    $data_->where('users.id_sub_departemen',$idSubDepartemen);
                }
                if($idOvertime!='')
                {
                    $data_->where('overtime.id_overtime',$idOvertime);
                }
                if($idKaryawan!='')
                {
                    $data_->where('overtime.id_karyawan',$idKaryawan);
                }
                if($status!='')
                {
                    $data_->where('overtime.status',$status);
                }
                if($tanggalAwal!='')
                {
                    $data_->whereBetween('overtime.tgl_lembur', array($tanggalAwal." 00:00:00", $tanggalAkhir." 23:59:59"));
                }
                if($idRequestOvertime!='')
                {
                    $data_->where('overtime.id_request_overtime',$idRequestOvertime);
                }
                if($pic!='')
                {
                    $data_->where('overtime.pic',$pic);
                }
                $data_->orderBy('overtime.tgl_lembur','asc');
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

    //  mengambil request Overtime by PIC Group
     public function getOvertimeRequestGroup($request)
     { 
        $idRequestOvertime = '';
        $pic = '';

        if (isset($request['id_request_overtime'])) {
            $idRequestOvertime = $request['id_request_overtime'];
        }
        if (isset($request['pic'])) {
            $pic = $request['pic'];
        }

        try
        {
            $data_ = DB::table('overtime')
            ->select('overtime.id_request_overtime',
            'overtime.tgl_lembur',
            'overtime.jam_awal',
            'overtime.jam_akhir',
            'overtime.jam_lembur',
            'overtime.keterangan',
            'overtime.pic');
            if($data_->exists())
            {
                if($idRequestOvertime!='')
                {
                    $data_->where('overtime.id_request_overtime',$idRequestOvertime);
                }
                if($pic!='')
                {
                    $data_->where('overtime.pic',$pic);
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
     
     //  mengambil request Overtime by ID
      public function getOvertimeRequestGroupKaryawan($request)
      { 
         $idRequestOvertime = '';
 
         if (isset($request['id_request_overtime'])) {
             $idRequestOvertime = $request['id_request_overtime'];
         }
         try
         {
             $data_ = DB::table('overtime')
             ->select('overtime.id_request_overtime',
            'users.departemen','users.sub_departemen','users.grade',
            'overtime.id_karyawan','users.name')
            ->join('users','users.id_karyawan','overtime.id_karyawan')
            ->where('overtime.id_request_overtime',$idRequestOvertime);
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
          catch (\Exception $ex) {
              return $ex;
          }
      }
    
     // mengambil data history approve
    public function getApproveHistory($typeHistory_,$idOvertime_)
    {
        // 0= pending, 1=approve, 2=reject, else '' all data
        $typeHistory = $typeHistory_;
        $idOvertime = $idOvertime_;
      
        $data_ = DB::table('overtime_history')
        ->select('overtime_history.id_overtime','overtime_history.status','overtime_history.telephone','overtime_history.id_karyawan_approve',
        'users.name',
        'users.grade',
        'overtime_history.tgl_approve',
        'overtime_history.note')
        ->join('users','users.id_karyawan','overtime_history.id_karyawan_approve')
        ->where('id_overtime',$idOvertime)
        ->orderBy('users.id_grade','desc');
        if($data_->exists())
        {
            if($typeHistory!='')
            {
                $data_->where('overtime_history.status',$typeHistory);
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

    public function getRequestOvertimeByID(Request $request)
    {
        $idOvertime = $request->id_overtime;
        try
        {
            $request=[];
            $request['id_overtime'] = $idOvertime;
            $request['id_karyawan'] = '';

            $data = $this->getOvertimeRequest($request);
            $dataHistory = $this->getApproveHistory('',$idOvertime);
          
            if($data !=null)
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Overtime By ID Successfuly',
                    'data' => $data,
                    'history_approve' =>$dataHistory,
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

    public function getRequestOvertime(Request $request)
    {
        try
        {
            $data = $this->getOvertimeRequest($request);
            if($data !=null)
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Lembur Request Successfuly',
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

     // get request overtime pending 
     public function getNotifApprove($idKaryawan_,$typeApprove_,$roleApprove_)
     {
         $idKaryawan = $idKaryawan_;
         $typeApprove = $typeApprove_;
         $roleApprove = $roleApprove_;
         try
         {            
            $data_ = DB::table('overtime_history')
            ->select('id','id_overtime','id_karyawan_approve')
            ->where('status','0')
            ->where('id_karyawan_approve',$idKaryawan);
    
             if($data_->exists())
             {
                 $result = $data_->get();
                 $lstIDTrnOutstanding=null;
                 $countLstOutstanding=0;
              
                 foreach($result as $x)
                 {
                    $idOvertime = $x->id_overtime;

                    //  cek apakah first
                    $_firstData = DB::table('overtime_history')
                    ->select('id','id_overtime','status','id_karyawan_approve')
                    ->where('id_overtime',$idOvertime)
                    ->orderBy('id','asc')
                    ->get();
                 
                    $_idKaryawan1=$_firstData[0]->id_karyawan_approve;
               
                    if($_firstData[0]->status=='0')
                    {
                        if($_idKaryawan1 == $idKaryawan)
                        {
                            if($_firstData[0]->status=='0')
                            {
                                $lstIDTrnOutstanding[$countLstOutstanding] = $_firstData[0]->id_overtime;
                                $countLstOutstanding++;
                            }
                        }
                    }
                    
                    if($_firstData->count()==2)
                    {
                        $_idKaryawan2=$_firstData[1]->id_karyawan_approve;
                        if($_firstData[0]->status=='1')
                        {
                          
                            if($_idKaryawan2 == $idKaryawan)
                            {
                              
                                if($_firstData[1]->status=='0')
                                {
                                    
                                    $lstIDTrnOutstanding[$countLstOutstanding] = $_firstData[1]->id_overtime;
                                    $countLstOutstanding++;
                                }
                             
                            }
                        }
                    }
                 }
              
                 // cek apakah ada data
                 if($lstIDTrnOutstanding!=null)
                 {
                     $data_ = DB::table('overtime')
                     ->select(
                     'overtime.id',
                     'overtime.id_overtime',
                     'users.departemen',
                     'users.sub_departemen',
                     'users.grade',
                     'users.name',
                     'overtime.id_karyawan',
                     'overtime.tgl_pengajuan',
                     'overtime.tgl_lembur',
                     'overtime.jam_lembur',
                     'overtime.total_jam')
                     ->join('users','users.id_karyawan','overtime.id_karyawan')
                     ->where('overtime.status','0')
                     ->whereIn('overtime.id_overtime',$lstIDTrnOutstanding)
                     ->orderBy('overtime.tgl_pengajuan','asc');
                    if($data_->exists())
                    {
                        $result = $data_->get();
                    
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
    
    // UPDATE
    public function updateActionOvertimeMst($request)
    {
        $idOvertime = $request['id_overtime'];
        $status = $request['status'];
        $keterangan = $request['keterangan'];
        $jamLembur = $request['jam_lembur'];
        try {
            // cek data
            $dt = DB::table('overtime')
            ->select('id_overtime')
            ->where('id_overtime',$idOvertime);
            if($dt->exists())
            {
                $data = $dt->first();
            
                DB::table('overtime')
                ->where('id_overtime','=',$idOvertime)
                ->update([
                    'jam_lembur'=>$jamLembur,
                    'keterangan'=>$keterangan,
                    'status' => $status,
                ]);
                
                // Reject
                if($status=='2')
                {
                    DB::table('overtime')
                    ->where('id_overtime','=',$idOvertime)
                    ->update([
                        'is_dell' => '0',
                    ]);
                }

                $result = 'update Overtime karyawan success';
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

    public function updateApproveHistory($idOvertime,$status,$note,$idKaryawanApprove,$tglApprove)
    {
        try
        {
            DB::table('overtime_history')
            ->where('id_overtime','=',$idOvertime)
            ->where('id_karyawan_approve',$idKaryawanApprove)
            ->update([
                'status' => $status,
                'tgl_approve' => $tglApprove,
                'note'=>$note,
            ]);

            return 'Update Approve History Overtime Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getListKaryawanOvertime($request)
    {
        $isSubDepartemen = $request['is_sub_departemen'];
        $idKaryawan = $request['id_karyawan'];
        try
        {
            // data user
            $c_users = new UsersController();
            $dtUsers = $c_users->getData($idKaryawan);
          
            $typeApprove='-';
            if($dtUsers !=null)
            {
                // manager
                if($dtUsers->id_grade =='LV-002')
                {
                    $typeGrade='1';
                    // get Role Overtime
                    $dtRoleApprove = DB::table('role_approve')
                    ->select('id_departemen','id_sub_departemen')
                    ->where('pic',$idKaryawan)
                    ->where('type_role','1')
                    ->get();
                 
                    // loop access
                    $countLstOutstanding=0;
                    $lstIDDepartemen=null;
                    $lstIDSubDepartemen=null;
                    foreach($dtRoleApprove as $v)
                    {
                        $lstIDDepartemen[$countLstOutstanding] = $v->id_departemen;
                        $lstIDSubDepartemen[$countLstOutstanding] = $v->id_sub_departemen;
                        $countLstOutstanding++;
                    }
              
                }
    
                // SPV
                if($dtUsers->id_grade=='LV-004')
                {
                    $typeGrade = '2';
                    $idDepartemen = $dtUsers->id_departemen;
                    $idSubDepartemen = $dtUsers->id_sub_departemen;
                    // $typeApprove = $dtUsers->type_approve;
                }
            }

            $data_ = DB::table('users')
                ->select(
                'users.departemen',
                'users.sub_departemen',
                'users.grade',
                'users.id_karyawan',
                'users.name')
                ->where('users.is_dell','1')
                ->orderBy('sub_departemen','asc');
                if($typeApprove=='-')
                {
                    if($typeGrade=='1')
                    {
                        // type approve manager
                        $data_->whereIn('id_departemen',$lstIDDepartemen);
                        if($isSubDepartemen=='true')
                        {
                            $data_->whereIn('id_sub_departemen',$lstIDSubDepartemen);
                        }     
                    }
                    if($typeGrade=='2')
                    {
                        // type approve SPV
                        $data_->where('id_departemen',$idDepartemen);
                        if($isSubDepartemen=='true')
                        {
                            $data_->where('id_sub_departemen',$idSubDepartemen);
                        } 
                    }
                }
                else
                {
                    $data_->where('type_approve',$typeApprove);
                }

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
}
