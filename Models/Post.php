<?php

namespace Modules\Blog\Models;


use App\Models\Role;
use App\Traits\Authority;
use App\Traits\HasCategory;
use App\Traits\HasMeta;
use App\Traits\HasRoles;
use App\Traits\HasSection;
use App\Traits\MoreText;
use App\Traits\Parametrized;
use App\Traits\Sluggable;
use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Post
 * @package Modules\Blog\Models
 *
 * @property int $id
 * @property string $title
 * @property string $lang
 * @property string $intro_text
 * @property string $full_text
 * @property string $text
 * @property string $slug
 *
 * @property Collection $params
 * @property bool $featured
 * @property string $image
 * @property int $hits
 * @property int $status
 *
 * @property Role[] $roles
 *
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class Post extends Model
{
    use SoftDeletes;
    use Parametrized;
    use Sluggable;
    use MoreText;
    use Authority;
    use Taggable;
    use HasMeta;
    use HasRoles;
    use HasCategory;
    use HasSection;

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_role');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeActive($query, $active = true)
    {
        return $query->where('status', $active);
    }

    public function getDefaultMeta(): array
    {
        return [
            'title' => $this->attributes['title']
        ];
    }
}
