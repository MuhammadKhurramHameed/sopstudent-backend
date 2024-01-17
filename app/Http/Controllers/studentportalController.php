<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Image;
use DB;
use Input;
use App\Item;
use Session;
use Response;
use Validator;
use URL;

class studentportalController extends Controller
{
	public $emptyarray = array();
	public function savemydocument(Request $request){
		$validate = Validator::make($request->all(), [
	    	'document_title'  		=> 'required',
	    	'document_expiredate'  	=> 'required',
	    	'document_file'  		=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		if ($request->has('document_file')) {
			// dd($request->document_file);
    		if( $request->document_file->isValid()){
	            $number = rand(1,999);
		        $numb = $number / 7 ;
				$name = "document_file";
		        $extension = $request->document_file->extension();
	            $documentname  = date('Y-m-d')."_".$numb."_".$name."_.".$extension;
	            $request->document_file->move(public_path('document_file/'),$documentname);
			}
        }else{
	        $documentname = 'no_image.jpg'; 
        }
		$adds = array(
		'document_title' 			=> $request->document_title,
		'document_expiredate' 		=> $request->document_expiredate,
		'document_file' 			=> $documentname,
		'document_extension'		=> $extension,
		'status_id'	 				=> 1,
		'created_by'		 		=> $request->userId,
		'created_at'	 			=> date('Y-m-d h:i:s'),
		);
		$save = DB::table('document')->insert($adds);
		if($save){
			return response()->json(['message' => 'Document Upload Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function documentlist(Request $request){
		$daat = DB::table('document')
		->select('*')
		->where('created_by','=',$request->userId)
		->where('document_expiredate','>=',date('Y-m-d'))
		->where('status_id','=',1)
		->get();		
		$docpath =  URL::to( '/' )."/public/document_file/";
		if($daat){
			return response()->json(['data' => $daat, 'docpath' => $docpath, 'message' => 'Document List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Document List'],200);
		}
	}

	public function savemyeducation(Request $request){
		$validate = Validator::make($request->all(), [
	    	'education_grade'  			=> 'required',
	    	'education_institutename'  	=> 'required',
	    	'education_majorsubject'  	=> 'required',
	    	'education_minorsubject'  	=> 'required',
	    	'education_resulttype'  	=> 'required',
	    	'education_resultgrade' 	=> 'required',
	    	'education_totalmarks' 		=> 'required',
	    	'education_obtainmarks'  	=> 'required',
	    	'education_passingyear'  	=> 'required',
	    	'education_city'  			=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
		'education_grade' 			=> $request->education_grade,
		'education_institutename' 	=> $request->education_institutename,
		'education_majorsubject' 	=> $request->education_majorsubject,
		'education_minorsubject' 	=> $request->education_minorsubject,
		'education_resulttype' 		=> $request->education_resulttype,
		'education_resultgrade' 	=> $request->education_resultgrade,
		'education_totalmarks' 		=> $request->education_totalmarks,
		'education_obtainmarks' 	=> $request->education_obtainmarks,
		'education_passingyear' 	=> $request->education_passingyear,
		'education_city' 			=> $request->education_city,
		'status_id'	 				=> 1,
		'created_by'		 		=> $request->userId,
		'created_at'	 			=> date('Y-m-d h:i:s'),
		);
		$save = DB::table('education')->insert($adds);
		if($save){
			return response()->json(['message' => 'Education Save Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function editmyeducation(Request $request){
		$validate = Validator::make($request->all(), [
	    	'education_id'  			=> 'required',
	    	'education_grade'  			=> 'required',
	    	'education_institutename'  	=> 'required',
	    	'education_majorsubject'  	=> 'required',
	    	'education_minorsubject'  	=> 'required',
	    	'education_resulttype'  	=> 'required',
	    	'education_resultgrade' 	=> 'required',
	    	'education_totalmarks' 		=> 'required',
	    	'education_obtainmarks'  	=> 'required',
	    	'education_passingyear'  	=> 'required',
	    	'education_city'  			=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
		'education_grade' 			=> $request->education_grade,
		'education_institutename' 	=> $request->education_institutename,
		'education_majorsubject' 	=> $request->education_majorsubject,
		'education_minorsubject' 	=> $request->education_minorsubject,
		'education_resulttype' 		=> $request->education_resulttype,
		'education_resultgrade' 	=> $request->education_resultgrade,
		'education_totalmarks' 		=> $request->education_totalmarks,
		'education_obtainmarks' 	=> $request->education_obtainmarks,
		'education_passingyear' 	=> $request->education_passingyear,
		'education_city' 			=> $request->education_city,
		);
		$save = DB::table('education')
		->where('education_id','=',$request->education_id)
		->update($adds);
		if($save){
			return response()->json(['message' => 'Education Updated Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function deletemyeducation(Request $request){
		$validate = Validator::make($request->all(), [
	    	'education_id'  => 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
	    	'status_id'     => 2,
    	);
		$save = DB::table('education')
		->where('education_id','=',$request->education_id)
		->update($adds);
		if($save){
			return response()->json(['message' => 'Education Deleted Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function educationlist(Request $request){
		$daat = DB::table('education')
		->select('*')
		->where('created_by','=',$request->userId)
		->where('status_id','=',1)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Education List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Education List'],200);
		}
	}

	public function savemycertificate(Request $request){
		$validate = Validator::make($request->all(), [
	    	'certificate_name'  			=> 'required',
	    	'certificate_description'  		=> 'required',
	    	'certificate_obtainmarks'  		=> 'required',
	    	'certificate_totalmarks'  		=> 'required',
	    	'certificate_duration'  		=> 'required',
	    	'certificate_year' 				=> 'required',
	    	'certificate_institutename' 	=> 'required',
	    	'certificate_city'  			=> 'required',
	    	'certificate_grade'  			=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
		'certificate_name' 			=> $request->certificate_name,
		'certificate_description' 	=> $request->certificate_description,
		'certificate_obtainmarks' 	=> $request->certificate_obtainmarks,
		'certificate_totalmarks' 	=> $request->certificate_totalmarks,
		'certificate_duration' 		=> $request->certificate_duration,
		'certificate_year' 			=> $request->certificate_year,
		'certificate_institutename' => $request->certificate_institutename,
		'certificate_city' 			=> $request->certificate_city,
		'certificate_grade' 		=> $request->certificate_grade,
		'status_id'	 				=> 1,
		'created_by'		 		=> $request->userId,
		'created_at'	 			=> date('Y-m-d h:i:s'),
		);
		$save = DB::table('certificate')->insert($adds);
		if($save){
			return response()->json(['message' => 'Certificate Save Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function editmycertificate(Request $request){
		$validate = Validator::make($request->all(), [
	    	'certificate_id'  			    => 'required',
	    	'certificate_name'  			=> 'required',
	    	'certificate_description'  		=> 'required',
	    	'certificate_obtainmarks'  		=> 'required',
	    	'certificate_totalmarks'  		=> 'required',
	    	'certificate_duration'  		=> 'required',
	    	'certificate_year' 				=> 'required',
	    	'certificate_institutename' 	=> 'required',
	    	'certificate_city'  			=> 'required',
	    	'certificate_grade'  			=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
    		'certificate_name' 			=> $request->certificate_name,
    		'certificate_description' 	=> $request->certificate_description,
    		'certificate_obtainmarks' 	=> $request->certificate_obtainmarks,
    		'certificate_totalmarks' 	=> $request->certificate_totalmarks,
    		'certificate_duration' 		=> $request->certificate_duration,
    		'certificate_year' 			=> $request->certificate_year,
    		'certificate_institutename' => $request->certificate_institutename,
    		'certificate_city' 			=> $request->certificate_city,
    		'certificate_grade' 		=> $request->certificate_grade,
    	);
		$save = DB::table('certificate')
		->where('certificate_id','=',$request->certificate_id)
		->update($adds);
		if($save){
			return response()->json(['message' => 'Certificate Updated Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function deletemycertificate(Request $request){
		$validate = Validator::make($request->all(), [
	    	'certificate_id'  => 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
	    	'status_id'     => 2,
    	);
		$save = DB::table('certificate')
		->where('certificate_id','=',$request->certificate_id)
		->update($adds);
		if($save){
			return response()->json(['message' => 'Education Deleted Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function certificatelist(Request $request){
		$daat = DB::table('certificate')
		->select('*')
		->where('created_by','=',$request->userId)
		->where('status_id','=',1)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Certificate List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Certificate List'],200);
		}
	}

	public function faqlist(Request $request){
		$daat = DB::table('faqs')
		->select('*')
		->where('status_id','=',1)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'FAQ List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Certificate List'],200);
		}
	}

	public function updateprofile(Request $request){
		if($request->image != "-"){
			if ($request->has('image')) {
				if( $request->image->isValid()){
					$number = rand(1,999);
					$numb = $number / 7 ;
					$name = "image";
					$extension = $request->image->extension();
					$userimage  = date('Y-m-d')."_".$numb."_".$name."_.".$extension;
					$request->image->move(public_path('userimage/'),$userimage);
					DB::table('users')
					->where('id','=',$request->userId)
					->update([
						'image' 	=> $userimage,
					]);
				}
			}
		}
		if($request->cover_image != "-"){
			if ($request->has('cover_image')) {
				if( $request->cover_image->isValid()){
					$number = rand(1,999);
					$numb = $number / 7 ;
					$name = "cover_image";
					$extension = $request->cover_image->extension();
					$usercoverimage  = date('Y-m-d')."_".$numb."_".$name."_.".$extension;
					$request->cover_image->move(public_path('userimage/'),$usercoverimage);
					DB::table('users')
					->where('id','=',$request->userId)
					->update([
						'cover_image' 	=> $usercoverimage,
					]);
				}
			}
		}
       	DB::table('users')
			->where('id','=',$request->userId)
			->update([
			'father_name' 			=> $request->father_name,
			'father_cnic'			=> $request->father_cnic,
			'father_occupation' 	=> $request->father_occupation,
			'religion' 				=> $request->religion,
			'nationality'			=> $request->nationality,
			'gender' 				=> $request->gender,
			'dob' 					=> $request->dob,
			'present_address' 		=> $request->present_address,
			'mother_name' 			=> $request->mother_name,
			'mother_cnic'			=> $request->mother_cnic,
			'street' 				=> $request->street,
			'town' 					=> $request->town,
			'district' 				=> $request->district,
			'province'				=> $request->province,
			'postal'				=> $request->postal,
			'father_mobile'			=> $request->father_mobile,
			'father_department'		=> $request->father_department,
			'father_designation'	=> $request->father_designation,
			'mother_occupation'		=> $request->mother_occupation,
			'mother_designation'	=> $request->mother_designation,
			'village'				=> $request->village,
		]);
		return response()->json(['message' => 'Profile Updated Successfully'],200);
	}
	public function quizdetails(Request $request){
        $validate = Validator::make($request->all(), [ 
	    	'exam_id'  	 => 'required',
		]);
		if ($validate->fails()) {
			return response()->json($validate->errors(), 400);
		}
		$tobeanswer = DB::table('answer')
		->select('question_id')
		->where('user_id','=',$request->userId)
		->get();
		$sorttobeanswer = array();
		if(isset($tobeanswer)){
			foreach($tobeanswer as $tobeanswers){
				$sorttobeanswer[] = $tobeanswers->question_id;
			}
		}
		$question = DB::table('question')
		->select('*')
		->where('status_id','=',1)
		->where('exam_slug','=',$request->exam_id)
		->where('questionstatus_id','=',2)
		->whereNotIn('question_id',$sorttobeanswer)
		->inRandomOrder()
		->first();
		if(isset($question->question_id)){
			$option = DB::table('option')
			->select('*')
			->where('status_id','=',1)
			->where('question_id','=',$question->question_id)
			->get();
		}else{
			$option = array();
		}
		$allquestion = DB::table('question')
		->select('question_id')
		->where('status_id','=',1)
		->where('questionstatus_id','=',2)
		->where('exam_slug','=',$request->exam_id)
		->orderBy('question_id','DESC')
		->get();
		$questionhistory = array();
		if(isset($allquestion)){
			$index=0;
			foreach($allquestion as $allquestions){
				$questionhistory[$index]['isanswer'] = DB::table('answer')
				->select('answer_id')
				->where('question_id','=',$allquestions->question_id)
				->where('user_id','=',$request->userId)
				->count();
				$questionhistory[$index]['id'] = $index;
				$index++;
			}
		}
		$totalquestion = DB::table('question')
		->select('*')
		->where('status_id','=',1)
		->where('questionstatus_id','=',2)
		->where('exam_slug','=',$request->exam_id)
		->count();
		$allquestion = DB::table('question')
		->select('question_id')
		->where('questionstatus_id','=',2)
		->where('exam_slug','=',$request->exam_id)
		->get();
		$sortallquestion = array();
		if(isset($allquestion)){
			foreach($allquestion as $allquestions){
				$sortallquestion[] = $allquestions->question_id;
			}
		}
		$totalanswer = DB::table('answer')
		->select('answer_id')
		->whereIn('question_id',$sortallquestion)
		->count();
		$getsubject = DB::table('exam')
		->select('exam_name')
		->where('exam_id','=',$request->exam_id)
		->first();
		$subject = $getsubject->exam_name;
		$optionpath =  "https://studentofpakistan.com/sopadminbackend/public/questionimage/".$question->question_id.'/';
		if($question){
			return response()->json(['question' => $question, 'option' => $option, 'questionhistory' => $questionhistory, 'totalquestion' => $totalquestion, 'totalanswer' => $totalanswer, 'subject' => $subject, 'optionpath' => $optionpath, 'message' => 'Quiz Details'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}	
	}
	public function submitquizanswer(Request $request){
		$validate = Validator::make($request->all(), [
	    	'exam_id'  		=> 'required',
	    	'question_id'  	=> 'required',
	    	'option_id'  	=> 'required',
	    ]);
		if ($validate->fails()) {    
			return response()->json($validate->errors(), 400);
		}
		if($request->option_id == 0){
			$adds = array(
				'exam_id' 		=> $request->exam_id,
				'question_id' 	=> $request->question_id,
				'option_id' 	=> $request->option_id,
				'option_blank' 	=> $request->option_blank,
				'user_id'		=> $request->userId,
			);
		}else{
			$adds = array(
				'exam_id' 		=> $request->exam_id,
				'question_id' 	=> $request->question_id,
				'option_id' 	=> $request->option_id,
				'user_id'		=> $request->userId,
			);
		}
		$save = DB::table('answer')->insert($adds);
		if($save){
			return response()->json(['message' => 'Answered Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function finishquiz(Request $request){
		$validate = Validator::make($request->all(), [ 
	    	'exam_id'  	 => 'required',
		]);
		if ($validate->fails()) {
			return response()->json($validate->errors(), 400);
		}
		$adds = array(
			'exam_id' 	=> $request->exam_id,
			'user_id'		=> $request->userId,
		);
		$save = DB::table('examfinish')->insert($adds);
		if($save){
			return response()->json(['message' => 'Submited Successfully'],200);
		}else{
			return response()->json("Oops! Something Went Wrong", 400);
		}
	}
	public function notifications(Request $request){
		$program = DB::table('programregistrations')
		->select('programId')
		->where('user_id','=',$request->userId)
		->get();
		if(isset($program)){
			$programid = array();
			foreach($program as $programs){
				$programid[] = $programs->programId;
			}
			$data = DB::table('notifications')
			->select('*')
			// ->whereIn('program_id',$programid)
			->get();
		}else{
			$data = array();
		}
		return response()->json(['records' => $data,'message' => 'Notification List'],200);	
	}
	public function studentexamlist(Request $request){
		$programreg = DB::table('programregistrations')
		->select('programId')
		->where('user_id','=',$request->userId)
		->get();
		$program = array();
		foreach($programreg as $programregs){
			$program[] = $programregs->programId;
		}
		$daat = DB::table('exam')
		->select('*')
		// ->whereIn('program_id',$program)
		->where('status_id','=',1)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Exam List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Exam List'],200);
		}
	}
	public function studentlmslist(Request $request){
		$daat = DB::table('lms')
		->select('*')
		->get();		
		$lmspath =  "https://studentofpakistan.com/sopadminbackend/public/lms_document/";
		if($daat){
			return response()->json(['data' => $daat, 'lmspath' => $lmspath, 'message' => 'LMS List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'LMS List'],200);
		}
	}
	public function studentprogramlist(Request $request){
		$daat = DB::table('program')
		->select('*')
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Program List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Program List'],200);
		}
	}
	public function studentbatchlist(Request $request){
		$validate = Validator::make($request->all(), [ 
	    	'programId'  	 => 'required',
		]);
		if ($validate->fails()) {
			return response()->json($validate->errors(), 400);
		}
		$daat = DB::table('batchdetails')
		->select('*')
		->where('programId','=',$request->programId)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Batch List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Batch List'],200);
		}
	}
	public function studentsubjectlist(Request $request){
		$data=DB::table('subjects')
		->select('id','subject_title','subject_image')
		->where('status_id', '=', 1)
		->get();
		$subjectpath =  "https://studentofpakistan.com/sopadminbackend/public/subjects/";
		if($data){
			return response()->json(['data' => $data, 'subjectpath' => $subjectpath, 'message' => 'Subject List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Subject List'],200);
		}
	}
	public function listOfAlbums(){
        $albums=DB::table('cover')->where('status', 1)->get();
        $path = 'https://studentofpakistan.com/sopadminbackend/public/Gallery/albumNo-';
        if($albums){
            return response()->json(["Albums"=>$albums, "imagePath"=>$path], 200);
        }else{
            return response()->json("OOps! something went wrong", 400);
        }
    }
}