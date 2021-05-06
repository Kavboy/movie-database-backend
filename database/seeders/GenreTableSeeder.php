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
            'name' => 'Action',
        ]);

        Genre::create([
            'name' => 'Abenteuer',
        ]);

        Genre::create([
            'name' => 'Animation',
        ]);

        Genre::create([
            'name' => 'Comedy',
        ]);

        Genre::create([
            'name' => 'Crime',
        ]);

        Genre::create([
            'name' => 'Documentary',
        ]);

        Genre::create([
            'name' => 'Drama',
        ]);

        Genre::create([
            'name' => 'Family',
        ]);

        Genre::create([
            'name' => 'Fantasy',
        ]);

        Genre::create([
            'name' => 'History',
        ]);

        Genre::create([
            'name' => 'Horror',
        ]);

        Genre::create([
            'name' => 'Music',
        ]);

        Genre::create([
            'name' => 'Mystery',
        ]);

        Genre::create([
            'name' => 'Romance',
        ]);

        Genre::create([
            'name' => 'Science Fiction',
        ]);

        Genre::create([
            'name' => 'TV Movie',
        ]);

        Genre::create([
            'name' => 'Thriller',
        ]);

        Genre::create([
            'name' => 'War',
        ]);

        Genre::create([
            'name' => 'Western',
        ]);

        Genre::create([
            'name' => 'Kids',
        ]);

        Genre::create([
            'name' => 'News',
        ]);

        Genre::create([
            'name' => 'Reality',
        ]);

        Genre::create([
            'name' => 'Soap',
        ]);

        Genre::create([
            'name' => 'Talk',
        ]);

        Genre::create([
            'name' => 'Politics',
        ]);
    }
}
