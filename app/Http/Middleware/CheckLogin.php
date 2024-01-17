<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;

class CheckLogin
 {
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */

    public function handle( Request $request, Closure $next )
 {
        if ( $request->header( 'authorization' ) && $request->id ) {
            $check = DB::table( 'users' )
            ->select( 'email' )
            ->where( 'id', '=', $request->id )
            ->count();
            if ( $check == 0 ) {
                return redirect( '/login' );

            } else {
                return $next( $request );
            }
        } else {
            return redirect( '/login' );
        }
    }
}
