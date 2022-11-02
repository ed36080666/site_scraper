<?php

namespace App\Jobs;

use App\Events\ProcessingStarted;
use App\ScrapeItem;
use App\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string url for the .mp4 file */
    protected $url;

    /** @var string friendly name used to save processed video */
    protected $filename;

    /** @var string path where processed video is saved */
    protected $output_path;

    /** @var string path where ffmpeg stores processing logs */
    protected $log_path;

    /** @var \App\Video */
    protected $video;

    /** @var \App\ScrapeItem */
    protected $scrape_item;

    /**
     * @var bool flag determining if the video URL is a stream (e.g. ".m3u8" extension). for streams
     * we cannot use the same ffprobe command to grab metadata like filesize, etc.
     */
    protected $is_stream;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url, string $filename, bool $is_stream = false)
    {
        $this->url = $url;
        $this->filename = $filename;
        $this->is_stream = $is_stream;
        $this->output_path = config('scrapers.ffmpeg.output_path');
        $this->log_path = config('scrapers.ffmpeg.log_path');

        $this->video = Video::create(['name' => $filename]);

        $this->scrape_item = ScrapeItem::create([
            'status' => ScrapeItem::STATUS_QUEUED,
            'url' => $url,
            'is_stream' => $is_stream,
            'scrapable_id' => $this->video->id,
            'scrapable_type' => $this->video->getMorphClass(),
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $output_path = $this->output_path;
        $log_path = $this->log_path . '/' . str_replace(' ', '_', $this->filename) . '[' . now()->timestamp . '].txt';

        // if we are scraping a stream (m3u8) we cannot get metadata the same with ffprobe.
        // we can still scrape without this metadata but we don't currently have a way
        // to reliably get progress information.
        $meta = $this->is_stream
            ? null // todo look for a solution to allow us to get at least some metadata
            : json_decode(shell_exec("ffprobe -v quiet -print_format json -show_format -show_streams {$this->url}"));

        $this->video->update([
            'codec' => $meta->streams[0]->codec_name ?? null,
            'width' => $meta->streams[0]->width ?? null,
            'height' => $meta->streams[0]->height ?? null,
            'duration' => $meta->format->duration ?? null,
            'size' => $meta->format->size ?? null,
            'bitrate' => $meta->format->bit_rate ?? null,
        ]);

        $this->scrape_item->update([
            'status' => ScrapeItem::STATUS_PROCESSING,
            'started_at' => now(),
            'path' => $output_path,
            'log_path' => $log_path,
        ]);

        event(new ProcessingStarted($this->video));

        try {
            shell_exec("ffmpeg -nostdin -i \"{$this->url}\" -c copy \"$output_path/{$this->filename}\" 1>$log_path 2>&1");
            //        shell_exec("ffmpeg -i \"{$this->url}\" -c copy \"/home/eric/Downloads/ffmpeg_test/{$this->filename}\" > /dev/null 2>&1 &");

        } catch (\Exception $e) {
            $this->scrape_item->update([
                'status' => ScrapeItem::STATUS_ERROR
            ]);
        }

        $this->scrape_item->update([
            'status' => ScrapeItem::STATUS_DONE,
            'finished_at' => now()
        ]);
    }

    public function failed(Throwable $exception)
    {
        dump($exception);
        $this->scrape_item->update([
            'status' => ScrapeItem::STATUS_ERROR
        ]);
    }
}
