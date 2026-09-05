<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

#[Fillable(['type', 'series_id', 'title', 'slug', 'excerpt', 'cover_image_path', 'content_blocks', 'status', 'sort_order', 'published_at'])]
class ContentItem extends Model
{
    use HasFactory;

    public const TYPES = ['post', 'course', 'book', 'announcement'];

    protected $table = 'content_items';

    protected static function booted(): void
    {
        static::saving(function (ContentItem $item): void {
            if (blank($item->title)) {
                throw ValidationException::withMessages([
                    'title' => 'A title is required for every content item.',
                ]);
            }

            if (! in_array($item->type, self::TYPES, true)) {
                throw new InvalidArgumentException('Unsupported content item type: '.(string) $item->type);
            }

            if (in_array($item->type, ['course', 'book'], true) && blank($item->cover_image_path)) {
                throw ValidationException::withMessages([
                    'cover_image_path' => 'A banner image is required for courses and books.',
                ]);
            }

            $hasText = collect($item->content_blocks ?? [])
                ->contains(function (mixed $block): bool {
                    if (! is_array($block) || ($block['type'] ?? null) !== 'rich_text') {
                        return false;
                    }

                    $body = data_get($block, 'data.body');

                    return is_string($body) && filled(trim(strip_tags($body)));
                });

            if (! $hasText) {
                throw ValidationException::withMessages([
                    'content_blocks' => 'At least one text block is required.',
                ]);
            }

            if (blank($item->slug)) {
                $baseSlug = Str::slug($item->title) ?: 'content';
                $item->slug = $baseSlug;
                $suffix = 2;

                while (static::query()
                    ->where('type', $item->type)
                    ->where('slug', $item->slug)
                    ->when($item->exists, fn (Builder $query) => $query->where(
                        $query->getModel()->getQualifiedKeyName(),
                        '!=',
                        $item->getKey(),
                    ))
                    ->exists()) {
                    $item->slug = $baseSlug.'-'.$suffix++;
                }
            }

            if ($item->type !== 'post') {
                $item->series_id = null;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'content_blocks' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ContentLink::class, 'content_item_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getRouteKeyName(): string
    {
        // Public type routes resolve slugs explicitly; Filament can safely
        // address records by ID even when slugs are reused across types.
        return 'id';
    }
}
