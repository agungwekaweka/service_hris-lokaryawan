<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\KomplementMst;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportKomplement implements ToModel,WithHeadingRow
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
              
                $idKaryawan = $row['id_absen'];
                $tahun = $row['tahun'];
                $jmlKomplementFull = $row['jml_komplement_full'];
                $sisaKomplementFull = $row['sisa_komplement_full'];
                $jmlKomplementSetengah = $row['jml_komplement_setengah'];
                $sisaKomplementSetengah = $row['sisa_komplement_setengah'];
                $tanggalAktif = $row['tanggal_aktif'];
                $tanggalExpired = $row['tanggal_expired'];
              
                // komplement Full
                DB::table('komplement_mst')
                ->where('id_komplement','=','40')
                ->where('tahun','=',$tahun)
                ->where('id_karyawan','=',$idKaryawan)
                ->update([
                    'jml_komplement' => $jmlKomplementFull,
                    'sisa_komplement' => $sisaKomplementFull,
                    'date_start' => $tanggalAktif,
                    'date_end'=>$tanggalExpired
                    ]);

                // komplement setengah
                DB::table('komplement_mst')
                ->where('id_komplement','=','288')
                ->where('tahun','=',$tahun)
                ->where('id_karyawan','=',$idKaryawan)
                ->update([
                    'jml_komplement' => $jmlKomplementSetengah,
                    'sisa_komplement' => $sisaKomplementSetengah,
                    'date_start' => $tanggalAktif,
                    'date_end'=>$tanggalExpired
                    ]);
            } catch (\Exception $ex) {
                return response()->json([$ex]);
            }
    }
}