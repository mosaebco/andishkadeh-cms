<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Inertia\Inertia;
use Inertia\Response;

class DonationController extends Controller
{
    public function __invoke(): Response
    {
        $setting = SiteSetting::query()
            ->active()
            ->where('key', 'donation')
            ->first();

        $minimumAmount = max(1, (int) ($setting?->settings['minimum_amount'] ?? 1));
        $quickAmounts = collect($setting?->settings['quick_amounts'] ?? [])
            ->map(fn ($amount): int => (int) $amount)
            ->filter(fn (int $amount): bool => $amount >= $minimumAmount)
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('Donation/Show', [
            'donation' => [
                'title' => $setting?->title ?: 'حمایت مالی از اندیشکده',
                'body' => $setting?->body ?: 'با حمایت شما، مسیر مطالعه، تولید اندیشه و انتشار محتوای دقیق ادامه پیدا می‌کند.',
                'minimumAmount' => $minimumAmount,
                'quickAmounts' => $quickAmounts,
                'gatewayReady' => false,
            ],
        ]);
    }
}
