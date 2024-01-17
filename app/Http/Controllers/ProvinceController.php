<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ProvinceController extends Controller
 {
    public function getProvince() {
        $province = DB::table( 'province' )->where( 'status', 'active' )->get();
        return $province;
    }

    public function getDistrict( Request $request ) {
        $provinceId = $request->provinceId;
        $district = DB::table( 'district' )->where( 'provinceId', $provinceId )->where( 'status', 'active' )->get();
        return $district;
    }

    public function getGrade() {
        $grade = DB::table( 'grade' )->where( 'status', 'active' )->get();
        return $grade;
    }

    public function getProgram() {
        $program = DB::table( 'program' )->where( 'status', 'active' )->get();
        return $program;
    }

    public function getBatch( Request $request ) {
        $batch = DB::table( 'batch' )->where( 'status', 'active' )->where( 'programId', $request->programId )->get();
        return $batch;
    }

}
