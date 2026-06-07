<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KomplementMst;
use App\Models\KomplementTrn;
use App\Models\KomplementTicketOrder;
use Carbon\Carbon;
use DateTime;

class KomplementController extends Controller
{
    // INSERT
    public function insertKomplemenMst($idKomplementMst,$idKomplement, $tahun,$idKaryawan,$tipeKomplement,$jmlKomplement,$qty)
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
                return 'data sudah ada';
            }
            else
            {
                    $data = new KomplementMst();
                    $data->id_komplement_mst = $idKomplementMst;
                    $data->id_komplement = $idKomplement;
                    $data->tahun = $tahun; 
                    $data->id_karyawan = $idKaryawan; 
                    $data->tipe_komplement = $tipeKomplement; 
                    $data->jml_komplement = $jmlKomplement;
                    $data->sisa_komplement = $qty; 
                    $data->date_start = $tahun.'-01-01';
                    $data->date_end = $tahun.'-12-31';
                    $data->is_dell = '1'; 
                    $data->save();
                    return 'success insert data master Komplement Karyawan';
            }

           
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function insertKomplemenTrn($idKomplemenTrn,$idKaryawan,$name,$email,$noHp,$tglPengajuan,$tanggalKedatangan,$kodeBooking,$orderId,$ticketOrder,$qtyTotal,$paymentMethods,$status)
    {
        try {
             // cek data
             $dt = DB::table('komplement_trn')
             ->select('id_komplemen_trn')
             ->where('id_komplemen_trn',$idKomplemenTrn);
             if($dt->exists())
             {
                 // data sudah ada
                 return 'data sudah ada';
             }
             else
             {
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
             }
  
         
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
    // END INSERT

    // GET
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

    public function getRequestKomplemenKaryawan($idKaryawan,$idKomplementTrn,$name,$tglPengajuan,$tglKedatangan,$kodeBooking,$orderId)
    {
        try
        {
            $data_ = DB::table('komplement_trn')
            ->select('id_komplemen_trn','id_karyawan','name','email','no_hp','tanggal_pengajuan','tanggal_kedatangan','kode_booking','order_id','ticket_order','qty_total','payment_methods','payment_link','status')
            ->orderBy('id_komplemen_trn','asc');
            if($idKomplementTrn!='')
            {
                $data_->where('id_komplemen_trn',$idKomplementTrn);
            }
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($name!='')
            {
                $data_->where('name','like','%'.$name.'%');
            }
            if($tglPengajuan!='')
            {
                $data_->where('tanggal_pengajuan',$tglPengajuan);
            }
            if($tglKedatangan!='')
            {
                $data_->where('tanggal_kedatangan',$tglKedatangan);
            }
            if($kodeBooking!='')
            {
                $data_->where('kode_booking',$kodeBooking);
            }
            if($orderId!='')
            {
                $data_->where('order_id',$orderId);
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

    public function getKomplemenKaryawanByOrderID($orderId)
    {
        try
        {
            $data_ = DB::table('komplement_trn')
            ->select('id_komplemen_trn','id_karyawan','name','email','no_hp','tanggal_pengajuan','tanggal_kedatangan','kode_booking','order_id','ticket_order','qty_total','payment_methods','payment_link','status')
            ->where('order_id',$orderId);
            if($data_->exists())
            {
                $result = $data_->first();
            }
            else
            {
                $result = 'Data Komplemen Karyawan By Order ID Tidak Ditemukan';
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getRequestKomplemenKaryawanComingSoon($idKaryawan)
    {
        try
        {
            $data_ = DB::table('komplement_trn')
            ->select('id_komplemen_trn','id_karyawan','name','email','no_hp','tanggal_pengajuan','tanggal_kedatangan','kode_booking','order_id','ticket_order','qty_total','payment_methods','payment_link','status')
            ->where('id_karyawan',$idKaryawan)
            ->where('status','1')
            ->orderBy('tanggal_kedatangan','desc');
            if($data_->exists())
            {
                $data = $data_->get();
            
                $data_ = null;
                foreach($data as $v)
                {
                 
                    // Convert the user-provided date to a Carbon instance
                    $userDate = Carbon::parse($v->tanggal_kedatangan);
               
                    // Get the current date
                    $currentDate = Carbon::now();
                
                    // Check if the user-provided date is greater than the current date
                    if ($userDate->greaterThan($currentDate) || $userDate->isToday()) {
                        // The user-provided date is greater than the current date
                        // Handle the situation accordingly, for example, return an error response
                        $data_ =  $v;
                    }
                    else
                    {
                        $result = null;
                    }
                        $result = $data_;
                }
             
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
    // END GET

    // public function updateStatusKomplementKaryawan($idMst,$idKomplement,$tahun,$idKaryawan,$jmlKomplement,$sisaKompelement)
    // {
    //     $idMst = $idMst;
    //     $idKomplement = $idKomplement;
    //     $tahun = $tahun;
    //     $idKaryawan = $idKaryawan;
    //     $jmlKomplement = $jmlKomplement;
    //     $sisaKompelement = $sisaKompelement;
    //     try
    //     {
    //          // cek data
    //          $dt = DB::table('komplement_mst')
    //          ->select('id_karyawan')
    //          ->where('id_karyawan',$idKaryawan)
    //          ->where('id_komplement',$idKomplement)
    //          ->where('tahun',$tahun);
    //          if($dt->exists())
    //          {
    //              // data sudah ada
    //          }
    //          else
    //          {
    //             DB::table('komplement_mst')
    //             ->where('id_komplement_mst',$idMst)
    //             ->where('id_komplement',$idKomplement)
    //             ->where('tahun',$tahun)
    //             ->where('id_karyawan',$idKaryawan)
    //             ->update([
    //                 'jml_komplement'=> $jmlKomplement,
    //                 'sisa_komplement'=> $sisaKompelement
    //             ]);
    //          }

    //         return 'Update Master Komplement Karyawan Successfuly';
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

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
            ->select('jml_komplement')
            ->where('is_dell','1')

            ->where('id_komplement',$idKomplement)
            ->where('id_komplement_mst',$idMst)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            $jmlKomplementMst = $jmlKomplementMst_->jml_komplement;
     
            // Get total request komplement Ticket Order Karyawan
            $jmlKomplementTrn_ = DB::table('komplement_ticket_order')
            ->select(DB::raw("sum(qty) as total"))
            ->where('is_dell','1')
            ->where('ticket_id',$idKomplement)
            ->where('id_komplemen_mst',$idMst)
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
            $data = DB::table('komplement_trn')
            ->select('kode_booking')
            ->where('id_komplemen_trn',$idKomplementTrn)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            if($data->kode_booking=='-')
            {
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
            }
            else
            {
                return false;
            }
           
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateReservationTicket($orderID,$kodeBooking,$status)
    {
        try {
            $data = DB::table('komplement_trn')
            ->select('kode_booking')
            ->where('order_id',$orderID)
            ->fist();
            if($data->kode_booking!='-')
            {
                $result_['update_reservationTicet'] = false;
            }
            else
            {
                DB::table('komplement_trn')
                ->where('order_id',$orderID)
                ->update([
                    'kode_booking'=>$kodeBooking,
                    'status'=>$status
                ]);
                $result_['update_reservationTicet'] = 'Update Order ID Karyawan Successfuly';
                if($status=='3')
                {
                    // get Data komplement TRN
                    $dataKomplementTrn = DB::table('komplement_trn')
                    ->select('id_komplemen_trn','id_karyawan')
                    ->where('order_id',$orderID)
                    ->first();
                    $idKaryawan = $dataKomplementTrn->id_karyawan;
                    $idKomplementTrn = $dataKomplementTrn->id_komplemen_trn;
                 
                    // get Data Komplement Ticket Order
                    $dataKomplementTicketOrder = DB::table('komplement_ticket_order')
                    ->select('id_komplemen_mst','ticket_id')
                    ->where('id_komplemen_trn',$idKomplementTrn)
                    ->get();
                    $result_['disable_ticketOrder'] = $this->disableTicketOrder($idKomplementTrn);
                    foreach($dataKomplementTicketOrder as $v)
                    {
                        $idKomplementMst = $v->id_komplemen_mst;
                        $idKomplement = $v->ticket_id;
                        $result_['update_master_komplement_karyawan'] = $this->updateMasterKomplementKaryawan($idKaryawan,$idKomplementMst,$idKomplementTrn,$idKomplement);
                    }
                }
            }
        return $result_;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    private function disableTicketOrder($idKomplementTrn)
    {
        try
        {   
            DB::table('komplement_ticket_order')
            ->where('id_komplemen_trn',$idKomplementTrn)
            ->update([
                'is_dell'=> 3
            ]);
            return 'disable komplement ticket Order Successfuly';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
