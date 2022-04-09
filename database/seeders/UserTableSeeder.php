<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $role = Role::all()->where( 'role', 'Admin' )->first();

        $user = new User;

        $user->username = env('APP_API_ADMIN_USERNAME','admin');
        $user->password = bcrypt( env('APP_API_ADMIN_PASSWORD', 'admin123') );
        $user->role()->associate( $role );

        $user->save();

        // show information in the command line after everything is run
        $this->command->info( 'user seeds finished.' );
    }
}
