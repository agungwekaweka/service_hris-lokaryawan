<?php

namespace App\Imports;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\CutiMst;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Http\Controllers\GenerateIDController;
use App\Http\Controllers\CutiController;
use Illuminate\Support\Facades\DB;

class ImportCuti implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
  
    public function model(array  $row)
    {
        try
            {
              
                $idKaryawan = $row['id_karyawan'];
                $tahun = $row['tahun'];
                $idCuti = $row['id_cuti'];
                $tipeCuti = $row['tipe_cuti'];
                $cuti = $row['cuti'];
                $jmlCuti = $row['jml_cuti'];
                $sisaCuti = $row['sisa_cuti'];
                $tipeMasaBerlaku = $row['tipe_masa_berlaku'];
                $masaBerlaku = $row['masa_berlaku'];
                $dateStart = $row['date_start'];
                $dateEnd = $row['date_end'];
                $tipeToleransiExpired = $row['tipe_toleransi_expired'];
                $toleransiExpired = $row['toleransi_expired'];
                $dateExpired = $row['date_expired'];
               
                // generate ID
                $c_generateID = new GenerateIDController();
                $idMst = $c_generateID->getIdCutiMst($tipeCuti);

                $data = new CutiMst();
                $data->id_cuti_mst = $idMst;
                $data->id_cuti = $idCuti;
                $data->tahun = $tahun; 
                $data->id_karyawan = $idKaryawan; 
                $data->tipe_cuti = $tipeCuti; 
                $data->cuti = $cuti; 
                $data->jml_cuti = $jmlCuti; 
                $data->sisa_cuti = $sisaCuti; 
                $data->tipe_masa_berlaku = $tipeMasaBerlaku;
                $data->masa_berlaku = $masaBerlaku;
                $data->date_start = $dateStart;
                $data->date_end = $dateEnd;
                $data->tipe_toleransi_expired = $tipeToleransiExpired;
                $data->toleransi_expired = $toleransiExpired;
                $data->date_expired = $dateExpired;
                $data->is_dell = '1';
                $data->save();

            } catch (\Exception $ex) {
                return response()->json([$ex]);
            }
    }
}