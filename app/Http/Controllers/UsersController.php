<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersController extends Controller
{
    // menambahkan data user ke table master
    // -> jika karyawan belum ada ditambahkan, jika karyawan sudah ada di update, jika karyawan nonn aktif di hapus
    public function insertUser($idDepartemen_,$departemen_, $idSubDepartemen_,$subDepartemen_,$idGrade_, $grade_, $name_,$noTelephone_, $idKaryawan_, $nik_, $password_, $isDell_,$doj_,$dob_)
    {
        // declare variable
        $idDepartemen = $idDepartemen_;
        $departemen = $departemen_;
        $idSubDepartemen = $idSubDepartemen_;
        $subDepartemen = $subDepartemen_;
        $idGrade = $idGrade_;
        $grade = $grade_;
        $name = $name_;
        $noTelephone =  $noTelephone_;
        $idKaryawan = $idKaryawan_;
        $nik = $nik_;
        $password = $password_;
        $doj = $doj_;
        $dob = $dob_;
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
                    $req['update_users'] = $this->update($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idKaryawan, $nik, $password,$doj,$dob);
                }
                else
                {
                    $req['delete_users'] = $this->delete();
                }
            }
            else
            {
                // data belum ada
                // insert
                $req['insert_users'] = $this->insert($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idKaryawan, $nik, $password,$doj,$dob);
            }

            return $req;
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
            'users.approve',
            'users.type_approve',
            'grade.approve_level_up',
            'grade.approve_level_down',
            'users.name',
            'users.no_telephone',
            'users.id_karyawan',
            'users.nik',
            'users.doj',
            'users.dob',
            'users.is_dell')
            ->where('users.id_karyawan',$idKaryawan)
            ->join('grade','grade.id_grade','users.id_grade')
            ->first();
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getDataByNoHp($noTelephone_)
    {
        $noTelephone = $noTelephone_;
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
            'users.doj',
            'users.dob',
            'users.is_dell')
            ->where('users.no_telephone',$noTelephone)
            ->join('grade','grade.id_grade','users.id_grade')
            ->first();
           
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function insert($idDepartemen_,$departemen_, $idSubDepartemen_,$subDepartemen_,$idGrade_, $grade_, $name_,$noTelephone_, $idKaryawan_, $nik_, $password_,$doj_,$dob_)
    {
        try
        {
            $data = new User();
            $data->id_departemen = $idDepartemen_;
            $data->departemen = $departemen_;
            $data->id_sub_departemen = $idSubDepartemen_; 
            $data->sub_departemen = $subDepartemen_; 
            $data->id_grade = $idGrade_;
            $data->grade = $grade_; 
            $data->name = $name_; 
            $data->no_telephone = $noTelephone_;
            $data->id_karyawan = $idKaryawan_; 
            $data->nik = $nik_; 
            $data->password = $password_; 
            $data->doj = $doj_;
            $data->dob = $dob_;
            $data->is_dell = '1';
            $data->save();
            return 'insert data users success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function update($idDepartemen_,$departemen_, $idSubDepartemen_,$subDepartemen_,$idGrade_, $grade_, $name_,$noTelephone_, $idKaryawan_, $nik_, $password_,$doj_,$dob_)
    {

        try
        {
            DB::table('users')
            ->where('id_karyawan','=',$idKaryawan_)
            ->update([
                'id_departemen'=> $idDepartemen_,
                'departemen'=> $departemen_,
                'id_sub_departemen'=>$idSubDepartemen_,
                'sub_departemen'=>$subDepartemen_,
                'id_grade' => $idGrade_,
                'grade' => $grade_,
                'name' => $name_,
                'no_telephone'=>$noTelephone_,
                'id_karyawan'=> $idKaryawan_,
                'nik'=> $nik_,
                'password'=> $password_,
                'doj'=>$doj_,
                'dob'=>$dob_
            ]);
            return 'update data users success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function disableUsers($idKaryawan)
    {
        try {
            // cek data
            $dt = DB::table('users')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan);
            if($dt->exists())
            {
                DB::table('users')
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

    private function delete($idKaryawan_)
    {
        try
        {
        DB::table('users')
        ->where('id_karyawan','=',$idKaryawan_)
        ->delete();
        return 'delete data users success';
        } catch (\Exception $ex) {
            return $ex;
        }
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
            return 'update akes approve users success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
