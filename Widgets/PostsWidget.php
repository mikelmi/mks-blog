<?php

namespace Modules\Blog\Widgets;


use App\Widgets\WidgetPresenter;
use Mikelmi\MksAdmin\Form\AdminModelForm;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\Post;

class PostsWidget extends WidgetPresenter
{
    /**
     * @return string
     */
    public function title(): string
    {
        return __('blog::messages.PostsWidget');
    }

    public function alias(): string
    {
        return 'blog.posts';
    }

    public function form(AdminModelForm $form, $mode = null)
    {
        $form->addGroup('blog', [
            'title' => $this->title(),
            'fields' => [
                ['name' => 'params[type]', 'nameSce' => 'params.type', 'label' => __('general.Template'),
                    'type' => 'select',
                    'options' => $this->getPresentersList(),
                    'value' => $this->model->param('type', 'list')
                ],
                ['name' => 'content', 'label' => __('general.Amount'), 'type' => 'number'],
                ['name' => 'params[cols]', 'nameSce' => 'params.cols', 'label' => __('general.cols'), 'type' => 'number',
                    'value' => $this->model->param('cols')
                ]
            ]
        ]);
    }

    public function getPresentersList()
    {
        return [
            'list' => trans('blog::messages.widget_list'),
            'list_photo' => trans('blog::messages.widget_list_photo'),
            'grid' => trans('blog::messages.widget_grid')
        ];
    }

    public function rules(): array
    {
        return [
            'content' => 'integer|min:1|max:500',
            'params.cols' => 'integer|min:1|max:12',
        ];
    }

    public function render(): string
    {
        $posts = Post::with('category')->ordered();
        
        $limit = (int) $this->model->content;

        if (!$limit) {
            $limit = 5;
        }
        
        $posts->limit($limit);

        $section = $this->model->param('section');
        $category = $this->model->param('category');

        $categories = [];

        if ($section && !$category) {
            $posts->where('section_id', $section);
        } elseif ($category) {
            $categoryModel = BlogCategory::find($category);
            if ($categoryModel) {
                $categories = $categoryModel->descendants->pluck('id')->toArray();
                $categories[] = $categoryModel->id;
            }
        }

        if ($categories) {
            $posts->whereIn('category_id', $categories);
        }

        $posts = $posts->get();
        
        $type = $this->model->param('type');
        
        if (!$type || !in_array($type, array_keys($this->getPresentersList()))) {
            $type = 'list';
        }

        $thumbnailUrl = route('thumbnail');
        $cols = $this->model->param('cols', 1);

        $showDate = settings('blog.show_date');
        $showAuthor = settings('blog.show_author');

        return $this->view('blog::widget.posts.' . $type, compact('posts', 'thumbnailUrl', 'cols', 'showAuthor', 'showDate'))
            ->render();
    }
}
