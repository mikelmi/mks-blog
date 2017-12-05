@component($template, ['title' => $title, 'attr' => $attr])

    <div class="row">
        @foreach($posts as $i => $post)
            <div class="media col-sm-{{12/$cols}}">
                @if($post->image)
                    <div class="media-left" href="#">
                        <img class="media-object img-thumbnail" src="{{$thumbnailUrl}}/{{$post->image}}?p=blog" alt="">
                    </div>
                @endif
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="{{route('blog.post', [$post->id, $post->slug])}}">
                            {{$post->title}}
                        </a>
                    </h4>
                    {!! $post->intro_text !!}
                </div>
            </div>
            @if($loop->iteration % $cols == 0)
                <div class="col-sm-12"> </div>
            @endif
        @endforeach
    </div>

@endcomponent