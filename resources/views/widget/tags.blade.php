@component($template, ['title' => $title, 'attr' => $attr])

@foreach($tags as $tag)
    <a class="tag tag-default" href="{{route('blog.tag', $tag->name)}}">{{$tag->name}}</a>
@endforeach

@endcomponent