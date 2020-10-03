<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class candoctor
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
        if (JWTAuth::user()->type_id == 2) {
            return $next($request);
        }
        return response()->json([
            'resp'=>'No autorizado',
            'data'=>''
        ], 401);
    }
}
