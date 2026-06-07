<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\CronJob;
use App\Http\Controllers\GenerateIDController;

use App\Http\Controllers\Service_Cuti;
use App\Http\Controllers\SentWhatsappController;

use Carbon\Carbon;
use DateTime;


class updateCutiTahunan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'update:cutiTahunan';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Menambahkan Data Master Cuti Tahunan From LOKAHR untuk Karyawan yg sudah berhak mendapatkan';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        try {
            // getData Karyawan validity Periode cuti expied
            $c_serviceCuti = new Service_Cuti();
            $result['update_masterCutiTahunan'] = $c_serviceCuti->updateMasterCutiTahunan();
          
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateCutiTahunan-update_masterCutiTahunan';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = json_encode($result);

            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            
            return 'Cron Job Update Cuti Tahunan Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateCutiTahunan';
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
