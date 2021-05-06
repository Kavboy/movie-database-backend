<?php

namespace Database\Seeders;

use App\Models\AgeRating;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgeRatingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AgeRating::create([
            'fsk' => '0',
        ]);

        AgeRating::create([
            'fsk' => '6',
        ]);

        AgeRating::create([
            'fsk' => '12',
        ]);

        AgeRating::create([
            'fsk' => '16',
        ]);

        AgeRating::create([
            'fsk' => '18',
        ]);

        // show information in the command line after everything is run
        $this->command->info('Age Rating seeds finished.');
    }
}
