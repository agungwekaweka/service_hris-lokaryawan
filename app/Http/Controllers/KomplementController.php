<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KomplementMst;
use App\Models\KomplementTrn;

class KomplementController extends Controller
{
    public function insertKomplemenMst($idKomplementMst,$idKomplement, $tahun,$idKaryawan,$tipeKomplement,$qty)
    {
        try {
            // cek data
            $dt = DB::table('komplement_mst')
            ->select('id_karyawan')
            ->where('id_karyawan',$idKaryawan)
            ->where('id_komplement',$idKomplement)
            ->where('tahun',$tahun);
            if($dt->exists())
            {
                // data sudah ada
            }
            else
            {
                    $data = new KomplementMst();
                    $data->id_komplement_mst = $idKomplementMst;
                    $data->id_komplement = $idKomplement;
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_komplement = $tipeKomplement; 
                    $data->sisa_komplement = $qty; 
                    $data->date_start = $tahun.'-01-01';
                    $data->date_end = $tahun.'-12-31';
                    $data->is_dell = '1'; 
                    $data->save();
            }

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertKomplemenTrn($idKomplementMst,$idKomplementTrn,$idKaryawan,$email,$noHp,$tanggalPengajuan,$tanggalKedatangan,$kodeBooking,$ticketOrder,$qtyTotal,$paymentMethods,$keterangan)
    {
        try {
            $data = new KomplementTrn();
            $data->id_komplemen_mst = $idKomplementMst;
            $data->id_komplemen_trn = $idKomplementTrn;
            $data->id_karyawan = $idKaryawan; 
            $data->email = $email; 
            $data->no_hp = $noHp; 
            $data->tanggal_pengajuan = $tanggalPengajuan; 
            $data->tanggal_kedatangan = $tanggalKedatangan;
            $data->kode_booking = $kodeBooking; 
            $data->ticket_order = $ticketOrder; 
            $data->qty_total = $qtyTotal; 
            $data->payment_methods = $paymentMethods; 
            $data->keterangan = $keterangan; 
            $data->is_dell = '1'; 
            $data->save();
            return 'success insert data';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertKomplemenTicketOrder($idKomplementMst,$idKomplementTrn,$ticketId,$productName,$ticketPriceId,$qty,$qtyBonus,$priceUnit,$subTotal)
    {
        try {
            $data = new KomplementTrn();
            $data->id_komplemen_mst = $idKomplementMst;
            $data->id_komplemen_trn = $idKomplementTrn;
            $data->ticket_id = $ticketId; 
            $data->product_name = $productName; 
            $data->ticket_price_id = $ticketPriceId; 
            $data->qty = $qty;
            $data->qty_bonus = $qtyBonus; 
            $data->price_unit = $priceUnit; 
            $data->subtotal = $subTotal; 
            $data->is_dell = '1'; 
            $data->save();
            return 'success insert data';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getTypeMasterKomplemen()
    {
        $data = DB::table('master_komplement')
        ->select('id_komplement','komplement','qty')
        ->where('is_dell','1')
        ->get();
        return $data;
    }

    public function getPriceMasterKomplemen($priceId)
    {
        $data = DB::table('master_komplement_price')
        ->select('ticket_id','ticket_price_id','price_unit','day')
        ->where('ticket_price_id',$priceId)
        ->first();
        return $data;
    }

    public function getKomplemenKaryawan($idKaryawan,$tahun,$ticketId)
    {
        try
        {
            $data_ = DB::table('komplement_mst')
            ->select('id_komplement_mst','id_komplement','id_komplement','tahun','id_karyawan','tipe_komplement','sisa_komplement','date_start','date_end')
            ->where('is_dell','1')
            ->where('id_karyawan',$idKaryawan)
            ->where('tahun',$tahun)
            ->orderBy('id_komplement','asc');
            if($ticketId!='')
            {
                $data_->where('id_komplement',$ticketId);
            }

            if($data_->exists())
            {
                $result = $data_->get();
            }
            else
            {
                $result = 'Data Komplemen Karyawan Tidak Ditemukan';
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateMasterKomplementKaryawan($idKaryawan_,$idMst_,$idTrn_,$idKomplement_)
    {
        $idKaryawan = $idKaryawan_;
        $idMst = $idMst_;
        $idTrn = $idTrn_;
        $idCuti = $idCuti_;
        try {
            $jmlCutiMst_ = 0;
            $jmlCutiTrn_ = 0;
            // Get Jml Cuti Master Periode karyawan
            $jmlCutiMst_ = DB::table('cuti_mst')
            ->select('jml_cuti')
            ->where('is_dell','1')
            ->where('sisa_cuti','<>',0)
            ->where('id_cuti',$idCuti)
            ->where('id_cuti_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlCutiMst = $jmlCutiMst_->jml_cuti;
          
            // Get total request Cuti Trn Karyawan
            $jmlCutiTrn_ = DB::table('cuti_trn')
            ->select(DB::raw("sum(total_cuti) as total"))
            ->where('is_dell','1')
            ->where('id_cuti',$idCuti)
            ->where('id_cuti_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();

            $jmlCutiTrn=$jmlCutiTrn_->total;
            $sisaCuti = $jmlCutiMst - $jmlCutiTrn;
            
            DB::table('cuti_mst')
            ->where('is_dell','1')
            ->where('sisa_cuti','<>',0)
            ->where('id_cuti','=',$idCuti)
            ->where('id_cuti_mst','=',$idMst)
            ->where('id_karyawan','=',$idKaryawan)
            ->update([
                'sisa_cuti'=> $sisaCuti
            ]);

            return 'Update Sisa Cuti Karyawan Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
