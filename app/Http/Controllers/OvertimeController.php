<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Overtime;
use App\Models\OvertimeHistory;
use Carbon\Carbon;
use DateTime;

class OvertimeController extends Controller
{
    // insert
    // menambahkan data lembur karyawan ke table master
    public function insertOvertimeMst($idOvertime_,$idKaryawan_,$nip_,$tglPengajuan_,$tglLembur_,$jamLembur_,$totalJam_,$keterangan_)
    {
        // declare variable
        $idOvertime = $idOvertime_;
        $idKaryawan = $idKaryawan_;
        $nip = $nip_;
        $tglPengajuan = $tglPengajuan_;
        $tglLembur = $tglLembur_;
        $jamLembur = $jamLembur_;
        $totalJam = $totalJam_;
        $keterangan = $keterangan_;
    
        try {
            // cek data
            $dt = DB::table('overtime')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('tgl_lembur',$tglLembur);
            if($dt->exists())
            {
                // data sudah ada
                return 'data sudah ada';
            }
            else
            {
                    $data = new Overtime();
                    $data->id_overtime = $idMst;
                    $data->id_karyawan = $idCuti;
                    $data->nip = $tahun; 
                    $data->tgl_pengajuan = $idKaryawan; 
                    $data->tgl_lembur = $tipeCuti; 
                    $data->jam_lembur = $cuti; 
                    $data->total_jam = $jml; 
                    $data->keterangan = $tipeMasaBerlaku;
                    // status 0=pending; 1=approve; 2=reject
                    $data->status = '0'; 
                    $data->is_dell = '1';
                    $data->save();
            }
            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertOvertimeHistory($idOvertime_,$status_,$telephone_,$idKaryawanApprove_,$tglApprove_,$note_)
    {
        // declare variable
        $idOvertime = $idOvertime_;
        $status = $status_;
        $telephone = $telephone_;
        $idKaryawanApprove = $idKaryawanApprove_;
        $tglApprove = $tglApprove_;
        $note = $note_;
    
        try {
            
            $data = new OvertimeHistory();
            $data->id_overtime = $idOvertime;
            $data->status = $status;
            $data->telephone = $telephone; 
            $data->id_karyawan_approve = $idKaryawanApprove; 
            $data->tgl_approve = $tglApprove; 
            $data->note = $note; 
            $data->save();

            return 'data berhasil ditambahkan';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
