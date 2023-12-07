<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_Cuti extends Controller
{
    // Get List Cuti Karyawan
     public function getListMasterCuti(Request $request)
     {
        $tahun = $request->tahun;
        $idCuti = $request->id_cuti;
        $tipeCuti = $request->tipe_cuti;
        $idKaryawan = $request->id_karyawan;

         try
         {
             $data_ = DB::table('cuti_mst')
             ->select(
             'users.departemen',
             'users.sub_departemen',
             'users.grade',
             'users.name',
             'cuti_mst.id_karyawan','cuti_mst.id_cuti_mst','cuti_mst.id_cuti','cuti_mst.tahun','cuti_mst.tipe_cuti','cuti_mst.cuti','cuti_mst.jml_cuti','cuti_mst.sisa_cuti','cuti_mst.tipe_masa_berlaku','cuti_mst.masa_berlaku','cuti_mst.date_start','cuti_mst.date_end')
             ->join('users','users.id_karyawan','cuti_mst.id_karyawan')
             ->where('cuti_mst.is_dell','1');
             if($data_->exists())
             {
                if($tahun !='')
                {
                    $data_->where('cuti_mst.tahun',$tahun);
                }
                if($idCuti !='')
                {
                    $data_->where('cuti_mst.id_cuti',$idCuti);
                }
                if($tipeCuti !='')
                {
                    $data_->where('cuti_mst.tipe_cuti',$tipeCuti);
                }
                if($idKaryawan !='')
                {
                    $data_->where('cuti_mst.id_karyawan',$idKaryawan);
                }
                    $data_->orderBy('users.nik','asc');
                    $data = $data_->get();
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Get Data Master Cuti Successfuly',
                        'data' => $data
                    ]);
             }
             else
             {
                 $result=response()->json([
                     'status' => 'failed',
                     'message' => 'Get Data Master Cuti Not Successfuly',
                 ]);
             }
 
             return $result;
         } catch (\Exception $ex) {
             return $ex;
         }
     }

      // Get List Request Cuti Karyawan
      public function getListRequestCuti(Request $request)
      {
         $tahun = $request->tahun;
         $idCutiMst = $request->id_cuti_mst;
         $idCutiTrn = $request->id_cuti_trn;
         $idCuti = $request->id_cuti;
         $tipeCuti = $request->tipe_cuti;
         $idKaryawan = $request->id_karyawan;
         $tglPengajuan = $request->tgl_pengajuan;
         $status = $request->status;
 
          try
          {
              $data_ = DB::table('cuti_trn')
              ->select(
              'users.departemen',
              'users.sub_departemen',
              'users.grade',
              'users.name',
              'cuti_trn.id_karyawan','cuti_trn.id_cuti_mst','cuti_trn.id_cuti_trn','cuti_trn.id_cuti',
              'cuti_trn.tahun','cuti_trn.tipe_cuti','cuti_trn.cuti','cuti_trn.tanggal',
              'cuti_trn.total_cuti','cuti_trn.keterangan','cuti_trn.tgl_pengajuan','cuti_trn.status','cuti_trn.note')
              ->join('users','users.id_karyawan','cuti_trn.id_karyawan');
              if($data_->exists())
              {
                 if($tahun !='')
                 {
                     $data_->where('cuti_trn.tahun',$tahun);
                 }
                 if($idCutiMst !='')
                 {
                     $data_->where('cuti_trn.id_cuti',$idCutiMst);
                 }
                 if($idCutiTrn !='')
                 {
                     $data_->where('cuti_trn.id_cuti',$idCutiTrn);
                 }
                 if($idCuti !='')
                 {
                     $data_->where('cuti_trn.id_cuti',$idCuti);
                 }
                 if($tipeCuti !='')
                 {
                     $data_->where('cuti_trn.tipe_cuti',$tipeCuti);
                 }
                 if($idKaryawan !='')
                 {
                     $data_->where('cuti_trn.id_karyawan',$idKaryawan);
                 }
                 if($tglPengajuan !='')
                 {
                     $data_->where('cuti_trn.tgl_pengajuan',$tglPengajuan);
                 }
                 if($status !='')
                 {
                     $data_->where('cuti_trn.status',$status);
                 }
                    $data_->orderBy('users.nik','asc');
                    $data = $data_->get();
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Get Data Master Cuti Successfuly',
                        'data' => $data
                    ]);
              }
              else
              {
                  $result=response()->json([
                      'status' => 'failed',
                      'message' => 'Get Data Master Cuti Not Successfuly',
                  ]);
              }
  
              return $result;
          } catch (\Exception $ex) {
              return $ex;
          }
      }

}
