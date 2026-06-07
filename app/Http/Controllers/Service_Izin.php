<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Model\Izin\IzinModel;

class Service_Izin extends Controller
{
    public function getCountPermission(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new IzinModel();
            $resultModel = $classModel->getCountPermission($request);
        
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Total Izin Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get Total Izin',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getIzin(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new IzinModel();
            $resultModel = $classModel->getIzin($request);
        
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Izin Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get Izin',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function createdIzin(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new IzinModel();
            $resultModel = $classModel->insertIzin($request); 
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Izin Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => $resultModel['message']
                    // 'data' => $resultModel['message']
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updatedIzin(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new IzinModel();
            $resultModel = $classModel->updatedIzin($request); 
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Updated Izin Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => $resultModel['message']
                    // 'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
