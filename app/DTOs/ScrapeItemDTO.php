<?php

namespace App\DTOs;

use App\Contracts\ScrapeItemInterface;

class ScrapeItemDTO
{
    public $id;
    public $name;
    public $progress;
    public $started_at;
    public $height;
    public $width;
    public $status;
    public $is_stream;
    public $type;
    public $file_exists;

    public function __construct(ScrapeItemInterface $item)
    {
        $this->id = $item->id();
        $this->name = $item->name();
        $this->progress = $item->progress();
        $this->height = $item->height();
        $this->width = $item->width();
        $this->status = $item->status();
        $this->is_stream = $item->isStream();
        $this->started_at = $item->startedAt();
        $this->type = $item->type();
        $this->file_exists = $item->fileExists();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'progress' => $this->progress,
            'height' => $this->height,
            'width' => $this->width,
            'status' => $this->status,
            'is_stream' => $this->is_stream,
            'started_at' => $this->started_at,
            'type' => $this->type,
            'file_exists' => $this->file_exists,
        ];
    }
}
