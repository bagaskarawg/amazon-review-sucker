@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ __('Tags') }}</h4>
                </div>
                <ul class="list-group">
                    @foreach($tags as $i => $tag)
                        <li class="list-group-item">
                            <span class="badge">{{ $tag->reviews_count }}</span>
                            <a href="{{ route('tags.show', $tag->id) }}">
                                {{ $tag->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            {!! $tags->links() !!}
        </div>
    </div>
</div>
@endsection
