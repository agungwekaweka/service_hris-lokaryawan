<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Service_Komplemen extends Controller
{
     // Get Master Komplemen By Tanggal 
     public function getListPriceMasterKomplemenByTanggal(Request $request)
     {
         $tanggal = $request->tanggal;

         try
         {
             $data_ = DB::table('master_komplement')
             ->select(
             'id_komplement','komplement','qty','is_dell'
             )
             ->where('is_dell','1');
             if($data_->exists()) 
             {       
                // cek tanggal
                $c_apiGuzzle = new API_Guzzle();
                $dayHistory = $c_apiGuzzle->postServiceCekEvent($tanggal);
                $data['is_holiday']=$dayHistory->holiday;

                $listMasterKomplement = $data_->get();
                $i=0;
                $ticketPriceId =0;
                foreach($listMasterKomplement as $x)
                {
                    if($x->id_komplement!='40')
                    { 
                        // ticket 50%
                        if($data['is_holiday']==true)
                        {
                            $ticketPriceId = 180;
                        }
                        elseif($data['is_holiday']==false)
                        {
                            $ticketPriceId=183;
                        }
                  
                    }
                    else
                    {
                        // ticket Normal (free full)
                        $ticketPriceId ='25';
                    }
                 
                    // get master komplement price
                    $c_komplementController  = new KomplementController();
                    $price_ = $c_komplementController->getPriceMasterKomplemen($ticketPriceId);
             
                    $priceUnit = $price_->price_unit;

                    $data[$i]['ticket_id'] =$x->id_komplement;
                    $data[$i]['komplement'] =$x->komplement;
                    $data[$i]['ticket_price_id'] =$ticketPriceId;
                    $data[$i]['price_unit'] =$priceUnit;
                  
                    $i++;
                }

                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Master Price Kompliment Successfuly',
                    'data' => $data
                ]);
             }
             else
             {
                 $result=response()->json([
                     'status' => 'failed',
                     'message' => 'Get Data Master Cuti Not Successfuly',
                 ]);
             }
 
             return $result;
         } catch (\Exception $ex) {
             return $ex;
         }
     }

     // Get List Cuti Karyawan
     public function getListMasterKomplemen(Request $request)
     {
        $tahun = $request->tahun;
        $idKaryawan = $request->id_karyawan;

         try
         {
             $data_ = DB::table('komplement_mst')
             ->select(
             'users.departemen',
             'users.sub_departemen',
             'users.grade',
             'users.name',
             'komplement_mst.id_karyawan','komplement_mst.tahun',
             DB::raw("(select tipe_komplement from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM001' limit 1) as tipe_komplement_gratis"),
             DB::raw("(select sisa_komplement from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM001' limit 1) as sisa_komplement_gratis"),
             DB::raw("(select date_start from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM001' limit 1) as date_start_gratis"),
             DB::raw("(select date_end from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM001' limit 1) as date_expied_gratis"),

             DB::raw("(select tipe_komplement from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM002' limit 1) as tipe_komplement_bayar"),
             DB::raw("(select sisa_komplement from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM002' limit 1) as sisa_komplement_bayar"),
             DB::raw("(select date_start from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM002' limit 1) as date_start_bayar"),
             DB::raw("(select date_end from komplement_mst where id_karyawan = komplement_mst.id_karyawan and id_komplement='KM002' limit 1) as date_expied_bayar")
                         
             )
             ->join('users','users.id_karyawan','komplement_mst.id_karyawan')
             ->where('komplement_mst.is_dell','1');
             if($data_->exists())
             {       
                if($idKaryawan !='')
                {
                    $data_->where('komplement_mst.id_karyawan',$idKaryawan);
                }
                    $data_->where('komplement_mst.tahun',$tahun);
                    $data_->distinct('komplement_mst.id_karyawan');
                    $data_->orderBy('users.nik','asc');
                    $data = $data_->get();
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Get Data Master Cuti Successfuly',
                        'data' => $data
                    ]);
             }
             else
             {
                 $result=response()->json([
                     'status' => 'failed',
                     'message' => 'Get Data Master Cuti Not Successfuly',
                 ]);
             }
 
             return $result;
         } catch (\Exception $ex) {
             return $ex;
         }
     }
}
