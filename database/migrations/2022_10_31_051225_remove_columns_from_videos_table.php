<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // sqlite is funky and laravel wasn't letting me just drop the columns I
        // wanted to drop... so I guess we'll just drop/recreate the table :eye-roll:
        Schema::dropIfExists('videos');
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('codec')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('duration')->nullable();
            $table->string('size')->nullable();
            $table->string('bitrate')->nullable();
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
        // nothing to do anymore here
    }
}
