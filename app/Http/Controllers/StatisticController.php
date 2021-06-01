<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatisticController extends Controller {
    /**
     * Display a listing of statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            if ( Statistic::all() ) {
                $statistics = Statistic::orderBy('month')->orderBy('year')->paginate(10);

                return response()->json( $statistics, 200 );
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
