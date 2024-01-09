<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogCron;

class CronJob extends Controller
{
    public function insertLog($request)
    { 
        $_class = $request['class'];
        $_status = $request['status'];
        $_report = $request['report'];
        try
        {
            $data = new LogCron();
            $data->class = $_class;
            $data->status = $_status;
            $data->report = $_report;
            $data->save();
        } catch (\Exception $ex) {
            return $ex;
        }
   }
}