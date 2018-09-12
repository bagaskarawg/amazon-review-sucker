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
                    @if($tags->count() > 0)
                        @foreach($tags as $i => $tag)
                            <li class="list-group-item">
                                <span class="badge">{{ $tag->reviews_count }}</span>
                                <a href="{{ route('tags.show', $tag->id) }}">
                                    {{ $tag->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item">No tags to be displayed.</li>
                    @endif
                </ul>
            </div>
            {!! $tags->links() !!}
        </div>
    </div>
</div>
@endsection
