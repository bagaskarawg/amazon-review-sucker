<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('product')->orderBy('review_date', 'DESC')->paginate(10);
        return view('reviews.index', compact('reviews'));
    }

    public function show($id)
    {
        $review = Review::with('product')->findOrFail($id);
        return view('reviews.show', compact('review'));
    }
}
