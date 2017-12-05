@extends('blog::layout')

@section('content')
    <div class="media blog-item">
        <div class="media-left">
            @if($item->image)
                <img class="media-object img-thumbnail" src="{{$thumbnailUrl}}/{{$item->image}}?p=blog" alt="">
            @endif
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                {{$item->title}}
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
                {!! $item->full_text !!}
            </div>

            <p class="blog-tags">
                @if ($item->tags->count())
                    <small class="text-muted"><i class="fa fa-tags"></i> @lang('general.Tags'): </small>
                    @foreach($item->tags as $tag)
                        <a href="{{route('blog.tag', $tag->name)}}" class="tag tag-default">{{$tag->name}}</a>
                    @endforeach
                @endif
            </p>
        </div>
    </div>
@endsection