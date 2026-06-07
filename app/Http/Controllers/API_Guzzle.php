<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class API_Guzzle extends Controller
{
    public function urlLokaHR()
    {
        $url= 'https://lokahr.salokapark.app/api/';
        return $url;
    }

    public function urlPayroll()
    {
        // server
        $url = 'http://10.10.10.26:8099/api/';
        // local
        // $url = 'http://192.168.0.75:8091/api/';
        return $url;
    }
 
    public function urlWebWhatsapp()
    {
        // server Foonte 
        $url = "https://api.fonnte.com/send";
        // server Saloka
        // $url = "103.164.114.22:8200/SendMessage";
        // $url = "wamembership.salokapark.site/SendWhatsAppImage";
        // lokal
        // $url = "10.10.10.28:8200/SendMessage";
        return $url;
    }

    public function urlLokaryawan()
    {
        // server
        $url = "https://servicelokaryawan.salokapark.app/";
        // lokal
        // $url = 'http://192.168.0.75:8099/';
        return $url;
    }

    public function urlTiketing()
    {
        // server
        // $url = 'http://103.164.114.22:8097/api/';
        $url = 'https://samedayreservation.salokapark.site/api/';
        // local
        // $url = 'http://10.10.10.35:8097/api/';
        return $url;
    }

    public function urlTiketingLocal()
    {
        // server
        // $url = 'https://servicereservasi.salokapark.app/api/';
          $url = 'http://10.10.100.15:8151/api/';
        // local 
        // $url = 'https://servicepg.salokapark.app/api/';
        return $url;
    }

    // --------------------------------------------------------------------------------
    // GET SERVICE
    // server LOKAHR
    public function getServiceLokaHR($var)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $url = $this->urlLokaHR();
            $request = $client->get($url.$var);
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // server WA
    public function getServiceWhatsapp($telephone,$message)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        try 
        {

            $client = new \GuzzleHttp\Client();

            $url = $this->urlWebWhatsapp();

            $response = $client->request('POST', $this->urlWebWhatsapp(), [
                'headers' => [
                     // 'Authorization' => '+PkfUaYYGfR1+gRCx9no',
                    'Authorization' => '2RhqkkL3Vrp8FkRFcRpi',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'target' => $telephone,
                    'message' => $message
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // END GET SERVICE
    // -------------------------------------------------------------------------------------------

    // POST
    // POST SERVICE LOKAHR
    public function postServiceLokaHR($var,$nip,$jsonTanggal,$keterangan,$valueKehadiran)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $url = $this->urlLokaHR().$var;
            $myBody['nip'] = $nip;
            $myBody['json_tanggal']=$jsonTanggal;
            $myBody['keterangan']=$keterangan;
            $myBody['value_kehadiran']=$valueKehadiran;
            $request = $client->post($url, ['form_params'=> $myBody]);      
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 

    public function postServiceIzinLokaHR($requestModule)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $var = $requestModule['var'];
            $url = $this->urlLokaHR().$var;
            $request = $client->post($url, ['form_params'=> $requestModule]);      
            $response = $request->getBody();   
            $jsonDecode = json_decode($response);
            return [
                'success' => true,
                'message' => 'Update Service LokaHR Successfuly',
                'data' => $jsonDecode
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    } 
    // END POST SERVICE 

    // POST SERVICE TIKETING
    public function postServiceTiketing($apiServiceName,$name,$email,$employee_id,$bookingDate,$ticketOrder)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $var =$apiServiceName;

            $url = $this->urlTiketingLocal().$var;
            $myBody['name'] = $name;
            $myBody['email'] = $email;
            $myBody['employee_id'] = $employee_id;
            $myBody['bookingDate'] = $bookingDate;
            $myBody['ticketOrder']= $ticketOrder;

            $request = $client->post($url,  ['form_params'=>$myBody]);

            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 

    public function postGetPaymentLink($apiServiceName,$orderId)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $var =$apiServiceName;
          
            $url = $this->urlTiketingLocal().$var;
            $myBody['orderID'] = $orderId;
            $request = $client->post($url,  ['form_params'=>$myBody]);
        
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 

    public function postServiceCekEvent($tanggal)
    {
        try {
           $client = new \GuzzleHttp\Client();
           $var ='get_holiday';

           $url = $this->urlTiketing().$var;
           $myBody['arrival_date'] = $tanggal;
      
           $request = $client->post($url,  ['form_params'=>$myBody]);
           $response = $request->getBody();
     
           $jsonDecode = json_decode($response);
           return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    // END POST SERVICE 

    // POST SERVICE PAYROLL
    public function postOvertime($apiServiceName,$orderId)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $var =$apiServiceName;
          
            $url = $this->urlTiketingLocal().$var;
            $myBody['orderID'] = $orderId;
            $request = $client->post($url,  ['form_params'=>$myBody]);
        
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 
    // END POST SERVICE PARROLL
}
