<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Jobs\ScrapeReviews;

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
            // 'asin' => 'required|unique:products,asin',
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

    public function show($id)
    {
        $product = Product::withCount('reviews')->with([
            'reviews' => function ($query) {
                $query->orderBy('review_date', 'DESC')->paginate(10);
            }
        ])->findOrFail($id);
        return view('products.show', compact('product'));
    }
}
