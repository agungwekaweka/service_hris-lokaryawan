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

class RequestCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reject:cutiExpied';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reject Cuti Tahunan Expied';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
          
            $dateNow = Carbon::now()->format('Y-m-d');
         
            // get List Cuti Expied
            $data_ = DB::table('cuti_trn')
            ->select('cuti_trn.id_cuti_mst',
            'cuti_trn.id_cuti_trn',
            'cuti_trn.id_cuti',
            'cuti_trn.id_periode',
            'cuti_trn.tahun',
            'cuti_trn.id_karyawan',
            'cuti_trn.tipe_cuti',
            'cuti_trn.cuti',
            'cuti_trn.tanggal',
            'cuti_trn.total_cuti',
            'cuti_trn.keterangan',
            'cuti_trn.tgl_pengajuan',
            'cuti_trn.status',
            'cuti_trn.note')
            ->where('cuti_trn.tanggal','like','%'.$dateNow.'%')
            ->where('status','0');
 
            if($data_->exists())
            {
                $data = $data_->get();

                foreach($data as $x)
                {
                    $idMst = $x->id_cuti_mst;
                    $idCutiTrn = $x->id_cuti_trn;
                    $idCuti = $x->id_cuti;
                    $idKaryawan = $x->id_karyawan;
                
                    $note = 'Status Cuti Expired';
                    $status='2';

                    // update master cuti
                    $c_cutiController = new CutiController();
                    // status 0=pending, 1=approve, 2=reject
                    $result_['update_actionCuti'] = $c_cutiController->updateActionCutiTrn($idCutiTrn,$status,$note);

                    // update data master cuti
                    $c_cuti = new CutiController();
                    $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$idMst,$idCutiTrn,$idCuti);

                    // get data cuti TRN by ID Trn
                    $c_cutiController = new CutiController();
                    $result_['get_dataCutiTRN'] = $c_cutiController->getCutiTrn($idCutiTrn);
                
                    $idKaryawan = $result_['get_dataCutiTRN']->id_karyawan;
                    $telephone = $result_['get_dataCutiTRN']->no_telephone;
                    $cuti = $result_['get_dataCutiTRN']->cuti;
                    $tanggal =$result_['get_dataCutiTRN']->tanggal;

                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCutiCancel($idCutiTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
                } 
                
                $result_= 'Ada Karyawan yg Terject Expied Cutinya';
            }
            else
            {
                $result_=null;
            }

            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'update_expiedCutiTahunan';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = $result_;

            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            
            return 'Cron Job Update Expired Cuti Tahunan Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'RequestCuti';
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
