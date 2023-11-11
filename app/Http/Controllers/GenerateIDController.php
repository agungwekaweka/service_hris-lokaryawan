<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// package external
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;

class GenerateIDController extends Controller
{
    public function getIdCutiMst($tipeCuti)
    {
        $id='-';
        $prefix = $tipeCuti.'-'.date('ymd');
        $id = IdGenerator::generate(['table' => 'cuti_mst', 'field' => 'id_cuti_mst', 'length' => 20, 'prefix' => $prefix]);
        return $id;
    }

    public function getIDCutiTrn($idCuti)
    {
        $id='-';
        $prefix = $idCuti.'-'.date('ymd').'-';
        $id = IdGenerator::generate(['table' => 'cuti_trn', 'field' => 'id_cuti_trn', 'length' => 20, 'prefix' => $prefix]);
        return $id;
    }
}
