<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KomplementMst;
use App\Models\KomplementTrn;
use App\Models\KomplementTicketOrder;

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

    public function insertKomplemenTrn($idKomplemenTrn,$idKaryawan,$name,$email,$noHp,$tglPengajuan,$tanggalKedatangan,$kodeBooking,$orderId,$ticketOrder,$qtyTotal,$paymentMethods,$status)
    {
        try {
            $data = new KomplementTrn();
            $data->id_komplemen_trn = $idKomplemenTrn;
            $data->id_karyawan = $idKaryawan; 
            $data->name = $name; 
            $data->email = $email; 
            $data->no_hp = $noHp; 
            $data->tanggal_pengajuan = $tglPengajuan; 
            $data->tanggal_kedatangan = $tanggalKedatangan;
            $data->kode_booking = $kodeBooking; 
            $data->order_id = $orderId; 
            $data->ticket_order = $ticketOrder; 
            $data->qty_total = $qtyTotal; 
            $data->payment_methods = $paymentMethods; 
            $data->status = $status; 
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
            $data = new KomplementTicketOrder();
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

    public function getRequestKomplemenKaryawan($idKaryawan,$idKomplementTrn)
    {
        try
        {
            $data_ = DB::table('komplement_trn')
            ->select('id_komplemen_trn','id_karyawan','name','email','no_hp','tanggal_pengajuan','tanggal_kedatangan','kode_booking','order_id','ticket_order','qty_total','payment_methods','payment_link','status')
            ->where('id_karyawan',$idKaryawan)
            ->orderBy('id_komplemen_trn','asc');
            if($idKomplementTrn!='')
            {
                $data_->where('id_komplemen_trn',$idKomplementTrn);
            }

            if($data_->exists())
            {
                $result = $data_->get();
            }
            else
            {
                $result = 'Data Request Komplemen Karyawan Tidak Ditemukan';
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getRequestKomplemenTicketOrderKaryawan($idKomplementTrn)
    {
        try
        {
            $data_ = DB::table('komplement_ticket_order')
            ->select('id_komplemen_mst','id_komplemen_trn','ticket_id','product_name','ticket_price_id','qty','price_unit','subtotal')
            ->where('id_komplemen_trn',$idKomplementTrn)
            ->orderBy('id_komplemen_trn','asc');
            if($data_->exists())
            {
                $result = $data_->get();
            }
            else
            {
                $result = 'Data Request Ticket Order Karyawan Tidak Ditemukan';
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // API GET REQUST KOMPLEMENT
    public function getRequestKomplemen(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $idKomplemenTrn = $request->id_komplement_trn;
        try
        {
            $data = $this->getRequestKomplemenKaryawan($idKaryawan,$idKomplemenTrn);
            $dataTicketOrder = null;
            if($idKomplemenTrn!='')
            {
                $dataTicketOrder = $this->getRequestKomplemenTicketOrderKaryawan($idKomplemenTrn);
            }
          
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Request Komplement Karyawan Successfuly',
                'data' => $data,
                'ticket_order' => $dataTicketOrder
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateMasterKomplementKaryawan($idKaryawan_,$idKomplementMst_,$idKomplementTrn_,$idKomplement_)
    {
        $idKaryawan = $idKaryawan_;
        $idMst = $idKomplementMst_;
        $idTrn = $idKomplementTrn_;
        $idKomplement = $idKomplement_;

        try {
            $jmlKomplementMst_ = 0;
            $jmlKomplementTrn_ = 0;
          
            // Get Jml Cuti Master Periode karyawan
            $jmlKomplementMst_ = DB::table('komplement_mst')
            ->select('sisa_komplement')
            ->where('is_dell','1')

            ->where('id_komplement',$idKomplement)
            ->where('id_komplement_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlKomplementMst = $jmlKomplementMst_->sisa_komplement;
     
            // Get total request komplement Ticket Order Karyawan
            $jmlKomplementTrn_ = DB::table('komplement_ticket_order')
            ->select(DB::raw("sum(qty) as total"))
            ->where('is_dell','1')
            ->where('ticket_id',$idKomplement)
            ->where('id_komplemen_mst',$idMst)
            ->where('id_komplemen_trn',$idTrn)
            ->first();

            $jmlKomplementTrn=$jmlKomplementTrn_->total;
            $sisaKompelement = $jmlKomplementMst - $jmlKomplementTrn;
            
            DB::table('komplement_mst')
            ->where('is_dell','1')
            ->where('id_komplement',$idKomplement)
            ->where('id_komplement_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->update([
                'sisa_komplement'=> $sisaKompelement
            ]);

            return 'Update Sisa Komplement Karyawan Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateOrderIDBooking($idKomplementTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status)
    {
        try {
            DB::table('komplement_trn')
            ->where('id_komplemen_trn',$idKomplementTrn)
            ->where('id_karyawan',$idKaryawan)
            ->update([
                'order_id'=> $orderID,
                'kode_booking'=>$kodeBooking,
                'payment_link'=>$paymentLink,
                'status'=>$status
            ]);
        return 'Update Order ID Karyawan Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateReservationTicket($orderID,$kodeBooking,$status)
    {
        try {
            DB::table('komplement_trn')
            ->where('order_id',$orderID)
            ->update([
                'kode_booking'=>$kodeBooking,
                'status'=>$status
            ]);
        return 'Update Order ID Karyawan Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
