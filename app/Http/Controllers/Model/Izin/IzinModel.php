<?php

namespace App\Http\Controllers\Model\Izin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

use App\Http\Controllers\GenerateIDController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SentWhatsappController;
use App\Http\Controllers\ClassUploadImageClass;
use App\Http\Controllers\API_Guzzle;

use App\Http\Controllers\Class_DB\Class_Izin;
use App\Http\Controllers\Class_DB\Class_IzinLampiran;
use App\Http\Controllers\Class_DB\Class_IzinApproveHistory;

class IzinModel
{
    public function getIzin($request)
    {
        try
        {
          
            $result=[];
            $classDB = new Class_Izin();
            $resultClassDB = $classDB->show($request);
          
            $result = $resultClassDB['data'];
          
            if (isset($request['id_izin']) && $request['id_izin']!='') 
            {
                $result=[];
                $result['get_izin'] = $resultClassDB['data'];

                $classDB = new Class_IzinLampiran();
                $resultClassDB = $classDB->show($request);
                $result['get_lampiran'] = $resultClassDB['data'];
            
                $classDB = new Class_IzinApproveHistory();
                $resultClassDB = $classDB->show($request);
                $result['get_approveHistory'] = $resultClassDB['data'];
            }
    
            return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
     
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function getCountPermission($request) // custom
    {
        try
        {
            $result=[];
            $idPeriode =''; $idDepartemen=''; $idSubDepartemen=''; $idKaryawan=''; $nip=''; $type=''; $years='';
            if (isset($request['id_periode']) && $request['id_periode']!='' ) {$idPeriode = $request['id_periode'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];}
            if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
            
          
            $data_ = DB::table('izin_mst')
            ->select('id_departemen','departemen','id_sub_departemen','sub_departemen','id_karyawan','nip','name','id_periode',
            DB::raw('(select count(x.id) from izin_mst x 
            where x.id_karyawan = izin_mst.id_karyawan 
            and x.id_periode= izin_mst.id_periode and x.status =1 
            and x.type not in (5,6) limit 1) as total_izin'))
            ->where('izin_mst.id_periode',$idPeriode);
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($nip!='')
            {
                $data_->where('nip',$nip);
            }
            if($type!='')
            {
                $data_->where('type',$type);
            }
            if($years!='')
            {
                $data_->where('years',$years);
            }
    
            if($data_->exists())
            {
                $data_->groupBy('id_karyawan');
                $data = $data_->get();
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $data
                ];
            }
            else
            {
                $data=0;
                return [
                    'success' => true,
                    'message' => 'Data Not Found',
                    'data' => $data
                ];
            }
            return $data;
        } catch (\Exception $ex) {
       
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function insertIzin($request)
    {
        try
        {
            DB::beginTransaction();
            $result =[];
            // Validate the incoming request data
            $request->validate([
                'id_departemen'                     => 'required|string',             // Required
                'departemen'                        => 'required|string',             // Required
                'id_sub_departemen'                 => 'required|string',             // Required
                'sub_departemen'                    => 'required|string',             // Required
                'id_karyawan'                       => 'required|string',             // Required
                'nip'                               => 'required|string',             // Required
                'name'                              => 'required|string',             // Required
                'id_periode'                        => 'required|string',             // Required
                'date_jadwal'                       => 'required|string',             // Required
                'type'                              => 'required|string',             // Required
                'jadwal_pulang'                     => 'required|string',             // Required
                'jadwal_masuk'                      => 'required|string',             // Required
                'perbaikan_absen_masuk'             => 'required|string',             // Required
                'perbaikan_absen_pulang'            => 'required|string',             // Required
                'note'                              => 'required|string',             // Required
            ]);

            $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; $idKaryawan=''; $nip=''; $name='';$idPeriode=''; $dateJadwal=''; $type=''; 
            $jadwalPulang=''; $jadwalMasuk=''; $perbaikanAbsenMasuk=''; $perbaikanAbsenPulang='';$jamIzinKeluar=''; $jamIzinPulang=''; $note=''; $lampiran='';

            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];}
            if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
            if (isset($request['id_periode']) && $request['id_periode']!='' ) {$idPeriode = $request['id_periode'];}
            if (isset($request['date_jadwal']) && $request['date_jadwal']!='' ) {$dateJadwal = $request['date_jadwal'];}
            if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
            if (isset($request['jadwal_pulang']) && $request['jadwal_pulang']!='' ) {$jadwalPulang = $request['jadwal_pulang'];}
            if (isset($request['jadwal_masuk']) && $request['jadwal_masuk']!='' ) {$jadwalMasuk = $request['jadwal_masuk'];}
            if (isset($request['perbaikan_absen_masuk']) && $request['perbaikan_absen_masuk']!='' ) {$perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];}
            if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];}
            if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];}
            if (isset($request['jam_izin_keluar']) && $request['jam_izin_keluar']!='' ) {$jamIzinKeluar = $request['jam_izin_keluar'];}
            if (isset($request['jam_izin_pulang']) && $request['jam_izin_pulang']!='' ) {$jamIzinPulang = $request['jam_izin_pulang'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
            if (isset($request['lampiran']) && $request['lampiran']!='' ) {$lampiran = $request['lampiran'];}
        
            if($type != '5' && $type != '6') // 5 = sakit; 6=keluar urusan kantor
            {
                // cek sisa izin
                $requestModule =[];
                $requestModule['id_periode'] = $idPeriode;
                $requestModule['id_karyawan'] = $idKaryawan;
                $resultPrivateFunction = $this->getCountPermission($requestModule);
           
                if(!$resultPrivateFunction['success'])
                {
                    DB::rollBack();
                    return $resultPrivateFunction;
                }
                if($resultPrivateFunction['message']=='Data Not Found')
                {
                    $resultCountPermission=0;
                }
                else
                {
                    // first 
                    $resultCountPermission=0;
                    $resultCountPermission = $resultPrivateFunction['data'][0]->total_izin;
                }

                if($resultCountPermission>=5)
                {
                    return [
                        'success' => false,
                        'message' => 'Request Perbaikan Absen Sudah Lebih dari 5x',
                        'data'=> 'Request Perbaikan Absen Sudah Lebih dari 5x'
                    ]; 
                }
            }
            
            // generate ID
            $requestClassGenerate=[];
            $requestClassGenerate['tipe_izin'] = $type;
            $requestClassGenerate['id_karyawan'] = $idKaryawan;
            $classGenerate = new GenerateIDController();
            $idIzin = $classGenerate->getIDIzin($requestClassGenerate);
        
            $requestModule=[];
            $requestModule['id_izin'] = $idIzin;
            $requestModule['id_departemen'] = $idDepartemen;
            $requestModule['departemen'] = $departemen;
            $requestModule['id_sub_departemen'] = $idSubDepartemen;
            $requestModule['sub_departemen'] = $subDepartemen;
            $requestModule['id_karyawan'] = $idKaryawan;
            $requestModule['nip'] = $nip;
            $requestModule['name'] = $name;
            $requestModule['id_periode'] = $idPeriode;
            $requestModule['date_jadwal'] = $dateJadwal;
            $requestModule['type'] = $type;
            $requestModule['jadwal_pulang'] = $jadwalPulang;
            $requestModule['jadwal_masuk'] = $jadwalMasuk;
            $requestModule['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
            $requestModule['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
            $requestModule['jam_izin_keluar'] = $jamIzinKeluar;
            $requestModule['jam_izin_pulang'] = $jamIzinPulang;
            $requestModule['status'] = '0'; // status draft = 0
            $requestModule['note'] = $note;
            $requestModule['date_created'] = Carbon::now()->format('Y-m-d');
            $requestModule['years'] = Carbon::now()->format('Y');
       
            $classDB = new Class_Izin();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_izin'] = $resultClassDB['data'];

            if($lampiran!='') {
                // call class upload Image
                $c_uploadImage = new ClassUploadImageClass();
                $urlPathImgLampiran = $c_uploadImage->processImageLampiranIzin($request->file('lampiran'),$idIzin);
               
                // insert class lampiran
                $requestModule = []; 
                $requestModule['id_izin'] = $idIzin;
                $requestModule['tahun'] = Carbon::now()->format('Y');
                $requestModule['url'] = $urlPathImgLampiran;

                $classDB = new Class_IzinLampiran();
                $resultClassDB = $classDB->insert($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['insert_lampiran'] = $resultClassDB['data'];
            }    

            // get list approve up Level
            $c_grade = new GradeController();
            $typeRole = '0'; // 0 digunakan untuk role Cuti
            $lstApproveGradeUp = $c_grade->getGradeLvUp($idKaryawan,$typeRole);
        
            if($lstApproveGradeUp!=null)
            {
                $firstLoad=true;
                foreach($lstApproveGradeUp as $v)
                {
                    // 0=pending, 1=approve, 2=reject
                    $idGrade = $v->id_grade;
                    $level = $v->level;
                    $departemen = $v->departemen;
                    $subDepartemen = $v->sub_departemen;
                    $idKaryawanApprove = $v->id_karyawan;
                    $grade = $v->grade;
                    $name = $v->name;
                    $telephone = $v->no_telephone;
                    // $telephone = '6285941304991';
                    $tglApprove = '0000-00-00';

                    if($firstLoad==true)
                    {
                        // sent whatsapp message
                        $requestWhatsapp=[];
                        $requestWhatsapp['id_karyawan'] = $idKaryawan;
                        $requestWhatsapp['telephone'] = $telephone;
                        $requestWhatsapp['id_izin'] = $idIzin;
                        $requestWhatsapp['type_izin'] = $type;
                        $requestWhatsapp['tanggal_izin'] = $dateJadwal;
                        $requestWhatsapp['jadwal_masuk'] = $jadwalMasuk;
                        $requestWhatsapp['jadwal_pulang'] = $jadwalPulang;
                        $requestWhatsapp['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
                        $requestWhatsapp['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
                        $requestWhatsapp['note'] = $note;
                  
                        $c_sentWaController = new SentWhatsappController();
                        $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveIzin($requestWhatsapp);
                    }
                    $firstLoad=false;

                    //insert approve history
                    $requesClassDB = [];
                    $requesClassDB['id_izin'] = $idIzin;
                    $requesClassDB['status'] = '0';
                    $requesClassDB['telephone'] = $telephone;
                    $requesClassDB['id_karyawan_approve'] = $idKaryawanApprove;
                    $requesClassDB['name'] = $name;
                    $requesClassDB['departemen'] = $departemen;
                    $requesClassDB['sub_departemen'] = $subDepartemen;
                    $requesClassDB['grade'] = $grade;
                    $requesClassDB['tgl_approve'] = $tglApprove;
                    $requesClassDB['note'] = '';
                    
                    $classDB = new Class_IzinApproveHistory();
                    $resultClassDB = $classDB->insert($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_approveHistory'] = $resultClassDB['data'];
                    break;
                }
            }
            else
            {
                return [
                    'success' => false,
                    'message' => 'Gagal Request Izin',
                    'data'=> 'ID Karyawan : '. $idKaryawan.' Tidak memiliki Atasan untuk Approve'
                ];
            }
        
            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];

        } catch (\Exception $ex) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }
    
    public function updatedIzin($request)
    {
        try
        {
            DB::beginTransaction();
            $result =[];
            // Validate the incoming request data
            $request->validate([
                'id'                            => 'required|string',             // Required
            ]);

            $id=''; $idIzin=''; $dateJadwal=''; $jadwalPulang=''; $jadwalMasuk=''; $perbaikanAbsenMasuk=''; $perbaikanAbsenPulang=''; 
            $jamIzinKeluar=''; $jamIzinPulang=''; $note=''; $lampiran=''; $status=''; $idKaryawanApprove=''; 

            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
            if (isset($request['date_jadwal']) && $request['date_jadwal']!='' ) {$dateJadwal = $request['date_jadwal'];}
            if (isset($request['jadwal_pulang']) && $request['jadwal_pulang']!='' ) {$jadwalPulang = $request['jadwal_pulang'];}
            if (isset($request['jadwal_masuk']) && $request['jadwal_masuk']!='' ) {$jadwalMasuk = $request['jadwal_masuk'];}
            if (isset($request['perbaikan_absen_masuk']) && $request['perbaikan_absen_masuk']!='' ) {$perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];}
            if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];}
            if (isset($request['jam_izin_keluar']) && $request['jam_izin_keluar']!='' ) {$jamIzinKeluar = $request['jam_izin_keluar'];}
            if (isset($request['jam_izin_pulang']) && $request['jam_izin_pulang']!='' ) {$jamIzinPulang = $request['jam_izin_pulang'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
            if (isset($request['lampiran']) && $request['lampiran']!='' ) {$lampiran = $request['lampiran'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$idKaryawanApprove = $request['id_karyawan_approve'];}
          
            if($status=='9') // reject
            {
                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['status'] = '9'; // approve; 2=reject; 0=draft
                $requestModule['reff_upload'] = 'Successfuly';

                $classDB = new Class_Izin();
                $resultClassDB = $classDB->update($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_izin'] = $resultClassDB['data'];

                // get data master
                $requesClassDB=[];
                $requesClassDB['id_izin'] = $idIzin;
                $classDB = new Class_Izin();
                $resultClassDB = $classDB->show($requesClassDB);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $dataIzin = $resultClassDB['data'][0];
               
                // sent whatsapp message
                $requestWhatsapp=[];
                $requestWhatsapp['id_karyawan'] = $dataIzin->id_karyawan;
                $requestWhatsapp['id_izin'] = $idIzin;
                $requestWhatsapp['type_izin'] = $dataIzin->type;;
                $requestWhatsapp['tanggal_izin'] = $dataIzin->date_jadwal;
                $requestWhatsapp['jadwal_masuk'] = $dataIzin->jadwal_masuk;
                $requestWhatsapp['jadwal_pulang'] = $dataIzin->jadwal_pulang;
                $requestWhatsapp['perbaikan_absen_masuk'] = $dataIzin->perbaikan_absen_masuk;
                $requestWhatsapp['perbaikan_absen_pulang'] = $dataIzin->perbaikan_absen_pulang;
                $requestWhatsapp['note'] = $dataIzin->note;

                $c_sentWaController = new SentWhatsappController();
                $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveIzinReject($requestWhatsapp);
                
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Insert successfuly',
                    'data'=> $result
                ];
            }
            if($status!='')
            {
                // get data master
                $requesClassDB=[];
                $requesClassDB['id_izin'] = $idIzin;
                $classDB = new Class_Izin();
                $resultClassDB = $classDB->show($requesClassDB);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $dataIzin = $resultClassDB['data'][0];
                $type = $dataIzin->type;
                $idKaryawan = $dataIzin->id_karyawan;
                $idPeriode = $dataIzin->id_periode;

                if($type != '5' && $type != '6') // 5 = sakit; 6=keluar urusan kantor
                {
                    // cek sisa izin
                    $requestModule =[];
                    $requestModule['id_periode'] = $idPeriode;
                    $requestModule['id_karyawan'] = $idKaryawan;
                    $resultPrivateFunction = $this->getCountPermission($requestModule);
               
                    if(!$resultPrivateFunction['success'])
                    {
                        DB::rollBack();
                        return $resultPrivateFunction;
                    }
                    if($resultPrivateFunction['message']=='Data Not Found')
                    {
                        $resultCountPermission=0;
                    }
                    else
                    {
                        // first 
                        $resultCountPermission=0;
                        $resultCountPermission = $resultPrivateFunction['data'][0]->total_izin;
                    }
    
                    if($resultCountPermission>=5)
                    {
                        return [
                            'success' => false,
                            'message' => 'Request Perbaikan Absen Sudah Lebih dari 5x',
                            'data'=> 'Request Perbaikan Absen Sudah Lebih dari 5x'
                        ]; 
                    }
                } 
              
                // get history approve
                $requestModule=[];
                $requestModule['id_izin'] = $idIzin;
                $requestModule['id_karyawan'] = $idKaryawanApprove;
                $classDB = new Class_IzinApproveHistory();
                $resultClassDB = $classDB->show($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $listApproveHistory = $resultClassDB['data'];

                foreach($listApproveHistory as $v)
                {
                    $idApproveHistory = $v->id;

                    $requestModule=[];
                    $requestModule['id'] = $idApproveHistory;
                    $requestModule['status'] = $status;
                    $requestModule['tgl_approve'] = Carbon::now()->format('Y-m-d H:i:s');
                    $classDB = new Class_IzinApproveHistory();
                    $resultClassDB = $classDB->update($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_approveHistory'] = $resultClassDB['data'];
                    break;
                } 

                // cek apakah masih ada history approve yg belum di setujui
                // get history approve
                $requestModule=[];
                $requestModule['id_izin'] = $idIzin;
                $requestModule['status'] = '0'; 
                $classDB = new Class_IzinApproveHistory();
                $resultClassDB = $classDB->show($requestModule);
            
                if(!$resultClassDB['success'])
                {
                    // selesai semua
                    $requestModule=[];
                    $requestModule['id'] = $id;
                    $requestModule['status'] = '1'; // approve; 9=reject; 0=draft
                    $requestModule['reff_upload'] = 'Successfuly';

                    $classDB = new Class_Izin();
                    $resultClassDB = $classDB->update($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_izin'] = $resultClassDB['data'];

                    // update tanggal di aplikasi lokaHR (API)
                    $requestModule = [];
                    $requestModule['id_izin'] = $idIzin;
                    $classDB = new Class_Izin();
                    $resultClassDB = $classDB->show($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['get_izin'] = $resultClassDB['data'];
               
                    $c_apiGuzzle = new API_Guzzle();
                    $var = 'update_jadwal_izin_karyawan';
     
                 
                    $valueKehadiran = $result['get_izin'][0]->type;
                    $request=[];
                    $request['var'] = $var;
                    $request['id_periode'] = $result['get_izin'][0]->id_periode;
                    $request['nip'] = $result['get_izin'][0]->nip;
                    $request['tanggal'] = $result['get_izin'][0]->date_jadwal;
                    # cek type izin
                    if($valueKehadiran=='1' || $valueKehadiran=='2' || $valueKehadiran=='3' || $valueKehadiran=='4'  || $valueKehadiran=='6' || $valueKehadiran=='7')
                    {
                        $valueKehadiran='0';
                    }
                    if($valueKehadiran=='5') // sakit
                    {
                        $valueKehadiran='5';
                    }
                    if($valueKehadiran=='8') // izin
                    {
                        $valueKehadiran='3';
                    }
                    # end      
                               
                    $request['kehadiran'] = $valueKehadiran;
                    $request['jam_absen'] = $result['get_izin'][0]->jadwal_masuk;
                    $request['jam_pulang'] = $result['get_izin'][0]->jadwal_pulang;
                    $request['jam_kehadiran_karyawan'] = $result['get_izin'][0]->perbaikan_absen_masuk;
                    $request['jam_pulang_karyawan'] = $result['get_izin'][0]->perbaikan_absen_pulang;
                    $request['jam_izin_keluar'] = $result['get_izin'][0]->jam_izin_keluar;
                    $request['jam_izin_pulang'] = $result['get_izin'][0]->jam_izin_pulang;
                    $request['note'] = $result['get_izin'][0]->note;
                    $request['reff'] = 'APP-'. Carbon::now()->format('Y-m-d').'-'.$result['get_izin'][0]->nip;
                    $resultClassDB = $c_apiGuzzle->postServiceIzinLokaHR($request);
               
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_serviceLokaHR'] = $resultClassDB['data'];
                    

                    // get data master
                    $requesClassDB=[];
                    $requesClassDB['id_izin'] = $idIzin;
                    $classDB = new Class_Izin();
                    $resultClassDB = $classDB->show($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $dataIzin = $resultClassDB['data'][0];
                    
                    // sent whatsapp message
                    $requestWhatsapp=[];
                    $requestWhatsapp['id_karyawan'] = $dataIzin->id_karyawan;
                    $requestWhatsapp['id_izin'] = $idIzin;
                    $requestWhatsapp['type_izin'] = $dataIzin->type;;
                    $requestWhatsapp['tanggal_izin'] = $dataIzin->date_jadwal;
                    $requestWhatsapp['jadwal_masuk'] = $dataIzin->jadwal_masuk;
                    $requestWhatsapp['jadwal_pulang'] = $dataIzin->jadwal_pulang;
                    $requestWhatsapp['perbaikan_absen_masuk'] = $dataIzin->perbaikan_absen_masuk;
                    $requestWhatsapp['perbaikan_absen_pulang'] = $dataIzin->perbaikan_absen_pulang;
                    $requestWhatsapp['note'] = $dataIzin->note;
            
                    $c_sentWaController = new SentWhatsappController();
                    $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveIzinDone($requestWhatsapp);
                }
                else
                {
                    $telephone = $resultClassDB['data'][0]->telephone;
                    // get data master
                    $requesClassDB=[];
                    $requesClassDB['id_izin'] = $idIzin;
                    $classDB = new Class_Izin();
                    $resultClassDB = $classDB->show($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $dataIzin = $resultClassDB['data'][0];

                    $requestWhatsapp=[];
                    $requestWhatsapp['id_karyawan'] = $dataIzin->id_karyawan;
                    $requestWhatsapp['telephone'] = $telephone;
                    $requestWhatsapp['id_izin'] = $idIzin;
                    $requestWhatsapp['type_izin'] = $dataIzin->type;
                    $requestWhatsapp['tanggal_izin'] = $dataIzin->date_jadwal;
                    $requestWhatsapp['jadwal_masuk'] = $dataIzin->jadwal_masuk;
                    $requestWhatsapp['jadwal_pulang'] = $dataIzin->jadwal_pulang;
                    $requestWhatsapp['perbaikan_absen_masuk'] = $dataIzin->perbaikan_absen_masuk;
                    $requestWhatsapp['perbaikan_absen_pulang'] = $dataIzin->perbaikan_absen_pulang;
                    $requestWhatsapp['note'] = $dataIzin->note;
                 
                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveIzin($requestWhatsapp);
                }

                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['date_jadwal'] = $dateJadwal;  
                $requestModule['jadwal_pulang'] = $jadwalPulang;
                $requestModule['jadwal_masuk'] = $jadwalMasuk;
                $requestModule['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
                $requestModule['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
                $requestModule['status'] = $status;
                $requestModule['note'] = $note;
           
                $classDB = new Class_Izin();
                $resultClassDB = $classDB->update($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_izin'] = $resultClassDB['data'];  
            }
            else
            {
                // draft
                if($lampiran!='') {
                    // call class upload Image
                    $c_uploadImage = new ClassUploadImageClass();
                    $urlPathImgLampiran = $c_uploadImage->processImageLampiranIzin($request->file('lampiran'),$idIzin);
                   
                    // insert class lampiran
                    $requestModule = []; 
                    $requestModule['id_izin'] = $idIzin;
                    $requestModule['tahun'] = Carbon::now()->format('Y');
                    $requestModule['url'] = $urlPathImgLampiran;
    
                    $classDB = new Class_IzinLampiran();
                    $resultClassDB = $classDB->insert($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_lampiran'] = $resultClassDB['data'];
                } 

                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['date_jadwal'] = $dateJadwal;  
                $requestModule['jadwal_pulang'] = $jadwalPulang;
                $requestModule['jadwal_masuk'] = $jadwalMasuk;
                $requestModule['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
                $requestModule['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
                $requestModule['note'] = $note;
           
                $classDB = new Class_Izin();
                $resultClassDB = $classDB->update($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_izin'] = $resultClassDB['data'];  
            }
        
            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];

        } catch (\Exception $ex) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    // get request Izin pending 
    public function getNotifApprove($request)
    {
         $idKaryawan = $request['id_karyawan'];
         try
         {            
             $data_ = DB::table('izin_approve_history')
             ->select('id','id_izin','id_karyawan_approve')
             ->where('status','0')
             ->where('id_karyawan_approve',$idKaryawan);
     
             if($data_->exists())
             {
                 $result = $data_->get();
             
                 $lstIDTrnOutstanding=null;
                 $countLstOutstanding=0;
                 
                 foreach($result as $x)
                  {
                     $idIzinTrn = $x->id_izin;
                      
                     //  cek apakah first
                     $_firstData = DB::table('izin_approve_history')
                     ->select('id','id_izin','status','id_karyawan_approve')
                     ->where('id_izin',$idIzinTrn)
                     ->orderBy('id','asc')
                     ->groupBy('id_karyawan_approve')
                     ->get();
            
                     $_idKaryawan1=$_firstData[0]->id_karyawan_approve;
                     
                     if($_firstData[0]->status=='0')
                     {
                         if($_idKaryawan1 == $idKaryawan)
                         {
                             if($_firstData[0]->status=='0')
                             {
                                 $lstIDTrnOutstanding[$countLstOutstanding] = $_firstData[0]->id_izin;
                                 $countLstOutstanding++;
                             }
                         }
                     }
 
                     if($_firstData->count()>=2)
                     {
                         $_idKaryawan2=$_firstData[1]->id_karyawan_approve;
                         if($_firstData[0]->status=='1')
                         {   
                             if($_idKaryawan2 == $idKaryawan)
                             {
                                 if($_firstData[1]->status=='0')
                                 {
                                     $lstIDTrnOutstanding[$countLstOutstanding] = $_firstData[1]->id_izin;
                                     $countLstOutstanding++;
                                 }
                             }
                         }
                     }
                  }
               
                 // cek apakah ada data
                 if($lstIDTrnOutstanding!=null)
                 {
                    $data_ = DB::table('izin_mst')
                    ->where('status','0')
                    ->whereIn('id_izin',$lstIDTrnOutstanding)
                    ->orderBy('created_at','asc');
                    if($data_->exists())
                    {
                        $result = $data_->get();
                    }   
                    else
                    {
                        $result = 'Data Tidak Ditemukan'; 
                    }
                 }
                 else
                 {
                     $result = 'Data Tidak Ditemukan'; 
                 }
             }
             else
             {
                 $result = 'Data Tidak Ditemukan';
             }
 
             return $result;
         } catch (\Exception $ex) {
             return $ex;
         }
    }
}
