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

class Cron_RequestCuti_AutoUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:requestCutiAutoUpload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload Request Cuti Approve to LokaHR';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $dateNow = Carbon::now()->format('Y-m-d');

            // get data cuti by date
            $lstDataCuti_ = DB::table('cuti_trn')
            ->select(
            'id_karyawan',
            DB::raw('(select nik from users where id_karyawan=cuti_trn.id_karyawan limit 1)as nip'),
            'tanggal','keterangan')
            ->where('status','1') // approve =1; 2=reject; 0=pending
            ->where('tipe_cuti','CT') // tipe cuti tahunan
            ->where('tanggal','like','%'.$dateNow.'%');
            if($lstDataCuti_->exists())
            {
                $lstDataCuti = $lstDataCuti_->get();
           
                $idKaryawan = '';
                $nip = '';
                $keterangan = '';
                foreach($lstDataCuti as $x)
                {
                    $idKaryawan = $x->id_karyawan;
                    $nip = $x->nip;
                    // $jsonDecode_tanggal = json_decode($x->tanggal);
                    $tanggal = $x->tanggal;
                    $keterangan = $x->keterangan;

                    // update tanggal di aplikasi lokaHR (API)
                    $c_apiGuzzle = new API_Guzzle();
                    $var = 'update_jadwal_karyawan';
                    // cuti tahunan = 6, cuti khusus = 66
                    $valueKehadiran = '6';
                    $result_['update_lokaHR_jadwal_karyawan'] = $c_apiGuzzle->postServiceLokaHR($var,$nip,$tanggal,$keterangan,$valueKehadiran);    
                }
     
                // insert history
                $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
                $_requestValue['service'] = 'CRON JOB';
                $_requestValue['class'] = 'Cron_RequestCuti_AutoUpload';
                $_requestValue['status'] ='Success';
                $_requestValue['report'] = $result_;

                $c_class = new CronJob();
                $c_class = $c_class->insertLog($_requestValue);

                // sent to developer
                $c_sentWhatsappController = new SentWhatsappController();
                $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            }
       
            return 'Cron Job Auto Upload Cuti Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'Cron_RequestCuti_AutoUpload';
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
}
