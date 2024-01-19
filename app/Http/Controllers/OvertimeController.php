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
        $idOvertime = $request['id_overtime'];
        $idKaryawan = $request['id_karyawan'];
        $nip = '-';
        $tglPengajuan = $request['tgl_pengajuan'];
        $tglLembur = $request['tgl_lembur'];
        $jamLembur = $request['jam_lembur'];
        $jamMulai = $request['jam_mulai'];
        $jamAkhir = $request['jam_akhir'];
        $totalJam = 0;
        $keterangan = $request['keterangan'];
        try {
            // cek data
            $dt = DB::table('overtime')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('tgl_lembur',$tglLembur);
            if($dt->exists())
            {
                // data sudah ada
                return 'data sudah ada';
            }
            else
            {
                    $data = new Overtime();
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
     public function getOvertimeRequest($idOvertime_,$idKaryawan_)
     { 
         try
         {
         $data_ = DB::table('overtime')
         ->select('overtime.id_overtime',
         'users.name',
         'users.nik',
         'users.no_telephone',
         'overtime.id_karyawan',
         'overtime.nip',
         'overtime.tgl_pengajuan',
         'overtime.tgl_lembur',
         'overtime.jam_lembur',
         'overtime.total_jam',
         'overtime.status',
         'overtime.keterangan')
         ->join('users','users.id_karyawan','overtime.id_karyawan');
         if($data_->exists())
         {
            if($idOvertime_!='')
            {
                $data_->where('overtime.id_overtime',$idOvertime_);
            }
            if($idKaryawan_!='')
            {
                $data_->where('overtime.id_karyawan',$idKaryawan_);
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

    public function getRequestOvertimeByID(Request $request)
    {
        $idOvertime = $request->id_overtime;
        try
        {
            $data = $this->getOvertimeRequest($idOvertime,'');
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
        $idKaryawan = $request->id_karyawan;

        try
        {
            $data = $this->getOvertimeRequest('',$idKaryawan);
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
           
             $data_ = DB::table('overtime')
             ->select(
             'overtime.id',
             'overtime.id_overtime')
             ->where('overtime.status','0')
             ->whereIn('overtime.id_karyawan',$lstDtUsers)
             ->orderBy('overtime.tgl_pengajuan','asc');
             if($data_->exists())
             {
                 $result = $data_->get();
     
                 $lstIDTrnOutstanding=null;
                 $countLstOutstanding=0;
                 foreach($result as $x)
                 {
                     $idOvertime = $x->id_overtime;
                 
                     // cek apakah masih ada history approve yg belum di setujui
                     //  0=pending, 1=approve, 2=reject
                     $result_ = $this->getApproveHistory(0,$idOvertime);
                  
                     if($result_==null)
                     {
                     // selesai semua
                         
                     }
                     else
                     {
                         // masih ada data yg pending
                         if($result_[0]->id_karyawan_approve == $idKaryawan)
                         {
                             $lstIDTrnOutstanding[$countLstOutstanding] = $result_[0]->id_overtime;
                             $countLstOutstanding++;
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
                     ->orderBy('overtime.tgl_pengajuan','asc')
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
    
    // UPDATE
    public function updateActionOvertimeMst($idOvertime_,$status_,$note_,$jamLembur_)
    {
        // declare variable
        $idOvertime = $idOvertime_;
        $status = $status_;
        $note = $note_;
        $jamLembur = $jamLembur_;
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
                    'keterangan'=>$note,
                    'status' => $status,
                ]);
                
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
}
