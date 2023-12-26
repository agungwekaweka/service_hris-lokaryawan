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
            $_requestValue['class'] = 'UpdateMasaBerlakuCuti';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = json_encode($result);
            
            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // get List Karyawan update
            $jsonDecodeListKaryawan = json_decode($result['disable_cutiTrn']['data']);
            foreach($jsonDecodeListKaryawan as $x)
            {
                $idAbsen = $x->id_karyawan;
                $tanggalBerlakuBaru =  Carbon::now()->format('Y-m-d');
                // insert Master Cuti 
                $tahun = Carbon::now()->format('Y');
                // get tipe Cuti
                $c_cuti = new CutiController();
                $lstMstCuti = $c_cuti->getTypeMasterCuti();
                foreach($lstMstCuti as $v)
                {
                    $idCuti = $v->id_cuti;
                    $tipeCuti = $v->tipe_cuti;
                    $cuti = $v->cuti;
                    $jmlHari = $v->jml_hari;
                    $masaBerlaku = $v->masa_berlaku;
                
                    // insert Master Cuti
                    $c_karyawan = new KaryawanController();
                    $result_['insert_cuti']= $c_karyawan->insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$jmlHari,$masaBerlaku,$tanggalBerlakuBaru);
                
                    // insert history
                    $_requestValue['class'] = 'UpdateMasaBerlakuCuti';
                    $_requestValue['status'] ='Success';
                    $_requestValue['report'] = json_encode($result);
                    
                    $c_class = new CronJob();
                    $c_class = $c_class->insertLog($_requestValue);
                }
            }

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
            
            return 'Cron Job Update Masa Berlaku Cuti Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['class'] = 'UpdateMasaBerlakuCuti';
            $_requestValue['status'] ='failed';
            $_requestValue['report'] = json_encode($ex);
        
            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);

            // sent to developer
            $c_sentWhatsappController = new SentWhatsappController();
            $c_sentWhatsappController->sentWAtoDeveloper(json_encode($_requestValue));
        }
    }
}