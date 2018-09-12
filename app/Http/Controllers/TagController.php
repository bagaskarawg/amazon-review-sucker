<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::withCount('reviews')->orderBy('reviews_count', 'desc')->paginate(10);
        return view('tags.index', compact('tags'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = Tag::withCount('reviews')->findOrFail($id);
        $reviews = $tag->reviews()->paginate(10);
        return view('tags.show', compact('tag', 'reviews'));
    }

    public function search(Request $request)
    {
        if (!$request->has('term')) {
            $tags = Tag::paginate(10);
        } else {
            $tags = Tag::select('name')->containing($request->get('term'))->paginate(10);
        }

        $formattedTags = collect();
        foreach ($tags as $tag) {
            $formattedTags->push([
                'id' => $tag->name,
                'text' => $tag->name
            ]);
        }

        return response()->json([
            'results' => $formattedTags,
            'pagination' => [
                'more' => $tags->currentPage() != $tags->lastPage()
            ]
        ]);
    }
}
