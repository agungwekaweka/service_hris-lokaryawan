<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// package external
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;

class GenerateIDController extends Controller
{
    public function getIdCutiMst($tipeCuti)
    {
        $id='-';
        $prefix = $tipeCuti.'-'.date('ymd');
        $prefixSubs = substr($prefix,0,5);
        $count = DB::table('cuti_mst')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('tipe_cuti',$tipeCuti)
        ->where('id_cuti_mst','like','%'.$prefixSubs.'%')
        ->first();
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
        // $id = IdGenerator::generate(['table' => 'cuti_mst', 'field' => 'id_cuti_mst', 'length' => 20, 'prefix' => $prefix]);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDCutiTrn($idCuti)
    {
        $id='-';
        $prefix = $idCuti.'-'.date('ymd');
        // $id = IdGenerator::generate(['table' => 'cuti_trn', 'field' => 'id_cuti_trn', 'length' => 20, 'prefix' => $prefix]);
        $prefixSubs = substr($prefix,0,8);
        $count = DB::table('cuti_trn')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_cuti',$idCuti)
        ->where('id_cuti_trn','like','%'.$prefixSubs.'%')
        ->first();

        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDCutiLampiran()
    {
        $id='-';
        $tahun = date('y');
        $count = DB::table('cuti_lampiran')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('tahun',$tahun)
        ->first();
        $formattedNumber = str_pad($count->count, 3, '0', STR_PAD_LEFT);
        $id = $formattedNumber;
        return $id;
    }
}
