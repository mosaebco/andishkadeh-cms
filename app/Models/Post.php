<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['type', 'series_id', 'title', 'slug', 'excerpt', 'cover_image_path', 'content_blocks', 'status', 'sort_order', 'published_at'])]
class Post extends ContentItem
{
    use HasFactory;

    protected static function booted(): void
    {
        // Set the discriminator before the shared model's validation callback
        // runs. Post is a convenience model over the shared table.
        static::saving(function (Post $post): void {
            $post->type = 'post';
        });

        parent::booted();

        static::addGlobalScope('post_type', function (Builder $query): void {
            $query->where($query->getModel()->getTable().'.type', 'post');
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
