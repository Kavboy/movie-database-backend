<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::create([
            'name' => 'action',
        ]);

        Genre::create([
            'name' => 'adventure',
        ]);

        Genre::create([
            'name' => 'animation',
        ]);

        Genre::create([
            'name' => 'comedy',
        ]);

        Genre::create([
            'name' => 'crime',
        ]);

        Genre::create([
            'name' => 'documentary',
        ]);

        Genre::create([
            'name' => 'drama',
        ]);

        Genre::create([
            'name' => 'family',
        ]);

        Genre::create([
            'name' => 'fantasy',
        ]);

        Genre::create([
            'name' => 'history',
        ]);

        Genre::create([
            'name' => 'horror',
        ]);

        Genre::create([
            'name' => 'music',
        ]);

        Genre::create([
            'name' => 'mystery',
        ]);

        Genre::create([
            'name' => 'romance',
        ]);

        Genre::create([
            'name' => 'science-fiction',
        ]);

        Genre::create([
            'name' => 'tv-movie',
        ]);

        Genre::create([
            'name' => 'thriller',
        ]);

        Genre::create([
            'name' => 'war',
        ]);

        Genre::create([
            'name' => 'western',
        ]);

        Genre::create([
            'name' => 'kids',
        ]);

        Genre::create([
            'name' => 'news',
        ]);

        Genre::create([
            'name' => 'reality',
        ]);

        Genre::create([
            'name' => 'soap',
        ]);

        Genre::create([
            'name' => 'talk',
        ]);

        Genre::create([
            'name' => 'politics',
        ]);
    }
}
