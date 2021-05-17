<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('age_ratings')->delete();
        DB::table('mediums')->delete();
        DB::table( 'user_media' )->delete();
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('genres')->delete();

        $this->call([
            RoleTableSeeder::class,
            UserTableSeeder::class,
            AgeRatingTableSeeder::class,
            GenreTableSeeder::class,
            MediumTableSeeder::class,
        ]);
    }
}
