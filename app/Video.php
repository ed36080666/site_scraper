<?php

namespace App;

use App\Contracts\ScrapeItemInterface;
use App\Traits\ScrapeItemTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model implements ScrapeItemInterface
{
    use ScrapeItemTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'codec',
        'width',
        'height',
        'duration',
        'size',
        'bitrate',
    ];

    public function scrapeItem(): MorphOne
    {
        return $this->morphOne(ScrapeItem::class, 'scrapable');
    }

    public function getLastLogLine()
    {
        if (!$this->scrapeItem->log_path || !file_exists($this->scrapeItem->log_path)) {
            return null;
        }

        $line = '';
        $f = fopen($this->scrapeItem->log_path, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        // Trim trailing newline chars of the file
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        // Read until the start of file or first newline char
        while ($char !== false && $char !== "\n" && $char !== "\r") {
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        fclose($f);

        return $line;
    }

    public function currentProcessingTime()
    {
        $ffmpeg_log_line = $this->getLastLogLine();

        if ($ffmpeg_log_line && str_contains($ffmpeg_log_line, 'time=')) {
            // example: frame=55972 fps= 60 q=-1.0 Lsize=  932819kB time=00:37:18.82 bitrate=3413.2kbits/s speed=2.41x
            // cut the string to begin at time=00:00:00
            $string = substr($ffmpeg_log_line, strpos($ffmpeg_log_line, 'time=') + 5);

            // trim end of the string to end up with only the timestamp
            $string = substr($string, 0, strpos($string, ' '));

            $time = Carbon::createFromTimeString($string);

            // calculate & return processing duration in seconds
            $total_seconds = 0;
            $total_seconds += $time->hour * 3600;
            $total_seconds += $time->minute * 60;
            $total_seconds += $time->second;

            return $total_seconds;
        }

        return null;
    }

    public function processingProgress()
    {
        if ($this->isDone()) {
            return 100;
        }

        $total_length = (int) $this->duration;
        $seconds_processed = (int) $this->currentProcessingTime();

        if (!$seconds_processed || !$total_length) {
            return 0;
        }

        return round(($seconds_processed / $total_length) * 100, 2);
    }

    public function height(): ?int
    {
        return $this->height;
    }

    public function width(): ?int
    {
        return $this->width;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function progress(): float
    {
        return $this->processingProgress();
    }

    public function type(): string
    {
        return 'video';
    }

    public function removeFiles(): void
    {
        $filepath = $this->path() . '/' . $this->name();
        if ($this->path() && file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
