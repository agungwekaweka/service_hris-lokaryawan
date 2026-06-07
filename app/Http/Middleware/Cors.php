<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        header_remove('Access-Control-Allow-Origin');
        // Hapus semua nilai header Access-Control-Allow-Origin yang sudah ada
        $response->headers->remove('Access-Control-Allow-Origin');

        // Use a method to determine if the response is an instance of BinaryFileResponse
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            // Correctly set headers on a BinaryFileResponse
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
        } else {
            // Set header Access-Control-Allow-Origin ke domain yang meminta akses
            $response->header('Access-Control-Allow-Origin', '*');
    
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); 
        }
        
        return $response;
    }
}
