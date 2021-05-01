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
        if(!$request->hasHeader("app-origin") || $request->header("app-origin") != "from anselme")
            return response()->json(["message"=>"This Application is for Authorized Users Only"]);
        else
            return $next($request);
    }
}
