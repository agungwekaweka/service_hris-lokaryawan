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

    // mengambil data karyawan approval di atas level grade karyawan  --------------------notice--------------
    // type role 0= role untuk Cuti;
    // type role 1= role untuk lembur;
    public function getGradeLvUp($idKaryawan,$typeRole)
    {
         // data user login
         $c_users = new UsersController();
         $dtUsers = $c_users->getData($idKaryawan);
      
         if($dtUsers !=null)
         {
            $idDepartemen = $dtUsers->id_departemen;
            $idSubDepartemen = $dtUsers->id_sub_departemen;
            $idGrade = $dtUsers->id_grade;
            $approveLvUp = $dtUsers->approve_level_up;
            $typeApprove = $dtUsers->type_approve;
   
            $c_gradeController = new GradeController();
            $dtGradeDsc = $c_gradeController->getTypeMasterGrade('desc');
            $gradeUp = $c_gradeController->getLevelGrade($dtGradeDsc,$idGrade,$approveLvUp);

            $request['id_departemen'] = $idDepartemen;
            $request['id_sub_departemen'] = $idSubDepartemen;
            $request['id_role_approve'] = $typeApprove;
            $request['grade_up'] = $gradeUp;
            $request['type_role'] = $typeRole;
         
            $x = $data = $this->cekApprove($request);

           return $x;
         }
         else
         {
            return null;
         }
    }

    private function cekApprove($request)
    {
        $idDepartemen = $request['id_departemen'];
        $idSubDepartemen = $request['id_sub_departemen'];
        $idRoleApprove = $request['id_role_approve'];
        $gradeUp = $request['grade_up'];
        $typeRole = $request['type_role'];

        $qry = DB::table('role_approve')
        ->select(
            DB::raw('(select departemen from users where users.id_karyawan=role_approve.pic limit 1) as departemen'),
            DB::raw('(select sub_departemen from users where users.id_karyawan=role_approve.pic limit 1) as sub_departemen'),
            'role_approve.pic as id_karyawan',
            'role_approve.id_grade as id_grade',
            DB::raw('(select grade from users where users.id_karyawan=role_approve.pic limit 1) as grade'),
            DB::raw('(select name from users where users.id_karyawan=role_approve.pic limit 1) as name'),
            DB::raw('(select no_telephone from users where users.id_karyawan=role_approve.pic limit 1) as no_telephone')
            );
        if($idRoleApprove!='')
        {
            $qry->where('role_approve.id_role_approve',$idRoleApprove);
        }
        if($idDepartemen!='')
        {
            $qry->where('role_approve.id_departemen',$idDepartemen);
        }
        if($idSubDepartemen!='')
        {
            $qry->where('role_approve.id_sub_departemen',$idSubDepartemen);
        }
        $qry->where('role_approve.type_role',$typeRole);
        $qry->whereIn('role_approve.id_grade',$gradeUp);
        $qry->orderBy('role_approve.ord','desc'); 
        $qry_ = $qry->get();
        return $qry_;
    }
    // end------------------------
}
