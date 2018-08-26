@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ __('Review Details') }}</h4>
                </div>
                <dl>
                    <dt>Product ASIN</dt>
                    <dd><a href="{{ route('products.show', $review->product) }}" target="_blank">{{ $review->product->asin }}</a></dd>
                    <dt>Title</dt>
                    <dd><a href="{{ $review->review_link }}" target="_blank">{{ $review->title }}</a></dd>
                    <dt>Score</dt>
                    <dd>{{ $review->score }}</dd>
                    <dt>Review Date</dt>
                    <dd>{{ Carbon\Carbon::parse($review->review_date)->format('M, d Y') }}</dd>
                    <dt>Author</dt>
                    <dd><a href="{{ $review->author_link }}" target="_blank">{{ $review->author }}</a></dd>
                    <dt># of comments</dt>
                    <dd>{{ $review->number_of_comments }}</dd>
                    <dt>Has Photo?</dt>
                    <dd>{{ $review->has_photo ? 'Yes' : 'No' }}</dd>
                    <dt>Has Video?</dt>
                    <dd>{{ $review->has_video ? 'Yes' : 'No' }}</dd>
                    <dt>Verified?</dt>
                    <dd>{{ $review->verified ? 'Yes' : 'No' }}</dd>
                    <dt>Content</dt>
                    <dd>{{ $review->body }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
