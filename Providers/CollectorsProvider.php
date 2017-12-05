<?php

namespace Modules\Blog\Providers;


use App\ServiceTag;
use Illuminate\Support\ServiceProvider;
use Modules\Blog\BlogSettings;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Services\BlogAdminMenu;
use Modules\Blog\Services\BlogRouteCollector;
use Modules\Blog\Widgets\PostsWidget;
use Modules\Blog\Widgets\TagsWidget;

class CollectorsProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->tag(BlogRouteCollector::class, ServiceTag::ROUTES);
        $this->app->tag(BlogAdminMenu::class, ServiceTag::ADMIN_MENU);
        $this->app->tag(BlogSettings::class, ServiceTag::SETTINGS);
        $this->app->tag(BlogCategory::class, ServiceTag::CATEGORIES);

        $this->app->tag([
            PostsWidget::class,
            TagsWidget::class
        ], ServiceTag::WIDGETS);
    }
}