<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\parentReferral;
use App\Models\secondreferral;
use App\Models\thirdreferral;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Image;
use ImageSettings;
use App\QuizCategory;
use App\LmsCategory;
use App\Payment;
use App\Bookmark;
use App\Feedback;
use Hash;
use App\Notifications\UserForgotPassword;
use App\Quiz;
use App\Instruction;
use App\QuizResult;
use App\Notifications\NewUserRegistration;
use DB;
use Datetime;
use Exception;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
 {

    public function profile( Request $request, $id )
 {

        $response[ 'status' ] = 0;
        $response[ 'message' ] = '';
        $user = User::where( 'id', '=', $id )->first();
        if ( !$user )
        {
            $response[ 'message' ] = 'Invalid Userid';
            return $response;
        }
        $userid=thirdreferral::where('userId', $id)->first();
        if(isset($user->id)){
            $registrationno;
            $registration = DB::table('programregistrations')->select('*')->where('user_id', $user->id)->first();
            if(isset($registration->user_id)){
            $ruserid = $registration->user_id;
            $character = 0;
            $batchalphabet = DB::table('batchalphbet')->select('*')->orderBy('batchalphbet_id','DESC')->get();
            foreach ($batchalphabet as $batchalphabets) {
                if ($ruserid >= $batchalphabets->batchalphbet_from && $ruserid <= $batchalphabets->batchalphbet_to) {
                    $character = $batchalphabets->batchalphbet_char;
                    break;
                }
            }
            $provinceinitial = DB::table('province')->select('initial')->where('id', $registration->provinceId)->first();
            $registrationyear = explode('-', $registration->created_at);
            $yearinitial = substr( $registrationyear[0], -2 );
            $sortuserid;
            $zero = '';
            if($userid > 9999){
                $length = strlen($userid)-1;
                $fristdigitid = substr($userid, 0, 1);
                $initialzero = 0;
                for($i = 1; $i<= $length; $i++){
                    $zero .= $initialzero;
                }
                $manualid = $fristdigitid.$zero;
                $userid = $userid-$manualid;
            }else{
                $userid = $user->user_id;  
            }
            if($userid <= 9){
                $sortuserid = '000'.$userid;
            }elseif($userid >= 10 && $userid <= 99){
                $sortuserid = '00'.$userid;
            }elseif($userid >= 100 && $userid <= 999){
                $sortuserid = '0'.$userid;
            }else{
                $sortuserid = $userid;
            }
            $registrationno = $provinceinitial->initial.$character.$registration->gradeId.$sortuserid.$yearinitial;
            }else{
                $registrationno = '-';
            }
            $user->registrationno = $registrationno;
        }
        if($userid==null){

          $userid=secondreferral::where('userId', $id)->first();

          if($userid==null){
            $userid=parentReferral::where('userId', $id)->first();

            if($userid==null){

              //generate new code
              $response['message'] = 'Referral Code does not exist!!';
              $response['status'] = 200;
             $response['user'] = $user;

              return $response;
            //---------------------------------------------

            }else{
              $response['data'] = $userid;
              $response['status'] = 200;
              $response['user'] = $user;
            }

          }else{  //secondTier is not null
            $response['data'] = $userid;
              $response['status'] = 200;
              $response['user'] = $user;
          }
          
        }else{
          $response['data'] = $userid;
              $response['status'] = 200;
              $response['user'] = $user;
        }
        
        return $response;
        
    }

    /**
    * This methos will return the list of settings available and the user selected settings
    */

    public function generateCode(Request $request){

        $validated=Validator::make($request->all(),[
          'province' => 'required',
          'district' => 'required',
          'user_id' => 'required'
        ]);
    
        if(count($validated->errors())){
          $response['message'] = $validate->errors()->messages();
                $response['status'] = 400;
        }
    
        $provinceID=$request->province;
        $districtID=$request->district;
    
        $userId=DB::table('parentreferrals')->where('userId', $request->user_id)->first();
    
        if($userId==null){
        $province_initial=DB::table('province')->select('initial')->where('id', $provinceID)->first();
            $district_initial=$districtID;
            if($districtID<=9){
              $district_initial="00".$districtID;
            }elseif($districtID<99){
              $district_initial="0".$districtID;
            }
            $year_initial=Date('y');
            $student_code=DB::table('parentreferrals')->first();
            // dd(($student_code));
            if($student_code==null){
              $student_code="0001";
            }else{
              $lastCode=DB::table('parentreferrals')->orderby('id', 'desc')->select('id')->first();
              
              $lastCode=$lastCode->id+1;
              // dd($lastCode);
              if($lastCode<=9){
                $student_code="000".$lastCode;
              }elseif($lastCode<99){
                $student_code="00".$lastCode;
              }elseif($lastCode<999){
                $student_code="0".$lastCode;
              }
              // dd($student_code);
            }
            $referral_code=$province_initial->initial.''.$year_initial.''.$district_initial.''.$student_code;
    
                  
                $data=new parentReferral();
                $data->referralCode = $referral_code;
                $data->userId = $request->user_id;
                $data->save();
    
                $response['message'] = "Referral Code generated successfully";
                $response['status'] = 200;
                $response['referralCode'] = $data;
    
                return $response;
    
          }else{
            $response['message'] = "Referral code already Exists";
                $response['status'] = 200;
                $response['referralCode'] = $userId;
    
                return $response;
    
          }
    
       }

       public function getReferee($id){
        $userid=secondreferral::where('userId', $id)->first();
        if($userid==null){
  
          $userid=parentreferral::where('userId', $id)->first();
          if($userid==null){
            return ("please generate your referral Code!!");
          }else{
            $referralCode=$userid->referralCode;
          $referees=secondreferral::join('users', 'users.id', '=', 'secondreferrals.userId')->
          select('secondreferrals.userId', 'secondreferrals.parentreferralCode', 'secondreferrals.referralCode', 'secondreferrals.id', 'users.name')
          ->where('parentreferralCode', $referralCode)->get();
         
          if($referees->count()==null){
            return ("No referees yet!");
          }else{
            // dd($referees);
            foreach($referees as $referee){
              $secondReferees=secondreferral::select('secondreferrals.id', 'secondreferrals.referralCode', 'secondreferrals.parentreferralCode', 'secondreferrals.userId', 'thirdreferrals.userId as thirdUser', 'thirdreferrals.referralCode as thirdreferralcode')->
              join('thirdreferrals','secondreferrals.referralCode', '=', 'thirdreferrals.parentreferralCode')->
              where('thirdreferrals.parentreferralCode', $referee->referralCode)
              ->get();
            }
            return [$secondReferees, $referees];
          }
          }
  
  
        }else{
          $referralCode=$userid->referralCode;
          $referees=thirdreferral::join('users', 'users.id', '=', 'thirdreferrals.userId')->
          select('thirdreferrals.userId', 'thirdreferrals.parentreferralCode', 'thirdreferrals.referralCode', 'thirdreferrals.id', 'users.name')
          ->where('parentreferralCode', $referralCode)->get();
          if($referees->count()==null){
            return ("No referees yet!");
          }else{
            return $referees;
          }
        }
        return $userid;
  
      }

    public function settings( Request $request, $id )
 {
        $response[ 'status' ] = 0;
        $response[ 'message' ] = '';
        $user = User::where( 'id', '=', $id )->first();
        if ( !$user )
 {
            $response[ 'message' ] = 'Invalid Userid';
            return $response;
        }

        $response[ 'quiz_categories' ]   = QuizCategory::get();
        $response[ 'lms_category' ]      = LmsCategory::get();

        $response[ 'selected_options' ] = [
            'quiz_categories'=>[],
            'lms_categories'=>[],
        ];
        if ( isset( $user->settings ) )
        $response[ 'selected_options' ] = json_decode( $user->settings )->user_preferences;
        $response[ 'status' ] = 1;
        return $response;

    }

    public function updateSettings( Request $request )
 {

    }

    public function paymentsHistory( $user_id )
 {

        $response[ 'status' ] = 1;
        $response[ 'message' ] = '';

        $records = Payment::select( [ 'item_name', 'plan_type', 'start_date', 'end_date', 'payment_gateway', 'updated_at', 'payment_status', 'id', 'cost', 'after_discount', 'paid_amount' ] )
        ->where( 'user_id', '=', $user_id )
        ->orderBy( 'updated_at', 'desc' )->get();
        $response[ 'data' ] = $records;
        return $response;
    }

    public function bookmarks( $user_id )
 {
        $response[ 'status' ] = 1;
        $response[ 'message' ] = '';

        $records = Bookmark::join( 'questionbank', 'questionbank.id', '=', 'bookmarks.item_id' )
        ->select( [ 'question_type', 'question', 'marks', 'bookmarks.id', 'bookmarks.user_id' ] )
        ->where( 'user_id', '=', $user_id )
        ->orderBy( 'bookmarks.updated_at', 'desc' )->get();

        $response[ 'data' ] = $records;
        return $response;
    }

    public function saveFeedback( Request $request )
 {
        $user_id = $request->user_id;

        $columns = array(
            'title'                   => 'bail|required|max:40',
            'subject'                 => 'bail|required|max:40',
            'description'             => 'bail|required',
        );

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'Validation Errors';
            $response[ 'errors' ] = $validated->errors()->messages();
            return $response;
        }

        $record = new Feedback();
        $name             =  $request->title;
        $record->title        = $name;
        // $record->slug         = $record->makeSlug( $name );
        //makeslug not working
        $record->slug         = str_slug( $name );
        $record->subject      = $request->subject;
        $record->description    = $request->description;
        $record->user_id      = $user_id;
        $record->save();

        $response[ 'status' ] = 1;
        $response[ 'message' ] = 'Feedback updated successfully';
        return $response;
    }

    /**
    * This method updates the password submitted by the user
    * @param  Request $request [ description ]
    * @return [ type ]           [ description ]
    */

    public function updatePassword( Request $request )
 {

        $response[ 'status' ] = 0;
        $response[ 'message' ] = '';

        $columns = [
            'old_password' => 'required',
            'password'     => 'required|confirmed',
        ];

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'Validation Errors';
            $response[ 'errors' ] = $validated->errors()->messages();
            return $response;
        }

        $credentials = $request->only( 'old_password', 'password', 'password_confirmation' );

        $id = $request->user_id;

        $user = User::where( 'id', '=', $id )->first();

        if ( !$user )
 {
            $response[ 'message' ] = 'Invalid Userid';
            return $response;
        }

        if ( Hash::check( $credentials[ 'old_password' ], $user->password ) ) {
            $password = $credentials[ 'password' ];
            $user->password = bcrypt( $password );
            $user->save();
            $response[ 'status' ] = 1;
            $response[ 'message' ] = 'Password updated successfully';

        } else {
            $response[ 'status' ] = 0;
            $response[ 'message' ] = 'Old and new passwords are not same';
        }

        return $response;
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */

    public function update( Request $request, $id )
 {

        $record     = User::where( 'id', $id )->first();

        if ( !$record )
 {
            $response[ 'message' ] = 'Invalid Userid';
            return $response;
        }
        $columns = [
            'name'      => 'bail|required|max:20|',
            'phone'     => 'bail|required|max:10',

        ];

        $response[ 'status' ] = 0;

        $response[ 'message' ] = '';

        $validated =  Validator::make( $request->all(), $columns );
        if ( count( $validated->errors() ) )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'Validation Errors';
            $response[ 'errors' ] = $validated->errors()->messages();
            return $response;
        }

        $name = $request->name;
        if ( $name != $record->name )
        $record->slug = str_slug( $name );
        // $record->slug = $record::makeSlug( $name );
        //makeslug not working

        $record->name = $name;

        $record->phone = $request->phone;
        $record->address = $request->address;
        $record->save();

        $response[ 'status' ] = 1;

        $response[ 'message' ] = 'Record updated successfully';
        return $response;
    }

    protected function processUpload( Request $request, User $user )
 {

        if ( $request->hasFile( 'image' ) ) {

            $imageObject = new ImageSettings();

            $destinationPath      = $imageObject->getProfilePicsPath();
            $destinationPathThumb = $imageObject->getProfilePicsThumbnailpath();

            $fileName = $user->id.'.'.$request->image->guessClientExtension();
            ;
            $request->file( 'image' )->move( $destinationPath, $fileName );
            $user->image = $fileName;

            Image::make( $destinationPath.$fileName )->fit( $imageObject->getProfilePicSize() )->save( $destinationPath.$fileName );

            Image::make( $destinationPath.$fileName )->fit( $imageObject->getThumbnailSize() )->save( $destinationPathThumb.$fileName );
            $user->save();
        }
    }

    /**
    * [ uploadUserProfileImage description ]
    * @param  Request $request [ description ]
    * @return [ type ]           [ description ]
    */

    public function uploadUserProfileImage( Request $request )
 {

        $validator = Validator::make( $request->all(), [
            'user_id'       => 'required',
            'image'         => 'required'
        ] );

        if ( $validator->fails() ) {

            $response[ 'status' ]  = 0;
            $response[ 'message' ] = 'Invalid input';
            $response[ 'errors' ]  = $validator->errors()->messages();
            return $response;
        }

        $user_id        = $request->user_id;

        $user = User::join( 'role_user', 'users.id', 'role_user.user_id' )
        ->select( [ 'users.*' ] )
        ->where( 'users.id', $user_id )
        ->where( 'users.role_id', getRoleData( 'student' ) )
        ->where( 'users.login_enabled', 1 )
        ->get();

        if ( count( $user ) ) {

            $user = $user[ 0 ];

            $previous_image = $user->image;

            $base64_string = base64_decode( $request->input( 'image' ) );
            $img_name = 'profile_image_'.time().'.'.'jpeg';

            //orginial image
            file_put_contents( 'public/uploads/users/'.$img_name, $base64_string );
            $user->image      = $img_name;
            $user->save();

            //thumb
            Image::make( IMAGE_PATH_PROFILE.$img_name )->fit( 100, 100 )->save( 'public/uploads/users/thumbnail/'.$img_name );

            $image_path =   public_path( 'uploads/users/'.$previous_image );

            if ( $previous_image && file_exists( $image_path ) ) {

                unlink( $image_path );

                $image_thumbpath = public_path( 'uploads/users/thumbnail/'.$previous_image );

                if ( file_exists( $image_thumbpath ) ) {
                    unlink( $image_thumbpath );
                }
            }

            $response[ 'status' ]  = 1;
            $response[ 'message' ] = 'Profile Image uploaded successfully..';
            $response[ 'image_name' ] = $img_name;
            return $response;

        } else {

            $response[ 'status' ] = 0;
            $response[ 'message' ] = 'Loggedin User record not found';
            return $response;
        }
    }

    /**
    * Delete Record based on the provided slug
    * @param  [ string ] $slug [ unique slug ]
    * @return Boolean
    */

    public function deleteBookmarkById( Request $request, $item_id )
 {

        $response[ 'status' ] = 0;

        $record = Bookmark::find( $item_id );

        if ( !$record )
 {
            $response[ 'status' ] = 0;
            $response[ 'message' ] = 'Invalid Bookmark record';

            return $response;
        }

        $record->delete();
        $response[ 'status' ] = 1;
        $response[ 'message' ] = 'Bookmark removed';
        return json_encode( $response );
    }

    /**
    * This method will reset
    * @param  Request $request [ description ]
    * @return [ type ]           [ description ]
    */

    public function resetUsersPassword( Request $request )
 {

        $user  = User::where( 'email', '=', $request->email )->first();

        $response[ 'status' ] = 0;
        $response[ 'message' ] = '';

        if ( !$user )
 {
            $response[ 'message' ] = 'Invalid User';
            return $response;
        }

        DB::beginTransaction();

        try {

            if ( $user != null ) {

                $password       = str_random( 8 );
                $user->password = bcrypt( $password );

                $user->save();

                DB::commit();

                $user->notify( new UserForgotPassword( $user, $password ) );

                $response[ 'status' ] = 1;
                $response[ 'message' ] = 'New password is sent to your email account';

            } else {

                $response[ 'status' ] = 0;
                $response[ 'message' ] = 'No Email exists';

            }
        } catch( Exception $ex ) {
            DB::rollBack();
            $response[ 'message' ] = $ex->getMessage();
        }

        return $response;

    }

    public function updateUserPreferrenses( Request $request, $user_id )
 {

        $record = User::where( 'id', $user_id )->first();

        $options = [];
        if ( $record->settings )
 {
            $options = ( array ) json_decode( $record->settings )->user_preferences;
        }

        $options = array();

        if ( $request->has( 'quiz_categories' ) ) {

            $options[ 'quiz_categories' ] = json_decode( $request->quiz_categories );
        }
        if ( $request->has( 'lms_categories' ) ) {

            $options[ 'lms_categories' ] = json_decode( $request->lms_categories );
        }

        $record->settings = json_encode( array( 'user_preferences'=>$options ) );
        $record->save();

        $response[ 'status' ] = 1;

        $response[ 'message' ] = 'User preferrences updated successfully';
        $response[ 'user_selected_data' ]   = $record->settings;

        return $response;
    }

    public function instructions( $exam_slug )
 {

        $instruction_page = '';
        $record = Quiz::where( 'slug', $exam_slug )->first();

        if ( !$record )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'Exam is not existed';
            return $response;
        }

        if ( $record->instructions_page_id )
        $instruction_page = Instruction::where( 'id', $record->instructions_page_id )->first();

        $response[ 'instruction_data' ] = '';

        if ( $instruction_page ) {
            $response[ 'instruction_data' ]  = $instruction_page->content;
            $response[ 'instruction_title' ] = $instruction_page->title;
        }

        $response[ 'status' ]  = 1;

        $response[ 'message' ] = '';

        return $response;

    }

    public function subjectAnalysis( $user_id )
 {
        $user = User::find( $user_id );

        if ( !$user )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'User is not found';
            return $response;
        }

        $records = array();
        $records = ( new QuizResult() )->getOverallSubjectsReport( $user );

        $result = array();
        foreach ( $records as $key=>$record ) {
            $record[ 'subject_id' ] = $key;
            array_push( $result, $record );
        }

        if ( !$result )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'No records are not available';
            return $response;
        }

        $response[ 'subjects' ]   = $result;
        $response[ 'user' ]       = $user;
        $response[ 'status' ]  = 1;

        $response[ 'message' ] = '';

        return $response;

    }

    public function examAnalysis( $user_id )
 {

        $user = User::find( $user_id );

        if ( !$user )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'User is not found';
            return $response;
        }

        $records = array();

        $records = Quiz::join( 'quizresults', 'quizzes.id', '=', 'quizresults.quiz_id' )
        ->select( [ 'title', 'is_paid', 'dueration', 'quizzes.total_marks',  DB::raw( 'count(quizresults.user_id) as attempts, quizzes.slug, user_id' ) ] )
        ->where( 'user_id', '=', $user->id )
        ->groupBy( 'quizresults.quiz_id' )
        ->get();

        $response[ 'records' ]   = $records;
        $response[ 'user' ]       = $user;
        $response[ 'status' ]  = 1;

        $response[ 'message' ] = '';

        return $response;

    }

    public function historyAnalysis( $user_id, $exam_id = '' )
 {
        $user = User::find( $user_id );

        if ( !$user )
 {
            $response[ 'status' ] = 0;

            $response[ 'message' ] = 'User is not found';
            return $response;
        }

        $exam_record = FALSE;
        if ( $exam_id )
 {
            $exam_record = Quiz::find( $exam_id );

        }
        $records = array();
        if ( !$exam_id )
        $records = Quiz::join( 'quizresults', 'quizzes.id', '=', 'quizresults.quiz_id' )
        ->select( [ 'title', 'is_paid', 'marks_obtained', 'exam_status', 'quizresults.created_at', 'quizzes.total_marks', 'quizzes.slug', 'quizresults.slug as resultsslug', 'user_id', 'exam_type' ] )
        ->where( 'user_id', '=', $user->id )
        ->orderBy( 'quizresults.updated_at', 'desc' )
        ->get();
        else
        $records = Quiz::join( 'quizresults', 'quizzes.id', '=', 'quizresults.quiz_id' )
        ->select( [ 'title', 'is_paid', 'marks_obtained', 'exam_status', 'quizresults.created_at', 'quizzes.total_marks', 'quizzes.slug', 'quizresults.slug as resultsslug', 'user_id' ] )
        ->where( 'user_id', '=', $user->id )
        ->where( 'quiz_id', '=', $exam_record->id )
        ->orderBy( 'quizresults.updated_at', 'desc' )
        ->get();

        $response[ 'records' ]   = $records;
        $response[ 'user' ]       = $user;
        $response[ 'status' ]  = 1;

        $response[ 'message' ] = '';

        return $response;
    }

    public function  saveBookmarks( Request $request ) 
 {
        $validator = Validator::make( $request->all(), [
            'item_id'       => 'required',
            'user_id'       => 'required'
        ] );

        if ( $validator->fails() ) {

            $response[ 'status' ]  = 0;
            $response[ 'message' ] = 'Invalid input';
            $response[ 'errors' ]  = $validator->errors()->messages();
            return $response;
        }

        $user_id        = $request->user_id;

        $user = User::join( 'role_user', 'users.id', 'role_user.user_id' )
        ->select( [ 'users.*' ] )
        ->where( 'users.id', $user_id )
        ->where( 'users.role_id', getRoleData( 'student' ) )
        ->where( 'users.login_enabled', 1 )
        ->get();

        if ( count( $user ) ) {

            $user = $user[ 0 ];

            //check bookmark already exist
            $bkmark_existed = Bookmark::where( 'user_id', $user_id )
            ->where( 'item_id', $request->item_id )
            ->get();

            if ( count( $bkmark_existed ) ) {

                $bkmark_existed = $bkmark_existed[ 0 ];
                $bkmark_existed->delete();
                $response[ 'status' ] = 0;
                $response[ 'message' ] = 'Bookmark removed ';
                return json_encode( $response );

            } else {

                $record = new Bookmark();

                $record->user_id = $user_id;
                $record->item_id = $request->item_id;
                $record->item_type = 'questions';

                $record->save();
                $response[ 'status' ] = 1;
                $response[ 'message' ] = 'Bookmark saved ';
                return json_encode( $response );
            }

        } else {

            $response[ 'status' ] = 0;
            $response[ 'message' ] = 'Loggedin User record not found';
            return $response;

        }
    }

    public function socialLoginUser( Request $request )
 {
        $email = $request->email;
        $name = $request->name;

        $user = User::where( 'email', '=', $email )->first();
        if ( !$user )
 {
            $data[ 'email' ] = $email;
            $data[ 'name' ] = $name;
            $data = ( object )$data;
            $user = $this->registerWithSocialLogin( $data );
        }

        if ( $request->device_id ) {
            $user->device_id = $request->device_id;
            $user->save();
        }
        return $user;

    }

    /**
    * This method accepts the user object from social login methods
    * Registers the user with the db
    * Sends Email with credentials list
    * @param  User   $user [ description ]
    * @return [ type ]       [ description ]
    */

    public function registerWithSocialLogin( $receivedData = '' )
 {
        $user           = new User();

        $password         = 'password';
        $user->password   = bcrypt( $password );
        $slug             = $user->makeSlug( $receivedData->name );
        $user->username   = $slug;
        $user->slug       = $slug;

        $role_id        = getRoleData( 'student' );

        $user->name  = $receivedData->name;
        $user->email = $receivedData->email;
        $user->role_id = $role_id;
        $user->login_enabled  = 1;
        if ( !env( 'DEMO_MODE' ) ) {
            $user->save();
            $user->roles()->attach( $user->role_id );
            try {
                $user->notify( new NewUserRegistration( $user, $user->email, $password ) );
            } catch( Exception $ex )
 {
                return $user;
            }

        }
        return $user;
    }

    public function updatePayment( Request $request )
 {

        $response[ 'status' ] = 0;
        $response[ 'message' ] = 'Oops..! something went wrong';

        try {

            $payment                  = new Payment();
            $payment->user_id         = $request->user_id;
            $payment->item_id         = $request->item_id;
            $payment->item_name       = $request->item_name;
            $payment->plan_type       = $request->plan_type;
            $payment->start_date      = $request->start_date;
            $payment->end_date        = $request->end_date;
            $payment->slug            = getHashCode();
            $payment->payment_gateway = $request->payment_gateway;
            $payment->transaction_id  = $request->transaction_id;
            $payment->actual_cost     = $request->actual_cost;
            $payment->coupon_applied  = $request->coupon_applied;
            $payment->coupon_id       = $request->coupon_id;
            $payment->discount_amount = $request->discount_amount;
            $payment->cost            = $request->actual_cost;
            $payment->after_discount  = $request->after_discount;
            $payment->payment_status  = $request->payment_status;
            $payment->paid_amount     = $request->after_discount;

            $payment->save();

            if ( $payment->coupon_applied )
 {
                $this->couponcodes_usage( $payment );
            }

            $response[ 'status' ] = 1;
            $response[ 'message' ] = 'Payment updated successfully';

        } catch( Exception $ex )
 {
            $response[ 'status' ] = 0;
            $response[ 'message' ] = $ex->getMessage();
        }

        return $response;
    }

    public function updateOfflinePayment( Request $request )
 {

        try {

            $payment                  = new Payment();
            $payment->user_id         = $request->user_id;
            $payment->item_id         = $request->item_id;
            $payment->item_name       = $request->item_name;
            $payment->plan_type       = $request->plan_type;
            $payment->start_date      = $request->start_date;
            $payment->end_date        = $request->end_date;
            $payment->slug            = getHashCode();
            $payment->payment_gateway = 'offline';
            $payment->actual_cost     = $request->actual_cost;
            $payment->coupon_applied  = $request->coupon_applied;
            $payment->coupon_id       = $request->coupon_id;
            $payment->discount_amount = $request->discount_amount;
            $payment->cost            = ( $request->cost ) ? $request->cost : $request->actual_cost;
            $payment->after_discount  = $request->after_discount;
            $payment->paid_amount     = $request->after_discount;
            $payment->payment_status  = 'pending';
            $payment->notes           = $request->payment_details;

            $other_details = array();

            $other_details[ 'is_coupon_applied' ] = $request->coupon_applied;
            $other_details[ 'coupon_applied' ]    = $request->coupon_applied;
            $other_details[ 'actual_cost' ]       = $request->actual_cost;
            $other_details[ 'after_discount' ]    = $request->after_discount;
            $other_details[ 'coupon_id' ]         = $request->coupon_id;
            $other_details[ 'payment_details' ]   = $request->payment_details;
            $other_details[ 'discount_availed' ]  = $request->discount_amount;

            $payment->other_details  = json_encode( $other_details );

            $payment->save();

            if ( $payment->coupon_applied )
 {
                $this->couponcodes_usage( $payment );
            }

            $response[ 'status' ] = 1;
            $response[ 'message' ] = 'Your Payment Request Submitted To Admin Successfully';

        } catch( Exception $ex )
 {
            $response[ 'status' ] = 0;
            $response[ 'message' ] = $ex->getMessage();
        }

        return $response;

    }

    public function couponcodes_usage( $payment_record )
 {
        $coupon_usage[ 'user_id' ] = $payment_record->user_id;
        $coupon_usage[ 'item_id' ] = $payment_record->item_id;
        $coupon_usage[ 'item_type' ] = $payment_record->plan_type;
        $coupon_usage[ 'item_cost' ] = $payment_record->actual_cost;
        $coupon_usage[ 'total_invoice_amount' ] = $payment_record->paid_amount;
        $coupon_usage[ 'discount_amount' ] = $payment_record->discount_amount;
        $coupon_usage[ 'coupon_id' ] = $payment_record->coupon_id;
        $coupon_usage[ 'updated_at' ] =  new DateTime();
        DB::table( 'couponcodes_usage' )->insert( $coupon_usage );
        return TRUE;
    }


    public function mail(){
        $OTP=rand('999', '9999');
        $user= 'fatima';
        
        $data=['OTP'=>$OTP, 'user'=>$user];
        $emailcandi='Kaneezfatimarajper55@gmail.com';
        try{
            Mail::send('mail', ['OTP'=>$OTP, 'user'=>$user],
            function ($message) use ($emailcandi) {
                $message->to($emailcandi);
                // $message->cc('recruitment@arcinventador.com');
                $message->subject('Subject');
            });
            // return true;
        
        }catch ( \Exception $e ) {
            echo json_encode($e);
        }
        // $OTP=rand('999', '9999');

        // $user= 'fatima';

        // return view('mail', compact('data'));
    }

   

}