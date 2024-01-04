<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RoleApprove;

class RoleApproveController extends Controller
{
    // menambahkan data Role Approve ke table master
    public function insertCustomRoleApprove($typeRole_,$idKaryawan_,$typeApprove_,$idApprove_)
    {
        // declare variable
        $typeRole = $typeRole_;
        $idKaryawan = $idKaryawan_;
        $typeApprove = $typeApprove_;
        $idApprove = $idApprove_;

        try {
            // cek data
            $dt = DB::table('role_approve')
            ->select('id')
            ->where('type_role',$typeRole)
            ->where('id_karyawan',$idKaryawan)
            ->where('type_approve',$typeApprove)
            ->where('id_approve',$idApprove);
            if($dt->exists())
            {
                $req = 'Data sudah ada, mohon cek kembali';
            }
            else
            {
                // data belum ada
                $req = $this->insert($typeRole,$idKaryawan,$typeApprove,$idApprove);
            }

            return $req;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function insert($typeRole,$idKaryawan_, $typeApprove_,$idApprove_)
    {
        $typeRole = $typeRole_;
        $idKaryawan = $idKaryawan_;
        $typeApprove = $typeApprove_;
        $idApprove = $idApprove_;
  
        try
        {
            $data = new RoleApprove();
            $data->type_role = $typeRole;
            $data->id_karyawan = $idKaryawan;
            $data->type_approve = $typeApprove;
            $data->id_approve = $idApprove; 
            $data->save();
            return 'insert role approve success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // private function delete($idKaryawan_, $typeApprove_,$idApprove_)
    // {
    //     $idKaryawan = $idKaryawan_;
    //     $typeApprove = $typeApprove_;
    //     $idApprove = $idApprove_;
    //     try
    //     {
    //         $data = new RoleApprove();
    //         $data->id_karyawan = $idKaryawan;
    //         $data->type_approve = $typeApprove;
    //         $data->id_approve = $idApprove; 
    //         $data->save();
    //         return 'data berhasil ditambahkan';
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }
}
