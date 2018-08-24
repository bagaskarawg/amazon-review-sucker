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

        $client = app()->scrapper;
        $getPagination = $client->request('GET', "https://www.amazon.com/product-reviews/{$product->asin}");
        $maxPage = $getPagination->filter('ul.a-pagination .page-button')->last()->text();

        for ($i = 1; $i <= $maxPage; $i++) {
            $crawl = $client->request('GET', "https://www.amazon.com/product-reviews/{$product->asin}?pageNumber={$i}");
            $data = $crawl->filter('#cm_cr-review_list .review')->each(function($node) {
                $title = $node->filter('a.review-title')->text();
                $body = $node->filter('.review-text')->html();
                $review_date = Carbon::parse($node->filter('.review-date')->first() ? str_replace('on ', '', $node->filter('.review-date')->text()) : '');
                $author = $node->filter('a.author')->text();
                $author_link = $node->filter('a.author')->link()->getUri();
                $number_of_comments = $node->filter('.review-comment-total')->text();
                $has_photo = $node->filter('.review-image-container')->count() > 0;
                $has_video = $node->filter('.video-block')->count() > 0;
                $verified = $node->filter('.review-data.review-format-strip > span.a-declarative')->text() == 'Verified Purchase';

                return [
                    'title' => $title,
                    'body' => $body,
                    'review_date' => $review_date,
                    'author' => $author,
                    'author_link' => $author_link,
                    'number_of_comments' => $number_of_comments,
                    'has_photo' => $has_photo,
                    'has_video' => $has_video,
                    'verified' => $verified,
                ];
            });

            $product->reviews()->createMany($data);
        }

        $product->state = 'completed';
        $product->save();
    }
}
