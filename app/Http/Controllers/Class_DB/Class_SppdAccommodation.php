<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SppdAccommodation;
use Carbon\Carbon;
use DateTime;

class Class_SppdAccommodation
{
    public function show($request)
    {
        try
        {
            // set value variable
            $idSppd=''; $idVariable=''; $variable=''; $value=''; $qty=''; $subTotal='';

            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
            if (isset($request['id_variable']) && $request['id_variable']!='' ) {$idVariable = $request['id_variable'];}
            if (isset($request['variable']) && $request['variable']!='' ) {$variable = $request['variable'];}
            if (isset($request['value']) && $request['value']!='' ) {$value = $request['value'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$subTotal = $request['sub_total'];}

            $data_ = DB::table('sppd_accommodation');
            if($idSppd!='')
            {
                $data_->where('id_sppd',$idSppd);
            }
            if($idVariable!='')
            {
                $data_->where('id_variable',$idVariable);
            }
            if($variable!='')
            {
                $data_->where('variable',$variable);
            }
            if($value!='')
            {
                $data_->where('value',$value);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($subTotal!='')
            {
                $data_->where('sub_total',$subTotal);
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
            $idSppd=''; $idVariable=''; $variable=''; $value=''; $qty=''; $subTotal='';

            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
            if (isset($request['id_variable']) && $request['id_variable']!='' ) {$idVariable = $request['id_variable'];}
            if (isset($request['variable']) && $request['variable']!='' ) {$variable = $request['variable'];}
            if (isset($request['value']) && $request['value']!='' ) {$value = $request['value'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$subTotal = $request['sub_total'];}
 
        try
        {
            // cek data
            // $request=[];
            // $request['id_sppd'] = $idSppd;
            // $request['id_variable'] = $idVariable;
            // $request['variable'] = $variable;
            // $request['value'] = $value;
            // $request['qty'] = $qty;
            // $request['sub_total'] = $subTotal;
        
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
            
                $data = new SppdAccommodation();
                $data->id_sppd = $idSppd;
                $data->id_variable = $idVariable;
                $data->variable = $variable;
                $data->value = $value;
                $data->qty = $qty;
                $data->sub_total = $subTotal;
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
            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$updateData['id_sppd'] = $request['id_sppd'];}
            if (isset($request['id_variable']) && $request['id_variable']!='' ) {$updateData['id_variable'] = $request['id_variable'];}
            if (isset($request['variable']) && $request['variable']!='' ) {$updateData['variable'] = $request['variable'];}
            if (isset($request['value']) && $request['value']!='' ) {$updateData['value'] = $request['value'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$updateData['sub_total'] = $request['sub_total'];}
           
            DB::table('sppd_accommodation')
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
