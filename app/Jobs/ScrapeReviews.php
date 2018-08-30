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
                $reviewLink = $node->filter('a.review-title')->count() > 0 ? $node->filter('a.review-title')->link()->getUri() : '';
                $title = $node->filter('a.review-title')->count() > 0 ? $node->filter('a.review-title')->text() : '';
                $score = $node->filter('.review-rating')->count() > 0 ? +str_replace(' out of 5 stars', '', $node->filter('.review-rating')->text()) : 0;
                $body = $node->filter('.review-text')->count() ? $node->filter('.review-text')->html() : '';
                $reviewDate = $node->filter('.review-date')->count() > 0 ? Carbon::parse(str_replace('on ', '', $node->filter('.review-date')->text())) : '';

                $authorDom = $node->filter('a.author');
                $author = 'A customer';
                $authorLink = null;
                if ($authorDom->count() > 0) {
                    $author = $authorDom->text();
                    $authorLink = $authorDom->link()->getUri();
                }

                $commentDom = $node->filter('.review-comment-total');
                $numberOfComments = $commentDom->count() ? $commentDom->text() : 0;

                $hasPhoto = $node->filter('.review-image-container')->count() > 0;
                $hasVideo = $node->filter('.video-block')->count() > 0;

                $verifiedDom = $node->filter('.review-data.review-format-strip > span.a-declarative');
                $verified = $verifiedDom->count() > 0 && $verifiedDom->text() == 'Verified Purchase';

                $childAsin = null;
                $childName = null;
                $childNameNode = $node->filter('.review-data.review-format-strip > a.a-link-normal');
                if ($childNameNode->count() > 0) {
                    $childAsin = explode('/', $childNameNode->link()->getUri())[5] ?? '';
                    $childName = $childNameNode->html();
                }

                $helpfulVotesCount = 0;
                $helpfulCountDom = $node->filter('.cr-vote .cr-vote-text');
                if ($helpfulCountDom->count() > 0) {
                    $helpfulVotesCount = explode(' ', $helpfulCountDom->text())[0] ?? 0;
                    if (strtolower($helpfulVotesCount) == 'one') $helpfulVotesCount = 1;
                }

                return [
                    'child_asin' => $childAsin,
                    'child_name' => substr($childName, 0, 255),
                    'review_link' => $reviewLink,
                    'title' => substr($title, 0, 255),
                    'score' => $score,
                    'body' => $body,
                    'review_date' => $reviewDate,
                    'author' => substr($author, 0, 255),
                    'author_link' => $authorLink,
                    'number_of_comments' => $numberOfComments,
                    'has_photo' => $hasPhoto,
                    'has_video' => $hasVideo,
                    'verified' => $verified,
                    'helpful_votes_count' => $helpfulVotesCount
                ];
            });

            $product->reviews()->createMany($data);
        }

        $product->state = 'completed';
        $product->save();
    }
}
