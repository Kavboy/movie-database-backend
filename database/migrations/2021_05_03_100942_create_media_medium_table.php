<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaMediumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_medium', function (Blueprint $table) {
            $table->foreignId('media_id')->references('id')->on('medias')->constrained()->onDelete('cascade');
            $table->foreignId('medium_id')->references('id')->on('mediums')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_medium');
    }
}
