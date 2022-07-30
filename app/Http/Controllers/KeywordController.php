<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
{
    /**
     * Display a listing of keywords.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response {
        try {
            if ( Keyword::all() ) {
                $keywords = Keyword::all();

                return response( $keywords, 200 );
            } else {
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

    /**
     * Store a newly created keyword in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ): \Illuminate\Http\Response {
        $validated = $request->validate( [
            'keyword' => [ 'required', 'string', 'unique:keywords', 'max:255' ],
        ] );

        try {
            $keyword = Keyword::create( [
                'keyword' => $validated['keyword'],
            ] );

            return response( $keyword, 201 );
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

    /**
     * Update the specified keyword in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $keyword
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, string $keyword ) {
        $validated = $request->validate( [
            'keyword' => [ 'required', 'string', 'max:255' ],
        ] );

        try {
            $foundKeyword = Keyword::find( $keyword );

            $foundKeyword->keyword = $validated['keyword'];

            $foundKeyword->save();

            return response( $foundKeyword, 200 );
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

    /**
     * Remove the specified keyword from storage.
     *
     * @param string $keyword
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( string $keyword ) {
        try {
            if ( Keyword::find( $keyword ) ) {
                Keyword::find( $keyword )->delete();

                return response()->json( [
                    'message' => 'Successfully deleted'
                ], 200 );
            }

            return response()->json( [
                'message' => 'No such keyword',
            ], 404 );

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
