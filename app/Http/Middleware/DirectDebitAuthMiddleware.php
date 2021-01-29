<?php

namespace App\Http\Middleware;

use Closure;

class DirectDebitAuthMiddleware
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
        //check if the request api key is equal to the Api key
       
        if(!$request->header('API-KEY') || $request->header('API-KEY') !== env('API_KEY')){
            return response()->json([
               'message'=> 'unauthorized user'
           ], 401);
       }
        return $next($request);
    }
}
