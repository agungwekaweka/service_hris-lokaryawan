<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Service_RoleApprove extends Controller
{
    public function createRoleApproveAll(Request $request)
    {
        $typeRole = $request->type_role;
        try
        {
            // get list Sub Departemen
            $c_apiGuzzle = new API_Guzzle();
            $var = 'get_sub_departemen';           
            $dataGuzzle = $c_apiGuzzle->getServiceLokaHR($var);
            $listSubDepartemen = $dataGuzzle->periode;
            
            // get list Grade
            $notIdGrade = ['LV-006'];
            $yesIdGrade=['LV-002','LV-004'];
            $listGrade_ = DB::table('grade')
            ->select('id_grade','level','ord')
            ->where('isDell','1')
            ->orderBy('ord','asc'); // active
            if($typeRole=='0')
            {
                $listGrade_->whereNotIn('id_grade',$notIdGrade);
            }
            elseif($typeRole=='1')
            {
                $listGrade_->whereIn('id_grade',$yesIdGrade);
            }
            $listGrade = $listGrade_->get();

            foreach ($listSubDepartemen as $v)
            {
                $idDepartemen = $v->id_dept;
                $idSubDepartemen = $v->id_subDepartemen;
                
                // management tidak ikut DP009
                if($idDepartemen!='DP009')
                {
                    $req_GetID['id_departemen'] = $idDepartemen;
                    $req_GetID['type_role'] = $typeRole;
                    $generateIDController = new GenerateIDController();
                    $idRoleApprove = $generateIDController->getIDRoleApprove($req_GetID);

                    // loope grade
                    foreach($listGrade as $x)
                    {
                        $idGrade = $x->id_grade;
                        $ord = $x->ord;
                        $pic = '-';

                        $dtUser_ = DB::table('users')
                        ->select('id_karyawan')
                        ->where('id_departemen',$idDepartemen)
                        ->where('id_sub_departemen',$idSubDepartemen)
                        ->where('id_grade',$idGrade)
                        ->where('is_dell','1'); // karyawan active
                        if($dtUser_->exists())
                        {
                            $dtUser = $dtUser_->first();
                            $pic = $dtUser->id_karyawan;
                        }

                        //  insert data
                        // type role 0 = cuti type role 1=lembur;
                        $request['id_role_approve'] = $idRoleApprove;
                        $request['id_departemen'] = $idDepartemen;
                        $request['id_sub_departemen'] = $idSubDepartemen;
                        $request['ord'] = $ord;
                        $request['id_grade'] = $idGrade;
                        $request['pic'] = $pic;
                        $request['type_role'] = $typeRole;

                        $roleApproveController = new RoleApproveController();
                        $roleApproveController->insertCustomRoleApprove($request);
                    }
                }
            }
            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function createRoleApprove(Request $request)
    {
        $idDepartemen = $request->id_departemen;
        $idSubDepartemen = $request->id_sub_departemen;
        $ord = $request->ord;
        $idGrade = $request->id_grade;
        $pic = $request->pic;
        $typeRole = $request->type_role;
        try
        {
            $req_GetID['id_departemen'] = $idDepartemen;
            $req_GetID['type_role'] = $typeRole;
            $generateIDController = new GenerateIDController();
            $idRoleApprove = $generateIDController->getIDRoleApprove($req_GetID);

            $request['id_role_approve'] = $idRoleApprove;
            $request['id_departemen'] = $idDepartemen;
            $request['id_sub_departemen'] = $idSubDepartemen;
            $request['ord'] =  $ord;
            $request['id_grade'] = $idGrade;
            $request['pic'] = $pic;
            $request['type_role'] = $typeRole;

            $roleApproveController = new RoleApproveController();
            $roleApproveController->insertCustomRoleApprove($request);

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updatePicApprove(Request $request)
    {
        $id = $request->id;
        $idDepartemen = $request->id_departemen;
        $idSubDepartemen = $request->id_sub_departemen;
        $idGrade = $request->id_grade;
        $pic = $request->pic;
        // $typeRole = $request->type_role;

        try
        {
            $request['id'] = $id;
            $request['id_departemen'] = $idDepartemen;
            $request['id_sub_departemen'] = $idSubDepartemen;
            $request['id_grade'] = $idGrade;
            $request['pic'] = $pic;
            // $request['type_role'] = $typeRole;
            $roleApproveController = new RoleApproveController();
            $roleApproveController->update($request);
            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getListRoleApprove(Request $request)
     {
        $idDepartemen = $request->id_departemen;
        $idSubDepartemen = $request->id_sub_departemen;
        $typeRole = $request->type_role;
        try
        {
            $request['id_departemen'] = $idDepartemen;
            $request['id_sub_departemen'] = $idSubDepartemen;
            $request['type_role'] = $typeRole;

            $roleApproveController = new RoleApproveController();
            $data = $roleApproveController->getListRoleApprove($request);

            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Role Approve Successfuly',
                'data' => $data
            ]);
 
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
