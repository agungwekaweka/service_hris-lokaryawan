<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportCuti;

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

        // Get List Request Cuti Karyawan Approve not Available
        public function getListRequestCutiApproveNotAvailable(Request $request)
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

     //  menambahkan master Cuti Tahunan karyawan yg sudah memenuhi syarat
     public function updateMasterCutiTahunan()
     {
        try
        {
        
             // get service API
             $typeService = 'get_karyawan_status_cuti';
             $json_data = new API_Guzzle();
             $data_jsonDecode = $json_data->getServiceLokaHR($typeService);
        
            // cek status API
            if($data_jsonDecode->status=='success')
            {
                // Karyawan Active
                $lstCutiTahunanActive = $data_jsonDecode->CutiActive;
                $tahun = Carbon::now()->format('Y');
           
                foreach($lstCutiTahunanActive as $x)
                {
                    // fill data
                    $idKaryawan = $x->id_absen;
                    $masaKerja = $x->masa_kerja;
                    $doj = $x->doj;
              
                    $carbonDate = Carbon::parse($doj);
                    $formattedDate = $tahun.'-'.$carbonDate->format('m-d'); // Format MM-DD
                    $today = Carbon::today(); // Tanggal sekarang
                    $compareDate = Carbon::parse($formattedDate); // Tanggal yang akan dicek

                    $isAfter = $today->greaterThan($compareDate); // Cek
                
                    // cek data double
                    $cutiMst_ = DB::table('cuti_mst')
                    ->select('id')
                    ->where('id_karyawan',$idKaryawan)
                    ->where('tahun', $tahun)
                    ->where('tipe_cuti','CT');

                    if($cutiMst_->exists())
                    {
                        continue;
                    }
      
                    if($isAfter==true)
                    {
                        // get tipe Cuti
                        $c_cuti = new CutiController();
                        $lstMstCuti = $c_cuti->getTypeMasterCuti();
                        $i=1;

                        // fill data
                        $jmlHari=0;

                        // Tanggal pertama
                        $doj = DB::table('users')
                        ->select('doj')
                        ->where('id_karyawan',$idKaryawan)
                        ->first();
                        $dateDoj = $doj->doj;
                        $date1 = Carbon::parse($dateDoj);
                        // Tanggal kedua
                        $now = Carbon::now()->format('Y-m-d');
                        $date2 = Carbon::parse($now);
                        // Menghitung selisih dalam tahun antara kedua tanggal
                        $diffInMonth = $date1->diffInMonths($date2);
                        
                        foreach($lstMstCuti as $v)
                        {
                            $idCuti = $v->id_cuti;
                            $tipeCuti = $v->tipe_cuti;
                            $cuti = $v->cuti;
                            $jmlHari = $v->jml_hari;

                            if($diffInMonth>=36)
                            {
                                $jmlHari = $jmlHari+2;
                            }
                            $masaBerlaku = $v->masa_berlaku;
                            $tipeMasaBerlaku = $v->tipe_masa_berlaku;
                            $tglBerlaku = $formattedDate;
                         
                            // insert Master Cuti
                            // generate ID
                            $c_generateID = new GenerateIDController();
                            $idMst = $c_generateID->getIdCutiMst($tipeCuti);
    
                            $c_cuti = new CutiController();
                            $result_[$i]['insert_masterCutiDB'] = $c_cuti->insertCutiMst($idMst,$idCuti, $tahun,$idKaryawan,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku);
                            $i++;
                        }
                    }
                }
            }
            else
            {
                $result_['error_service'] ='getServiceLokaHR';
            }
            return $result_;
        } catch (\Exception $ex) {
            return $ex;
         }
     }

     public function importDataMasterCuti(Request $request)
     {
        try
        {
	        // validasi
            $this->validate($request, [
                'file' => 'required|mimes:csv,xls,xlsx'
            ]);
     
		    // menangkap file excel
		    $file = $request->file('file');
     
		    // membuat nama file unik
		    $nama_file = rand().$file->getClientOriginalName();
          
		    // Excel::import(new karyawanImport, 'http://10.10.10.9:8099/storage/'.$nama_file);
            Excel::import(new ImportCuti,$file);
     
            $result=response()->json([
                'status' => 'success',
                'message' => 'Import Data Master Komplement Successfuly'
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
     }

     public function hrUpdateActionCuti(Request $request)
     {
        try
        {
            $idCutiTrn = $request->id_cuti_trn;
            $status = $request->status;
            $note = $request->note;
            $idKaryawanApprove = $request->id_karyawan_approve;
            try {
                $tglApprove = Carbon::now()->format('Y-m-d H:i:s');

                $request=[];
                $request['id_cuti_trn'] = $idCutiTrn;
                $request['status'] = $status;
                $request['note'] = $note;
                $request['id_karyawan_approve'] = $idKaryawanApprove;

                $req = $this->actionUpdateRequestCuti($request);
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Update Action Cuti Successfuly',
                    'data' => $req
                ]);
    
                return $result;
            } catch (\Exception $ex) {
                return $ex;
            }
        } catch (\Exception $ex) {
            return $ex;
        }
     }

     private function actionUpdateRequestCuti($request)
     {
        try
        {
            // get data cuti TRN by ID Trn
            $c_cutiController = new CutiController();
            $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);
        
            $idMst = $result_['get_dataCutiTRN']->id_cuti_mst;
            $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
            $nip = $result_['get_dataCutiTRN']->nik;
            $name = $result_['get_dataCutiTRN']->name;
            $telephone = $result_['get_dataCutiTRN']->no_telephone;
            $idCuti = $result_['get_dataCutiTRN']->id_cuti;
            $tahun = $result_['get_dataCutiTRN']->tahun;
            $cuti = $result_['get_dataCutiTRN']->cuti;
            $tanggal =$result_['get_dataCutiTRN']->tanggal;
            $keterangan = $result_['get_dataCutiTRN']->keterangan;

            // jika aprove ditolak langsung update master cuti TRN
            if($status=='2')
            {
                // update master cuti
                $c_cutiController = new CutiController();
                // status 0=pending, 1=approve, 2=reject
                $result_['update_actionCuti'] = $c_cutiController->updateActionCutiTrn($idCutiTrn,$status,$note);
            
                // update data master cuti
                $c_cuti = new CutiController();
                $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$idMst,$idCutiTrn,$idCuti);

                // sent whatsapp message
                $c_sentWaController = new SentWhatsappController();
                $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCutiCancel($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
            }
            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
     }
}
