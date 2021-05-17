<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GenreController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            if ( Genre::all() ) {
                $genres = Genre::all();

                return response( $genres, 200 );
            } else {
                error_log( 'else' );

                return response( '', 404 );
            }
        } catch ( QueryException $ex ) {
            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response( $res, 500 );
        }
    }
}
