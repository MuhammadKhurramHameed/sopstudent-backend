<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Validator;

class ContactController extends Controller
{
    
    public function createTicket(Request $request){

        $validate=Validator::make($request->all(), [
            'subject'=> 'required',
            'message' => 'required',
            'image' => 'required'
        ]);

        if($validate->fails()){
            $response['message'] = $validate->errors()->getMessages();
            $response['status'] = 400;
            return $response;
        }
        $fileName=time() .".".$request->file('image')->getClientOriginalExtension();
            $contact = [
                'subject'     => $request->subject,
                'message'     => $request->message,
                'image'       => $fileName,
                'created_by'  => $request->userId
            ];

            $contacts= DB::table('contacts')->insert($contact);
            // dd($contacts);
            $request->file('image')->storeAs('public/uploads', $fileName);

            $response['message']= "Ticket have been saved successfully!!";
            $response['status'] = 200;
            return $response;
    }
    public function ticketlist(Request $request){
		$daat = DB::table('contacts')
		->select('*')
		->where('created_by','=',$request->userId)
		->get();		
		if($daat){
			return response()->json(['data' => $daat,'message' => 'Contact List'],200);
		}else{
			$emptyarray = array();
			return response()->json(['data' => $emptyarray,'message' => 'Contact List'],200);
		}
	}
}
