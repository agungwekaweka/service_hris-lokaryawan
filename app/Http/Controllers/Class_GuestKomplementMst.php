<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\guest_komplement_mst;
use Carbon\Carbon;
use DateTime;

class Class_GuestKomplementMst extends Controller
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idUsers = ''; $idKomplementMst=''; $qty=''; $sisa=''; $years='';
       
        // declare variable set
        if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
        if (isset($request['id_komplement_mst'])) {$idKomplementMst = $request['id_komplement_mst'];}
        if (isset($request['qty'])) {$qty = $request['qty'];}
        if (isset($request['sisa'])) {$sisa = $request['sisa'];}
        if (isset($request['years'])) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('guest_komplement_mst');
            if($idUsers!='')
            {
                $data_->where('id_users',$idUsers);
            }
            if($idKomplementMst!='')
            {
                $data_->where('id_komplement_mst',$idKomplementMst);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($sisa!='')
            {
                $data_->where('sisa',$sisa);
            }
            if($years!='')
            {
                $data_->where('years',$years);
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
        $idUsers = ''; $idKomplementMst=''; $qty=''; $years='';
       
        // declare variable set
        if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
        if (isset($request['id_komplement_mst'])) {$idKomplementMst = $request['id_komplement_mst'];}
        if (isset($request['qty'])) {$qty = $request['qty'];}
        if (isset($request['sisa'])) {$sisa = $request['sisa'];}
        if (isset($request['years'])) {$years = $request['years'];}
        
        try
        {
            // cek data
            $request=[];
            // $request['id_visitors'] = $idVisitors;
            // $dataTransaction = $this->show($request);
     
            // if(isset($dataTransaction))
            // {
            //     // data sudah ada
            //     return 'double data';
            // }
            // else
            // {
                $data = new guest_komplement_mst();
                $data->id_users = $idUsers;
                $data->id_komplement_mst = $idKomplementMst;
                $data->qty = $qty;
                $data->sisa = $sisa;
                $data->years = $years;
                $data->is_dell = '0';
                $data->save();
            // }
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

     /**
     * Update table
     */
    public function update($request)
    {
     
        // set value variable
        $idUsers = '';
        $idKomplementMst = '';
        $updateData =[];
        try
        {
            // declare variable set
            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
            if (isset($request['id_komplement_mst'])) {$idKomplementMst = $request['id_komplement_mst'];}
            if (isset($request['qty'])) {$updateData['qty'] = $request['qty'];}
            if (isset($request['sisa'])) {$updateData['sisa'] = $request['sisa'];}
            if (isset($request['years'])) {$updateData['years'] = $request['years'];}
            if (isset($request['is_dell'])) {$updateData['is_dell'] = $request['is_dell'];}
       
            DB::table('guest_komplement_mst')
            ->where('id_users','=',$idUsers)
            ->where('id_komplement_mst','=',$idKomplementMst)
            ->update($updateData);

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }


}
