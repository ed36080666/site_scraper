<?php

use App\Video;
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
        $videos = Video::all();

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

        $videos->each(function (Video $video) {
            $newVideo = new Video;
            $newVideo->forceFill([
                'id' => $video->id,
                'name' => $video->name,
                'codec' => $video->codec,
                'width' => $video->width,
                'height' => $video->height,
                'duration' => $video->duration,
                'size' => $video->size,
                'bitrate' => $video->bitrate,
                'created_at' => $video->created_at,
                'updated_at' => $video->updated_at
            ]);
            $newVideo->save();
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
