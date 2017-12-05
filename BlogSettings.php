<?php

namespace Modules\Blog;


use App\Settings\SettingsScope;
use Illuminate\Config\Repository;

class BlogSettings extends SettingsScope
{
    public function name(): string
    {
        return 'blog';
    }

    public function title(): string
    {
        return __('blog::messages.Blog');
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            ['name' => 'per_page', 'label' => __('general.per_page'), 'type' => 'number',
                'placeholder' => '10'],
            ['name' => 'thumbnail_size', 'label' => __('general.thumbnail_size'), 'type' => 'size',
                'width' => [
                    'name' => 'blog[thumbnail_size][width]',
                    'nameSce' => 'blog.thumbnail_size.width',
                    'min' => 50,
                    'max' => 3000,
                    'placeholder' => '100'
                ],
                'height' => [
                    'name' => 'blog[thumbnail_size][height]',
                    'nameSce' => 'blog.thumbnail_size.height',
                    'min' => 50,
                    'max' => 3000,
                    'placeholder' => '100'
                ],
            ],
            ['name' => 'show_author', 'label' => __('general.Show author'), 'type' => 'toggle'],
            ['name' => 'show_date', 'label' => __('general.Show date'), 'type' => 'toggle'],
        ];
    }

    public function rules(): array
    {
        return [
            'per_page' => 'integer|min:1|max:100',
            'thumbnail_size.width' => 'integer|min:1|max:500',
            'thumbnail_size.height' => 'integer|min:1|max:500',
        ];
    }
}