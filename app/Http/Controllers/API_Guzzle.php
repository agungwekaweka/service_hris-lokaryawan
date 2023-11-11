<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class API_Guzzle extends Controller
{
    public function getServiceLokaHR($var)
    {
        try {
            $client = new \GuzzleHttp\Client();
            // server
            // $request = $client->get('https://lokahr.salokapark.app/api/'.$var);
            // local
            $request = $client->get('http://192.168.0.75:8099/api/'.$var);
            $response = $request->getBody();
            $jsonDecode = json_decode($response);
            return $jsonDecode;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getServiceWhatsapp($telephone,$message)
    {
        try {
            $client = new \GuzzleHttp\Client();
            // server
            $url = "103.164.114.22:8200/SendMessage";
            // lokal
            // $url = "10.10.10.28:8200/SendMessage";
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
    
}
