<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\Http\Controllers\CutiController;
use App\Http\Controllers\SentWhatsappController;
use App\Http\Controllers\CronJob;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;

class RequestCutiTahunan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:cutiTahunanLampiran';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim Notifikasi Lampiran File Belum ditambahkan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $threeDaysLater = Carbon::now()->subDays(3)->format('Y-m-d');
            
            // get List Cuti Notify
            $data_ = DB::table('cuti_trn')
            ->select('id_cuti_mst','id_cuti_trn')
            ->whereBetween('tgl_pengajuan', array($threeDaysLater." 00:00:00", $threeDaysLater." 23:59:59"))
            ->where('tipe_cuti','CK')
            ->get();
            // ->get()->pluck('cuti_trn.id_cuti_mst')->toArray();
            foreach($data_ as $v)
            {
                $idCutiTrn = $v->id_cuti_trn;
                $idCutiMst = $v->id_cuti_mst;

                $dataCutiLampiran = DB::table('cuti_lampiran')
                ->select('id_cuti_mst','id_cuti_trn')
                ->where('id_cuti_mst',$idCutiMst)
                ->where('id_cuti_trn',$idCutiTrn);
                if($dataCutiLampiran->exists())
                {

                }
                else
                {
                    // get data cuti TRN by ID Trn
                    $c_cutiController = new CutiController();
                    $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);
                
                    $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
                    $telephone = $result_['get_dataCutiTRN']->no_telephone;
                    $cuti = $result_['get_dataCutiTRN']->cuti;
                    $tanggal =$result_['get_dataCutiTRN']->tanggal;
                    $note = 'Lampiran File Belum di upload!';
                   
                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappNotifyLampiranCK($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
                }
            }

            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'RequestCutiTahunan';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = $result_;

            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            
            return 'Cron Job Notify Lampiran Cuti Khusus Success';
        } catch (\Exception $ex) {

            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'RequestCutiTahunan';
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
