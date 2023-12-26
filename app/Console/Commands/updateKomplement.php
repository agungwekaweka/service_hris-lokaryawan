<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\CronJob;
use App\Http\Controllers\Service_Komplemen;
use App\Http\Controllers\SentWhatsappController;

use Carbon\Carbon;
use DateTime;

class updateKomplement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:komplement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menambahkan Data Master Komplement untuk Karyawan yg sudah berhak mendapatkan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // getData Karyawan validity Periode cuti expied
            $c_serviceKomplement = new Service_Komplemen();
            $result['update_masterKomplement'] = $c_serviceKomplement->updateMasterKomplement();
         
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateKomplement';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = json_encode($result);
            
            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);
            
            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));

            return 'Cron Job Update Komplement Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateKomplement';
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
