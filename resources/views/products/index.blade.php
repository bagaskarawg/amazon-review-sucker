@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ __('Products') }}</h4>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product ASIN</th>
                            <th>State</th>
                            <th># of Reviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $product)
                            <tr>
                                <td>{{ (((request()->page ?? 1) - 1) * 10) + ($i + 1) }}</td>
                                <td><a href="{{ route('products.show', $product) }}">{{ $product->asin }}</a></td>
                                <td>{{ ucwords(implode(explode('_', $product->state), ' ')) }}</td>
                                <td>{{ $product->reviews_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $products->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
