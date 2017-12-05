<?php
/**
 * User: mike
 * Date: 29.11.17
 * Time: 10:50
 */

namespace Modules\Blog\Services;


use App\Contracts\RouteCollector;
use App\Services\CategoryManager;
use Illuminate\Support\Collection;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\Post;

class BlogRouteCollector implements RouteCollector
{
    /**
     * @return array
     */
    public function map(): array
    {
        $group = __('blog::messages.Blog');

        return [
            'blog.index' => [
                'text' => $group,
                'group' => $group
            ],
            'blog.section' => [
                'text' => __('general.Section'),
                'group' => $group,
                'extended' => 'select',
            ],
            'blog.category' => [
                'text' => __('general.Category'),
                'group' => $group,
                'extended' => 'select',
            ],
            'blog.post' => [
                'text' => __('blog::messages.Post'),
                'group' => $group,
                'extended' => true,
            ],
            'blog.tag' => [
                'text' => __('general.Tag'),
                'group' => $group,
            ],
        ];
    }

    /**
     * @return array
     */
    public function params(): array
    {
        return [
            'blog.category' => 'collectCategories',
            'blog.section' => 'collectSections',
            'blog.post' => 'collectPosts',
        ];
    }

    /**
     * @return CategoryManager
     */
    private function cm(): CategoryManager
    {
        return resolve(CategoryManager::class);
    }

    public function collectCategories(Collection $data)
    {
        $items = $this->cm()->getSelectOptionsFlatten(BlogCategory::class);

        $data->put('items', $items);
        $data->put('title', __('general.Categories'));
    }

    public function collectSections(Collection $data)
    {
        $items = $this->cm()->getSelectSections(BlogCategory::class);

        $data->put('items', $items);
        $data->put('title', trans('general.Section'));
    }

    public function collectPosts(Collection $data)
    {
        $columns = ['id', 'title', 'lang' ,'slug', 'created_at'];

        $items = Post::ordered()->active()->select($columns);
        $search = request('q');

        if ($search) {
            $items->where('title', 'like', '%'.$search.'%');
        }

        $pagination = $items->paginate(10)->toArray();

        $data->put('items', $pagination['data']);

        unset($pagination['data']);
        $data->put('pagination', $pagination);

        $data->put('title', trans('blog::messages.Posts'));

        $data->put('columns', [
            'id' => 'ID',
            'title' => trans('general.Title'),
            'lang' => trans('general.Language'),
            'created_at' => trans('general.Created at')
        ]);
    }
}