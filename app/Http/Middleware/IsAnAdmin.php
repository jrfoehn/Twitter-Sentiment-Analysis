<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class IsAnAdmin
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
        if(Auth::user()){
            if(Auth::user()->admin === 1) {
                return $next($request);
            } 
            else {
                return redirect('home')->with('message','You are not allowed to access this page');
            }
        }
        else 
        {
            return redirect('home')->with('message','You have to login');
        }   
    }

}
