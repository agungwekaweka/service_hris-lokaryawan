<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\KomplementMst;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Http\Controllers\UsersController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportUpdateRoleApprove implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
  
    public function model(array $row)
    {
        try
        {
            $nik_ = $row['nik'];
            $approve_ = $row['approve'];
            $typeApprove_ = $row['type_approve'];
          
            $idKaryawan_ ='';
            $dtUsers = DB::table('users')
            ->select('id_karyawan')
            ->where('nik',$nik_)
            ->first();
         
            $idKaryawan_ = $dtUsers->id_karyawan;
          
            $usersController = new UsersController();
            $result['Update_AksesApprove'] = $usersController->updateAksesApprove($idKaryawan_,$approve_,$typeApprove_);
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
}
