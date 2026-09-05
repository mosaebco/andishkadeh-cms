<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\ContactMethod;
use App\Models\ContentItem;
use App\Models\Post;
use App\Models\Series;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $banners = Banner::query()
            ->visible()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Banner $banner): array => [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'imageUrl' => $this->imageUrl($banner->image_path),
                'linkUrl' => $banner->link_url,
                'linkLabel' => $banner->link_label,
            ])
            ->values();

        $publishedSeries = Series::query()
            ->published()
            ->with(['posts' => fn (HasMany $query) => $query->published()])
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();

        $series = $publishedSeries->map(fn (Series $item): array => [
            'id' => $item->id,
            'type' => 'series',
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'coverImageUrl' => $this->imageUrl($item->cover_image_path),
            'url' => route('series.show', $item),
            'posts' => $item->posts->map(fn (Post $post): array => $this->postSummary($post))->values(),
        ])->values();

        $standaloneAndSeriesPosts = Post::query()
            ->published()
            ->where(function ($query): void {
                $query
                    ->whereNull('series_id')
                    ->orWhereHas('series', fn ($seriesQuery) => $seriesQuery->published());
            })
            ->with('series')
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $postsAndSeries = $series
            ->map(fn (array $item): array => [
                'id' => $item['id'],
                'type' => 'series',
                'title' => $item['title'],
                'slug' => $item['slug'],
                'excerpt' => $item['description'],
                'coverImageUrl' => $item['coverImageUrl'],
                'publishedAt' => null,
                'url' => $item['url'],
            ])
            ->concat($standaloneAndSeriesPosts->map(fn (Post $post): array => $this->postSummary($post)))
            ->take(8)
            ->values();

        $announcements = $this->typedContent('announcement');
        $courses = $this->typedContent('course');
        $books = $this->typedContent('book');

        $settings = SiteSetting::query()
            ->active()
            ->whereIn('key', ['about', 'registration'])
            ->get()
            ->keyBy('key');

        $about = $settings->get('about');
        $registration = $settings->get('registration');

        return Inertia::render('Home', [
            'banners' => $banners,
            // Kept for compatibility with the original prototype while the
            // frontend transitions to postsAndSeries.
            'series' => $series,
            'postsAndSeries' => $postsAndSeries,
            'announcements' => $announcements,
            'courses' => $courses,
            'books' => $books,
            'about' => [
                'title' => $about?->title ?: 'درباره ما',
                'body' => $about?->body ?: 'اندیشکده بستری برای مطالعه، تولید اندیشه و انتشار محتوای دقیق و قابل اتکا است.',
            ],
            'registration' => [
                'title' => $registration?->title ?: 'ثبت‌نام در مؤسسه',
                'body' => $registration?->body ?: 'برای آشنایی و ثبت‌نام در مؤسسه، فرم مربوط را تکمیل کنید.',
                'url' => $registration?->url,
            ],
            'contactMethods' => ContactMethod::query()
                ->visible()
                ->get()
                ->map(fn (ContactMethod $method): array => [
                    'label' => $method->label,
                    'type' => $method->type,
                    'value' => $method->value,
                    'icon' => $method->icon,
                ])
                ->values(),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function typedContent(string $type): Collection
    {
        return ContentItem::query()
            ->published()
            ->ofType($type)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(8)
            ->get()
            ->map(fn (ContentItem $item): array => [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt,
                'coverImageUrl' => $this->imageUrl($item->cover_image_path),
                'publishedAt' => $item->published_at?->toIso8601String(),
                'url' => route($item->type.'s.show', ['slug' => $item->slug]),
            ])
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function postSummary(Post $post): array
    {
        return [
            'id' => $post->id,
            'type' => 'post',
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'coverImageUrl' => $this->imageUrl($post->cover_image_path),
            'publishedAt' => $post->published_at?->toIso8601String(),
            'url' => route('posts.show', $post),
            'seriesTitle' => $post->series?->title,
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? '/storage/'.ltrim($path, '/') : null;
    }
}
