<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\guest_users;
use Carbon\Carbon;
use DateTime;

class Class_GuestUsers extends Controller
{
   /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idUsers = ''; $nik=''; $name=''; $grade=''; $email=''; $telephone=''; $isDell='';       
        // declare variable set
        if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
        if (isset($request['nik'])) {$nik = $request['nik'];}
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['grade'])) {$grade = $request['grade'];}
        if (isset($request['email'])) {$email = $request['email'];}
        if (isset($request['telephone'])) {$telephone = $request['telephone'];}
        if (isset($request['is_dell'])) {$isDell = $request['is_dell'];}

        try
        {
            $data_ = DB::table('guest_users');
            if($idUsers!='')
            {
                $data_->where('id_users',$idUsers);
            }
            if($nik!='')
            {
                $data_->where('nik',$nik);
            }
            if($name!='')
            {
                $data_->where('name','like','%'.$name.'%');
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($email!='')
            {
                $data_->where('email',$email);
            }
            if($telephone!='')
            {
                $data_->where('telephone',$telephone);
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
        $idUsers = ''; $nik=''; $name=''; $grade=''; $email=''; $telephone='';
       
        // declare variable set
        if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
        if (isset($request['nik'])) {$nik = $request['nik'];}
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['grade'])) {$grade = $request['grade'];}
        if (isset($request['email'])) {$email = $request['email'];}
        if (isset($request['telephone'])) {$telephone = $request['telephone'];}
        
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
                $data = new guest_users();
                $data->id_users = $idUsers;
                $data->nik = $nik;
                $data->name = $name;
                $data->grade = $grade; 
                $data->email = $email; 
                $data->telephone = $telephone;  
                $data->is_dell = 0;  
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
        $updateData =[];
        try
        {
            // declare variable set
            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}

            if (isset($request['nik']) && $request['nik']!='') {$updateData['nik'] = $request['nik'];}
            if (isset($request['name']) && $request['name']!='') {$updateData['name'] = $request['name'];}
            if (isset($request['grade']) && $request['grade']!='') {$updateData['grade'] = $request['garade'];}
            if (isset($request['email']) && $request['email']!='') {$updateData['email'] = $request['email'];}
            if (isset($request['telephone']) && $request['telephone']!='') {$updateData['telephone'] = $request['telephone'];}
            if (isset($request['is_dell']) && $request['is_dell']!='') {$updateData['is_dell'] = $request['is_dell'];}
       
            DB::table('guest_users')
            ->where('id_users','=',$idUsers)
            ->update($updateData);

            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
