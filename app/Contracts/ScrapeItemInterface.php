<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface ScrapeItemInterface
{
    public function id(): int;
    public function fileExists(): bool;
    public function height(): ?int;
    public function isStream(): bool;
    public function name(): string;
    public function progress(): float;
    public function removeFiles(): void;
    public function status(): string;
    public function startedAt(); // todo typehint
    public function scrapeItem(): MorphOne;
    public function type(): string;
    public function width(): ?int;
}
