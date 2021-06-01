<?php

namespace App\Http\Controllers;

use App\Models\AgeRating;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgeRatingController extends Controller
{
    /**
     * Display a listing of the age ratings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()  {
        try {
            if ( AgeRating::all() ) {
                $ageRatings = AgeRating::orderBy( 'id' )->get();

                return response()->json( $ageRatings, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }


            return response()->json( $res, 500 );
        }
    }
}
