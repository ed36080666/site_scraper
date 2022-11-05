<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScrapeItem extends Model
{
    use SoftDeletes;

    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';
    const STATUS_DONE = 'done';

    protected $fillable = [
        'status',
        'started_at',
        'finished_at',
        'url',
        'is_stream',
        'path',
        'log_path',
        'scrapable_id',
        'scrapable_type'
    ];

    protected $casts = [
        'is_stream' => 'boolean',
    ];

    public function scrapable()
    {
        return $this->morphTo();
    }
}
