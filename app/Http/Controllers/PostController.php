<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function show(string $post): Response
    {
        $post = Post::query()
            ->published()
            ->where('slug', $post)
            ->where(function ($query): void {
                $query
                    ->whereNull('series_id')
                    ->orWhereHas('series', fn ($seriesQuery) => $seriesQuery->published());
            })
            ->with(['series', 'links'])
            ->firstOrFail();

        return Inertia::render('Posts/Show', [
            'post' => [
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'coverImageUrl' => $post->cover_image_path ? '/storage/'.ltrim($post->cover_image_path, '/') : null,
                'publishedAt' => $post->published_at?->toIso8601String(),
                'contentBlocks' => $post->content_blocks ?? [],
                'links' => $post->links->map(fn ($link): array => [
                    'label' => $link->label,
                    'url' => $link->url,
                    'description' => $link->description,
                ])->values(),
                'series' => $post->series ? [
                    'title' => $post->series->title,
                    'slug' => $post->series->slug,
                ] : null,
            ],
        ]);
    }
}
