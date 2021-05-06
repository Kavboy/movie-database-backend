<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            if ( Location::all() ) {
                $locations = Location::all();

                return response( $locations, 200 );
            } else {
                return response( '', 404 );
            }
        } catch ( \Illuminate\Database\QueryException $ex ) {
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        $validated = $request->validate( [
            'location' => [ 'required', 'string', 'unique:locations', 'max:45' ],
        ] );

        try {
            $location = Location::create( [
                'location' => $validated['location'],
            ] );

            return response( $location, 201 );
        } catch ( \Illuminate\Database\QueryException $ex ) {
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
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id ) {
        try {
            if ( Location::find( $id ) ) {
                $location = Location::find( $id );

                return response( $location, 200 );
            } else {
                return response( '', 404 );
            }
        } catch ( \Illuminate\Database\QueryException $ex ) {
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id ) {
        $validated = $request->validate( [
            'location' => [ 'required', 'string', 'max:45' ],
        ] );

        try {
            $location = Location::find( $id );

            $location->location = $validated['location'];

            $location->save();

            return response( $location, 200 );
        } catch ( \Illuminate\Database\QueryException $ex ) {
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( int $id ) {
        try {
            if ( Location::find( $id ) ) {
                // TODO replace location in movie with null before delete
                Location::find( $id )->delete();

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
