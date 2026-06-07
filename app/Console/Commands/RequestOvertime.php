<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\SentWhatsappController;
use App\Http\Controllers\CronJob;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;

class RequestOvertime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reject:overtimeExpied';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reject Overtime Expied';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
          
            $dateNow = Carbon::now()->format('Y-m-d');
            $hoursNow = Carbon::now()->format('H:i');
          
            // get List Overtime Expied
            $data_ = DB::table('overtime')
            ->select('overtime.id_overtime',
            'overtime.id_karyawan',
            'overtime.nip',
            'overtime.tgl_pengajuan',
            'overtime.tgl_lembur',
            'overtime.jam_lembur',
            'overtime.total_jam',
            'overtime.status',
            'overtime.keterangan')
            ->where('overtime.tgl_lembur',$dateNow)
            ->where('overtime.jam_awal','like',$hoursNow.'%')
            ->where('status','0');
 
            if($data_->exists())
            {
                $data = $data_->get();
             
                foreach($data as $x)
                {
                    // update master overtime
                    $request=[];
                    $request['id_overtime'] = $x->id_overtime;
                    $request['status'] = '2';
                    $request['keterangan'] = $x->keterangan;
                    $request['jam_lembur'] =$x->jam_lembur;

                    $c_overtimeController = new OvertimeController();
                    // status 0=pending, 1=approve, 2=reject
                    $result_['update_masterOvertime'] = $c_overtimeController->updateActionOvertimeMst($request);
                 
                    // get data overtime by ID Trn
                    $request=[];
                    $request['id_overtime'] = $x->id_overtime;
                    $request['id_karyawan'] = '';

                    $c_overtimeController = new OvertimeController();
                    $result_['get_dataOvertimeByID'] = $c_overtimeController->getOvertimeRequest($request);

                    $idOvertime =$x->id_overtime;
                    $telephone = $result_['get_dataOvertimeByID'][0]->no_telephone;
                    $idKaryawan = $result_['get_dataOvertimeByID'][0]->id_karyawan;
                    $tanggalLembur =  $result_['get_dataOvertimeByID'][0]->tgl_lembur;
                    $jmLembur = $result_['get_dataOvertimeByID'][0]->jam_lembur;
                    $keterangan = $result_['get_dataOvertimeByID'][0]->keterangan;
                    $note = $keterangan.'-- *Status Lembur Expired*';

                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappOvertimeDiTolak($idOvertime,$telephone,$idKaryawan,$tanggalLembur,$jmLembur,$note);
                } 

                $result_ = 'Ada Karyawan yg Terject Expied Overtimenya';
            }
            else
            {
                $result_=null;
            }

            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'update_expiedOvertime';
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
            $_requestValue['class'] = 'RequestOvertime';
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
