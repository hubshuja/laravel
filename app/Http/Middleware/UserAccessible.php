<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class UserAccessible
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

            if(!Auth::user()){
                // redirect page or error.
                 return response()->json(['success' => 'Not allowed!'], 400); 
            }

            return $next($request);
        }
}
