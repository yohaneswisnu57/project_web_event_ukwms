<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\UserManual;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManualTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/panduan-pengguna')
            ->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_authenticated_user_can_read_the_manual(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(UserManual::class)
            ->assertOk()
            ->assertSee('Daftar Isi')
            ->assertSee('Verifikasi Pembayaran')
            ->assertSee('Scan Kehadiran');
    }

    public function test_manual_is_registered_in_panel_navigation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->assertContains(UserManual::class, Filament::getPanel('admin')->getPages());
        $this->assertSame('Panduan Pengguna', UserManual::getNavigationLabel());
        $this->assertSame('/admin/panduan-pengguna', parse_url(UserManual::getUrl(panel: 'admin'), PHP_URL_PATH));
    }

    public function test_manual_has_no_public_routes(): void
    {
        $this->get('/panduan-pengguna')->assertNotFound();
        $this->get('/user-manual')->assertNotFound();
    }
}
