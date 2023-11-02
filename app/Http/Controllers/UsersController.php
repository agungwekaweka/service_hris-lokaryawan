<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersController extends Controller
{
    // menambahkan data user ke table master
    // -> jika karyawan belum ada ditambahkan, jika karyawan sudah ada di update, jika karyawan nonn aktif di hapus
    public function insertUser($idDepartemen_,$departemen_, $idSubDepartemen_,$subDepartemen_, $grade_, $name_, $idKaryawan_, $nik_, $password_, $isDell_)
    {
        // declare variable
        $idDepartemen = $idDepartemen_;
        $departemen = $departemen_;
        $idSubDepartemen = $idSubDepartemen_;
        $subDepartemen = $subDepartemen_;
        $grade = $grade_;
        $name = $name_;
        $idKaryawan = $idKaryawan_;
        $nik = $nik_;
        $password = $password_;
        $isDell = $isDell_;

        try {
            // cek data
            $dt = DB::table('users')
            ->select('id')
            ->where('id_karyawan',$idKaryawan);
            if($dt->exists())
            {
                // data sudah ada
                // data enable (update) 1
                // data disable (delete) 2
                if($isDell=='1')
                {
                    $req = $this->update($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen, $grade, $name, $idKaryawan, $nik, $password);
                }
                else
                {
                    $req = $this->delete();
                }
            }
            else
            {
                // data belum ada
                // insert
                $req = $this->insert($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen, $grade, $name, $idKaryawan, $nik, $password);
            }

            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getData($idKaryawan_)
    {
        $idKaryawan = $idKaryawan_;
        try
        {
            $data = DB::table('users')
            ->select(
            'users.id_departemen',
            'users.departemen',
            'users.id_sub_departemen',
            'users.sub_departemen',
            'users.id_grade',
            'users.grade',
            'grade.approve_level_up',
            'grade.approve_level_down',
            'users.name',
            'users.no_telephone',
            'users.id_karyawan',
            'users.nik',
            'users.is_dell')
            ->where('users.id_karyawan',$idKaryawan)
            ->join('grade','grade.id_grade','users.id_grade')
            ->first();
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function insert($idDepartemen_,$departemen_, $idSubDepartemen_,$subDepartemen_, $grade_, $name_, $idKaryawan_, $nik_, $password_)
    {
        $data = new User();
        $data->id_departemen = $idDepartemen_;
        $data->departemen = $departemen_;
        $data->id_sub_departemen = $idSubDepartemen_; 
        $data->sub_departemen = $subDepartemen_; 
        $data->grade = $grade_; 
        $data->name = $name_; 
        $data->id_karyawan = $idKaryawan_; 
        $data->nik = $nik_; 
        $data->password = $password_; 
        $data->is_dell = '1';
        $data->save();
    }

    private function update($idDepartemen_,$idSubDepartemen_,$grade_,$name_,$idKaryawan_,$nik_,$password_)
    {
        DB::table('users')
        ->where('id_karyawan','=',$idKaryawan_)
        ->update([
            'id_departemen'=> $idDepartemen_,
            'id_sub_departemen'=>$idSubDepartemen_,
            'grade' => $grade_,
            'name' => $name_,
            'id_karyawan'=> $idKaryawan_,
            'nik'=> $nik_,
            'password'=> $password_
        ]);
    }

    private function delete($idKaryawan_)
    {
        DB::table('users')
        ->where('id_karyawan','=',$idKaryawan_)
        ->delete();
    }

    public function updateAksesApprove($idKaryawan_,$approve_,$typeApprove_)
    {
        $idKaryawan = $idKaryawan_;
        $approve = $approve_;
        $typeApprove = $typeApprove_;
        try
        {
            DB::table('users')
            ->where('id_karyawan','=',$idKaryawan)
            ->update([
                'approve'=> $approve,
                'type_approve'=>$typeApprove
            ]);
            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
