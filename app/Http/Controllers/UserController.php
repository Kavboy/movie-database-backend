<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\u;

class UserController extends Controller {

    /**
     * Display a listing of the users.
     *
     * @return JsonResponse
     */
    public function index( Request $request ) {

        $perPage = env( 'PER_PAGE', 10 );

        if ( $request->get( 'per_page' ) ) {
            $perPage = $request->get( 'per_page' );
        }

        try {
            $users = User::orderBy( 'id' )->paginate( $perPage );

            $data = $users->getCollection();
            $data->each( function ( $item ) {
                $item->setHidden( [] )->setVisible( [ 'id', 'username', 'role' ] );
            } );
            $users->setCollection( $data );


            if ( $users ) {
                return response()->json( $users, 200 );
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
     * Display the specified user.
     *
     * @param string $username
     *
     * @return JsonResponse
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
     * Method to update data of authenticated user
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateOwn(Request $request) {
        $fields = $request->validate( [
            'username' => [ 'string', 'max:255' ],
            'role'     => [ 'string', 'exists:roles,role' ]
        ] );

        $user = $request->user();

        return $this->update($user, $fields);
    }

    /**
     * Method to update other users
     *
     * @param Request $request
     * @param string $username
     *
     * @return JsonResponse
     */
    public function updateOthers(Request $request, string $username) {
        $fields = $request->validate( [
            'username' => [ 'string', 'max:255' ],
            'role'     => [ 'string', 'exists:roles,role' ]
        ] );

        try {
            if ( User::where( 'username', $username )->first() ) {
                $user = User::where( 'username', $username )->first();
            } else {
                return response()->json( [
                    'message' => 'Wrong Information'
                ], 404 );
            }

            return $this->update($user, $fields );

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
     * Util to update user data
     *
     * @param User $user
     * @param array $fields
     *
     * @return JsonResponse
     */
    public function update( User $user, array $fields) {
        try {
            if ( $fields['username'] ) {
                $user->username = $fields['username'];
            }

            if ( $fields['role'] && Role::where( 'role', $fields['role'] )->first() ) {

                $user->role()->dissociate();

                $role = Role::where( 'role', $fields['role'] )->first();

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
     * Removes the logged in user from storage.
     *
     * @param string $username
     *
     * @return JsonResponse
     */
    public function destroyOwn( Request $request ) {
        try {
            $this->destroy( $request->user()->username );
            return response()->json( 'Successfully', 200 );
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
     * Remove the specified user from storage.
     *
     * @param string $username
     *
     * @return JsonResponse
     */
    public function destroy( string $username ) {
        try {
            $user = User::where( 'username', $username )->first();

            if ( $user ) {
                if ( $user->role == 'Admin' ) {
                    if ( User::all()->where( 'role', 'Admin' )->count() > 1 ) {
                        User::where( 'username', $username )->first()->delete();

                        return response()->json( [
                            'message' => 'Successfully deleted'
                        ], 200 );

                    }

                    return response()->json( [
                        'message' => 'Not possible because condition not satisfied',
                    ], 412 );
                } else {
                    User::where( 'username', $username )->first()->delete();

                    return response()->json( [
                        'message' => 'Successfully deleted'
                    ], 200 );
                }
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
     * Creation of new users
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store( Request $request ) {
        $fields = $request->validate( [
            'username' => [ 'required', 'string', 'filled', 'max:255', 'unique:users' ],
            'password' => [ 'required', 'string', 'filled', 'min:8', 'confirmed' ],
            'role'     => [ 'required', 'string', 'filled' ]
        ] );

        try {
            if ( Role::where( 'role', $fields['role'] )->first() ) {
                $role = Role::where( 'role', $fields['role'] )->first();

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
     * For login of users
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login( Request $request ) {
        $fields = $request->validate( [
            'username' => [ 'required', 'string', 'filled' ],
            'password' => [ 'required', 'string', 'filled' ],
        ] );

        try {
            if ( Auth::guard( 'web' )->attempt( [
                'username' => $fields['username'],
                'password' => $fields['password']
            ] ) ) {
                error_log('get session');
                $request->session()->regenerate();

                return response()->json( Auth::user(), 200 );
            } else {
                error_log('wrong credentials');
                return response()->json( "Provided credentials wrong", 401 );
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
     * Logout of the user
     *
     * @return JsonResponse
     */
    public function logout( Request $request ) {
        Auth::guard( 'web' )->logout();
        return response()->json( [ 'message' => 'Logged Out' ], 200 );
    }

    /**
     * For users to update their own password
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeOwnPasswordRequest( Request $request ) {
        try {
            $fields = $request->validate( [
                'old_password' => [ 'required', 'filled' ],
                'new_password' => [ 'required', 'filled', 'min:8', 'confirmed' ],
            ] );

            $user = $request->user();

            if ( $user ) {
                if ( Hash::check( $fields['old_password'], $user->password ) ) {
                    $this->changePassword( $user->username, $fields['new_password'] );

                    $res['success'] = true;

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
     * @return JsonResponse
     */
    public function changePasswordRequest( Request $request, string $username ) {
        try {
            $fields = $request->validate( [
                'new_password' => [ 'required', 'filled', 'confirmed', 'min:8' ],
            ] );

            $this->changePassword( $username, $fields['password'] );

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
        $user           = User::where( 'username', $username )->first();
        $user->password = Hash::make( $newPassword );
        $user->save();
    }
}
