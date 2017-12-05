@component($template, ['title' => $title, 'attr' => $attr])

    <div class="list-group">
        @foreach($posts as $post)
            <a class="list-group-item list-group-item-action" href="{{route('blog.post', [$post->id, $post->slug])}}">
                <div class="media">
                    @if($post->image)
                        <div class="media-left" href="#">
                            <img class="media-object img-thumbnail" src="{{$thumbnailUrl}}/{{$post->image}}?p=blog" alt="">
                        </div>
                    @endif
                    <div class="media-body">
                        <h4 class="media-heading">{{$post->title}}</h4>
                        {!! $post->intro_text !!}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

@endcomponent