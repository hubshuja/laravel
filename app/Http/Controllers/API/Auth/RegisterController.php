<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request; 
use Mail;
use DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    public function store(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            
            'name' => 'required', 
            
            'username'=>'required|unique:users|min:6|max:20',
            
            'email' => 'required|email|unique:users', 
            
            'password' =>  'required|min:6',
            
            'confirm_password' => 'required|same:password', 
            
            'user_role' => 'required', 
            
            'avatar'=>'required |image|mimes:jpeg,png,jpg,gif,svg|dimensions:width=256,height=256',
        ]);
        
      if ($validator->fails()) { 
    
            return response()->json(['error'=>$validator->errors()], 400);  
            
        }
        $input = $request->all(); 
        
         $imageName = time().'.'.$request->avatar->extension();  
     
        $request->avatar->move(public_path('images'), $imageName);
        
        $input['avatar'] = $imageName;
        
        $input['password'] = bcrypt($input['password']); 
        
        $user = User::create($input); 
        
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        
        $success['name'] =  $user->name;
        
        
        return response()->json(['success'=>$success], 200); 
    }
    
    public function saveInviteUser(Request $request)
    {
        
        
       $input = $request->all();
        
       if(!empty($input['token']))
       {
           
       $users =  User::where('share_link', $input['token'])->first();
                    
         return response()->json(['data'=>$users], 200); 
           
       }
       
         return response()->json(['data'=>''], 400); 
      
        
    }
    
    public function update(Request $request, $id){
        
        $user = User::find($id);
        
         $validator = Validator::make($request->all(), [ 
            
            'name' => 'required', 
            
            'username'=>'required|min:6|max:20|unique:users,username,'.$user->id,
            
            'email' => 'required|email|unique:users,email,'.$user->id, 
            
            'password' =>  'required|min:6',
            
            'confirm_password' => 'required|same:password', 
             
            'avatar'=> 'required |image|mimes:jpeg,png,jpg,gif,svg|dimensions:width=256,height=256',
        ]);
        
      if ($validator->fails()) { 
    
            return response()->json(['error'=>$validator->errors()], 400);  
            
        }
        
        if(!empty($user))
        {
            $input = $request->all();
            
            $user->name = $input['name'];
            
            $user->username = $input['username'];
            
            $user->email = $input['email'];
            
            $user->password =   bcrypt($input['password']); 
            
            $user->username = $input['username'];
            
            $user->user_role = 'user';
            
            $user->share_link = NULL;
            
             $imageName = time().'.'.$request->avatar->extension();  
     
            $request->avatar->move(public_path('images'), $imageName);

             $user->avatar =  $imageName;
             
             $user->registered_at =  date('Y-m-d');
             
             
             $token = random_int(100000, 999999);
             
             $user->activation_token = $token;

            $user->save(); 
               
        $token = url('/api/ativate-account?token='.$token);
        
        $data = array('name'=>$user->name, 
                     'token'=>$token,
                     'user_email'=>$user->email,
                     'admin_email'=>'admin@admin.com',
                     'admin_name'=>'admin',
                    );
           try {
                Mail::send('mail-token', $data, function($message) use ($data) {

                   $message->to($data['user_email'], 'Admin')->subject

                      ('Account activation');

                   $message->from($data['admin_email'], $data['admin_name']);

                });
           }
         catch(\Exception $e) {
             //The email system on local is not working so i am retruning the share link in the catch block
                       DB::commit();
             return response()->json(['link'=>$token], 400); 

         }
               
               
            
            
        }
        else{
            
            
        }
    }
    
    public function activateAccount(Request $request)
    {
        
        $input = $request->all();
        
       if(!empty($input['token']))
       {
           
       $users =  User::where('activation_token', $input['token'])->first();
       
       if(!empty($users))
       {
           $users->activation_token =  null;
           
           $users->save();
           
       }
                    
         return response()->json(['message'=>'Account activated successfully!'], 200); 
           
       }
       
         return response()->json(['data'=>''], 400); 
      
        
    }
}
