<?php

namespace App\Jobs;

use App\PhotoGallery;
use App\ScrapeItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessPhotoGallery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string base url for the gallery */
    protected $gallery_url;

    /** @var array urls for all photos to scrape */
    protected $photo_urls;

    /** @var string name used for the directory containing all photos */
    protected $directory_name;

    /** @var string path where gallery folder is saved */
    protected $output_path;

    /** @var string path where scraping processing logs are stored */
    protected $log_path;

    /** @var \App\PhotoGallery */
    protected $photo_gallery;

    /** @var \App\ScrapeItem */
    protected $scrape_item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $gallery_url, array $photo_urls, string $directory_name)
    {
        $this->gallery_url = $gallery_url;
        $this->photo_urls = $photo_urls;
        $this->directory_name = $directory_name;
        $this->output_path = config('scrapers.photo_gallery.output_path');
        $this->log_path = config('scrapers.photo_gallery.log_path');

        $this->photo_gallery = PhotoGallery::create([
            'name' => $directory_name,
            'number_photos' => count($this->photo_urls)
        ]);

        $this->scrape_item = ScrapeItem::create([
            'status' => ScrapeItem::STATUS_QUEUED,
            'url' => $gallery_url,
            'scrapable_id' => $this->photo_gallery->id,
            'scrapable_type' => $this->photo_gallery->getMorphClass()
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $output_path = "{$this->output_path}/{$this->directory_name}";
        $log_path = $this->log_path . '/' . str_replace(' ', '_', $this->directory_name) . '[' . now()->timestamp . '].txt';

        $photo_count = count($this->photo_urls);
        file_put_contents($log_path, "Scraping {$photo_count} photos into {$output_path}...\n0");

        $this->photo_gallery->update([
            'number_photos' => count($this->photo_urls),
        ]);

        $this->scrape_item->update([
            'status' => ScrapeItem::STATUS_PROCESSING,
            'started_at' => now(),
            'path' => $output_path,
            'log_path' => $log_path
        ]);

        try {
            if (!is_dir($output_path)) {
                mkdir($output_path);
            }

            $i = 1;
            foreach($this->photo_urls as $photo_url) {
                $size = getimagesize($photo_url);
                $extension = image_type_to_extension($size[2]);
                $filename = str_pad($i, 4, "0", STR_PAD_LEFT);
                $filename .= $extension;

                $img_data = file_get_contents($photo_url);
                file_put_contents("{$output_path}/{$filename}", $img_data);

                $log_string = file_get_contents($log_path);
                $log_string .= "\n{$i}";
                file_put_contents($log_path, $log_string);

                $i += 1;
            }
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
        $this->scrape_item->update([
            'status' => ScrapeItem::STATUS_ERROR
        ]);
    }
}
