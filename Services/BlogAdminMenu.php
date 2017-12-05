<?php
/**
 * User: mike
 * Date: 29.11.17
 * Time: 10:46
 */

namespace Modules\Blog\Services;

use App\Contracts\AdminMenuBuilder;
use Lavary\Menu\Builder as MenuBuilder;

class BlogAdminMenu implements AdminMenuBuilder
{
    /**
     * @param MenuBuilder $builder
     */
    public function build(MenuBuilder $builder)
    {
        $builder->add(trans('blog::messages.Blog'), [
            'href' => '#/blog',
            'hash' => 'blog',
            'icon' => 'file-text'
        ]);
    }
}