<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(!$request->hasHeader("Authorization"))
            return response()->json(["message"=>"Auth Key Required ..."]);

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message'=>'user not found']);
        }else{
            return $next($request);
        }
    }
}
