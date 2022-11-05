<?php

namespace App\Traits;

use App\ScrapeItem;

trait ScrapeItemTrait
{
    public function id(): int
    {
        return $this->scrapeItem->id;
    }

    public function startedAt(): ?string
    {
        return $this->scrapeItem->started_at;
    }

    public function logExists(): bool
    {
        return $this->scrapeItem->log_path && file_exists($this->scrapeItem->log_path);
    }

    public function path(): ?string
    {
        return $this->scrapeItem->path;
    }

    public function isStream(): bool
    {
        return (bool) $this->scrapeItem->is_stream;
    }

    public function status(): string
    {
        return $this->scrapeItem->status;
    }

    public function isProcessing(): bool
    {
        return $this->scrapeItem->status === ScrapeItem::STATUS_PROCESSING;
    }

    public function isDone(): bool
    {
        return $this->scrapeItem->status === ScrapeItem::STATUS_DONE;
    }

    public function isErrored(): bool
    {
        return $this->scrapeItem->status === ScrapeItem::STATUS_ERROR;
    }

    public function isQueued(): bool
    {
        return $this->scrapeItem->status === ScrapeItem::STATUS_QUEUED;
    }
}
