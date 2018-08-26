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
            $crawl = $client->request('GET', "https://www.amazon.com/product-reviews/{$product->asin}?sortBy=recent&reviewerType=all_reviews&formatType=all_formats&pageNumber={$i}");
            $data = $crawl->filter('.review-views .review')->each(function($node) {
                $review_link = $node->filter('a.review-title')->count() > 0 ? $node->filter('a.review-title')->link()->getUri() : '';
                $title = $node->filter('a.review-title')->count() > 0 ? $node->filter('a.review-title')->text() : '';
                $score = $node->filter('.review-rating')->count() > 0 ? +str_replace(' out of 5 stars', '', $node->filter('.review-rating')->text()) : '';
                $body = $node->filter('.review-text')->count() > 0 ? $node->filter('.review-text')->html() : '';
                $review_date = Carbon::parse($node->filter('.review-date')->count() > 0 ? str_replace('on ', '', $node->filter('.review-date')->text()) : '');
                $author = $node->filter('a.author')->count() > 0 ? $node->filter('a.author')->text() : 'A customer';
                $author_link = $node->filter('a.author')->count() > 0 ? $node->filter('a.author')->link()->getUri() : '';
                $number_of_comments = $node->filter('.review-comment-total')->text();
                $has_photo = $node->filter('.review-image-container')->count() > 0;
                $has_video = $node->filter('.video-block')->count() > 0;
                $verified = $node->filter('.review-data.review-format-strip > span.a-declarative')->count() > 0 && $node->filter('.review-data.review-format-strip > span.a-declarative')->text() == 'Verified Purchase';

                return [
                    'review_link' => $review_link,
                    'title' => $title,
                    'score' => $score,
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
