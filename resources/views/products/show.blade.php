@extends('layouts.app')

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
                    <dd>{{ $product->asin }}</dd>
                    <dt>State</dt>
                    <dd>{{ ucwords(implode(explode('_', $product->state), ' ')) }}</dd>
                    <dt>Number of Reviews</dt>
                    <dd>{{ $product->reviews_count }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
