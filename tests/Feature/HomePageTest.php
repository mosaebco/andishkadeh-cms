<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\ContentItem;
use App\Models\Post;
use App\Models\Series;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_only_contains_current_banners_and_published_content(): void
    {
        Banner::factory()->create(['title' => 'Visible banner']);
        Banner::factory()->inactive()->create(['title' => 'Hidden banner']);

        $series = Series::factory()->create(['title' => 'Visible series']);
        Post::factory()->for($series)->create(['title' => 'Visible post']);
        Post::factory()->for($series)->draft()->create(['title' => 'Draft post']);
        Series::factory()->draft()->create(['title' => 'Draft series']);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('banners', 1)
                ->where('banners.0.title', 'Visible banner')
                ->has('series', 1)
                ->where('series.0.title', 'Visible series')
                ->has('series.0.posts', 1)
                ->where('series.0.posts.0.title', 'Visible post'));
    }

    public function test_future_published_content_does_not_appear_early(): void
    {
        $series = Series::factory()->create([
            'status' => 'published',
            'published_at' => now()->addHour(),
        ]);

        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page->has('series', 0));
    }

    public function test_homepage_exposes_the_approved_content_groups(): void
    {
        ContentItem::factory()->announcement()->create(['slug' => 'announcement-home']);
        ContentItem::factory()->course()->create(['slug' => 'course-home']);
        ContentItem::factory()->book()->create(['slug' => 'book-home']);
        Post::factory()->create(['series_id' => null, 'slug' => 'standalone-home']);

        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page
                ->has('announcements', 1)
                ->has('courses', 1)
                ->has('books', 1)
                ->has('postsAndSeries', 1)
                ->where('announcements.0.title', fn ($title): bool => is_string($title))
                ->where('courses.0.type', 'course')
                ->where('books.0.type', 'book'));
    }

    public function test_content_sections_have_full_listing_pages(): void
    {
        ContentItem::factory()->announcement()->create(['title' => 'First announcement', 'slug' => 'first-announcement']);
        ContentItem::factory()->announcement()->create(['title' => 'Second announcement', 'slug' => 'second-announcement']);
        ContentItem::factory()->course()->create(['slug' => 'course-listing']);
        ContentItem::factory()->book()->create(['slug' => 'book-listing']);
        Post::factory()->create(['series_id' => null, 'slug' => 'post-listing']);

        $this->get('/announcements')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing')
                ->where('kind', 'announcements')
                ->has('items', 2));

        $this->get('/courses')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing')
                ->where('kind', 'cards')
                ->has('items', 1));

        $this->get('/books')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing')
                ->has('items', 1));

        $this->get('/posts')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing')
                ->has('items', 1)
                ->where('items.0.slug', 'post-listing'));
    }

    public function test_donation_page_is_public_but_gateway_is_pending(): void
    {
        $this->get('/donation')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Donation/Show')
                ->where('donation.gatewayReady', false));
    }

    public function test_donation_quick_amounts_respect_the_configured_minimum(): void
    {
        SiteSetting::query()->create([
            'key' => 'donation',
            'settings' => [
                'minimum_amount' => 100000,
                'quick_amounts' => [50000, 100000, 250000, 250000],
            ],
        ]);

        $this->get('/donation')
            ->assertInertia(fn (Assert $page) => $page
                ->where('donation.minimumAmount', 100000)
                ->where('donation.quickAmounts', [100000, 250000]));
    }
}
