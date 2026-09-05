<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;

class SeriesController extends Controller
{
    public function show(string $series): Response
    {
        $series = Series::query()
            ->published()
            ->where('slug', $series)
            ->with(['posts' => fn (HasMany $query) => $query->published()])
            ->firstOrFail();

        return Inertia::render('Series/Show', [
            'series' => [
                'title' => $series->title,
                'description' => $series->description,
                'coverImageUrl' => $series->cover_image_path ? '/storage/'.ltrim($series->cover_image_path, '/') : null,
                'posts' => $series->posts->map(fn ($post): array => [
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'coverImageUrl' => $post->cover_image_path ? '/storage/'.ltrim($post->cover_image_path, '/') : null,
                    'publishedAt' => $post->published_at?->toIso8601String(),
                ])->values(),
            ],
        ]);
    }
}
