<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\secondreferral;
use App\Models\thirdreferral;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

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

    // use RegistersUsers;

    /**
    * Where to redirect users after login / registration.
    *
    * @var string
    */
    protected $redirectTo = '/home';

    /**
    * Create a new controller instance.
    *
    * @return void
    */

    public function __construct()
 {
        $this->middleware( 'guest' );
    }

    /**
    * This method is used to register users from mobile app
    * @param  Request $request [ description ]
    * @return [ type ]           [ description ]
    */


    


    public function postRegisterApp( Request $request )
 {
        // dd( $request );
        $columns = array(
            'name'     => 'required|max:20|',
       );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
        {
            // $response[ 'status' ] = 0;

            // $response[ 'message' ] = 'Validation Errors';
            // $response[ 'errors' ] = $validated->errors()->messages();
            // return $response;
             return response()->json("Name must be less then 20 characters", 400);
        }
         $columns = array(
            'username' => 'required|unique:users,phone',
      );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
        {
           return response()->json("Phone no already exist", 400);
        }
         $columns = array(
            'email'    => 'required|unique:users,email',
        );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
        {
           return response()->json("Email already exist", 400);
        }
         $columns = array(
            'password' => 'required|min:8',
        );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
        {
             return response()->json("Password must at leats 8 characters", 400);
        }
         $columns = array(
          'password_confirmation'=>'required|min:5|same:password',
        );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
        {
            return response()->json("Password & Confirm Password must be same", 400);
        }
        
       
        $role_id = 5;
        $response[ 'status' ] = 0;

        $response[ 'message' ] = '';

        try 
        {
            $slug = str_slug( $request->name );
            $userinfo = [
                'name'              => $request->name,
                'phone'             => $request->username,
                'email'             => $request->email,
                'password'          => bcrypt( $request->password ),
                'role_id'           => $role_id,
                'slug'              => $slug,
                'login_enabled'     => 1,
                'device_id'         => $request->device_id,
                'role_id'           => 5,
            ];
            
           

            // return $userId;
            //check for referral code
        $referralcode=$request->referralCode;
        if(isset($referralcode)){
            $parentcode=DB::table('parentreferrals')->where('referralCode', $referralcode)->first();

            if($parentcode==null){
                $secondcode=DB::table('secondreferrals')->where('referralCode', $referralcode)->first(); 
                // $countsecond= $secondcode->count();
                if($secondcode==null) {
                    // $rec=User::where('id', $userId)->delete();
                    // DB::rollBack();
                    $response['status'] = 0;        
                    $response['message'] = 'Referral Code is not mandatory';
                    $response['errors'] = 'Invalid Referral Code';

                    return $response;
                }else{
                    $user = User::create( $userinfo );

                    $userId=$user->id;

                    $countthird=DB::table('thirdreferrals')->where('parentreferralCode', $referralcode)->count();
                    $countthird=1+$countthird;
                    $data=new thirdreferral();
                    $data->parentReferralCode = $referralcode;
                    $data->referralCode = $referralcode.'/'.$countthird;
                    $data->userId = $userId;

                    $data->save();

                } 
            }else{
                $user = User::create( $userinfo );

                $userId=$user->id;

                $countsecond=DB::table('secondreferrals')->where('parentreferralCode', $referralcode)->count();
                $countsecond=1+$countsecond;
                $data=new secondreferral();
                $data->parentReferralCode = $referralcode;
                $data->referralCode = $referralcode.'-'.$countsecond;
                $data->userId = $userId;

                $data->save();

            }

        }else{
            $user = User::create( $userinfo );
    
            $userId=$user->id;

        }
        //check for referral code ends

        //send OTP
        $OTP=rand('999', '9999');
        $userName= $user->name;
        $email=$user->email;
        $email_expires_at=now()->addMinutes(10);
        
        $data=['OTP'=>$OTP, 'user'=>$userName];
        $emailcandi=$email;
        // try{
        //     Mail::send('mail', ['OTP'=>$OTP, 'user'=>$userName],
        //     function ($message) use ($emailcandi) {
        //         $message->to($emailcandi);
        //         // $message->cc('recruitment@arcinventador.com');
        //         $message->subject('Subject');
        //     });

        //     DB::table('users')->where('id', $user->id)->update(['activation_code'=>$OTP, 'email_expires_at'=>$email_expires_at]);

        //     return true;
        
        // }catch ( \Exception $e ) {
        //     echo json_encode($e);
        // }
            
        $response['data']=$user->id;
        $response['status'] = 1;
        $response['message'] = 'Registered Successfully. Please verify your email';
            
            
        
        }catch( Exception $ex )
        {
            $response[ 'status' ] = 0;
            $message = $ex->getMessage();
            $response[ 'message' ] = $message;
        }

        return $response;
    }


    

    public function emailverification(Request $request){

        $validate=Validator::make($request->all(), [
            'otp' => 'required'
        ]);

        // return count($validate->errors());
        if(count($validate->errors())){
            $response['status']=400;
            $response['message']=$validate->errors()->messages();
            return $response;
        }
        $user=DB::table('users')->where('activation_code', $request->otp)->first();

        // dd($user);
        if(isset($user->activation_code)){
            if($request->otp!=$user->activation_code){
                $response['message']="Please enter correct OTP!!";
                $response['status']=401;
            }elseif($request->otp==$user->activation_code && now()->isAfter($user->email_expires_at)){
                $response['message']='OTP expired... Please generate new OTP!';
                $response['status']=401;
            }elseif($request->otp==$user->activation_code && now()->isBefore($user->email_expires_at)){
                DB::table('users')->where('activation_code', $request->otp)->update(['is_verified'=>1]);
                $response['data']=$user->id;
                $response['message']="Email verified!!";
                $response['status']=200;
            }
        }else{
            // $response['message']="Entered OTP is invalid";
            // $response['status']=401;
               return response()->json("Entered OTP is invalid", 400);
        }

        return $response;


    }


    public function generateOTP(Request $request){
        
        $user=DB::table('users')->where('email', $request->email)->first();

        // dd($user);
        $OTP=rand('999', '9999');
        $userName= $user->name;
        $email=$user->email;
        $email_expires_at=now()->addMinutes(10);
        
        // $data=['OTP'=>$OTP, 'user'=>$userName];
        $emailcandi=$email;
        try{
            if($request->isforget == 0)
                Mail::send('mail', ['OTP'=>$OTP, 'user'=>$userName],
                function ($message) use ($emailcandi) {
                    $message->to($emailcandi);
                    // $message->cc('recruitment@arcinventador.com');
                    $message->subject('Subject');
                });
            else{
                Mail::send('forgetmail', ['OTP'=>$OTP, 'user'=>$userName],
                function ($message) use ($emailcandi) {
                    $message->to($emailcandi);
                    // $message->cc('recruitment@arcinventador.com');
                    $message->subject('Subject');
                });
            }

            DB::table('users')->where('id', $user->id)->update(['activation_code'=>$OTP, 'email_expires_at'=>$email_expires_at]);

            $response['message']='New OTP is sent... please check your email!';
            $response['status']=200;
        }catch(\Exception $e){
            $response['message']=json_encode($e);
            $response['status']=401;
        }

        return $response;
    }

    public function resetpassword( Request $request )
    {
        $validate = Validator::make($request->all(), [ 
            'password' => 'required|min:5',
            'password_confirmation'=>'required|min:5|same:password',
        ]);
        if ($validate->fails()) {    
            return response()->json("The password must be at least 5 characters.", 400);
        }
        try 
        {
            DB::table('users')->where('id', $request->userId)->update(['password'=>bcrypt( $request->password)]);
            $response['status'] = 200;
            $response['message'] = 'Password Reset Successfully';
        }catch( Exception $ex )
        {
            $response[ 'status' ] = 400;
            $message = $ex->getMessage();
            $response[ 'message' ] = $message;
        }
           return $response;
       }
}


