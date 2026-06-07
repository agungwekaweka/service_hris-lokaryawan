<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\RequestOvertime;
use DateTime;

// use App\Imports\ImportKomplement;

class Service_Overtime extends Controller
{
    public function getRequestOvertime(Request $request)
    {
        $idDepartemen = $request->id_departemen;
        $idSubDepartemen = $request->id_sub_departemen;
        $idOvertime = $request->id_overtime;
        $idKaryawan = $request->id_karyawan;
        $status = $request->status;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        try
        {
            $request['id_departemen'] = $idDepartemen;
            $request['id_sub_departemen'] = $idSubDepartemen;
            $request['id_overtime'] = $idOvertime;
            $request['id_karyawan'] = $idKaryawan;
            $request['status'] = $status;
            $request['tanggal_awal'] = $tanggalAwal;
            $request['tanggal_akhir'] = $tanggalAkhir;

            $OvertimeController = new OvertimeController();
            $data = $OvertimeController->getOvertimeRequest($request);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Overtime Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    

    public function exportRequestOvertime(Request $request)
    {
        try
        {
            $status = $request['status'];
            $tanggalAwal = $request['tanggal_awal'];
            $tanggalAkhir = $request['tanggal_akhir'];
     
            $param = array(
                'status' => $status,
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir
            );
            $dateNow = Carbon::now()->format('Y-m-d H:i:s');
            return Excel::download(new RequestOvertime($param), 'Export-Request Overtime '.$dateNow.'.xlsx');
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
}
