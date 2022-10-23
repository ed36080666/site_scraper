<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';
    const STATUS_DONE = 'done';

    protected $fillable = [
        'name',
        'status',
        'started_at',
        'finished_at',
        'codec',
        'width',
        'height',
        'duration',
        'size',
        'bitrate',
        'url',
        'path',
        'log_path',
        'is_stream',
    ];

    protected $appends = [
        'progress'
    ];

    public function getProgressAttribute()
    {
        return $this->processingProgress();
    }

    public function scopeInProgress(Builder $builder)
    {
        return $builder->where('status', self::STATUS_PROCESSING)
            ->where('finished_at', null);
    }

    public function getLastLogLine()
    {
        if (!$this->log_path || !file_exists($this->log_path)) {
            return null;
        }

        $line = '';
        $f = fopen($this->log_path, 'r');
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

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isErrored(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    public function isQueued(): bool
    {
        return $this->status === self::STATUS_QUEUED;
    }

    public function buildPath(): string
    {
        return $this->path . '/' . $this->name;
    }
}
