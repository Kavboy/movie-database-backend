<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

/**
 * @group Roles
 *
 * Class RolesController
 * @package App\Http\Controllers
 */
class RoleController extends Controller {
    /**
     * Display a listing of roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            if ( Role::all() ) {
                $roles = Role::all();

                return response()->json( $roles, 200 );
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
