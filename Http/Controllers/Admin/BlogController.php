<?php

namespace Modules\Blog\Http\Controllers\Admin;


use App\Http\Controllers\Admin\AdminController;
use App\Traits\CrudPermissions;
use Illuminate\Http\Request;
use Mikelmi\MksAdmin\Form\AdminModelForm;
use Mikelmi\MksAdmin\Traits\CountItemsResponse;
use Mikelmi\MksAdmin\Traits\CrudRequests;
use Mikelmi\MksAdmin\Traits\ToggleRequests;
use Mikelmi\MksAdmin\Traits\TrashRequests;
use Mikelmi\SmartTable\SmartTable;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\Post;

class BlogController extends AdminController
{
    use CrudRequests,
        TrashRequests,
        ToggleRequests,
        CountItemsResponse,
        CrudPermissions;

    public $modelClass = Post::class;

    public $countScopes = ['all', 'trash'];

    public $toggleField = 'status';

    public $permissionsPrefix = 'blog';

    protected function dataGridUrl($scope = null): string
    {
        return route('admin::blog.index', $scope);
    }

    protected function dataGridJson(SmartTable $smartTable, $scope = null)
    {
        $items = $scope == 'trash' ? Post::onlyTrashed() : new Post();

        $items = $items->select([
            'posts.id',
            'posts.image',
            'posts.title',
            'posts.lang',
            'posts.slug',
            'posts.featured',
            'posts.status',
            'posts.hits',
            'posts.created_at',
            'posts.category_id',
            'categories.title as category_title',
            'posts.section_id',
            'sections.title as section_title',
            'users.name as user_name',
        ])->leftJoin('sections','sections.id','=','posts.section_id')
            ->leftJoin('categories','categories.id','=','posts.category_id')
            ->leftJoin('users','users.id','=','posts.created_by');

        return $smartTable->make($items)
            ->setSearchColumns(['title', 'intro_text', 'full_text'])
            ->apply()
            ->orderBy('posts.created_at', 'desc')
            ->orderBy('posts.id', 'asc')
            ->response();
    }

    protected function dataGridOptions($scope = null): array
    {
        $actions[] = ['type' => 'edit', 'url' => hash_url('blog/edit/{{row.id}}')];
        $tools = [];

        $canEdit = $this->canEdit();
        $canDelete = $this->canDelete();
        $canCreate = $this->canCreate();
        $canToggle = $this->canToggle();

        $actions = [];

        if ($canEdit) {
            $actions[] = ['type' => 'edit', 'url' => hash_url('blog/edit/{{row.id}}')];
        }

        if ($scope !== 'trash') {
            $actions[] = ['type' => 'link', 'url' => url('blog/post/{{row.id}}/{{row.slug}}'),
                'target' => '_blank', 'icon' => 'external-link', 'title' => __('general.View'), 'btnType' => 'outline-info no-b'];
        }

        if ($scope == 'trash') {
            $actions[] = ['type' => 'restore', 'url' => route('admin::blog.restore')];
            $tools[] = ['type' => 'restore', 'url' => route('admin::blog.restore')];
        } else {
            $actions[] = ['type' => 'trash', 'url' => route('admin::blog.toTrash')];
            $tools[] = ['type' => 'trash', 'url' => route('admin::blog.toTrash')];
        }

        if ($canDelete) {
            $actions[] = ['type' => 'delete', 'url' => route('admin::blog.delete')];
        }

        return [
            'title' => __('blog::messages.Blog'),
            'createLink' => $canCreate ? hash_url('blog/create') : false,
            'toggleButton' => $canToggle ?
                [route('admin::blog.toggle.batch', 1), route('admin::blog.toggle.batch', 0)] : false,
            'tools' => $tools,
            'deleteButton' => $canDelete ? route('admin::blog.delete') : false,

            'columns' => [
                ['key' => 'id', 'title' => 'ID', 'sortable' => true, 'searchable' => false],
                ['key' => 'image', 'title' => '', 'type' => 'thumbnail'],
                ['key' => 'title', 'type' => 'link',  'title'=> __('general.Title'), 'sortable' => true, 'searchable' => true,
                    'url' => hash_url('blog/edit/{{row.id}}')],
                ['key' => 'lang', 'title' => __('general.Language'), 'type' => 'language', 'sortable' => true, 'searchable' => true],
                ['key' => 'status', 'title' => __('general.Status'), 'type' => 'status', 'url' => route('admin::blog.toggle'),
                    'sortable' => true, 'searchable' => true,
                    'disabled' => !$canToggle
                ],
                ['key' => 'section_title', 'title' => __('general.Section'), 'type' => 'section', 'searchKey' => 'section_id',
                    'sortable' => true, 'searchable' => true, 'categoryType' => BlogCategory::class],
                ['key' => 'category_title', 'title' => __('general.Category'), 'type' => 'category', 'searchKey' => 'category_id',
                    'sortable' => true, 'searchable' => true, 'categoryType' => BlogCategory::class],
                ['key' => 'created_at', 'type' => 'date', 'title' => __('general.Created at')],
                ['key' => 'user_name', 'type' => 'user', 'title' => __('general.Author'), 'id' => 'user_id'],
                ['type' => 'actions', 'actions' => $actions],
            ],
            'baseUrl' => hash_url('blog'),
            'scopes' => [
                ['title' => __('blog::messages.Posts'), 'badge'=>'{{page.model.count_all}}'],
                ['name' => 'trash', 'title' => __('admin::messages.Trash'), 'icon' => 'trash', 'badge'=>'{{page.model.count_trash}}']
            ],
            'rowAttributes' => [
                'ng-class' => "{'table-warning': !row.status}"
            ]
        ];
    }

    protected function formModel($model = null)
    {
        if ($model instanceof Post) {
            return $model;
        }

        return $model ? Post::withTrashed()->find($model) : new Post();
    }

    public function form(Post $model, $mode = null)
    {
        $form = new AdminModelForm($model);

        $form->setAction(route('admin::blog.' . ($model->id ? 'update':'store'), $model->id));
        $form->addBreadCrumb(__('blog::messages.Blog'), hash_url('blog'));
        $form->addBreadCrumb(__('blog::messages.Posts'), hash_url('blog'));
        $form->setBackUrl(hash_url('blog'));

        if ($this->canCreate($model)) {
            $form->setNewUrl(hash_url('blog/create'));
        }

        if ($model->id) {
            if ($this->canEdit($model)) {
                $form->setEditUrl(hash_url('blog/edit', $model->id));
            }
            if ($this->canDelete($model)) {
                $form->setDeleteUrl(route('admin::blog.delete', $model->id));
            }
        }

        if (!$model->trashed() && $model->id) {
            $form->addField([
                'type' => 'link',
                'label' => __('general.View'),
                'url' => route('blog.post', ['id' => $model->id, 'slug' => $model->slug]),
                'target' => '_blank'
            ]);
        }

        $form->addGroup('general', [
            'title' => __('blog::messages.Post'),
            'fields' => [
                ['name' => 'lang', 'type' => 'language'],
                ['name' => 'title', 'required' => true, 'label' => __('general.Title')],
                ['name' => 'image', 'type' => 'image', 'label' => __('general.Image')],
                ['name' => 'status', 'type' => 'toggle', 'label' => __('general.Active')],
                ['name' => 'category_id', 'type' => 'category', 'label' => __('general.Category'), 'categoryType' => BlogCategory::class],
                ['name' => 'text', 'label' => __('general.Text'), 'type' => 'editor', 'allowContent' => true],
                ['name' => 'tags[]', 'type' => 'tags', 'model' => $model],
            ]
        ]);

        $fields = [
            ['name' => 'params[show_author]', 'nameSce' => 'params.show_author', 'type' => 'toggleDefault',
                'label' => __('general.Show author'), 'value' => $model->param('show_author')],
            ['name' => 'params[show_date]', 'nameSce' => 'params.show_date', 'type' => 'toggleDefault',
                'label' => __('general.Show date'), 'value' => $model->param('show_date')],
            ['name' => 'params[hide_title]', 'nameSce' => 'params.hide_title', 'type' => 'toggle',
                'label' => __('general.Hide Title'), 'value' => $model->param('hide_title')],
            ['name' => 'params[roles]', 'nameSce' => 'params.roles', 'type' => 'rolesShow',
                'value' => $model->param('roles'), 'model' => $model],
        ];

        if ($model->id) {
            array_unshift(
                $fields,
                ['name' => 'created_at', 'type' => 'staticText', 'label' => __('general.Created at')],
                ['value' => $model->getAuthorName(), 'type' => 'staticText', 'label' => __('general.Author')]
            );
        }

        $form->addGroup('params', [
            'title' => __('general.Params'),
            'fields' => $fields,
        ]);

        $form->addGroup('meta', [
            'title' => __('general.meta_tags'),
            'fields' => [
                ['name' => 'meta', 'type' => 'meta', 'value' => $model->getMetaArray()],
            ],
        ]);

        return $form;
    }

    public function save(Request $request, Post $model)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $model->title = $request->input('title');
        $model->text = $request->input('text', '');
        $model->status = $request->input('status', 0);

        if ($request->exists('lang')) {
            $model->lang = $request->input('lang');
        }

        $model->image = $request->input('image');

        $model->params = $request->input('params', []);
        $model->setMeta($request->input('meta'));

        \DB::beginTransaction();

        if ($request->exists('category')) {
            $category_id = $request->get('category');
            $model->category()->associate($category_id ?: null);
        }

        if (!$model->category_id) {
            $section_id = $request->get('section');
            $model->section()->associate($section_id ?: null);
        }

        $model->save();

        $rolesShowing = $model->param('roles');

        $roles = !$rolesShowing || $rolesShowing == '1' ? [] : (array)$request->input('roles');
        $model->syncRoles($roles);

        if ($request->exists('tags')) {
            $model->syncTags($request->get('tags'));
        }

        if ($request->header('X-Submit-Flag') == 2) {
            $model->restore();
        }

        \DB::commit();

        $this->flashSuccess(trans('general.Saved'));

        return $this->redirect([
            '/blog' . ($model->trashed() ? '/trash' : ''),
            '/blog/edit',
            '/blog'
        ]);
    }

    protected function afterDelete($response)
    {
        $this->triggerClearCache();

        return $this->setItemsCount($response);
    }

    protected function afterTrash($response)
    {
        $this->triggerClearCache();

        return $this->setItemsCount($response);
    }

    protected function afterRestore($response)
    {
        $this->triggerClearCache();

        return $this->setItemsCount($response);
    }
}