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

    //  untuk notifikasi karyawan yg mempunyai akses aprove
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

    // mengambil data karyawan approval di atas level grade karyawan 
    public function getGradeLvUp($idKaryawan)
    {
         // data user login
         $c_users = new UsersController();
         $dtUsers = $c_users->getData($idKaryawan);
     
         if($dtUsers !=null)
         {
            $idDepartemen = $dtUsers->id_departemen;
            $idSubDepartemen = $dtUsers->id_sub_departemen;
            $idGrade = $dtUsers->id_grade;
            $grade = $dtUsers->grade;
            $approveLvUp = $dtUsers->approve_level_up;
   
           $c_gradeController = new GradeController();
           $dtGradeDsc = $c_gradeController->getTypeMasterGrade('desc');
           $gradeUp = $c_gradeController->getLevelGrade($dtGradeDsc,$idGrade,$approveLvUp);
           $firstApprove=true;
           foreach($gradeUp as $v)
           {
               // cek sub departemen 
               $data = $this->cekApproveSubDept($v,$idSubDepartemen);
               if($data->exists())
               {
                   $x[]=$data->first();
                   continue;
               }
               // cek data di departemen
               $data = $this->cekApproveDept($v,$idDepartemen);
               if($data->exists())
               {
                   $x[]=$data->first();
                   continue;
               }
   
               // cek data di custom
               // cek type approve = 2 (id_karyawan)
               $data = $this->cekApproveCustom($idKaryawan,$idGrade);
               if($data->exists())
               {
                   $x[]=$data->first();
                   continue;
               }
               // cek type approve = 1 (id_sub_dept)
               $data = $this->cekApproveCustom($idSubDepartemen,$idGrade);
               if($data->exists())
               {
                   $x[]=$data->first();
                   continue;
               }
               // cek type approve = 0 (id_dept)
               $data = $this->cekApproveCustom($idDepartemen,$idGrade);
               if($data->exists())
               {
                   $x[]=$data->first();
                   continue;
               }
           }
           return $x;
         }
         else
         {
            return null;
         }
    }

    private function cekApproveCustom($idApprove,$idGrade)
    {
        $qry = DB::table('role_approve')
        ->select('users.departemen','users.sub_departemen','users.id_karyawan','users.grade','users.name','users.no_telephone')
        ->join('users','users.id_karyawan','role_approve.id_karyawan');
        // ->where('users.id_grade','<>',$idGrade);
        $qry->where('id_approve',$idApprove);
        $qry_ = $qry;
        return $qry_;
    }

    private function getQryUserApprove()
    {
        $qry = DB::table('users')
        ->select('departemen','sub_departemen','id_karyawan','grade','name','no_telephone')
        ->where('is_dell','1')
        ->where('approve','1');
        return $qry;
    }

    private function cekApproveSubDept($idGrade,$idSubDepartemen)
    {
        $qry = $this->getQryUserApprove();
        $qry->where('id_grade',$idGrade);
        $qry->where('id_sub_departemen',$idSubDepartemen);
        $qry_ = $qry;
        return $qry_;
    }

    private function cekApproveDept($idGrade,$idDepartemen)
    {
        $qry = $this->getQryUserApprove();
        $qry->where('id_grade',$idGrade);
        $qry->where('id_departemen',$idDepartemen);
        $qry_ = $qry;
        return $qry_;
    }
    // end------------------------
}
