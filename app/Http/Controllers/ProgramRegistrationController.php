<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
;
use \App;
use App\Models\User;
use Auth;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\programregistration;
use DB;

class ProgramRegistrationController extends Controller
 {

    // use AuthenticatesUsers;

    public function register( Request $request ) {
        $response[ 'status' ] = 0;
        $response[ 'message' ] = 'Invalid Response!';

        try {
            $validate = \Validator::make( $request->all(), [
                'provinceId'=>'required', 
                'districtId'=>'required', 
                'batchId'=>'required',
                'programId'=>'required', 
                'gradeId'=>'required', 
                'user_id'=>'required' 
                ]);
                if ( count( $validate->errors() ) ) {
                    $response[ 'message' ] = $validate->errors()->all();
                    $response[ 'status' ] = 422;
                }
                $register = new programregistration;
                $register->provinceId = $request->provinceId;
                $register->districtId = $request->districtId;
                $register->batchId = $request->batchId;
                $register->programId = $request->programId;
                $register->gradeId = $request->gradeId;
                $register->user_id = $request->user_id;
                $register->save();
                $response[ 'message' ] = 'Registered Successfully!!';
                $response[ 'status' ] = 200;
            } catch( \Exception $e ) {
                $response[ 'status' ] = 400;
                $response[ 'message' ] = $e->getMessage();
            }
            return $response;
        }
    }
