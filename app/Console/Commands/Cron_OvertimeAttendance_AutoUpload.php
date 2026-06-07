<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\Http\Controllers\API_Guzzle;
use App\Http\Controllers\SentWhatsappController;
use App\Http\Controllers\CronJob;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;

class Cron_OvertimeAttendance_AutoUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:overtimeAttendanceAutoUpload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Upload Attendace Record From LokaHR per 1 bulan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try
        {
            $now = Carbon::now()->format('d');
            if($now=='01')
            {
                $yesterday = Carbon::yesterday()->format('Y-m');
                $dtOvertime_ = DB::table('overtime')
                ->select('id','id_karyawan','tgl_lembur')
                ->where('status','1') // approve = 1, 0=pending, 2=reject
                ->where('jam_absen_jsonObject',null)
                ->where('tgl_lembur','like','%'.$yesterday.'%');
                if($dtOvertime_->exists()) // ada data
                {
                    $dtOvertime = $dtOvertime_->get();
                 
                    // dd($dtOvertime);
                    foreach($dtOvertime as $v)
                    {
                        $idKaryawan = '';
                        $tglLembur = '';
                        $id = $v->id;
                        $idKaryawan = $v->id_karyawan;
                        $tglLembur = $v->tgl_lembur;
                    
                        // get service API LOKAHR
                        $apiGuzzle = new API_Guzzle();    
                        $client = new \GuzzleHttp\Client();
                      
                        $url = $apiGuzzle->urlLokaHR().'get_attendance_karyawan';
                     
                    
                        $myBody['id_karyawan'] = $idKaryawan;
                        $myBody['tanggal_awal']=$tglLembur;
                        $myBody['tanggal_akhir']=$tglLembur;
           
                        $request = $client->post($url, ['form_params'=> $myBody]);      
                        $response = $request->getBody();
                        
                        $jsonDecode = json_decode($response);
                    
                        
                        // cek status API
                        if($jsonDecode->status=='success')
                        {
                            $jamAbsenMasuk = '';
                            $jamAbsenPulang = '';
                            $jsonDataAbsen = '';
    
                            // komplement Active
                            $firstDataArray = '';
                            $lastDataArray = '';
                            $dtAttendance = $jsonDecode->data;
                            $firstDataArray = reset($dtAttendance);
                            $lastDataArray = end($dtAttendance);
               
                            $request =[];
                            $request['id'] = $id;
                            $request['jam_absen_masuk'] = $firstDataArray->jam;
                            $request['jam_absen_pulang'] = $lastDataArray->jam;
                            $request['json_data_absen'] = json_encode($dtAttendance);
                            $result['update_master_overtime'] = $this->updateMasterOvertime($request);
                        }
                    }
                }
                else
                {
                    // tidak ada data
                }
            }

               // insert history
               $_requestValue=[];
               $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
               $_requestValue['service'] = 'CRON JOB';
               $_requestValue['class'] = 'Cron_OvertimeAttendance_AutoUpload';
               $_requestValue['status'] ='Success';
               $_requestValue['report'] = $result;

               $c_class = new CronJob();
               $c_class = $c_class->insertLog($_requestValue);

               // sent to developer
               $c_sentWhatsappController = new SentWhatsappController();
               $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            return 'success';
        } catch (\Exception $ex) {
               // insert history
               $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
               $_requestValue['service'] = 'CRON JOB';
               $_requestValue['class'] = 'Cron_OvertimeAttendance_AutoUpload';
               $_requestValue['status'] ='failed';
               $_requestValue['report'] = json_encode($ex);
           
               $c_class = new CronJob();
               $c_class = $c_class->insertLog($_requestValue);
   
               // sent to developer
               $c_sentWhatsappController = new SentWhatsappController();
               $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
               return 'Failed Cron Job';
        }
    }

    private function updateMasterOvertime($request)
    {
        $id = $request['id'];
        $jamAbsenMasuk = $request['jam_absen_masuk'];
        $jamAbsenPulang = $request['jam_absen_pulang'];
        $jsonDataAbsen = $request['json_data_absen'];

        // update overtime absensi
        DB::table('overtime')
        ->where('id','=',$id)
        ->update([
            'jam_absen_masuk' => $jamAbsenMasuk,
            'jam_absen_pulang' => $jamAbsenPulang,
            'jam_absen_jsonObject' => $jsonDataAbsen
        ]);
        return 'success';
    }
}
