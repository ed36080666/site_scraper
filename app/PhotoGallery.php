<?php

namespace App;

use App\Contracts\ScrapeItemInterface;
use App\Traits\ScrapeItemTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PhotoGallery extends Model implements ScrapeItemInterface
{
    use ScrapeItemTrait;

    protected $fillable = [
        'name',
        'number_photos'
    ];

    public function scrapeItem(): MorphOne
    {
        return $this->morphOne(ScrapeItem::class, 'scrapable');
    }

    public function height(): ?int
    {
        return null;
    }

    public function width(): ?int
    {
        return null;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function progress(): float
    {
        // todo
        return 0;
    }

    public function type(): string
    {
        return 'gallery';
    }
}
