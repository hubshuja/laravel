<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\Role; 
use Password;
use DB;
use Mail;

class UserController extends Controller 
{
public $successStatus = 200;

    public function details() 
    { 
        $user = Auth::user(); 
        
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
    /*
     * 
     * 
     * Generate unique number 
     */
    private function generateRandomNumber($len = 16) {
        
    $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    
    $randomNumber = '';
    
    for ($i = 0; $i < $len; $i++) {
        
        $randomNumber .= $char[rand(0, $len - 1)];
        
    }
    
    return $randomNumber;
}
    /**
     * 
     * 
     * Share link with user
     */
    
    public function shareLink(Request $request){
        
        /*
         * 
         * Only user with admin role is allowed to share link
         */
        if(!empty(Auth::user()) && Auth::user()->user_role == "admin")
        {
            
             try {
         
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [ 
            
            'email' => 'required|email|unique:users', 
             ]);
            
             if ($validator->fails()) { 
    
            return response()->json(['error'=>$validator->errors()], 400);  
            
        }
        
        $input = $request->all(); 
        
        $share_link_token = $this->generateRandomNumber(16);

       $input['share_link'] = $share_link_token;
       
       $input['registered_atsdfds'] = date('Y-m-d');
        
        $user = User::create($input); 
       
       // print_r($user);
        
        $user->share_link = $share_link_token;
        
        $user->save();
        
        //Send invitation link by email
        
        $share_link = url('/api/create-user-invite?token='.$share_link_token);
        
        $data = array('name'=>Auth::user()->name, 
                     'share_link'=>$share_link,
                     'user_email'=>$input['email'],
                     'admin_email'=>$user->email,
                     'admin_name'=>$user->name,
                    );
           try {
                Mail::send('mail', $data, function($message) use ($data) {

                   $message->to($data['user_email'], 'Admin')->subject

                      ('Invitation');

                   $message->from($data['admin_email'], $data['admin_name']);

                });
           }
         catch(\Exception $e) {
             //The email system on local is not working so i am retruning the share link in the catch block
                       DB::commit();
             return response()->json(['link'=>$share_link], 400); 

         }
             DB::commit();
             
         
          return response()->json(['link'=>$share_link], 400); 
       
           }
          catch(\Exception $e) {
              
            DB::rollback();
            
              return response()->json(['error' => $e->getMessage()], 400); 
        }
            
        }
        else{
            
            
             return response()->json(['message' => 'Sorry you are not allowed'], 400); 
        }
        
      
        
            
       
        
    }
}