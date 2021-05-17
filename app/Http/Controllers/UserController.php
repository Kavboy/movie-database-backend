<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request ) {

        $perPage = env( 'PER_PAGE', 5 );

        if ( $request->get( 'per_page' ) ) {
            $perPage = $request->get( 'per_page' );
        }

        if ( User::all() ) {
            $users = User::paginate( $perPage );

            return response()->json( $users, 200 );
        } else {
            return response()->json( [], 404 );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( string $username ) {
        try {

            if ( User::find( $username ) ) {
                $user = User::find( $username );

                return response()->json( $user, 200 );
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, string $username ) {
        $fields = $request->validate( [
            'username' => [ 'string', 'max:255', 'unique:users' ],
            'role'     => [ 'string' ]
        ] );

        try {
            if ( User::find( $username ) ) {
                $user = User::find( $username );
            } else {
                return response()->json( [
                    'message' => 'Please provide a valid username'
                ], 404 );
            }

            if ( $fields['username'] ) {
                $user->username = $fields['username'];
            }

            if ( $fields['role'] && Role::find( $fields['role'] ) ) {

                $user->role()->dissociate();

                $role = Role::find( $fields['role'] );

                $user->role()->associate( $role );

            } else {
                return response()->json( [
                    'message' => 'Please provide a valid role'
                ], 404 );
            }

            $user->save();

            return response()->json( [
                'user'    => $user,
                'message' => 'update successful'
            ], 200 );

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
     * Remove the specified resource from storage.
     *
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyOwn( Request $request ) {
        try {
            $this->destroy( $request->user()->username );
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
     * Remove the specified resource from storage.
     *
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( string $username ) {
        try {
            if ( User::find( $username ) ) {
                User::find( $username )->delete();

                return response()->json( [
                    'message' => 'Successfully deleted'
                ], 200 );
            }

            return response()->json( [
                'message' => 'No such user',
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request ) {
        $fields = $request->validate( [
            'username' => [ 'required', 'string', 'filled', 'max:255', 'unique:users' ],
            'password' => [ 'required', 'string', 'filled', 'min:8', 'confirmed' ],
            'role'     => [ 'required', 'string', 'filled' ]
        ] );

        try {
            if ( Role::find( $fields['role'] ) ) {
                $role = Role::find( $fields['role'] );

                $user           = new User;
                $user->username = $fields['username'];
                $user->password = Hash::make( $fields['password'] );
                $user->role()->associate( $role );

                $user->save();

            } else {
                return response()->json( [
                    'message' => 'Please provide a valid role'
                ], 404 );
            }

            return response()->json( [
                'user'    => $user,
                'message' => 'creation successful'
            ], 201 );

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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login( Request $request ) {
        $fields = $request->validate( [
            'username' => [ 'required', 'string', 'filled', 'max:255' ],
            'password' => [ 'required', 'string', 'filled', 'min:8' ],
        ] );

        try {

            if(Auth::guard('web')->attempt(['username' => $fields['username'], 'password' => $fields['password']])){
                $request->session()->regenerate();
                return response()->json(Auth::user(), 200);
            } else {
                return response()->json("Provided credentials wrong", 401);
            }

            //Check email
            /*$user = User::where( 'username', $fields['username'] )->first();

            //Check password
            if ( ! $user || ! Hash::check( $fields['password'], $user->password ) ) {
                return response()->json( [
                    'message' => 'Bad credentials'
                ], 401 );
            }

            $token = $user->createToken( 'myapptoken' )->plainTextToken;

            return response()->json( [
                'user'    => $user,
                'token'   => $token,
                'message' => 'login successful'
            ], 200 );*/

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
     * Logout of the user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {
        Auth::guard('web')->logout();
        error_log($request->session()->getId());

        return response()->json( [ 'message' => 'Logged Out' ], 200 );
    }

    /**
     * For users to update their own password
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOwnPasswordRequest( Request $request ) {
        try {
            $fields = $request->validate( [
                'old_password' => [ 'required', 'filled' ],
                'new_password' => [ 'required', 'filled', 'min:8', 'confirmed' ],
            ] );

            $user = $request->user();

            error_log( $user );

            if ( $user ) {
                if ( Hash::check( $fields['oldPassword'], $user->password ) ) {
                    $this->changePassword( $user->username, $fields['newPassword'] );

                    $res = $user;

                    return response()->json( $res, 200 );
                } else {
                    $res['success'] = false;

                    return response()->json( $res, 401 );
                }
            }

            return response()->json( [], 404 );
        } catch ( \Exception $ex ) {
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
     * For administrators to update other users passwords
     *
     * @param Request $request
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePasswordRequest( Request $request, string $username ) {
        try {
            $fields = $request->validate( [
                'new_password' => [ 'required', 'filled', 'min:8' ],
            ] );

            $this->changePassword( $username, $fields['new_password'] );

            $res['success'] = true;

            return response()->json( $res, 200 );
        } catch ( \Exception $ex ) {
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
     * Util for updating passwords
     *
     * @param string $username
     * @param string $newPassword
     */
    public function changePassword( string $username, string $newPassword ) {
        $user           = User::find( $username );
        $user->password = Hash::make( $newPassword );
        $user->save();
    }
}
