<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('product', 'tags')->orderBy('review_date', 'DESC')->paginate(10);
        return view('reviews.index', compact('reviews'));
    }

    public function show($id)
    {
        $review = Review::with('product', 'tags')->findOrFail($id);
        return view('reviews.show', compact('review'));
    }

    public function attachTag(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->attachTag($request->tag);
        $success = $review->save();

        return response()->json([
            'success' => $success,
        ]);
    }

    public function detachTag(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->detachTag($request->tag);
        $success = $review->save();

        return response()->json([
            'success' => $success,
        ]);
    }
}
