<?php

namespace Modules\Blog\Models;


use App\Contracts\CategoryType;
use App\Models\Category;

class BlogCategory extends Category implements CategoryType
{
    public function getUrl()
    {
        return route('blog.category', [$this->id, $this->slug]);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return __CLASS__;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return __('blog::messages.Blog');
    }
}