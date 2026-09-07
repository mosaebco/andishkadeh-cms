<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\Post;
use App\Models\Series;
use Inertia\Inertia;
use Inertia\Response;

class ContentListingController extends Controller
{
    public function announcements(): Response
    {
        return $this->typed('announcement', 'اخبار و اطلاعیه‌ها', 'تازه‌ها', 'announcements');
    }

    public function courses(): Response
    {
        return $this->typed('course', 'دوره‌ها و برنامه‌ها', 'آموزش', 'cards');
    }

    public function books(): Response
    {
        return $this->typed('book', 'کتاب‌ها', 'مطالعه', 'cards');
    }

    public function posts(): Response
    {
        $series = Series::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Series $item): array => [
                'id' => $item->id,
                'type' => 'series',
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->description,
                'coverImageUrl' => $this->imageUrl($item->cover_image_path),
                'publishedAt' => $item->published_at?->toIso8601String(),
                'url' => route('series.show', ['series' => $item->slug]),
            ]);

        $posts = Post::query()
            ->published()
            ->where(fn ($query): mixed => $query
                ->whereNull('series_id')
                ->orWhereHas('series', fn ($seriesQuery) => $seriesQuery->published()))
            ->with('series')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Post $post): array => [
                'id' => $post->id,
                'type' => 'post',
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'coverImageUrl' => $this->imageUrl($post->cover_image_path),
                'publishedAt' => $post->published_at?->toIso8601String(),
                'url' => route('posts.show', ['post' => $post->slug]),
                'seriesTitle' => $post->series?->title,
            ]);

        return Inertia::render('Listing', [
            'title' => 'مطالب و مجموعه‌ها',
            'eyebrow' => 'خواندنی‌ها',
            'kind' => 'cards',
            'items' => $series->concat($posts)->sortByDesc(fn (array $item): string => $item['publishedAt'] ?? '')->values(),
        ]);
    }

    private function typed(string $type, string $title, string $eyebrow, string $kind): Response
    {
        $items = ContentItem::query()
            ->published()
            ->ofType($type)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ContentItem $item): array => [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt,
                'coverImageUrl' => $this->imageUrl($item->cover_image_path),
                'publishedAt' => $item->published_at?->toIso8601String(),
                'url' => route($type.'s.show', ['slug' => $item->slug]),
            ])
            ->values();

        return Inertia::render('Listing', compact('title', 'eyebrow', 'kind', 'items'));
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? '/storage/'.ltrim($path, '/') : null;
    }
}
