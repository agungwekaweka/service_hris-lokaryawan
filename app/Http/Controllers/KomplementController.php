<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KomplementMst;

class KomplementController extends Controller
{
    public function insertKomplemenMst($idKomplement, $tahun,$idKaryawan,$tipeKomplement,$qty)
    {
        try {
            // cek data
            $dt = DB::table('komplement_mst')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('id_komplement',$idKomplement);
            if($dt->exists())
            {
                // data sudah ada
                
            }
            else
            {
                    $data = new KomplementMst();
                    $data->id_komplement = $idKomplement;
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_komplement = $tipeKomplement; 
                    $data->sisa_komplement = $qty; 
                    $data->date_start = $tahun.'-01-01';
                    $data->date_end = $tahun.'-12-31';
                    $data->is_dell = '1'; 
                    $data->save();
            }

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertKomplemenTrn($idKomplement,$idKaryawan,$tanggal,$tipeKomplement,$komplement,$hargaNormal,$hargaKomplement,$kodeBooking,$keterangan,$reff1,$reff2,$reff3)
    {
        try {

            $data = new KomplementTrn();
            $data->id_komplement = $idKomplement;
            $data->id_karyawan = $idKaryawan; 
            $data->tanggal = $tanggal; 
            $data->tipe_komplement = $tipeKomplement; 
            $data->komplement = $sisaKomplement; 
            $data->harga_normal = $sisaKomplement; 
            $data->harga_komplement = $sisaKomplement; 
            $data->kode_booking = $sisaKomplement; 
            $data->keterangan = $sisaKomplement; 
            $data->reff_1 = $sisaKomplement; 
            $data->reff_2 = $sisaKomplement; 
            $data->reff_3 = $sisaKomplement; 
            $data->is_dell = '1'; 
            $data->save();
            
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getTypeMasterKomplemen()
    {
        $data = DB::table('master_komplement')
        ->select('id_komplement','komplement','qty')
        ->where('is_dell','1')
        ->get();
        return $data;
    }

    // get request cuti pending 
    // public function getNotifApprove($idKaryawan_)
    // {
    //     $idKaryawan = $idKaryawan_;

    //     try
    //     {
    //         $data_ = DB::table('cuti_trn')
    //         ->select('cuti_trn.id',
    //         'cuti_trn.id_cuti',
    //         'cuti_trn.tahun',
    //         'cuti_trn.id_karyawan',
    //         'cuti_trn.cuti',
    //         'cuti_trn.tanggal',
    //         'cuti_trn.keterangan',
    //         'cuti_trn.tgl_pengajuan')
    //         ->where('cuti_trn.status','0')
    //         ->orderBy('cuti_trn.tgl_pengajuan','asc');
    //         if($data_->exists())
    //         {
    //             $result = $data_->get();
    //         }
    //         else
    //         {
    //             $result = 'ID Karyawan Tidak Ditemukan';
    //         }

    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    public function getKomplemen(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $tahun = $request->tahun;
        try
        {
            $data_ = DB::table('komplement_mst')
            ->select('id_komplement','tahun','id_karyawan','tipe_komplement','sisa_komplement','date_start','date_end')
            ->where('is_dell','1')
            ->where('id_karyawan',$idKaryawan)
            ->where('tahun',$tahun)
            ->orderBy('id_komplement','asc');
            if($data_->exists())
            {
                $data = $data_->get();
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Komplemen Successfuly',
                    'data' => $data
                ]);
            }
            else
            {
                $data = 'ID Karyawan Tidak Ditemukan';
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Get Data Komplemen Not Successfuly',
                    'data' => $data
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
