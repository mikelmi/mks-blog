<?php

namespace Modules\Blog\Providers;


use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config([
            'image.presets.blog' => [
                'w' => settings('blog.thumbnail_size.width', 100),
                'h' => settings('blog.thumbnail_size.height', 100),
                'fit' => 'fill',
            ]
        ]);
    }
}