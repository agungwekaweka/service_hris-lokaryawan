<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\KomplementTrn;
use Carbon\Carbon;
use DateTime;

class Class_KomplementTrn extends Controller
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idKomplementTrn = ''; $idKaryawan=''; $name=''; $email=''; $noHp=''; $tglPengajuan=''; $tglKedatangan=''; $kodeBooking=''; $orderID=''; $ticketOrder=''; $qtyTotal=''; $paymentMethods=''; $paymentLink=''; $status=''; $isDell='';
       
        // declare variable set
        if (isset($request['id_komplement_trn'])) {$idKomplementTrn = $request['id_komplement_trn'];}
        if (isset($request['id_karyawan'])) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['email'])) {$email = $request['email'];}
        if (isset($request['no_hp'])) {$noHp = $request['no_hp'];}
        if (isset($request['tanggal_pengajuan'])) {$tglPengajuan = $request['tanggal_pengajuan'];}
        if (isset($request['tanggal_kedatangan'])) {$tglKedatangan = $request['tanggal_kedatangan'];}
        if (isset($request['kode_booking'])) {$kodeBooking = $request['kode_booking'];}
        if (isset($request['order_id'])) {$orderID = $request['order_id'];}
        if (isset($request['ticket_order'])) {$ticketOrder = $request['ticket_order'];}
        if (isset($request['qty_total'])) {$qtyTotal = $request['qty_total'];}
        if (isset($request['payment_methods'])) {$paymentMethods = $request['payment_methods'];}
        if (isset($request['payment_link'])) {$paymentLink = $request['payment_link'];}
        if (isset($request['status'])) {$status = $request['status'];}
        if (isset($request['is_dell'])) {$isDell = $request['is_dell'];}

        try
        {
            $data_ = DB::table('komplement_trn');
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
                $data_->where('name',$name);
            }
            if($email!='')
            {
                $data_->where('email',$email);
            }
            if($noHp!='')
            {
                $data_->where('no_hp',$noHp);
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
            if($orderID!='')
            {
                $data_->where('order_id',$orderID);
            }
            if($ticketOrder!='')
            {
                $data_->where('ticket_order',$ticketOrder);
            }
            if($qtyTotal!='')
            {
                $data_->where('qty_total',$qtyTotal);
            }
            if($paymentMethods!='')
            {
                $data_->where('payment_methods',$paymentMethods);
            }
            if($paymentLink!='')
            {
                $data_->where('payment_link',$paymentLink);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($isDell!='')
            {
                $data_->where('is_dell',$isDell);
            }

            if($data_->exists())
            {
                $data = $data_->get();
            }
            else
            {
                $data = null;
            }
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

     /**
     * Create table
     */
    public function insert($request)
    {
        // set value variable
        $idKomplementTrn = ''; $idKaryawan=''; $name=''; $email=''; $noHp=''; $tglPengajuan=''; $tglKedatangan=''; $kodeBooking=''; $orderID=''; $ticketOrder=''; $qtyTotal=''; $paymentMethods=''; $status=''; $isDell='';
       
        // declare variable set
        if (isset($request['id_komplement_trn'])) {$idKomplementTrn = $request['id_komplement_trn'];}
        if (isset($request['id_karyawan'])) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['email'])) {$email = $request['email'];}
        if (isset($request['no_hp'])) {$noHp = $request['no_hp'];}
        if (isset($request['tanggal_pengajuan'])) {$tglPengajuan = $request['tanggal_pengajuan'];}
        if (isset($request['tanggal_kedatangan'])) {$tglKedatangan = $request['tanggal_kedatangan'];}
        if (isset($request['kode_booking'])) {$kodeBooking = $request['kode_booking'];}
        if (isset($request['order_id'])) {$orderID = $request['order_id'];}
        if (isset($request['ticket_order'])) {$ticketOrder = $request['ticket_order'];}
        if (isset($request['qty_total'])) {$qtyTotal = $request['qty_total'];}
        if (isset($request['payment_methods'])) {$paymentMethods = $request['payment_methods'];}
        if (isset($request['payment_link'])) {$paymentLink = $request['payment_link'];}
        if (isset($request['status'])) {$status = $request['status'];}
        if (isset($request['is_dell'])) {$isDell = $request['is_dell'];}
        
        try
        {
            // cek data
            $request=[];
            $request['id_komplement_trn'] = $idKomplementTrn;
            $request['id_karyawan'] = $idKaryawan;
            $request['name'] = $name;
            $request['email'] = $email;
            $request['no_hp'] = $noHp;
            $request['tanggal_pengajuan'] = $tglPengajuan;
            $request['tanggal_kedatangan'] = $tglKedatangan; 
            $dataTransaction = $this->show($request);

            if(isset($dataTransaction))
            {
                // data sudah ada
                return 'double data';
            }
            else
            {
                $data = new KomplementTrn();
                $data->id_komplemen_trn = $idKomplementTrn;
                $data->id_karyawan = $idKaryawan;
                $data->name = $name;
                $data->email = $email;
                $data->no_hp = $noHp;
                $data->tanggal_pengajuan = $tglPengajuan;
                $data->tanggal_kedatangan = $tglKedatangan;
                $data->kode_booking = $kodeBooking;
                $data->order_id = $orderID;
                $data->ticket_order = $ticketOrder;
                $data->qty_total = $qtyTotal;
                $data->payment_methods = $paymentMethods;
                $data->payment_link = $paymentLink;
                $data->status = $status;
                $data->is_dell = '1';
                $data->save();
            }
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
