<?php

namespace App\Jobs;

use App\Events\ProcessingStarted;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url, string $filename)
    {
        $this->url = $url;
        $this->filename = $filename;
        $this->output_path = config('scrapers.ffmpeg.output_path');
        $this->log_path = config('scrapers.ffmpeg.log_path');

        $this->video = Video::create([
            'name' => $filename,
            'status' => Video::STATUS_QUEUED,
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

        $meta = json_decode(shell_exec("ffprobe -v quiet -print_format json -show_format -show_streams {$this->url}"));

        $this->video->update([
            'status' => Video::STATUS_PROCESSING,
            'started_at' => now(),
            'codec' => $meta->streams[0]->codec_name,
            'width' => $meta->streams[0]->width,
            'height' => $meta->streams[0]->height,
            'duration' => $meta->format->duration,
            'size' => $meta->format->size,
            'bitrate' => $meta->format->bit_rate,
            'url' => $meta->format->filename,
            'path' => $output_path,
            'log_path' => $log_path
        ]);

        event(new ProcessingStarted($this->video));

        try {
            shell_exec("ffmpeg -nostdin -i \"{$this->url}\" -c copy \"$output_path/{$this->filename}\" 1>$log_path 2>&1");
            //        shell_exec("ffmpeg -i \"{$this->url}\" -c copy \"/home/eric/Downloads/ffmpeg_test/{$this->filename}\" > /dev/null 2>&1 &");

        } catch (\Exception $e) {
            $this->video->update([
                'status' => Video::STATUS_ERROR
            ]);
        }

        $this->video->update([
            'status' => Video::STATUS_DONE,
            'finished_at' => now()
        ]);
    }

    public function failed(Throwable $exception)
    {
        $this->video->update([
            'status' => Video::STATUS_ERROR
        ]);
    }
}
