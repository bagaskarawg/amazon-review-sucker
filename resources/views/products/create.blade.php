@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="panel">
                <div class="panel-header">
                    <h4 class="panel-heading">{{ __('Create Product') }}</h4>
                </div>

                <div class="panel-body">
                    <form method="POST" action="{{ route('products.store') }}" aria-label="{{ __('Create Product') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="asin" class="col-sm-4 col-form-label text-md-right">{{ __('Product ASIN') }}</label>

                            <div class="col-md-6">
                                <input id="asin" type="text" class="form-control{{ $errors->has('asin') ? ' is-invalid' : '' }}" name="asin" value="{{ old('asin') }}" required autofocus>

                                @if ($errors->has('asin'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('asin') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
