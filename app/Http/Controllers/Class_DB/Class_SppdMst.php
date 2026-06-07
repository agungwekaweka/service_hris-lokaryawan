<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SppdMst;
use Carbon\Carbon;
use DateTime;

class Class_SppdMst
{
    public function show($request)
    {
        try
        {
            // set value variable
            $idSppd=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; $idKaryawan=''; $nip=''; $name=''; $grade=''; $city=''; $depatureTime=''; $afterWorkTime=''; $longDay=''; $night=''; $total=''; $status='';

            if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];}
            if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
            if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
            if (isset($request['depature_time']) && $request['depature_time']!='' ) {$depatureTime = $request['depature_time'];}
            if (isset($request['after_work_time']) && $request['after_work_time']!='' ) {$afterWorkTime = $request['after_work_time'];}
            if (isset($request['long_day']) && $request['long_day']!='' ) {$longDay = $request['long_day'];}
            if (isset($request['night']) && $request['night']!='' ) {$night = $request['night'];}
            if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            
            $data_ = DB::table('sppd_mst');
            if($idSppd!='')
            {
                $data_->where('id_sppd',$idSppd);
            }
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($departemen!='')
            {
                $data_->where('departemen',$departemen);
            }
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            if($subDepartemen!='')
            {
                $data_->where('sub_departemen',$subDepartemen);
            }
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($nip!='')
            {
                $data_->where('nip',$nip);
            }
            if($name!='')
            {
                $data_->where('name',$name);
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($city!='')
            {
                $data_->where('city',$city);
            }
            if($depatureTime!='')
            {
                $data_->where('depature_time',$depatureTime);
            }
            if($afterWorkTime!='')
            {
                $data_->where('after_work_time',$afterWorkTime);
            }
            if($longDay!='')
            {
                $data_->where('long_day',$longDay);
            }
            if($night!='')
            {
                $data_->where('night',$night);
            }
            if($total!='')
            {
                $data_->where('total',$total);
            }
            if($status!='')
            {
                $data_->where('status',$status);
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
        $idSppd='-'; $idDepartemen='-'; $departemen='-'; $idSubDepartemen='-'; $subDepartemen='-'; $idKaryawan='-'; 
        $nip='-'; $name='-'; $grade='-'; $city='-';$dateStart=''; $dateFinish=''; $depatureTime='00:00:00'; $afterWorkTime='00:00:00'; $longDay='0'; $night='0'; $total=0; $status='0';
        $note=''; $years=Carbon::now()->format('Y');

        if (isset($request['id_sppd']) && $request['id_sppd']!='' ) {$idSppd = $request['id_sppd'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
        if (isset($request['date_start']) && $request['date_start']!='' ) {$dateStart = $request['date_start'];}
        if (isset($request['date_finish']) && $request['date_finish']!='' ) {$dateFinish = $request['date_finish'];}
        if (isset($request['depature_time']) && $request['depature_time']!='' ) {$depatureTime = $request['depature_time'];}
        if (isset($request['after_work_time']) && $request['after_work_time']!='' ) {$afterWorkTime = $request['after_work_time'];}
        if (isset($request['long_day']) && $request['long_day']!='' ) {$longDay = $request['long_day'];}
        if (isset($request['night']) && $request['night']!='' ) {$night = $request['night'];}
        if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            // cek data
            // $request=[];
            // $request['id_sppd'] = $idSppd;
            // $request['id_karyawan'] = $idKaryawan;
            // $request['type'] = $type;
            // $request['jadwal_pulang'] = $jadwalPulang;
            // $request['jadwal_masuk'] = $jadwalMasuk;
            // $request['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
            // $request['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
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
                $data = new SppdMst();
                $data->id_sppd = $idSppd;
                $data->id_departemen = $idDepartemen;
                $data->departemen = $departemen;
                $data->id_sub_departemen = $idSubDepartemen;
                $data->sub_departemen = $subDepartemen;
                $data->id_karyawan = $idKaryawan;
                $data->nip = $nip;
                $data->name = $name;
                $data->grade = $grade; 
                $data->city = $city; 
                $data->date_start = $dateStart; 
                $data->date_finish = $dateFinish; 
                $data->depature_time = $depatureTime; 
                $data->after_work_time = $afterWorkTime; 
                $data->long_day = $longDay; 
                $data->night = $night; 
                $data->total = $total; 
                $data->note = $note; 
                $data->status = $status; 
                $data->years = $years; 
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
            if (isset($request['id_izin']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$updateData['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$updateData['departemen'] = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$updateData['id_sub_departemen'] = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$updateData['sub_departemen'] = $request['sub_departemen'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$updateData['id_karyawan'] = $request['id_karyawan'];}
            if (isset($request['nip']) && $request['nip']!='' ) {$updateData['nip'] = $request['nip'];}
            if (isset($request['name']) && $request['name']!='' ) {$updateData['name'] = $request['name'];}
            if (isset($request['id_periode']) && $request['id_periode']!='' ) {$updateData['id_periode'] = $request['id_periode'];}
            if (isset($request['date_jadwal']) && $request['date_jadwal']!='' ) {$updateData['date_jadwal'] = $request['date_jadwal'];}
            if (isset($request['type']) && $request['type']!='' ) {$updateData['type'] = $request['type'];}
            if (isset($request['jadwal_pulang']) && $request['jadwal_pulang']!='' ) {$updateData['jadwal_pulang'] = $request['jadwal_pulang'];}
            if (isset($request['jadwal_masuk']) && $request['jadwal_masuk']!='' ) {$updateData['jadwal_masuk'] = $request['jadwal_masuk'];}
            if (isset($request['perbaikan_absen_masuk']) && $request['perbaikan_absen_masuk']!='' ) {$updateData['perbaikan_absen_masuk'] = $request['perbaikan_absen_masuk'];}
            if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$updateData['perbaikan_absen_pulang'] = $request['perbaikan_absen_pulang'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['date_created']) && $request['date_created']!='' ) {$updateData['date_created'] = $request['date_created'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            if (isset($request['reff_upload']) && $request['reff_upload']!='' ) {$updateData['reff_upload'] = $request['reff_upload'];}
          
            DB::table('sppd_mst')
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
