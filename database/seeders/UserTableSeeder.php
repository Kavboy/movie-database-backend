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

        $user->username = 'admin';
        $user->password = bcrypt( 'admin123' );
        $user->role()->associate( $role );

        $user->save();

        // show information in the command line after everything is run
        $this->command->info( 'user seeds finished.' );
    }
}
