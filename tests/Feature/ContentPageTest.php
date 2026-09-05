<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\ContentLink;
use App\Models\Post;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use InvalidArgumentException;
use Tests\TestCase;

class ContentPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_published_series_and_its_posts_are_public(): void
    {
        $series = Series::factory()->create(['slug' => 'public-series']);
        Post::factory()->for($series)->create(['slug' => 'public-post']);

        $this->get('/series/public-series')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Series/Show')->has('series.posts', 1));

        $this->get('/posts/public-post')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Posts/Show')->where('post.series.slug', 'public-series'));
    }

    public function test_draft_content_is_not_public(): void
    {
        $series = Series::factory()->draft()->create(['slug' => 'draft-series']);
        $post = Post::factory()->for($series)->create(['slug' => 'hidden-by-series']);
        $draftPost = Post::factory()->for(Series::factory())->draft()->create(['slug' => 'draft-post']);

        $this->get('/series/'.$series->slug)->assertNotFound();
        $this->get('/posts/'.$post->slug)->assertNotFound();
        $this->get('/posts/'.$draftPost->slug)->assertNotFound();
    }

    public function test_a_standalone_post_is_public_without_a_series(): void
    {
        $post = Post::factory()->create([
            'series_id' => null,
            'slug' => 'standalone-post',
        ]);

        $this->get('/posts/standalone-post')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Posts/Show')
                ->where('post.title', $post->title)
                ->where('post.series', null));
    }

    public function test_course_book_and_announcement_pages_use_their_type_routes(): void
    {
        foreach ([
            ['type' => 'course', 'slug' => 'sample-course'],
            ['type' => 'book', 'slug' => 'sample-book', 'cover_image_path' => 'test/book.jpg'],
            ['type' => 'announcement', 'slug' => 'sample-announcement'],
        ] as $attributes) {
            if ($attributes['type'] === 'course') {
                $attributes['cover_image_path'] = 'test/course.jpg';
            }

            ContentItem::factory()->create($attributes);
        }

        $this->get('/courses/sample-course')->assertInertia(fn (Assert $page) => $page
            ->component('Content/Show')
            ->where('content.type', 'course'));
        $this->get('/books/sample-book')->assertInertia(fn (Assert $page) => $page
            ->component('Content/Show')
            ->where('content.type', 'book'));
        $this->get('/announcements/sample-announcement')->assertInertia(fn (Assert $page) => $page
            ->component('Content/Show')
            ->where('content.type', 'announcement'));
    }

    public function test_content_slugs_are_generated_and_suffixed_within_each_type(): void
    {
        $first = ContentItem::factory()->create([
            'title' => 'A Shared Content Title',
            'slug' => null,
        ]);
        $second = ContentItem::factory()->create([
            'title' => 'A Shared Content Title',
            'slug' => null,
        ]);
        $sameSlugDifferentType = ContentItem::factory()->course()->create([
            'title' => 'A Shared Content Title',
            'slug' => null,
        ]);

        $this->assertSame('a-shared-content-title', $first->slug);
        $this->assertSame('a-shared-content-title-2', $second->slug);
        $this->assertSame('a-shared-content-title', $sameSlugDifferentType->slug);
    }

    public function test_series_slugs_are_generated_and_suffixed(): void
    {
        $first = Series::factory()->create(['title' => 'A Series Title', 'slug' => null]);
        $second = Series::factory()->create(['title' => 'A Series Title', 'slug' => null]);

        $this->assertSame('a-series-title', $first->slug);
        $this->assertSame('a-series-title-2', $second->slug);
    }

    public function test_non_post_content_cannot_be_assigned_to_a_series(): void
    {
        $series = Series::factory()->create();
        $course = ContentItem::factory()->course()->create(['series_id' => $series->id]);

        $this->assertNull($course->fresh()->series_id);
    }

    public function test_unknown_content_types_are_rejected_by_the_model(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ContentItem::factory()->create(['type' => 'unknown']);
    }

    public function test_courses_and_books_require_a_banner_image(): void
    {
        $this->expectException(ValidationException::class);

        ContentItem::factory()->create(['type' => 'course', 'cover_image_path' => null]);
    }

    public function test_content_items_require_a_non_empty_text_block(): void
    {
        $this->expectException(ValidationException::class);

        ContentItem::factory()->create(['content_blocks' => []]);
    }

    public function test_related_links_are_returned_in_admin_defined_order(): void
    {
        $item = ContentItem::factory()->announcement()->create(['slug' => 'ordered-links']);

        ContentLink::query()->create([
            'content_item_id' => $item->id,
            'label' => 'Second',
            'url' => 'https://example.com/second',
            'sort_order' => 20,
        ]);
        ContentLink::query()->create([
            'content_item_id' => $item->id,
            'label' => 'First',
            'url' => 'https://example.com/first',
            'sort_order' => 10,
        ]);

        $this->get('/announcements/ordered-links')
            ->assertInertia(fn (Assert $page) => $page
                ->where('content.links.0.label', 'First')
                ->where('content.links.1.label', 'Second'));
    }

    public function test_future_content_is_not_public_before_its_publish_time(): void
    {
        $item = ContentItem::factory()->announcement()->create([
            'slug' => 'future-announcement',
            'status' => 'published',
            'published_at' => now()->addHour(),
        ]);

        $this->get('/announcements/'.$item->slug)->assertNotFound();
    }
}
