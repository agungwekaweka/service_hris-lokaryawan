<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IzinApproveHistory;
use Carbon\Carbon;
use DateTime;

class Class_IzinApproveHistory
{
    public function show($request)
    {
        try
        {
            // set value variable
            $id=''; $idIzin=''; $status=''; $telephone=''; $idkaryawanApprove=''; $tglApprove=''; $note='';
            
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}
            if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$idkaryawanApprove = $request['id_karyawan_approve'];}
            if (isset($request['tgl_approve']) && $request['tgl_approve']!='' ) {$tglApprove = $request['tgl_approve'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
          
            $data_ = DB::table('izin_approve_history');
            if($id!='')
            {
                $data_->where('id',$id);
                $data = $data_->get();
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $data
                ];
            }
            if($idIzin!='')
            {
                $data_->where('id_izin',$idIzin);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($telephone!='')
            {
                $data_->where('telephone',$telephone);
            }
            if($idkaryawanApprove!='')
            {
                $data_->where('id_karyawan_approve',$idkaryawanApprove);
            }
            if($tglApprove!='')
            {
                $data_->where('tgl_approve',$tglApprove);
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
        $idIzin=''; $status=''; $telephone=''; $idkaryawanApprove=''; $tglApprove=''; $note=''; 

        if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}
        if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$idkaryawanApprove = $request['id_karyawan_approve'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['tgl_approve']) && $request['tgl_approve']!='' ) {$tglApprove = $request['tgl_approve'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}

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
                $data = new IzinApproveHistory();
                $data->id_izin = $idIzin;
                $data->status = $status;
                $data->telephone = $telephone;
                $data->id_karyawan_approve = $idkaryawanApprove;
                $data->name = $name;
                $data->departemen = $departemen;
                $data->sub_departemen = $subDepartemen;
                $data->grade = $grade;
                $data->tgl_approve = $tglApprove;
                $data->note = $note;
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
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['telephone']) && $request['telephone']!='' ) {$updateData['telephone'] = $request['telephone'];}
            if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$updateData['id_karyawan_approve'] = $request['id_karyawan_approve'];}
            if (isset($request['tgl_approve']) && $request['tgl_approve']!='' ) {$updateData['tgl_approve'] = $request['tgl_approve'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
      
            DB::table('izin_approve_history')
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
