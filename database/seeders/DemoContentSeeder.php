<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\ContactMethod;
use App\Models\ContentItem;
use App\Models\ContentLink;
use App\Models\Post;
use App\Models\Series;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        Banner::query()->updateOrCreate(
            ['title' => 'روایتی روشن برای فردای ایران'],
            [
                'subtitle' => 'مجموعه‌ای برای خواندن، شنیدن و دیدن روایت‌های دقیق‌تر',
                'image_path' => 'demo/banner-one.svg',
                'link_url' => '#announcements',
                'link_label' => 'ادامه',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Banner::query()->updateOrCreate(
            ['title' => 'از پرسش تا شناخت'],
            [
                'subtitle' => 'اندیشه از یک پرسش خوب آغاز می‌شود',
                'image_path' => 'demo/banner-two.svg',
                'link_url' => '#posts-series',
                'link_label' => 'بیشتر بخوانید',
                'sort_order' => 2,
                'is_active' => true,
            ],
        );

        $seriesData = [
            ['title' => 'رسانه و روایت', 'slug' => 'media-and-narrative', 'description' => 'نگاهی روشن به نقش رسانه، روایت و بازنمایی واقعیت در جهان امروز.', 'cover' => 'demo/cover-red.svg'],
            ['title' => 'جامعه و فرهنگ', 'slug' => 'society-and-culture', 'description' => 'یادداشت‌ها و گفت‌وگوهایی درباره تحولات فرهنگی و مسائل اجتماعی.', 'cover' => 'demo/cover-green.svg'],
            ['title' => 'آینده‌پژوهی', 'slug' => 'future-studies', 'description' => 'پرسش‌هایی درباره فردا و مسیرهایی که تصمیم‌های امروز پیش روی ما می‌گذارند.', 'cover' => 'demo/cover-dark.svg'],
        ];

        foreach ($seriesData as $seriesIndex => $item) {
            $series = Series::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'cover_image_path' => $item['cover'],
                    'status' => 'published',
                    'published_at' => now()->subDays(10 - $seriesIndex),
                    'sort_order' => $seriesIndex + 1,
                ],
            );

            foreach (range(1, 3) as $postIndex) {
                $title = [
                    'media-and-narrative' => ['چرا روایت‌ها مسیر رویدادها را تغییر می‌دهند؟', 'رسانه چگونه مسئله می‌سازد؟', 'از خبر تا تحلیل؛ فاصله‌ای که باید دید'],
                    'society-and-culture' => ['گفت‌وگو؛ حلقه گمشده تغییر اجتماعی', 'فرهنگ عمومی چگونه دگرگون می‌شود؟', 'اعتماد اجتماعی و آینده جامعه'],
                    'future-studies' => ['آینده را چگونه می‌توان خواند؟', 'نشانه‌های کوچک و تغییرات بزرگ', 'سناریو؛ تمرینی برای تصمیم بهتر'],
                ][$item['slug']][$postIndex - 1];

                Post::query()->updateOrCreate(
                    ['slug' => $item['slug'].'-'.$postIndex],
                    [
                        'type' => 'post',
                        'series_id' => $series->id,
                        'title' => $title,
                        'excerpt' => 'این متن نمونه برای نمایش ساختار بصری صفحه است و بعداً با محتوای اصلی اندیشکده جایگزین می‌شود.',
                        'cover_image_path' => $seriesData[($seriesIndex + $postIndex - 1) % 3]['cover'],
                        'content_blocks' => [
                            ['type' => 'rich_text', 'data' => ['body' => '<p>این نوشته نمونه‌ای از محتوای اندیشکده است. مدیر سایت می‌تواند متن، تصویر، صدا، ویدئو و پیوندهای مرتبط را در بخش‌های مشخص صفحه قرار دهد.</p>']],
                            ['type' => 'quote', 'data' => ['text' => 'هر پاسخ دقیق از یک پرسش روشن آغاز می‌شود.', 'citation' => 'یادداشت اندیشکده']],
                        ],
                        'status' => 'published',
                        'published_at' => now()->subDays(8 - $postIndex),
                        'sort_order' => $postIndex,
                    ],
                );
            }
        }

        $typedContent = [
            [
                'type' => 'announcement',
                'slug' => 'registration-announcement',
                'title' => 'آغاز ثبت‌نام برنامه‌های تازه',
                'excerpt' => 'خبرها و اطلاعیه‌های اندیشکده را از این بخش دنبال کنید.',
                'cover_image_path' => null,
                'content_blocks' => [
                    ['type' => 'rich_text', 'data' => ['body' => '<p>این یک اطلاعیه نمونه برای نمایش فهرست اخبار و اطلاعیه‌ها است.</p>']],
                ],
                'sort_order' => 1,
            ],
            [
                'type' => 'course',
                'slug' => 'creative-thinking-course',
                'title' => 'دوره آشنایی با تفکر خلاق',
                'excerpt' => 'معرفی یک دوره نمونه با محتوای چندرسانه‌ای.',
                'cover_image_path' => 'demo/cover-green.svg',
                'content_blocks' => [
                    ['type' => 'rich_text', 'data' => ['body' => '<p>این صفحه صرفاً برای معرفی دوره و جزئیات آن است.</p>']],
                    ['type' => 'video', 'data' => ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => 'ویدئوی معرفی دوره']],
                ],
                'sort_order' => 1,
            ],
            [
                'type' => 'book',
                'slug' => 'sample-andisheh-book',
                'title' => 'کتاب نمونه اندیشه و جامعه',
                'excerpt' => 'معرفی کتاب‌های منتخب اندیشکده.',
                'cover_image_path' => 'demo/cover-dark.svg',
                'content_blocks' => [
                    ['type' => 'rich_text', 'data' => ['body' => '<p>این متن نمونه برای معرفی کتاب و موضوعات آن نوشته شده است.</p>']],
                ],
                'sort_order' => 1,
            ],
        ];

        foreach ($typedContent as $item) {
            $content = ContentItem::query()->updateOrCreate(
                ['type' => $item['type'], 'slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'cover_image_path' => $item['cover_image_path'],
                    'content_blocks' => $item['content_blocks'],
                    'status' => 'published',
                    'sort_order' => $item['sort_order'],
                    'published_at' => now()->subDay(),
                ],
            );

            if ($item['type'] === 'course' || $item['type'] === 'announcement') {
                ContentLink::query()->updateOrCreate(
                    ['content_item_id' => $content->id, 'label' => 'اطلاعات بیشتر'],
                    [
                        'url' => 'https://example.com/',
                        'description' => 'پیوند نمونه برای نمایش لینک‌های مرتبط.',
                        'sort_order' => 1,
                    ],
                );
            }
        }

        SiteSetting::query()->updateOrCreate(
            ['key' => 'about'],
            [
                'title' => 'درباره ما',
                'body' => '<p>اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی بستری برای مطالعه، تولید اندیشه و انتشار محتوای دقیق و قابل اتکا است.</p>',
                'is_active' => true,
            ],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'registration'],
            [
                'title' => 'ثبت‌نام در مؤسسه',
                'body' => '<p>برای آشنایی و ثبت‌نام در مؤسسه، فرم مربوط را تکمیل کنید.</p>',
                'url' => 'https://example.com/registration-form',
                'is_active' => true,
            ],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'donation'],
            [
                'title' => 'حمایت مالی از اندیشکده',
                'body' => '<p>با حمایت شما، مسیر مطالعه، تولید اندیشه و انتشار محتوای دقیق ادامه پیدا می‌کند.</p>',
                'settings' => ['minimum_amount' => 10000, 'quick_amounts' => [50000, 100000, 250000]],
                'is_active' => true,
            ],
        );

        foreach ([
            ['label' => 'تلفن مؤسسه', 'type' => 'phone', 'value' => '021-00000000', 'sort_order' => 1],
            ['label' => 'ایمیل', 'type' => 'email', 'value' => 'info@example.com', 'sort_order' => 2],
            ['label' => 'اینستاگرام', 'type' => 'social', 'value' => 'https://instagram.com/', 'sort_order' => 3],
        ] as $method) {
            ContactMethod::query()->updateOrCreate(
                ['label' => $method['label']],
                [...$method, 'is_visible' => true],
            );
        }
    }
}
