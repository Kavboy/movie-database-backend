<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Movie', 'TV']);
            $table->string('title', 255);
            $table->date('release_date');
            $table->text('overview')->default('');
            $table->string('poster_path', 255);
            $table->unsignedBigInteger('tmdb_id')->nullable()->default(NULL);
            $table->string('youtube_link')->nullable()->default(NULL);
            $table->json('cast')->nullable()->default(NULL);
            $table->foreignId('age_rating')->constrained();
            $table->foreignId('location')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
