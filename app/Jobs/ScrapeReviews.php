<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Product;
use Carbon\Carbon;

class ScrapeReviews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product;
        $product->state = 'in_progress';
        $product->save();

        $reviews = node_execute('scraper', [
            'asin' => $product->asin
        ], true);

        if (isset($reviews->error) || !is_array($reviews)) {
            throw new \Exception(file_get_contents(base_path('storage/logs/reviews.log')));
        }

        $product->reviews()->createMany($reviews);

        $product->state = 'completed';
        $product->save();
    }
}
