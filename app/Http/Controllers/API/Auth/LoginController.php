<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(){ 
        
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            
            $user = Auth::user(); 
            
            if($user->user_role != "admin" && $user->activation_token !=null )
            {
                
                Auth::logout();
                
                 return response()->json(['message'=>'Please activate your account, link is already shared with you on your email'], 200); 
                
            }
            
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            
            return response()->json(['success' => $success], 200); 
            
        } 
        else{ 
            
            return response()->json(['error'=>'Username or password is wrong'], 401); 
        } 
    }
}
