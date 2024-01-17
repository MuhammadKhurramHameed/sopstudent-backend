<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Repositories\LoginRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use JWTAuth;
use Image;
use DB;
use Input;
use App\Item;
use Session;
use Response;
use Validator;
use URL;

class loginController extends Controller
 {
    protected $login;

    public function __construct( LoginRepository $login )
 {
        $this->login = $login;
    }

    // public function login( Request $request ) {
    //     $validate = Validator::make( $request->all(), [
    //         'email' 		=> 'required',
    //         'password'	=> 'required',
    //     ] );
    //     if ( $validate->fails() ) {

    //         return response()->json( 'Enter Credentials To Signin', 400 );
    //     }
    //     $token = $this->login->athenticate( $request );
    //     $user = JWTAuth::user();

    //     $user=DB::table('users')->select('id  as user_id', 'email', 'role_id', 'name', 'is_verified' ,'username')->where('id', $user->id)->first();
    //     // dd($test);
    //     $path = URL::to( '/' ).'/public/user_picture/';
    //     if ( $token ) {
    //         return response()->json( [ 'token' => $token, 'data' => $user, 'path' => $path, 'message' => 'Login Successfully' ], 200 );
    //     } else {
    //         return response()->json( 'Invalid Email Or Password', 400 );
    //     }
    // }

    public function login( Request $request ) {
        $validate = Validator::make( $request->all(), [
            'email' 		=> 'required',
            'password'	    => 'required',
        ] );
        if ( $validate->fails() ) {

            return response()->json( 'Enter Credentials To Signin', 400 );
        }
        $token = $this->login->athenticate( $request );
        // dd($token);
        // dd($token);
        if ( $token ) {
            $user = JWTAuth::user();
            $user=DB::table('users')
            ->select('id  as user_id', 'email', 'role_id','image',  'name' , 'is_verified', 'username')
            ->where('id', $user->id)
            ->where('status_id', '=', 1)
            ->where('role_id', '=', 1)
            ->first();
            if(isset($user->email)){
                
                $path = URL::to( '/' ).'/public/userimage/';
                if($user->is_verified){
                    return response()->json( [ 'token' => $token, 'data' => $user, 'path' => $path, 'message' => 'Login Successfully' ], 200 );
                }else{
                    return response()->json( 
                        [
                            'success' => false,
                            'message' => 'Please verify your email!',
                        ], 400 );
                }
            }else{
                return response()->json( 
                    [
                        'success' => false,
                        'message' => 'Only Students Are Allowed To Login',
                    ], 400 );
            }
        } else {
            return response()->json( 
                [
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                 ], 400 );
        }
    }
}