<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IzinLampiran;
use Carbon\Carbon;
use DateTime;

class Class_IzinLampiran
{
    public function show($request)
    {
        try
        {
            // set value variable
            $id=''; $idIzin=''; $tahun=''; $url='';

            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
            if (isset($request['tahun']) && $request['tahun']!='' ) {$tahun = $request['tahun'];}
            if (isset($request['url']) && $request['url']!='' ) {$url = $request['url'];}

            $data_ = DB::table('izin_lampiran')
            ->select('id_izin','tahun',
            DB::raw("CONCAT('https://servicelokaryawan.salokapark.app/storage/',url) as url"));

            if($idIzin!='')
            {
                $data_->where('id_izin',$idIzin);
            }
            if($tahun!='')
            {
                $data_->where('tahun',$tahun);
            }
            if($url!='')
            {
                $data_->where('url',$url);
            }
           
            if($data_->exists())
            {
                $data = $data_->get();
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $data
                ];
            }
            else
            {
                $data = null;
                return [
                    'success' => false,
                    'message' => 'Data Not Found',
                    'data' => $data
                ];
            }
            return $data;
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

     /**
     * Create table
     */
    public function insert($request)
    {
        // set value variable
        $idIzin=''; $tahun=''; $url='';

        if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
        if (isset($request['tahun']) && $request['tahun']!='' ) {$tahun = $request['tahun'];}
        if (isset($request['url']) && $request['url']!='' ) {$url = $request['url'];}

        try
        {
            // cek data
            // $request=[];
            // $request['id_izin'] = $idIzin;
            // $request['id_karyawan'] = $idKaryawan;
            // $request['type'] = $type;
            // $request['date_jadwal'] = $dateJadwal;
        
            // $dataTransaction = $this->show($request);
            // if($dataTransaction['success'])
            // {
            //     return [
            //         'success' => false,
            //         'message' => 'Double Data',
            //         'data' => $dataTransaction
            //     ];
            // }
            // else
            // {
                $data = new IzinLampiran();
                $data->id_izin = $idIzin;
                $data->tahun = $tahun;
                $data->url = $url;
                $data->save();

                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            // }
            return $data;
        } catch (\Exception $ex) {
            # End Log Error
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    /**
     * Update table
     */
    public function update($request)
    {
        // set value variable
        $id = '';
        $updateData =[];
        try
        {
            // declare variable set
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_izin']) && $request['id_izin']!='' ) {$updateData['id_izin'] = $request['id_izin'];}
            if (isset($request['tahun']) && $request['tahun']!='' ) {$updateData['tahun'] = $request['tahun'];}
            if (isset($request['url']) && $request['url']!='' ) {$updateData['url'] = $request['url'];}
          
            DB::table('izin_lampiran')
            ->where('id','=',$id)
            ->update($updateData);
   
            return [
                'success' => true,
                'message' => 'Update successfuly',
                'data' => $updateData
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }
}