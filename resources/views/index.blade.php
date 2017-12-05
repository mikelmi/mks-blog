@extends('blog::layout')

@section('content')
    <div class="media-list blog-list">
        @foreach($items as $item)
            <div class="media blog-item">
                <div class="media-left">
                    @if($item->image)
                        <img class="media-object img-thumbnail" src="{{$thumbnailUrl}}/{{$item->image}}?p=blog" alt="">
                    @endif
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="{{route('blog.post', [$item->id, $item->slug])}}">
                            {{$item->title}}
                        </a>
                    </h4>
                    <p class="blog-info-row text-muted">
                        @if ($item->param('show_date', $showDate, true))
                            <small><i class="fa fa-calendar"></i> {{$item->created_at->format('Y-m-d')}}</small>
                        @endif
                        @if ($item->param('show_author', $showAuthor, true) && $item->author)
                            <small><i class="fa fa-user"></i> {{$item->author_name}}</small>
                        @endif
                    </p>
                    <div class="blog-content">
                        {!! $item->intro_text !!}
                        <p class="text-xs-right">
                            <a href="{{route('blog.post', [$item->id, $item->slug])}}">@lang('general.more')</a>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div>
        {{$items->links()}}
    </div>
@endsection