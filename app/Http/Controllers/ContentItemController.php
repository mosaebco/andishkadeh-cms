<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use Inertia\Inertia;
use Inertia\Response;

class ContentItemController extends Controller
{
    public function course(string $slug): Response
    {
        return $this->show('course', $slug);
    }

    public function book(string $slug): Response
    {
        return $this->show('book', $slug);
    }

    public function announcement(string $slug): Response
    {
        return $this->show('announcement', $slug);
    }

    public function show(string $type, string $slug): Response
    {
        abort_unless(in_array($type, ['course', 'book', 'announcement'], true), 404);

        $item = ContentItem::query()
            ->published()
            ->ofType($type)
            ->where('slug', $slug)
            ->with('links')
            ->firstOrFail();

        return Inertia::render('Content/Show', [
            'content' => [
                'type' => $item->type,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt,
                'coverImageUrl' => $item->cover_image_path ? '/storage/'.ltrim($item->cover_image_path, '/') : null,
                'publishedAt' => $item->published_at?->toIso8601String(),
                'contentBlocks' => $item->content_blocks ?? [],
                'links' => $item->links->map(fn ($link): array => [
                    'label' => $link->label,
                    'url' => $link->url,
                    'description' => $link->description,
                ])->values(),
            ],
        ]);
    }
}
