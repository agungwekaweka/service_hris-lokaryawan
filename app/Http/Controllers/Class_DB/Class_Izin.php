<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IzinMst;
use Carbon\Carbon;
use DateTime;

class Class_Izin
{
    public function show($request)
    {
        try
        {
            // set value variable
            $id=''; $idIzin=''; $idKaryawan=''; $idPeriode=''; $dateJadwal=''; $type=''; $jadwalPulang=''; $jadwalMasuk=''; $perbaikanAbsenMasuk=''; $perbaikanAbsenPulang=''; $status=''; $note=''; $dateCreated=''; $years='';
            $idDepartemen=''; $idSubDepartemen='';
                
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
            if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['id_periode']) && $request['id_periode']!='' ) {$idPeriode = $request['id_periode'];}
            if (isset($request['date_jadwal']) && $request['date_jadwal']!='' ) {$dateJadwal = $request['date_jadwal'];}
            if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
            if (isset($request['jadwal_pulang']) && $request['jadwal_pulang']!='' ) {$jadwalPulang = $request['jadwal_pulang'];}
            if (isset($request['jadwal_masuk']) && $request['jadwal_masuk']!='' ) {$jadwalMasuk = $request['jadwal_masuk'];}
            if (isset($request['perbaikan_absen_masuk']) && $request['perbaikan_absen_masuk']!='' ) {$perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];}
            if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
            if (isset($request['date_created']) && $request['date_created']!='' ) {$dateCreated = $request['date_created'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
            $data_ = DB::table('izin_mst');
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
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            if($idIzin!='')
            {
                $data_->where('id_izin',$idIzin);
            }
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($idPeriode!='')
            {
                $data_->where('id_periode',$idPeriode);
            }
            if($dateJadwal!='')
            {
                $data_->where('date_jadwal',$dateJadwal);
            }
            if($type!='')
            {
                $data_->where('type',$type);
            }
            if($jadwalPulang!='')
            {
                $data_->where('jadwal_pulang',$jadwalPulang);
            }
            if($jadwalMasuk!='')
            {
                $data_->where('jadwal_masuk',$jadwalMasuk);
            }
            if($perbaikanAbsenMasuk!='')
            {
                $data_->where('perbaikan_absen_masuk',$perbaikanAbsenMasuk);
            }
            if($perbaikanAbsenPulang!='')
            {
                $data_->where('perbaikan_absen_pulang',$perbaikanAbsenPulang);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($note!='')
            {
                $data_->where('note',$note);
            }
            if($dateCreated!='')
            {
                $data_->where('date_created',$dateCreated);
            }
            if($years!='')
            {
                $data_->where('years',$years);
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
        $idIzin=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; $idKaryawan='';$idPeriode=''; 
        $dateJadwal=''; $type=''; $jadwalPulang=''; $jadwalMasuk=''; $perbaikanAbsenMasuk=0; $perbaikanAbsenPulang=''; $jamIzinKeluar=''; $jamIzinPulang='';
        $status=''; $note=''; $dateCreated=''; $years='';
        
        if (isset($request['id_izin']) && $request['id_izin']!='' ) {$idIzin = $request['id_izin'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['nip']) && $request['nip']!='' ) {$nip = $request['nip'];} 
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['id_periode']) && $request['id_periode']!='' ) {$idPeriode = $request['id_periode'];}
        if (isset($request['date_jadwal']) && $request['date_jadwal']!='' ) {$dateJadwal = $request['date_jadwal'];}
        if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
        if (isset($request['jadwal_pulang']) && $request['jadwal_pulang']!='' ) {$jadwalPulang = $request['jadwal_pulang'];}
        if (isset($request['jadwal_masuk']) && $request['jadwal_masuk']!='' ) {$jadwalMasuk = $request['jadwal_masuk'];}
        if (isset($request['perbaikan_absen_masuk']) && $request['perbaikan_absen_masuk']!='' ) {$perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];}
        if (isset($request['perbaikan_absen_pulang']) && $request['perbaikan_absen_pulang']!='' ) {$perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];}
        if (isset($request['jam_izin_keluar']) && $request['jam_izin_keluar']!='' ) {$jamIzinKeluar = $request['jam_izin_keluar'];}
        if (isset($request['jam_izin_pulang']) && $request['jam_izin_pulang']!='' ) {$jamIzinPulang = $request['jam_izin_pulang'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['date_created']) && $request['date_created']!='' ) {$dateCreated = $request['date_created'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
 
        try
        {
            // cek data
            $request=[];
            $request['id_izin'] = $idIzin;
            $request['id_karyawan'] = $idKaryawan;
            $request['type'] = $type;
            $request['jadwal_pulang'] = $jadwalPulang;
            $request['jadwal_masuk'] = $jadwalMasuk;
            $request['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
            $request['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
            $request['date_jadwal'] = $dateJadwal;
        
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
                $data = new IzinMst();
                $data->id_izin = $idIzin;
                $data->id_departemen = $idDepartemen;
                $data->departemen = $departemen;
                $data->id_sub_departemen = $idSubDepartemen;
                $data->sub_departemen = $subDepartemen;
                $data->id_karyawan = $idKaryawan;
                $data->nip = $nip;
                $data->name = $name;
                $data->id_periode = $idPeriode; 
                $data->date_jadwal = $dateJadwal; 
                $data->type = $type; 
                $data->jadwal_pulang = $jadwalPulang; 
                $data->jadwal_masuk = $jadwalMasuk; 
                $data->perbaikan_absen_masuk = $perbaikanAbsenMasuk; 
                $data->perbaikan_absen_pulang = $perbaikanAbsenPulang; 
                $data->jam_izin_keluar = $jamIzinKeluar; 
                $data->jam_izin_pulang = $jamIzinPulang; 
                $data->status = $status; 
                $data->note = $note; 
                $data->date_created = $dateCreated; 
                $data->years = $years; 
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
            if (isset($request['jam_izin_keluar']) && $request['jam_izin_keluar']!='' ) {$updateData['jam_izin_keluar'] = $request['jam_izin_keluar'];}
            if (isset($request['jam_izin_pulang']) && $request['jam_izin_pulang']!='' ) {$updateData['jam_izin_pulang'] = $request['jam_izin_pulang'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['date_created']) && $request['date_created']!='' ) {$updateData['date_created'] = $request['date_created'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            if (isset($request['reff_upload']) && $request['reff_upload']!='' ) {$updateData['reff_upload'] = $request['reff_upload'];}
          
            DB::table('izin_mst')
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
