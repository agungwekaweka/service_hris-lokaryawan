<?php

namespace App\Imports;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\GenerateIDController;
use App\Http\Controllers\KomplementGuest;

class ImportGuest implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array  $row)
    {
        try
            {
                // declare variable
                $idUsers=''; $nik=''; $name=''; $grade=''; $email=''; $telephone='';

                // generate ID Guest
                $c_generateID = new GenerateIDController();
                $idUsers = $c_generateID->getIdGuest();
            
                if(isset($row['nik'])) {$nik = $row['nik'];}
                if(isset($row['nama'])) {$name = $row['nama'];}
                if(isset($row['jabatan'])) {$grade = $row['jabatan'];}
                if(isset($row['email'])) {$email = $row['email'];}
                if(isset($row['telephone'])) {$telephone = $row['telephone'];}

                
                // insert 
                $requestKomplementGuest =[];
                $requestKomplementGuest['id_users']=$idUsers;
                $requestKomplementGuest['nik']=$nik;
                $requestKomplementGuest['name']=$name;
                $requestKomplementGuest['grade']=$grade;
                $requestKomplementGuest['email']=$email;
                $requestKomplementGuest['telephone']=$telephone;

                $komplementGuest = new KomplementGuest();
                $komplementGuest->insertGuestUsers($requestKomplementGuest);
            } catch (\Exception $ex) {
                return response()->json([$ex]);
            }
    }
}