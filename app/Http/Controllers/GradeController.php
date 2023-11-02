<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function getTypeMasterGrade($typeOrd)
    {
        $data = DB::table('grade')
        ->select('ord','id_grade','level')
        ->where('isDell','1')
        ->orderBy('ord',$typeOrd)
        ->get();
        return $data;
    }

    public function getLevelGrade($dtGrade,$idGrade,$approveLv)
    {
        try
        {
        $grade = array();
        $lvGrade = 1;
        $thisData = false;
        foreach($dtGrade as $x)
        {
            if($idGrade==$x->id_grade)
            {
                $thisData=true;
            }
            else
            {
                if($thisData==true)
                {
                    if($lvGrade==$approveLv)
                    {
                        $grade[]= $x->id_grade;
                        break;
                    }
                    else
                    {
                        $grade[]= $x->id_grade;
                    }
                    $lvGrade++;
                }
            }
        }
        return $grade;
        } catch (\Exception $ex) {
            return $ex;
        }        
    }

    public function getKaryawanApproveByGrade($typeApprove,$roleApprove,$gradeDown,$idDepartemen,$idSubDepartemen)
    {
        try
        {
            $lstDtUsers_ = DB::table('users')
            ->select('id_karyawan')
            ->where('is_dell','1');
            // cek type Approve
            // approve custom
            if($typeApprove=='9')
            {
                $lstDtUsers=[];
                $lstDtUsersList=[];
                foreach($roleApprove as $x)
                {
                    $lstDtUsers_ = DB::table('users')
                    ->select('id_karyawan')
                    ->where('is_dell','1');
                    $typeApproveRole = $x->type_approve;
                    $idApproveRole = $x->id_approve;
                    
                    // approve by Departemen
                    if($typeApproveRole=='0')
                    {
                        // memakai Grade
                        $lstDtUsers_->whereIn('id_grade',$gradeDown);
                        $lstDtUsers_->where('id_departemen',$idApproveRole);
                        $lstDtUsersList = $lstDtUsers_->get()->pluck('id_karyawan')->toArray();
                    }
                    // approve by Sub Departemen
                    elseif($typeApproveRole=='1')
                    {
                        // memakai Grade
                        $lstDtUsers_->whereIn('id_grade',$gradeDown);
                        $lstDtUsers_->where('id_sub_departemen',$idApproveRole);
                        $lstDtUsersList = $lstDtUsers_->get()->pluck('id_karyawan')->toArray();
                    }
                    // approve by Karyawan
                    elseif($typeApproveRole=='2')
                    {
                        // tanpa grade
                        $lstDtUsers_->where('id_karyawan',$idApproveRole);
                        $lstDtUsersList = $lstDtUsers_->get()->pluck('id_karyawan')->toArray();
                    }
                    foreach($lstDtUsersList as $b)
                    {
                        array_push($lstDtUsers,$b);
                    }          
                }
            }
            else
            {
                // memakai grade
                $lstDtUsers_->whereIn('id_grade',$gradeDown);
                // approve by Departemen
                if($typeApprove=='0')
                {
                    $lstDtUsers_->where('id_departemen',$idDepartemen);
                }
                // approve by Sub Departemen
                elseif($typeApprove=='1')
                {
                    $lstDtUsers_->where('id_sub_departemen',$idSubDepartemen);
                }
                $lstDtUsers = $lstDtUsers_->get()->pluck('id_karyawan')->toArray();
            }
           return $lstDtUsers;
        } catch (\Exception $ex) {
            return $ex;
        }        
    }
}
