<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportGuest;

class Service_KomplementGuest extends Controller
{
    /**CREATE REQUEST Survey
     * PARAM REQUEST : 
     * visitors, json_request_aloha, json_request_feedback
     */
    public function requestKomplementUserGuest(Request $request)
    {
        try
        {
            $komplementGuest = new KomplementGuest();
            $data = $komplementGuest->requestKomplementUsersGuest($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Request Komplement Users Guest Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getKomplementUserGuest(Request $request)
    {
        try
        {
            $komplementGuest = new KomplementGuest();
            $data = $komplementGuest->getKomplementUsersGuest($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Komplement Users Guest Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function importUsersGuest(Request $request)
    {
       try
       {
           // validasi
           $this->validate($request, [
               'file' => 'required|mimes:csv,xls,xlsx'
           ]);
    
           // menangkap file excel
           $file = $request->file('file');
    
           // membuat nama file unik
           $nama_file = rand().$file->getClientOriginalName();

           // Excel::import(new karyawanImport, 'http://10.10.10.9:8099/storage/'.$nama_file);
         
           Excel::import(new ImportGuest,$file);
        
           $result=response()->json([
               'status' => 'success',
               'message' => 'Import Data Master Users Guest Komplement Successfuly'
           ]);
           return $result;
       } catch (\Exception $ex) {
           return $ex;
       }
    }

    public function getUsersGuest(Request $request)
    {
        try
        {
            $komplementGuest = new KomplementGuest();
            $data = $komplementGuest->getUsersGuest($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Users Guest Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateUsersGuest(Request $request)
    {
        try
        {
            $komplementGuest = new KomplementGuest();
            $data = $komplementGuest->updateUsersGuest($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Users Guest Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

}
