<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use DateTime;

class HODController extends Controller
{
    // hod melakukan action terkait request cuti
    public function actionRequestCuti(Request $request) {  
        $id = $request->id;
        $status = $request->status;
        $note = $request->note;
        $reff = $request->reff;
        try {
            $tglApprove = Carbon::now()->format('Y-m-d H:i:s');
            $req = $this->updateRequestCuti($id,$status,$note,$reff,$tglApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Action Cuti Successfuly',
                'data' => $req
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get notif approve 
    public function getNotifApprove(Request $request) {  
        $idkaryawan = $request->id_karyawan;
        try {
            $canApprove = $this->cekApprove($idkaryawan);
            if($canApprove->approve=='1')
            {
                $roleApprove = $this->roleApprove($idkaryawan);

                $c_cuti = new CutiController();
                // $c_komplement = new KomplementController();
                $reqCuti = $c_cuti->getNotifApprove($idkaryawan,$canApprove->type_approve,$roleApprove);
                // $reqKomplemen = $c_komplement->getNotifApprove($idkaryawan);

                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Notif Approve Successfuly',
                    'dataCuti' => $reqCuti,
                    // 'dataKomplemen' => $reqKomplemen
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'ID Karyawan tidak memiliki Access Approve',
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function cekApprove($idKaryawan_)
    {
        $idKaryawan = $idKaryawan_;
        try
        {
            $dt_ = DB::table('users')
            ->select('approve','type_approve')
            ->where('id_karyawan',$idKaryawan);
            if($dt_->exists())
            {
                $dt = $dt_->first();
            }
            else
            {
                $dt = false;
            }
            return $dt;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function roleApprove($idKaryawan_)
    {
        $idKaryawan=$idKaryawan_;
        try
        {
            $dt_ = DB::table('role_approve')
            ->select('id_karyawan','type_approve','id_approve')
            ->where('id_karyawan',$idKaryawan);
            if($dt_->exists())
            {
                $dt = $dt_->get();
            }
            else
            {
                $dt = false;
            }
            return $dt;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
