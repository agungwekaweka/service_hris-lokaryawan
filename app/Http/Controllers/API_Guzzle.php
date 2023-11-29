<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class API_Guzzle extends Controller
{
    private function urlLokaHR()
    {
        // $url= 'https://lokahr.salokapark.app/api/';
        $url = 'http://192.168.0.75:8091/api/';
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

    // GET SERVICE
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

    // POST SERVICE
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
}
