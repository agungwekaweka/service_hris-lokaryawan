<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Service_RoleApprove extends Controller
{
    public function getListRoleApprove(Request $request)
     {
        $idApprove = $request->id_approve;
        $typeRole = $request->type_role;
         try
         {
            $data_ = DB::table('role_approve')
            ->select('id');
            if($idApprove !='')
            {
                $data_->where('id_approve',$idApprove);
            }
            if($typeRole!='')
            {
                $data_->where('type_role',$typeRole);
            }  
             if($data_->exists())
             {
                    // $dtKaryawan=DB::table('role_approve')
                    // ->select('id_karyawan')
                    // ->get()->pluck('id_karyawan')->toArray();
                    $dtKaryawan=DB::table('role_approve')
                    ->select('id_karyawan')
                    ->where('type_role',$typeRole)
                    ->get();
                    $listArray=[];
                    foreach ($dtKaryawan as $v)
                    {
                        $listArray[]=$v->id_karyawan;
                    }
                    
                    $data = DB::table('users')
                    ->select('users.departemen',
                    'users.sub_departemen',
                    'users.grade',
                    'users.nik',
                    'users.id_karyawan',
                    'users.name',
                    'role_approve.type_approve',
                    'role_approve.id_approve')
                    ->where('users.approve','1')
                    ->whereIn('users.id_karyawan',$listArray)
                    ->join('role_approve','role_approve.id_karyawan','users.id_karyawan')
                    ->orderBy('users.id_grade','asc')
                    ->orderBy('role_approve.type_approve','asc')
                    ->get();

                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Get Data Role Approve Successfuly',
                        'data' => $data
                    ]);
             }
             else
             {
                 $result=response()->json([
                     'status' => 'failed',
                     'message' => 'Get Data Role Approve Not Successfuly',
                 ]);
             }
 
             return $result;
         } catch (\Exception $ex) {
             return $ex;
         }
     }

     public function cekRoleApproveKaryawan(Request $request)
     {
        $typeRole = $request->type_role;
        $idKaryawan = $request->id_karyawan;
         try
         {
            // get list approve up Level by type Role
            $c_grade = new GradeController();
            $data = $c_grade->getGradeLvUp($idKaryawan,$typeRole);
            if($data !=null)
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Role Approve Successfuly',
                    'data' => $data
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'ID Karyawan : '. $idKaryawan.' Tidak memiliki Atasan untuk Approve'
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
