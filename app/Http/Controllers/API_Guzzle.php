<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class API_Guzzle extends Controller
{
    private function urlLokaHR()
    {
        $url= 'https://lokahr.salokapark.app/api/';
        // $url = 'http://192.168.0.75:8091/api/';
        return $url;
    }
 
    private function urlWebWhatsapp()
    {
        // server
        // $url = "103.164.114.22:8200/SendMessage";
        // lokal
        $url = "10.10.10.28:8200/SendMessage";
        return $url;
    }

    public function urlLokaryawan()
    {
        // server
        // $url = "https://servicelokaryawan.salokapark.app/";
        // lokal
        $url = 'http://192.168.0.75:8099/';
        return $url;
    }

    public function urlTiketing()
    {
        // server
        $url = 'http://10.10.10.35:8097/api/';
        return $url;
    }

    public function urlTiketingLocal()
    {
        $url = 'http://192.168.0.139:8000/api/';
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
        try 
        {
            $client = new \GuzzleHttp\Client();

            $url = $this->urlWebWhatsapp();
            $myBody['apikey'] = "123456";
            $myBody['recipients']=[
                $telephone
            ];
            $myBody['message']=$message;
            $request = $client->post($url,  ['form_params'=>$myBody]);
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
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
            $request = $client->post($url,  ['form_params'=>$myBody]);
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 
    // END POST SERVICE 

    // POST SERVICE TIKETING
    public function postServiceTiketing($cusName,$cusEmail,$arrival,$amountTotal,$bookingCode,$qty,$qtyBonus,$paymentMethods,$ticketOrder)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $var ='create-reservasi';

            $url = $this->urlTiketingLocal().$var;
            $myBody['cust_name'] = $cusName;
            $myBody['cust_email'] = $cusEmail;
            $myBody['arrival'] = $arrival;
            $myBody['amount_total'] = $amountTotal;
            $myBody['booking_code'] = $bookingCode;
            $myBody['qty'] = $qty;
            $myBody['qty_bonus'] = $qtyBonus;
            $myBody['payment_methods'] = $paymentMethods;
            $myBody['ticket_order']=[
                $ticketOrder
            ];
       
            $request = $client->post($url,  ['form_params'=>$myBody]);
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 
    // END POST SERVICE 

    // POST TICKETING
     // server Ticketing
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
}
