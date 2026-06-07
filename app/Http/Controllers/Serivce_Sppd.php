<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Model\Sppd\SppdModel;

class Serivce_Sppd extends Controller
{
    public function getSppd(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new SppdModel();
            $resultModel = $classModel->getSppd($request);
        
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data SPPD Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get SPPD',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function createdSppd(Request $request)
    {
        try
        {
     
            $result = [];
            $classModel = new SppdModel();
            $resultModel = $classModel->insertSppd($request); 
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created SPPD Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created SPPD',
                    'data' => $resultModel['message']
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updatedSppd(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new SppdModel();
            $resultModel = $classModel->updatedSppd($request); 
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Updated SPPD Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Updated SPPD',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
