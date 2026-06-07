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

class Cron_OvertimeJamKerja_AutoUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:overrtimeJamKerjaAutoUpload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
                ->select('overtime.id','overtime.id_karyawan',
                'overtime.tgl_lembur',
                DB::raw('(select users.nik from users where users.id_karyawan = overtime.id_karyawan limit 1) as nik'))
                ->where('overtime.status','1') // approve = 1, 0=pending, 2=reject
                ->where('overtime.tgl_pengajuan','like','%'.$yesterday.'%')
                ->where('overtime.jam_jadwal_masuk',null)
                ->limit(50);
              
                if($dtOvertime_->exists()) // ada data
                {
                    $dtOvertime = $dtOvertime_->get();
                    foreach($dtOvertime as $v)
                    {
                        $idKaryawan = '';
                        $tglLembur = '';
                        $id = $v->id;
                        $idKaryawan = $v->nik;
                        $tglLembur = $v->tgl_lembur;
    
                        // get service API LOKAHR
                        $apiGuzzle = new API_Guzzle();    
                        $client = new \GuzzleHttp\Client();
    
                        $url = $apiGuzzle->urlLokaHR().'get_jadwal_jamKerja_karyawan';
                   
                        $myBody['id_karyawan'] = $idKaryawan; // nip
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
                    
                            $request =[];
                            $request['id'] = $id;
                            $request['jam_jadwal_masuk'] = '';
                            $request['jam_jadwal_pulang'] = '';
                    
                            $request['jam_jadwal_masuk'] = $dtAttendance[0]->jam_absen;
                            $request['jam_jadwal_pulang'] = $dtAttendance[0]->jam_pulang;
                          
                            $result['update_master_overtime'] = $this->updateMasterOvertime($request);
                        }
                    }
                }
                else
                {
                    // tidak ada data
                    $result = '-';
                }
            }

               // insert history
               $_requestValue=[];
               $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
               $_requestValue['service'] = 'CRON JOB';
               $_requestValue['class'] = 'Cron_OvertimeJamKerja_AutoUpload';
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
               $_requestValue['class'] = 'Cron_OvertimeJamKerja_AutoUpload';
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
        $jamJadwalMasuk = $request['jam_jadwal_masuk'];
        $jamJadwalPulang = $request['jam_jadwal_pulang'];

        // update overtime absensi
        DB::table('overtime')
        ->where('id','=',$id)
        ->update([
            'jam_jadwal_masuk' => $jamJadwalMasuk,
            'jam_jadwal_pulang' => $jamJadwalPulang,
        ]);
        return 'success';
    }
}