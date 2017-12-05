<?php

namespace Modules\Blog\Http\Controllers;


use App\Http\Controllers\SiteController;
use App\Models\Section;
use App\Services\TagService;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\Post;

class BlogController extends SiteController
{
    public function index($sectionId = null, $categoryId = null, $tag = null)
    {
        $items = Post::with('category')->withAuthor()->active()->ordered();

        $section = null;
        $category = null;

        if ($sectionId) {
            if ($section = Section::byType(BlogCategory::class)->find($sectionId)) {
                $items->where('section_id', $section->id);
                $this->seo()->setTitle($section->title);
                $this->breadcrumbs()->add($section->title);
            }
        }

        if ($categoryId) {
            $category = BlogCategory::find($categoryId);
            if ($category) {
                $categories = $category->descendants->pluck('id')->toArray();
                $categories[] = $category->id;
                $items->whereIn('category_id', $categories);

                $this->seo()->setTitle($category->title);
                $this->setCategoryBreadcrumbs($category);
            }
        }

        if ($tag) {
            $items->withAnyTags($tag);
            if (app(TagService::class)->find($tag)) {
                $this->breadcrumbs()->add($tag);
                $this->seo()->setTitle($tag);
            }
        }

        $items = $items->paginate(settings('blog.per_page', 10));
        $thumbnailUrl = route('thumbnail');

        $showDate = settings('blog.show_date');
        $showAuthor = settings('blog.show_author');
        
        return view('blog::index', compact('items', 'category', 'tag', 'thumbnailUrl', 'showDate', 'showAuthor'));
    }

    public function category($id)
    {
        return $this->index(null, $id);
    }

    public function section($id)
    {
        return $this->index($id);
    }

    public function tag($tag)
    {
        return $this->index(null, null, $tag);
    }

    public function post($id)
    {
        $item = Post::with(['category', 'tags'])->withAuthor()->active()->findOrFail($id);

        $showDate = settings('blog.show_date');
        $showAuthor = settings('blog.show_author');

        $thumbnailUrl = route('thumbnail');

        $category = $item->category;

        if ($category) {
            $this->setCategoryBreadcrumbs($category, false);
        }

        $this->setModelMeta($item);

        return view('blog::post', compact('item', 'showDate', 'showAuthor', 'thumbnailUrl', 'category'));
    }
}
