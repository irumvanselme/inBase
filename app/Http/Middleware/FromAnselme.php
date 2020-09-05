<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FromAnselme
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
        if(!$request->hasHeader("app-orgin") || $request->header("app-orgin") != "from_anselme")
            return response()->json(["message"=>"This Application is for Authorized Users Only"]);
        else
            return $next($request);
    }
}
