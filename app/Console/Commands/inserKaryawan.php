<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\CronJob;

class inserKaryawan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:karyawan';

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
        try {
            // getData Karyawan validity Periode cuti expied
            $c_karyawan = new KaryawanController();
            $result['insert_karyawan'] = $c_karyawan->insertKaryawan();

            // insert history
            $_requestValue['class'] = 'InsertKaryawan';
            $_requestValue['status'] ='Success';
            $_requestValue['report'] = json_encode($result);
        
            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);
            return 'Cron Job Menambahkan Karyawan Baru Success';
        } catch (\Exception $ex) {
            // insert history
            $_requestValue['class'] = 'InsertKaryawan';
            $_requestValue['status'] ='failed';
            $_requestValue['report'] = json_encode($ex);
        
            $c_class = new CronJob();
            $c_class = $c_class->insertLog($_requestValue);
        }
    }
}
