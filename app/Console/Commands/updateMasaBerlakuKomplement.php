<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Controllers\GenerateIDController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\CronJob;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SentWhatsappController;

use Carbon\Carbon;
use DateTime;


class updateMasaBerlakuKomplement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:masaBerlakuKomplement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Master Komplement Validity Period Expied and create new komplemen tahunan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
                DB::table('komplement_mst')
                    ->where('tahun',Carbon::now()->format('Y'))
                    ->update([
                        'is_dell'=> '0',
                    ]);

                // insert history
                $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
                $_requestValue['service'] = 'CRON JOB';
                $_requestValue['class'] = 'updateMasaBerlakuKomplement';
                $_requestValue['status'] ='Success';
                $_requestValue['report'] = 'Success Update Masa Berlaku Komplement';
                
                $c_class = new CronJob();
                $c_class = $c_class->insertLog($_requestValue); 

                // sent to developer
                $c_sentWhatsappController = new SentWhatsappController();
                $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            
            return 'Cron Job Update komplemen Tahunan Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateMasaBerlakuKomplemen';
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
