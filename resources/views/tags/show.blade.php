@extends('layouts.app')

@section('custom-css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection

@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.js"></script>
<script>
    $(document).ready(function() {
        $('.tags').select2();
    });
</script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        {{ __('Tag') }} <b><i>{{ $tag->name }}</i></b>
                        <span class="badge">{{ $tag->reviews_count }}</span>
                    </h4>
                </div>
                @foreach($reviews as $review)
                    <div class="media">
                        <div class="media-body" style="border-radius: 10px 0 0 0;padding:10px; border: 1px solid rgba(0, 0, 0, .4);">
                            <h5 class="media-heading"><a href="{{ $review->review_link }}" target="_blank" style="text-decoration: none">{{ $review->title }}</a></h5>
                            <div><a href="https://www.amazon.com/dp/{{ $review->product->asin }}" target="_blank" style="color: darkgrey;">{!! $review->product->asin !!}</a></div>
                            <div><a href="https://www.amazon.com/dp/{{ $review->child_asin }}" target="_blank" style="color: grey;">{!! $review->child_name !!}</a></div>
                            <hr style="margin: 5px" />
                            {!! $review->body !!}
                        </div>
                        <div class="media-right" style="border-radius: 0 10px 0 0;padding:10px; border: 1px solid rgba(0, 0, 0, .4);border-left: none; width: 175px;">
                            @for($i = 1; $i <= $review->score; $i++)
                                <i class="fa fa-star" style="color: gold"></i>
                            @endfor
                            <div><a href="{{ $review->author_link }}" target="_blank" style="text-decoration: none;">{{ $review->author }}</a></div>
                            <div>{{ date('Y-m-d', strtotime($review->review_date)) }}</div>
                            <div class="label label-{{ $review->verified ? 'success' : 'default' }}">{{ $review->verified ? 'Verified' : 'Not Verified' }}</div>
                            <div>{{ $review->number_of_comments }} comments</div>
                            <div>{{ $review->helpful_votes_count }} helpful votes</div>
                            <div>Has Photo? <span class="label label-{{ $review->has_photo ? 'success' : 'danger' }}">{{ $review->has_photo ? 'Yes' : 'No' }}</span></div>
                            <div>Has Video? <span class="label label-{{ $review->has_video ? 'success' : 'danger' }}">{{ $review->has_video ? 'Yes' : 'No' }}</span></div>
                            <hr style="margin: 5px" />
                            <div><a href="{{ route('reviews.show', $review) }}">Review Detail</a></div>
                        </div>
                    </div>
                    <div style="border-radius: 0 0 10px 10px;margin-bottom: 10px;padding:10px; border: 1px solid rgba(0, 0, 0, .4);">
                        Tags:
                        <select class="tags form-control" data-review-id="{{ $review->id }}" multiple="" disabled="">
                            @foreach($review->tags->pluck('name') as $tag)
                                <option value="{{ $tag }}" selected="selected">{{ $tag }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                {!! $reviews->render() !!}
            </div>
        </div>
    </div>
</div>
@endsection
