<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SppdLampiran;
use Carbon\Carbon;
use DateTime;

class Class_SppdLampiran 
{
    public function show($request)
    {
        try
        {
            // set value variable
            $idSppd=''; $tahun=''; $url=''; $note='';

            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
            if (isset($request['tahun']) && $request['tahun']!='' ) {$tahun = $request['tahun'];}
            if (isset($request['url']) && $request['url']!='' ) {$url = $request['url'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}

            $data_ = DB::table('sppd_lampiran');
            if($idSppd!='')
            {
                $data_->where('id_sppd',$idSppd);
            }
            if($tahun!='')
            {
                $data_->where('tahun',$tahun);
            }
            if($url!='')
            {
                $data_->where('url',$url);
            }
            if($note!='')
            {
                $data_->where('note',$note);
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
         $idSppd=''; $tahun=''; $url=''; $note='';

         if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
         if (isset($request['tahun']) && $request['tahun']!='' ) {$tahun = $request['tahun'];}
         if (isset($request['url']) && $request['url']!='' ) {$url = $request['url'];}
         if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
 
        try
        {
            // cek data
            $request=[];
            $request['id_sppd'] = $idSppd;
            $request['tahun'] = $tahu ;
            $request['url'] = $url;
            $request['note'] = $note;
        
            $dataTransaction = $this->show($request);
            if($dataTransaction['success'])
            {
                return [
                    'success' => false,
                    'message' => 'Double Data',
                    'data' => $dataTransaction
                ];
            }
            else
            {
                $data = new SppdLampiran();
                $data->id_sppd = $idSppd;
                $data->tahun = $tahun;
                $data->url = $url;
                $data->note = $note;
                $data->save();

                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            }
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
            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$updateData['id_sppd'] = $request['id_sppd'];}
            if (isset($request['tahun']) && $request['tahun']!='' ) {$updateData['tahun'] = $request['tahun'];}
            if (isset($request['url']) && $request['url']!='' ) {$updateData['url'] = $request['url'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            
            DB::table('sppd_lampiran')
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
