<?php
namespace App\Repositories;

use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginRepository
 {
//     public function athenticate( $request )
//  {
//         $credentials = $request->only( 'email', 'password' );
//         try {
//             if ( ! $token = JWTAuth::attempt( $credentials ) ) {
//                 $token = JWTAuth::attempt( $credentials );
//                 return response()->json( [
//                     'success' => false,
//                     'message' => 'Login credentials are invalid.',
//                 ], 400 );
//             }
//         } catch ( JWTException $e ) {
//             return $credentials;
//             return response()->json( [
//                 'success' => false,
//                 'message' => 'Could not create token.',
//             ], 500 );
//         }
//         return $token;
//     }
    public function athenticate( $request )
 {
        $credentials = $request->only( 'email', 'password' );
        try {
            $token = JWTAuth::attempt( $credentials );
            if ( ! $token) {
                return $token;
            }
        } catch ( JWTException $e ) {
            // return $credentials;
            return response()->json( [
                'token' => $token,
                'success' => false,
                'message' => 'Could not create token.',
            ], 500 );
        }
        return $token;
    }
}