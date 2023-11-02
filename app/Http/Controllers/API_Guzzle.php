<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class API_Guzzle extends Controller
{
    public function getServiceLokaHR($var)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('https://lokahr.salokapark.app/api/'.$var);
        $response = $request->getBody();
        $jsonDecode = json_decode($response);
        return $jsonDecode;
    }
}
