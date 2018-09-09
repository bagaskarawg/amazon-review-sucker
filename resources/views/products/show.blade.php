@extends('layouts.app')

@section('custom-css')
<style>
.a-icon.a-icon-text-separator:before {
    content: ' | ';
}
.toggle-handle.btn.btn-default {
    background: white;
}
</style>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('custom-js')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ __('Product Information') }}</h4>
                </div>
                <dl>
                    <dt>Product ASIN</dt>
                    <dd><a href="https://www.amazon.com/dp/{{ $product->asin }}" target="_blank">{{ $product->asin }}</a></dd>
                    <dt>State</dt>
                    <dd>{{ ucwords(implode(explode('_', $product->state), ' ')) }}</dd>
                    <dt>Number of Reviews</dt>
                    <dd>{{ $product->reviews_count }}</dd>
                </dl>
            </div>
            <div class="col-xs-12 col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">{{ __('Summary') }}</h4>
                    </div>
                    <table style="width: 100%">
                        <tr>
                            <th><a href="https://www.amazon.com/dp/{{ $product->asin }}" target="_blank">{{ $product->asin }}</a></td>
                            <th class="text-xs-right">Total</td>
                            <th class="text-xs-right">Verified</td>
                            <th class="text-xs-right">Unverified</td>
                        </tr>
                        <tr>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avg_rating))
                                        <i class="fa fa-star" style="color: gold"></i>
                                    @else
                                        -
                                    @endif
                                @endfor
                                &middot;
                                {{ round($avg_rating, 2) }}
                            </td>
                            <td class="text-xs-right">{{ $product->reviews_count }}</td>
                            <td class="text-xs-right">{{ $total_verified }}</td>
                            <td class="text-xs-right">{{ $total_unverified }}</td>
                        </tr>
                        @foreach($scores as $score)
                            <tr>
                                <td>
                                    <div style="float:left;padding: 5px 0;margin: 11px 0;">
                                        {{ $score->score }} <i class="fa fa-star" style="color: gold"></i>
                                    </div>
                                    <div style="float:left;width: 75%;margin-left: 10px;">
                                        <div class="progress progress-lg" style="margin-bottom: 11px;">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ round(($score->count / $product->reviews_count) * 100, 2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ round(($score->count / $product->reviews_count) * 100, 2) }}%;">
                                                {{ round(($score->count / $product->reviews_count) * 100, 2) }}%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-xs-right">{{ $score->count }}</td>
                                <td class="text-xs-right">{{ $score->verified }}</td>
                                <td class="text-xs-right">{{ $score->unverified }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-xs-12 col-md-3 col-md-offset-1">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Filters</h4>
                    </div>
                    <form action="">
                        <div class="m-b-1">
                            <label for="only_verified">
                                Show Only Verified Reviews:<br/>
                                <input type="checkbox" id="only_verified" name="only_verified" value="1"
                                    data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
                                    {!! request()->only_verified == '1' ? 'checked' : '' !!} />
                            </label>
                        </div>
                        <div class="m-b-1">
                            Stars:<br/>
                            @for($i = 5; $i >= 1; $i--)
                                <label for="{{ $i }}-stars">
                                    <input type="checkbox" name="stars[{{ $i }}]" id="{{ $i }}-stars" value="{{ $i }}"
                                        {!! (request()->get('stars')[$i] ?? 0) == $i ? 'checked' : '' !!}> {{ $i }}
                                </label>
                            @endfor
                        </div>
                        <div class="m-b-1">
                            <label for="variant">Select Variant:</label>
                            <select name="variant" id="variant" class="form-control">
                                <option value="">All Variants</option>
                                @foreach($variants as $variant)
                                    <option value="{!! $variant->child_asin !!}" {!! $variant->child_asin == request()->variant ? 'selected' : '' !!}>{!! $variant->child_name !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div>
        </div>
        @if($product->state == 'completed')
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ __('Reviews') }}</h3>
                    </div>
                    @foreach($reviews as $i => $review)
                        <div class="media">
                            <div class="media-body" style="border-radius: 10px 0 0 10px;padding:10px; border: 1px solid rgba(0, 0, 0, .4);">
                                <h5 class="media-heading"><a href="{{ $review->review_link }}" target="_blank" style="text-decoration: none">{{ $review->title }}</a></h5>
                                <div><a href="https://www.amazon.com/dp/{{ $review->child_asin }}" target="_blank" style="color: grey;">{!! $review->child_name !!}</a></div>
                                <hr style="margin: 5px" />
                                {!! $review->body !!}
                            </div>
                            <div class="media-right" style="border-radius: 0 10px 10px 0;padding:10px; border: 1px solid rgba(0, 0, 0, .4);border-left: none; width: 175px;">
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
                    @endforeach
                </div>
                {!! $reviews->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection
