@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ __('Reviews') }}</h4>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product ASIN</th>
                            <th>Title</th>
                            <th>Score</th>
                            <th style="width:125px;">Review Date</th>
                            <th>Author</th>
                            <th># of comments</th>
                            <th>Has Photo?</th>
                            <th>Has Video?</th>
                            <th>Verified?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $i => $review)
                            <tr>
                                <td>{{ (((request()->page ?? 1) - 1) * 10) + ($i + 1) }}</td>
                                <td><a href="{{ route('products.show', $review->product) }}" target="_blank">{{ $review->product->asin }}</a></td>
                                <td><a href="{{ $review->review_link }}" target="_blank">{{ $review->title }}</a></td>
                                <td>{{ $review->score }}</td>
                                <td>{{ Carbon\Carbon::parse($review->review_date)->format('M, d Y') }}</td>
                                <td><a href="{{ $review->author_link }}" target="_blank">{{ $review->author }}</a></td>
                                <td class="text-xs-right">{{ $review->number_of_comments }}</td>
                                <td class="text-xs-right">{{ $review->has_photo ? 'Yes' : 'No' }}</td>
                                <td class="text-xs-right">{{ $review->has_video ? 'Yes' : 'No' }}</td>
                                <td class="text-xs-right">{{ $review->verified  ? 'Yes' : 'No' }}</td>
                                <td class="text-xs-right"><a href="{{ route('reviews.show', $review) }}">Details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $reviews->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
