<?php

namespace Modules\Blog\Widgets;


use App\Widgets\WidgetPresenter;
use Cviebrock\EloquentTaggable\Services\TagService;
use Illuminate\Database\Eloquent\Collection;
use Mikelmi\MksAdmin\Form\AdminModelForm;
use Modules\Blog\Models\Post;

class TagsWidget extends WidgetPresenter
{

    /**
     * @return string
     */
    public function title(): string
    {
        return __('blog::messages.TagsWidget');
    }

    public function alias(): string
    {
        return 'blog.tags';
    }

    public function form(AdminModelForm $form, $mode = null)
    {
        $form->addGroup('tags', [
            'title' => $this->title(),
            'fields' => [
                ['name' => 'content', 'label' => __('general.Amount'), 'type' => 'number'],
            ]
        ]);
    }

    public function rules(): array
    {
        return [
            'content' => 'integer|min:1|max:500',
        ];
    }

    public function render(): string
    {
        /** @var TagService $tagService */
        $tagService = app(TagService::class);

        /** @var Collection $tags */
        $tags = $tagService->getAllTags(Post::class);

        $limit = (int) $this->model->content;
        if ($limit) {
            $tags = $tags->splice(0, $limit);
        }

        return $this->view('blog::widget.tags', compact('tags'))->render();
    }
}