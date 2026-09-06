<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_verified_admin_can_open_content_resources(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/banners')->assertOk();
        $this->actingAs($admin)->get('/admin/series')->assertOk();
        $this->actingAs($admin)->get('/admin/series/create')->assertOk();
        $this->actingAs($admin)->get('/admin/posts')->assertOk();
        $this->actingAs($admin)->get('/admin/posts/create')->assertOk();
        $this->actingAs($admin)->get('/admin/content-items')->assertOk();
        $this->actingAs($admin)->get('/admin/content-items/create')->assertOk();
        $this->actingAs($admin)->get('/admin/site-settings')->assertOk();
        $this->actingAs($admin)->get('/admin/site-settings/create')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-methods')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-methods/create')->assertOk();
        $this->actingAs($admin)->get('/admin/donation-transactions')->assertOk();
    }

    public function test_jalali_date_picker_is_rendered_on_content_forms(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/series/create')
            ->assertOk()
            ->assertSee('filament-jalali', escape: false)
            ->assertSee('fi-fo-date-time-picker-panel', escape: false)
            ->assertSee('YYYY/MM/DD HH:mm', escape: false);
    }

    public function test_a_guest_is_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
