<?php

namespace App\Http\Controllers;

use App\Models\Medium;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediumController extends Controller {
    /**
     * Display a listing of mediums.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            if ( Medium::all() ) {
                $mediums = Medium::all();

                return response( $mediums, 200 );
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
     * Store a newly created medium in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        $validated = $request->validate( [
            'medium' => [ 'required', 'string', 'unique:mediums', 'max:45' ],
        ] );

        try {
            $medium = Medium::create( [
                'medium' => $validated['medium'],
            ] );

            return response( $medium, 201 );
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
     * Update the specified medium in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, int $id ) {
        $validated = $request->validate( [
            'medium' => [ 'required', 'string', 'max:45' ],
        ] );

        try {
            $medium = Medium::find( $id );

            $medium->medium = $validated['medium'];

            $medium->save();

            return response( $medium, 200 );
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
     * Remove the specified medium from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( int $id ) {
        try {
            if ( Medium::find( $id ) ) {
                Medium::find( $id )->delete();

                return response()->json( [
                    'message' => 'Successfully deleted'
                ], 200 );
            }

            return response()->json( [
                'message' => 'No such location',
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
