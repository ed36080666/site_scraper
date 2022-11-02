<?php

use App\ScrapeItem;
use App\Video;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrape_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('scrapable');
            $table->string('status');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->dateTime('url')->nullable();
            $table->boolean('is_stream')->default(false);
            $table->string('path')->nullable();
            $table->string('log_path')->nullable();
            $table->timestamps();
        });

        Video::all()->each(function (Video $video) {
            ScrapeItem::create([
                'scrapable_id' => $video->id,
                'scrapable_type' => $video->getMorphClass(),
                'status' => $video->status,
                'started_at' => $video->started_at,
                'finished_at' => $video->finished_at,
                'url' => $video->url,
                'path' => $video->path,
                'log_path' => $video->log_path,
                'is_stream' => $video->is_stream ?? false
            ]);
        });;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scrape_items');
    }
}
