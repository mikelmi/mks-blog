<?php
/**
 * User: mike
 * Date: 29.11.17
 * Time: 10:42
 */

\Mikelmi\MksAdmin\Services\AdminRoute::group('BlogController', 'blog', null, ['trash' => true, 'toggle' => true]);