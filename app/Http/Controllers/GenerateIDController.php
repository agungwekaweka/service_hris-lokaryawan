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
      
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDCutiTrn($idCuti)
    {
        $id='-';
        $prefix = $idCuti.'-'.date('ymd');
    
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

    public function getIDKomplemenMst($idKomplemen)
    {
        $id='-';
        $prefix = $idKomplemen.'-'.date('ymd');
        $prefixSubs = substr($prefix,0,8);
        $count = DB::table('komplement_mst')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_komplement',$idKomplemen)
        ->where('id_komplement_mst','like','%'.$prefixSubs.'%')
        ->first();
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDKomplemenTrn($idKaryawan)
    {
        $id='-';
        $prefix = 'CP'.$idKaryawan .'-'.date('ymd');
     
        $prefixSubs = substr($prefix,0,9);
        $count = DB::table('komplement_trn')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_karyawan',$idKaryawan)
        ->where('id_komplemen_trn','like','%'.$prefixSubs.'%')
        ->first();

        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDOvertimeMst()
    {
        $id='-';
        $prefix = 'OT'.'-'.date('ymd');
        $prefixSubs = substr($prefix,0,5);
      
        $count = DB::table('overtime')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_overtime','like','%'.$prefixSubs.'%')
        ->first();
        
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    public function getIDRoleApprove($request)
    {
        $idDepartemen = $request['id_departemen'];
        $typeRole = $request['type_role'];

        $id='-';
        $prefix = $typeRole.'-'.$idDepartemen;

        $count = DB::table('role_approve')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_role_approve','like','%'.$prefix.'%')
        ->first();
        
        $formattedNumber = str_pad($count->count, 2, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    // belum jadi di pastikan ---------------------
    // public function getBookingCode($idKaryawan)
    // {
    //     $id='-';
    //     $prefix = $idKaryawan .date('ymd');
    //     $prefixSubs = substr($prefix,0,6);
    
    //     $count = DB::table('komplement_trn')
    //     ->select(DB::raw('COUNT(ID) as count'))
    //     ->where('id_karyawan',$idKaryawan)
    //     ->where('kode_booking','like','%'.$prefixSubs.'%')
    //     ->first();
       
    //     $formattedNumber = str_pad($count->count, 4, '0', STR_PAD_LEFT);
    //     $id = $prefix.$formattedNumber;
    //     return $id;
    // }
}
