<?php

namespace Database\Seeders;

use App\Models\Medium;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Medium::create([
            'medium' => 'Bluray',
        ]);

        Medium::create([
            'medium' => 'DVD',
        ]);

        // show information in the command line after everything is run
        $this->command->info('Medium seeds finished.');
    }
}
