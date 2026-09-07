<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

#[Fillable(['title', 'slug', 'description', 'cover_image_path', 'status', 'sort_order', 'published_at'])]
class Series extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected static function booted(): void
    {
        static::saving(function (Series $series): void {
            if (blank($series->title)) {
                throw ValidationException::withMessages([
                    'title' => 'A title is required for every series.',
                ]);
            }

            if ($series->status === 'published' && blank($series->published_at)) {
                $series->published_at = now();
            }

            if (blank($series->slug)) {
                $baseSlug = Str::slug($series->title) ?: 'series';
                $series->slug = $baseSlug;
                $suffix = 2;

                while (static::query()
                    ->where('slug', $series->slug)
                    ->when($series->exists, fn (Builder $query) => $query->where(
                        $query->getModel()->getQualifiedKeyName(),
                        '!=',
                        $series->getKey(),
                    ))
                    ->exists()) {
                    $series->slug = $baseSlug.'-'.$suffix++;
                }
            }
        });
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('sort_order')->orderBy('published_at');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
