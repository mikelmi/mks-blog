@component($template, ['title' => $title, 'attr' => $attr])

    <div class="list-group">
        @foreach($posts as $post)
            <a class="list-group-item list-group-item-action" href="{{route('blog.post', [$post->id, $post->slug])}}">
                {{$post->title}}
            </a>
        @endforeach
    </div>

@endcomponent