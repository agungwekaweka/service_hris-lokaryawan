<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\CronJob;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SentWhatsappController;

use Carbon\Carbon;
use DateTime;

class updateMasaBerlakuCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:masaBerlakuCuti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Master Cuti Validity Period Expied, Cron Start At 00:05:00';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // getData Karyawan validity Periode cuti expied
            $c_cutiController = new CutiController();
            $result['disable_cutiTrn'] = $c_cutiController->disableCutiTrnValidatePeriodeExpied();
            
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateMasaBerlakuCuti-disable_cutiTrn';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = json_encode($result);

            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));

            $tahun = Carbon::now()->format('Y');
            $tglBerlaku = Carbon::now()->format('Y-m-d');
            // get tipe Cuti
            $c_cuti = new CutiController();
            $lstMstCuti = $c_cuti->getTypeMasterCuti();

            // list Data Karyawan disable Cuti Master
            $lstKaryawanStatusNonActive = json_decode($result['disable_cutiTrn']['data']);

            if($lstKaryawanStatusNonActive !=null)
            {
                foreach($lstKaryawanStatusNonActive as $x)
                {
                    // fill data
                    $idKaryawan = $x->id_karyawan;
    
                    foreach($lstMstCuti as $v)
                    {
                        $idCuti = $v->id_cuti;
                        $tipeCuti = $v->tipe_cuti;
                        $cuti = $v->cuti;
                        $jmlHari = $v->jml_hari;
                        $masaBerlaku = $v->masa_berlaku;
                        $tipeMasaBerlaku = $v->tipe_masa_berlaku;
                             
                        $c_generateID = new GenerateIDController();
                        $idMst = $c_generateID->getIdCutiMst($tipeCuti);
                
                        $c_cuti = new CutiController();
                        $result_['insert_masterCutiDB'] = $c_cuti->insertCutiMst($idMst,$idCuti, $tahun,$idKaryawan,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku);
                    }
                }
    
                // insert history
                $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
                $_requestValue['service'] = 'CRON JOB';
                $_requestValue['class'] = 'updateMasaBerlakuCuti-update_masterCutiTahunan';
                $_requestValue['status'] ='Success';
                $_requestValue['report'] = json_encode($result);
                
                $c_class = new CronJob();
                $c_class = $c_class->insertLog($_requestValue); 
                
                // sent to developer
                $c_sentWhatsappController = new SentWhatsappController();
                $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            }
            
            return 'Cron Job Update Cuti Tahunan Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['apps'] = 'Service_HRIS-Lokaryawan';
            $_requestValue['service'] = 'CRON JOB';
            $_requestValue['class'] = 'updateMasaBerlakuCuti';
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