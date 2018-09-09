<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Jobs\ScrapeReviews;
use DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('reviews')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'asin' => 'required|unique:products,asin',
        ]);

        $product = new Product;
        $product->asin = $request->asin;
        $product->state = 'draft';

        if ($product->save()) {
            ScrapeReviews::dispatch($product);
            return redirect()->route('products.show', $product)->with('status', 'Product created and job dispatched!');            ;
        } else {

        }
    }

    public function show(Request $request, $id)
    {
        $product = Product::withCount('reviews')->findOrFail($id);
        $variants = Review::where('product_id', $id)->whereRaw("child_name != '' AND child_name IS NOT NULL")->groupBy(['child_name', 'child_asin'])->get(['child_name', 'child_asin']);
        $query = Review::where('product_id', $id)->orderBy('review_date', 'DESC');
        if ($request->has('only_verified') && $request->only_verified == '1') {
            $query = $query->where('verified', 1);
        }
        if ($request->has('stars')) {
            $query = $query->whereIn('score', $request->stars);
        }
        if ($request->has('variant') && $request->variant != '') {
            $query = $query->where('child_asin', $request->variant);
        }
        $reviews = $query->paginate(10);

        $avg_rating = Review::where('product_id', $id)->groupBy('product_id')->avg('score');
        $total_verified = Review::where([
            ['product_id', $id],
            ['verified', 1]
        ])->count();
        $total_unverified = Review::where([
            ['product_id', $id],
            ['verified', 0]
        ])->count();


        $verified_query = Review::select('product_id', 'score', DB::raw('COUNT(verified) AS count'))
                                ->where([
                                    ['product_id', $id],
                                    ['verified', 1]
                                ])
                                ->groupBy('score');
        $unverified_query = Review::select('product_id', 'score', DB::raw('COUNT(verified) AS count'))
                                ->where([
                                    ['product_id', $id],
                                    ['verified', 0]
                                ])
                                ->groupBy('score');
        $scores = Review::select(
            'reviews.score',
            DB::raw('COUNT(reviews.verified) AS count'),
            DB::raw('IFNULL(verified.count, 0) AS verified'),
            DB::raw('IFNULL(unverified.count, 0) AS unverified')
        )
        ->leftJoinSub($verified_query, 'verified', function ($join) {
            $join->on('reviews.product_id', '=', 'verified.product_id')
                    ->on('reviews.score', '=', 'verified.score');
        })
        ->leftJoinSub($unverified_query, 'unverified', function ($join) {
            $join->on('reviews.product_id', '=', 'unverified.product_id')
                    ->on('reviews.score', '=', 'unverified.score');
        })
        ->where('reviews.product_id', $id)
        ->groupBy('reviews.score')
        ->orderBy('reviews.score', 'desc')
        ->get();

        return view('products.show', compact('product', 'variants', 'reviews', 'avg_rating', 'total_verified', 'total_unverified', 'scores'));
    }
}
