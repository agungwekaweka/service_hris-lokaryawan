<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogActivity as logActivitys;

class LogActivity extends Controller
{
    public function insertActivity($request)
    { 
        $tipe = $request['tipe'];
        $menu = $request['menu'];
        $module = $request['module'];
        $keterangan = $request['keterangan'];
        $pic = $request['pic'];
        try
        {
            $data = new logActivitys();
            $data->tipe = $tipe;
            $data->menu = $menu;
            $data->module = $module;
            $data->keterangan = $keterangan;
            $data->pic = $pic;
            $data->save();
        } catch (\Exception $ex) {
            return $ex;
        }
   }
}
