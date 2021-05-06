<?php

namespace App\Http\Controllers;

use App\Models\AgeRating;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgeRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()  {
        try {
            if ( AgeRating::all() ) {
                $ageRatings = AgeRating::all()->sortBy( 'fsk' );

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        try {
            if (AgeRating::find($id)) {
                $ageRating = AgeRating::find($id);
                $res = $ageRating;

                return response()->json($res, 200);
            } else {
                return response()->json([], 404);
            }
        } catch (QueryException $ex) {
            if (env('APP_DEBUG')) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error($ex);
                $res['message'] = 'Something unexpected happened, please try again later';
            }
            return response()->json($res, 500);
        }
    }
}
