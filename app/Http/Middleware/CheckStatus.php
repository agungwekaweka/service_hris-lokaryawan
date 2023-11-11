<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle($request, Closure $next)
    {
        try
        {
            if (session()->has('status')) {
                $status = $request->session()->get('status');
                if ($status !== 'logged in') {
                    return response()->json('Your account is inactive');
                } else {
                    return $next($request);
                }
            } else {
                return response()->json('Your account not has session');
            }
        }
        catch (\Exception $ex) {
            return $ex;
        }
    }
}
