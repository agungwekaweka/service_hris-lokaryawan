<?php

namespace App\Http\Controllers\Model\Sppd;

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

use App\Http\Controllers\Class_DB\Class_SppdMst;
use App\Http\Controllers\Class_DB\Class_SppdAccommodation;
use App\Http\Controllers\Class_DB\Class_SppdLampiran;
use App\Http\Controllers\Class_DB\Class_SppdApproveHistory;

class SppdModel extends Controller
{
    public function getSppd($request)
    {
        try
        {
          
            $result=[];
            $classDB = new Class_SppdMst();
            $resultClassDB = $classDB->show($request);
          
            $result = $resultClassDB['data'];
          
            if (isset($request['id_sppd']) && $request['id_sppd']!='') 
            {
                $result=[];
                $result['get_sppd'] = $resultClassDB['data'];

                $classDB = new Class_SppdAccommodation();
                $resultClassDB = $classDB->show($request);
                $result['get_accommodation'] = $resultClassDB['data'];

                $classDB = new Class_SppdLampiran();
                $resultClassDB = $classDB->show($request);
                $result['get_lampiran'] = $resultClassDB['data'];
            
                $classDB = new Class_SppdApproveHistory();
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

    public function insertSppd($request)
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
                'grade'                             => 'required|string',             // Required
                'city'                              => 'required|string',             // Required
                'accommodation'                     => 'required|string',             // Required
            ]);

            $idSppd=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; $idKaryawan=''; $nip=''; $name=''; $grade='';
            $city=''; $dateStart=''; $dateFinish=''; $depatureTime=''; $afterWorkTime=''; $longDay=''; $night=''; $total=''; $note=''; $status='';
            $accommodation=''; $lampiran='';

            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];}
            if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
            if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
            if (isset($request['date_start']) && $request['date_start']!='' ) {$dateStart = $request['date_start'];}
            if (isset($request['date_finish']) && $request['date_finish']!='' ) {$dateFinish = $request['date_finish'];}
            if (isset($request['depature_time']) && $request['depature_time']!='' ) {$depatureTime = $request['depature_time'];}
            if (isset($request['after_work_time']) && $request['after_work_time']!='' ) {$afterWorkTime = $request['after_work_time'];}
            if (isset($request['long_day']) && $request['long_day']!='' ) {$longDay = $request['long_day'];}
            if (isset($request['night']) && $request['night']!='' ) {$night = $request['night'];}
            if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['accommodation']) && $request['accommodation']!='' ) {$accommodation = $request['accommodation'];}
            if (isset($request['lampiran']) && $request['lampiran']!='' ) {$lampiran = $request['lampiran'];}

            // generate ID
            $requestClassGenerate=[];
            $requestClassGenerate['id_karyawan'] = $idKaryawan;
            $classGenerate = new GenerateIDController();
            $idSppd = $classGenerate->getIDSppd($requestClassGenerate);
  
            $requestModule=[];
            $requestModule['id_sppd'] = $idSppd;
            $requestModule['id_departemen'] = $idDepartemen;
            $requestModule['departemen'] = $departemen;
            $requestModule['id_sub_departemen'] = $idSubDepartemen;
            $requestModule['sub_departemen'] = $subDepartemen;
            $requestModule['id_karyawan'] = $idKaryawan;
            $requestModule['nip'] = $nip;
            $requestModule['name'] = $name;
            $requestModule['grade'] = $grade;
            $requestModule['city'] = $city;
            $requestModule['date_start'] = $dateStart;
            $requestModule['date_finish'] = $dateFinish;
            $requestModule['depature_time'] = $depatureTime;
            $requestModule['after_work_time'] = $afterWorkTime;
            $requestModule['long_day'] = $longDay;
            $requestModule['night'] = $night;
            $requestModule['total'] = $total;
            $requestModule['note'] = $note;
            $requestModule['status'] = '0';
            $requestModule['years'] = Carbon::now()->format('Y');

            $classDB = new Class_SppdMst();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_sppdMst'] = $resultClassDB['data'];

            // if($lampiran!='') {
            //     // call class upload Image
            //     $c_uploadImage = new ClassUploadImageClass();
            //     $urlPathImgLampiran = $c_uploadImage->processImageLampiranSppd($request->file('lampiran'),$idIzin);
               
            //     // insert class lampiran
            //     $requestModule = []; 
            //     $requestModule['id_sppd'] = $idSppd;
            //     $requestModule['tahun'] = Carbon::now()->format('Y');
            //     $requestModule['url'] = $urlPathImgLampiran;

            //     $classDB = new Class_SppdLampiran();
            //     $resultClassDB = $classDB->insert($requestModule);
            //     if(!$resultClassDB['success'])
            //     {
            //         DB::rollBack();
            //         return $resultClassDB;
            //     }
            //     $result['insert_lampiran'] = $resultClassDB['data'];
            // }    

            if($accommodation!='')
            {
                $jsonDecodeAccommodation = json_decode($accommodation);
                foreach($jsonDecodeAccommodation as $v)
                {
                    $idVariable='-'; $variable=''; $value='-'; $qty=0; $subTotal=0;

                    if (isset($v->id_variable) && $v->id_variable!='' ) {$idVariable = $v->id_variable;}
                    if (isset($v->variable) && $v->variable!='' ) {$variable = $v->variable;}
                    if (isset($v->value) && $v->value!='' ) {$value = $v->value;}
                    if (isset($v->qty) && $v->qty!='' ) {$qty = $v->qty;}
                    if (isset($v->sub_total) && $v->sub_total!='' ) {$subTotal = $v->sub_total;}

                    $requesClassDB = [];
                    $requesClassDB['id_sppd'] = $idSppd;
                    $requesClassDB['id_variable'] = $idVariable;
                    $requesClassDB['variable'] = $variable;
                    $requesClassDB['value'] = $value;
                    $requesClassDB['qty'] = $qty;
                    $requesClassDB['sub_total'] = $subTotal;
              
                    $classDB = new Class_SppdAccommodation();
                    $resultClassDB = $classDB->insert($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_accommodation'] = $resultClassDB['data'];
                }
            }
            # approve
            // get list approve up Level (Manager, HR & Legal, General Manager, Manager FA)
            // get Manger
            $approve['Manager'] = DB::table('role_approve')
            ->select('users.name','users.departemen','users.sub_departemen','users.grade','users.no_telephone','users.id_karyawan')
            ->join('users','users.id_karyawan','role_approve.pic')
            ->where('role_approve.id_departemen',$idDepartemen)
            ->where('role_approve.id_sub_departemen',$idSubDepartemen)
            ->where('role_approve.id_grade','LV-002')
            ->first();
     
            //insert approve history
            $requesClassDB = [];
            $requesClassDB['id_sppd'] = $idSppd;
            $requesClassDB['status'] = '0';
            $requesClassDB['telephone'] = $approve['Manager']->no_telephone;
            $requesClassDB['id_karyawan_approve'] = $approve['Manager']->id_karyawan;
            $requesClassDB['name'] = $approve['Manager']->name;
            $requesClassDB['departemen'] = $approve['Manager']->departemen;
            $requesClassDB['sub_departemen'] = $approve['Manager']->sub_departemen;
            $requesClassDB['grade'] = $approve['Manager']->grade;
             
            $classDB = new Class_SppdApproveHistory();
            $resultClassDB = $classDB->insert($requesClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_approveManager'] = $resultClassDB['data'];

            // get data master
            $requesClassDB=[];
            $requesClassDB['id_sppd'] = $idSppd;
            $classDB = new Class_SppdMst();
            $resultClassDB = $classDB->show($requesClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $dataMaster = $resultClassDB['data'][0];

            $telephone=$approve['Manager']->no_telephone;
            $telephone='6285941304991';
            // sent whatsapp message
            $requestWhatsapp=[];
            $requestWhatsapp['id_karyawan'] = $dataMaster->id_karyawan;
            $requestWhatsapp['telephone'] = $telephone;
            $requestWhatsapp['id_sppd'] = $idSppd;
            $requestWhatsapp['city'] = $dataMaster->city;
            $requestWhatsapp['date_start'] = $dataMaster->date_start;
            $requestWhatsapp['date_finish'] = $dataMaster->date_finish;
            $requestWhatsapp['depature_time'] = $dataMaster->depature_time;
            $requestWhatsapp['after_work_time'] = $dataMaster->after_work_time;
            $requestWhatsapp['long_day'] = $dataMaster->long_day;
            $requestWhatsapp['night'] = $dataMaster->night;
            $requestWhatsapp['note'] = $dataMaster->note;
        
            $c_sentWaController = new SentWhatsappController();
            $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveSppd($requestWhatsapp);

            $approve['HR_Legal_Manager'] = DB::table('role_approve')
            ->select('users.name','users.departemen','users.sub_departemen','users.grade','users.no_telephone','users.id_karyawan')
            ->join('users','users.id_karyawan','role_approve.pic')
            ->where('role_approve.id_departemen','DP014')
            ->where('role_approve.id_grade','LV-002')
            ->first();

            $approve['GM'] = DB::table('role_approve')
            ->select('users.name','users.departemen','users.sub_departemen','users.grade','users.no_telephone','users.id_karyawan')
            ->join('users','users.id_karyawan','role_approve.pic')
            ->where('role_approve.id_departemen',$idDepartemen)
            ->where('role_approve.id_sub_departemen',$idSubDepartemen)
            ->where('role_approve.id_grade','LV-001')
            ->first();

            //insert approve history
            $requesClassDB = [];
            $requesClassDB['id_sppd'] = $idSppd;
            $requesClassDB['status'] = '0';
            $requesClassDB['telephone'] = $approve['GM']->no_telephone;
            $requesClassDB['id_karyawan_approve'] = $approve['GM']->id_karyawan;
            $requesClassDB['name'] = $approve['GM']->name;
            $requesClassDB['departemen'] = $approve['GM']->departemen;
            $requesClassDB['sub_departemen'] = $approve['GM']->sub_departemen;
            $requesClassDB['grade'] = $approve['GM']->grade;
             
            $classDB = new Class_SppdApproveHistory();
            $resultClassDB = $classDB->insert($requesClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_approveGM'] = $resultClassDB['data'];

            $approve['FA'] = DB::table('role_approve')
            ->select('users.name','users.departemen','users.sub_departemen','users.grade','users.no_telephone','users.id_karyawan')
            ->join('users','users.id_karyawan','role_approve.pic')
            ->where('role_approve.id_departemen','DP013')
            ->where('role_approve.id_grade','LV-002')
            ->first();
            
            //insert approve history
            $requesClassDB = [];
            $requesClassDB['id_sppd'] = $idSppd;
            $requesClassDB['status'] = '0';
            $requesClassDB['telephone'] = $approve['FA']->no_telephone;
            $requesClassDB['id_karyawan_approve'] = $approve['FA']->id_karyawan;
            $requesClassDB['name'] = $approve['FA']->name;
            $requesClassDB['departemen'] = $approve['FA']->departemen;
            $requesClassDB['sub_departemen'] = $approve['FA']->sub_departemen;
            $requesClassDB['grade'] = $approve['FA']->grade;
             
            $classDB = new Class_SppdApproveHistory();
            $resultClassDB = $classDB->insert($requesClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_approveFA'] = $resultClassDB['data'];
            # end approve

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
    
    public function updatedSppd($request)
    {
        try
        {
            DB::beginTransaction();
            $result =[];
            // Validate the incoming request data
            $request->validate([
                'id'                            => 'required|string',             // Required
            ]);

            $id=''; $idSppd=''; $city=''; $dateStart=''; $dateFinish=''; $depatureTime=''; $afterWorkTime=''; $longDay=''; $night=''; $total=''; 
            $note=''; $status=''; $accommodation=''; $lampiran='';

            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
            if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
            if (isset($request['date_start']) && $request['date_start']!='' ) {$dateStart = $request['date_start'];}
            if (isset($request['date_finish']) && $request['date_finish']!='' ) {$dateFinish = $request['date_finish'];}
            if (isset($request['depature_time']) && $request['depature_time']!='' ) {$depatureTime = $request['depature_time'];}
            if (isset($request['after_work_time']) && $request['after_work_time']!='' ) {$afterWorkTime = $request['after_work_time'];}
            if (isset($request['long_day']) && $request['long_day']!='' ) {$longDay = $request['long_day'];}
            if (isset($request['night']) && $request['night']!='' ) {$night = $request['night'];}
            if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
            if (isset($request['accommodation']) && $request['accommodation']!='' ) {$accommodation = $request['accommodation'];}
            if (isset($request['lampiran']) && $request['lampiran']!='' ) {$lampiran = $request['lampiran'];}
            if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$idKaryawanApprove = $request['id_karyawan_approve'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}

            if($status!='')
            {
                // get history approve
                $requestModule=[];
                $requestModule['id_sppd'] = $idSppd;
                $requestModule['id_karyawan'] = $idKaryawanApprove;
                $requestModule['status'] = '0';
                $classDB = new Class_SppdApproveHistory();
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
                    $classDB = new Class_SppdApproveHistory();
                    $resultClassDB = $classDB->update($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_approveHistory'] = $resultClassDB['data'];
                } 

                // cek apakah masih ada history approve yg belum di setujui
                // get history approve
                $requestModule=[];
                $requestModule['id_sppd'] = $idSppd;
                $requestModule['status'] = '0'; 
                $classDB = new Class_SppdApproveHistory();
                $resultClassDB = $classDB->show($requestModule);
            
                if(!$resultClassDB['success'])
                {
                    // selesai semua
                    $requestModule=[];
                    $requestModule['id'] = $id;
                    $requestModule['status'] = '11'; // approve; 2=reject; 0=draft

                    $classDB = new Class_SppdMst();
                    $resultClassDB = $classDB->update($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_izin'] = $resultClassDB['data'];

                    // update tanggal di aplikasi lokaHR (API)
                    // $requestModule = [];
                    // $requestModule['id_sppd'] = $idSppd;
                    // $classDB = new Class_SppdMst();
                    // $resultClassDB = $classDB->show($requestModule);
                    // if(!$resultClassDB['success'])
                    // {
                    //     DB::rollBack();
                    //     return $resultClassDB;
                    // }
                    // $result['get_sppdMst'] = $resultClassDB['data'];
               
                    // $c_apiGuzzle = new API_Guzzle();
                    // $var = 'update_jadwal_spp_karyawan';
     
                    // $valueKehadiran = $result['get_izin'][0]->type;
                    // $request=[];
                    // $request['var'] = $var;
                    // $request['id_periode'] = $result['get_izin'][0]->id_periode;
                    // $request['nip'] = $result['get_izin'][0]->nip;
                    // $request['tanggal'] = $result['get_izin'][0]->date_jadwal;
                    // $request['kehadiran'] = $valueKehadiran;
                    // $request['jam_absen'] = $result['get_izin'][0]->jadwal_masuk;
                    // $request['jam_pulang'] = $result['get_izin'][0]->jadwal_pulang;
                    // $request['jam_kehadiran_karyawan'] = $result['get_izin'][0]->perbaikan_absen_masuk;
                    // $request['jam_pulang_karyawan'] = $result['get_izin'][0]->perbaikan_absen_pulang;
                    // $request['reff'] = 'APP-'. Carbon::now()->format('Y-m-d').'-'.$result['get_izin'][0]->nip;
                 
                    // $resultClassDB = $c_apiGuzzle->postServiceIzinLokaHR($request);
                    // if(!$resultClassDB['success'])
                    // {
                    //     DB::rollBack();
                    //     return $resultClassDB;
                    // }
                    // $result['update_serviceLokaHR'] = $resultClassDB['data'];

                    // get data master
                    $requesClassDB=[];
                    $requesClassDB['id_sppd'] = $idSppd;
                    $classDB = new Class_SppdMst();
                    $resultClassDB = $classDB->show($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $dataMaster = $resultClassDB['data'][0];
                    
                    // sent whatsapp message
                    $requestWhatsapp=[];
                    $requestWhatsapp['id_karyawan'] = $dataMaster->id_karyawan;
                    $requestWhatsapp['id_sppd'] = $idSppd;
                    $requestWhatsapp['city'] = $dataMaster->city;
                    $requestWhatsapp['date_start'] = $dataMaster->date_start;
                    $requestWhatsapp['date_finish'] = $dataMaster->date_finish;
                    $requestWhatsapp['depature_time'] = $dataMaster->depature_time;
                    $requestWhatsapp['after_work_time'] = $dataMaster->after_work_time;
                    $requestWhatsapp['long_day'] = $dataMaster->long_day;
                    $requestWhatsapp['night'] = $dataMaster->night;
                    $requestWhatsapp['note'] = $dataMaster->note;
            
                    $c_sentWaController = new SentWhatsappController();
                    $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveSppdDone($requestWhatsapp);
                }
                else
                {
                    $telephone = $resultClassDB['data'][0]->telephone;
                    $telephone='6285941304991';
                    // get data master
                    $requesClassDB=[];
                    $requesClassDB['id_sppd'] = $idSppd;
                    $classDB = new Class_SppdMst();
                    $resultClassDB = $classDB->show($requesClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $dataMaster = $resultClassDB['data'][0];

                    $requestWhatsapp=[];
                    $requestWhatsapp['id_karyawan'] = $dataMaster->id_karyawan;
                    $requestWhatsapp['telephone'] = $telephone;
                    $requestWhatsapp['id_sppd'] = $idSppd;
                    $requestWhatsapp['city'] = $dataMaster->city;
                    $requestWhatsapp['date_start'] = $dataMaster->date_start;
                    $requestWhatsapp['date_finish'] = $dataMaster->date_finish;
                    $requestWhatsapp['depature_time'] = $dataMaster->depature_time;
                    $requestWhatsapp['after_work_time'] = $dataMaster->after_work_time;
                    $requestWhatsapp['long_day'] = $dataMaster->long_day;
                    $requestWhatsapp['night'] = $dataMaster->night;
                    $requestWhatsapp['note'] = $dataMaster->note;
                 
                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    // $result['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveSppd($requestWhatsapp);

                    $requestModule=[];
                    $requestModule['id'] = $id;
                    $requestModule['status'] = $status;
               
                    $classDB = new Class_SppdMst();
                    $resultClassDB = $classDB->update($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_SppdMst'] = $resultClassDB['data'];  
                }
            }
            else
            {
                // draft
                // if($lampiran!='') {
                //     // call class upload Image
                //     $c_uploadImage = new ClassUploadImageClass();
                //     $urlPathImgLampiran = $c_uploadImage->processImageLampiranIzin($request->file('lampiran'),$idIzin);
                   
                //     // insert class lampiran
                //     $requestModule = []; 
                //     $requestModule['id_izin'] = $idIzin;
                //     $requestModule['tahun'] = Carbon::now()->format('Y');
                //     $requestModule['url'] = $urlPathImgLampiran;
    
                //     $classDB = new Class_IzinLampiran();
                //     $resultClassDB = $classDB->insert($requestModule);
                //     if(!$resultClassDB['success'])
                //     {
                //         DB::rollBack();
                //         return $resultClassDB;
                //     }
                //     $result['insert_lampiran'] = $resultClassDB['data'];
                // } 

                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['city'] = $dateJadwal;  
                $requestModule['date_start'] = $jadwalPulang;
                $requestModule['date_finish'] = $jadwalMasuk;
                $requestModule['depature_time'] = $perbaikanAbsenMasuk;
                $requestModule['after_work_time'] = $perbaikanAbsenPulang;
                $requestModule['long_day'] = $note;
                $requestModule['night'] = $note;
           
                $classDB = new Class_SppdMst();
                $resultClassDB = $classDB->update($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_SppdMst'] = $resultClassDB['data'];  
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

    // get request Sppd pending 
    public function getNotifSppd($request)
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
