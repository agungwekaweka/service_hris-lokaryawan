<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RoleApprove;

class RoleApproveController extends Controller
{
    // menambahkan data Role Approve ke table master
    public function insertCustomRoleApprove($request)
    {
        // declare variable
        $idRoleApprove = $request['id_role_approve'];
        $idDepartemen = $request['id_departemen'];
        $idSubDepartemen = $request['id_sub_departemen'];
        $ord = $request['ord'];
        $idGrade = $request['id_grade'];
        $pic = $request['pic'];
        $typeRole = $request['type_role'];

        try {
            // cek data
            // $dt = DB::table('role_approve')
            // ->select('id')
            // ->where('id_departemen',$idDepartemen)
            // ->where('id_sub_departemen',$idSubDepartemen)
            // ->where('id_grade',$idGrade)
            // ->where('pic',$pic)
            // ->where('type_role',$typeRole);
            // if($dt->exists())
            // {
            //     $req = 'Data sudah ada, mohon cek kembali';
            // }
            // else
            // {
                // data belum ada
                $req = $this->insert($request);
            // }

            return $req;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function insert($request)
    {
        // declare variable
        $idRoleApprove = $request['id_role_approve'];
        $idDepartemen = $request['id_departemen'];
        $idSubDepartemen = $request['id_sub_departemen'];
        $ord = $request['ord'];
        $idGrade = $request['id_grade'];
        $pic = $request['pic'];
        $typeRole = $request['type_role'];
        try
        {
            $data = new RoleApprove();
            $data->id_role_approve = $idRoleApprove;
            $data->id_departemen = $idDepartemen;
            $data->id_sub_departemen = $idSubDepartemen;
            $data->ord = $ord;
            $data->id_grade = $idGrade; 
            $data->pic = $pic; 
            $data->type_role = $typeRole; 
            $data->save();
            return 'insert role approve success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get list role approve general departemen
    public function getListRoleApprove($request)
    {
        $idDepartemen = $request['id_departemen'];
        $idSubDepartemen = $request['id_sub_departemen'];
        $typeRole = $request['type_role'];
        try
        {
            $data_ = DB::table('role_approve')
            ->select(
            'role_approve.id_departemen',
            'role_approve.id_sub_departemen',
            DB::raw('(select level from grade where grade.id_grade = role_approve.id_grade limit 1) as grade'),
            DB::raw('(select name from users where users.id_karyawan = role_approve.pic limit 1) as name'))
            ->where('type_role',$typeRole);
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }     
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            $data_->orderBy('role_approve.id_sub_departemen','asc');
            $data_->orderBy('role_approve.ord','asc');
            $data = $data_->get();
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function roleApproveKaryawan($request)
    {
        $typeRole = $request['type_role'];
        $idKaryawan = $request['id_karyawan'];
        try
        {
            
            
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function update($request)
    {
        $id = $request['id'];
        $idDepartemen = $request['id_departemen'];
        $idSubDepartemen = $request['id_sub_departemen'];
        $idGrade = $request['id_grade'];
        $pic = $request['pic'];
        // $typeRole = $request['type_role'];

        try
        {
            // update by Departemen
            if($idDepartemen!='' && $idSubDepartemen=='')
            {   
                DB::table('role_approve')
                ->where('id_departemen','=',$idDepartemen)
                ->where('id_grade','=',$idGrade)
                // ->where('type_role',$typeRole)
                ->update([
                    'pic'=>$pic
                ]);
                return 'Update Role Approve Success';
            }

            // update by Sub Departemen
            if($idSubDepartemen !='')
            {
                DB::table('role_approve')
                ->where('id_sub_departemen','=',$idSubDepartemen)
                ->where('id_grade','=',$idGrade)
                // ->where('type_role',$typeRole)
                ->update([
                    'pic'=>$pic
                ]);
                return 'Update Role Approve Success';
            }

            // update by ID GRADE
            if($idGrade!='')
            {
                DB::table('role_approve')
                ->where('id_grade','=',$idGrade)
                // ->where('type_role',$typeRole)
                ->update([
                    'pic'=>$pic
                ]);
                return 'Update Role Approve Success';
            }

            // update by ID
            if($id!='')
            {
                DB::table('role_approve')
                ->where('id','=',$id)
                // ->where('type_role',$typeRole)
                ->update([
                    'pic'=>$pic
                ]);
                return 'Update Role Approve Success';
            }
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // public function deleteCustomRoleApprove($idKaryawan,$idRoleApprove)
    // {
    //     $idKaryawan = $idKaryawan;
    //     $idRoleApprove = $idRoleApprove;
    //     try
    //     {
    //         if($idKaryawan !='')
    //         {
    //             DB::table('role_approve')
    //             ->where('id_karyawan','=',$idKaryawan)
    //             ->delete();
    //         }
    //         elseif($idRoleApprove!='')
    //         {
    //             DB::table('role_approve')
    //             ->where('id','=',$idRoleApprove)
    //             ->delete();
    //         }
    //         return 'success delete data';
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }
}
