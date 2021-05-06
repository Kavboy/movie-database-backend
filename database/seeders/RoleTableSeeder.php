<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'role' => 'Admin',
        ]);

        Role::create([
            'role' => 'Creator',
        ]);

        Role::create([
            'role' => 'User',
        ]);

        // show information in the command line after everything is run
        $this->command->info('Role seeds finished.');

    }
}
