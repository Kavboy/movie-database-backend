<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgeRatingController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group( [ 'prefix' => '/v1' ], function () {

    Route::post( '/login', [ UserController::class, 'login' ] );
    Route::get( '/check', function () {
        if ( Auth::check() ) {
            return response( '', 204 );
        } else {
            return response()->json( 'OK', 200 );
        }
    } );

    Route::group( [ 'prefix' => '/media' ], function () {
        Route::get( '', [ MediaController::class, 'index' ] );
        Route::get( '/{id}', [ MediaController::class, 'show' ] );
        Route::post( '/titles', [ MediaController::class, 'titles' ] );
        Route::get( '/news', [ MediaController::class, 'news' ] );
        Route::post( '/search', [ MediaController::class, 'search' ] );
    } );

    Route::middleware( [ 'auth:sanctum' ] )->group( function () {
        Route::post( '/logout', [ UserController::class, 'logout' ] );

        Route::group( [ 'prefix' => '/user' ], function () {
            Route::get( '', [ UserController::class, 'index' ] )->middleware( [ 'role:Admin' ] );
            Route::get( '/table', [ UserController::class, 'table' ] )->middleware( [ 'role:Admin' ] );
            Route::get( '/whoami', function ( Request $request ) {
                return $request->user();
            } );
            Route::post( '/change_password', [
                UserController::class,
                'changeOwnPasswordRequest'
            ] )->middleware( [ 'role:Admin:Creator:User' ] );
            Route::post( '/admin_change_password', [
                UserController::class,
                'changePasswordRequest'
            ] )->middleware( [ 'role:Admin' ] );
            Route::put( '', [ UserController::class, 'store' ] )->middleware( [ 'role:Admin' ] );
            Route::patch( '/{username}', [
                UserController::class,
                'update'
            ] )->middleware( [ 'role:Admin:Creator:User' ] );
            Route::delete( '/own', [
                UserController::class,
                'destroyOwn'
            ] )->middleware( [ 'role:Admin:Creator:User' ] );
            Route::delete( '/{username}', [ UserController::class, 'destroy' ] )->middleware( [ 'role:Admin' ] );
        } );

        Route::group( [ 'prefix' => 'media' ], function () {
            Route::get( '/table', [ MediaController::class, 'table' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::put( '', [ MediaController::class, 'store' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::patch( '/{id}', [ MediaController::class, 'update' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::delete( '/{id}', [ MediaController::class, 'destroy' ] )->middleware( [ 'role:Admin:Creator' ] );
        } );

        /**
         * Role routes
         */
        Route::group( [ 'prefix' => 'role' ], function () {
            Route::get( '', [ RoleController::class, 'index' ] )->middleware( [ 'role:Admin' ] );
            Route::get( '/{id}', [ RoleController::class, 'show' ] )->middleware( [ 'role:Admin' ] );
        } );

        /**
         * Medium routes
         */
        Route::group( [ 'prefix' => 'medium' ], function () {
            Route::get( '', [ MediumController::class, 'index' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::get( '/{id}', [ MediumController::class, 'show' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::put( '', [ MediumController::class, 'store' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::patch( '', [ MediumController::class, 'update' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::delete( '/{id}', [ MediumController::class, 'destroy' ] )->middleware( [ 'role:Admin:Creator' ] );
        } );

        Route::get('genre', [GenreController::class, 'index'])->middleware(['role:Admin:Creator']);

        /**
         * FSk routes
         */
        Route::group( [ 'prefix' => 'fsk' ], function () {
            Route::get( '', [ AgeRatingController::class, 'index' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::get( '/{id}', [ AgeRatingController::class, 'show' ] )->middleware( [ 'role:Admin:Creator' ] );
        } );

        /**
         * Location routes
         */
        Route::group( [ 'prefix' => 'location' ], function () {
            Route::get( '', [ LocationController::class, 'index' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::get( '/{id}', [ LocationController::class, 'show' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::put( '', [ LocationController::class, 'store' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::patch( '', [ LocationController::class, 'update' ] )->middleware( [ 'role:Admin:Creator' ] );
            Route::delete( '', [ LocationController::class, 'destroy' ] )->middleware( [ 'role:Admin:Creator' ] );
        } );
    } );
} );



