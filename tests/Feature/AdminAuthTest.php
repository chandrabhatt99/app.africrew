<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('AfriCrew');
        $response->assertSee('Admin System Sign In');
    }

    public function test_admin_can_authenticate_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@africrew.com',
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@africrew.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_non_admin_cannot_login_to_admin_portal(): void
    {
        $user = User::factory()->create([
            'email' => 'user@africrew.com',
            'is_admin' => false,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'user@africrew.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@africrew.com',
            'is_admin' => true,
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@africrew.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_authenticated_admin_is_redirected_away_from_login_screen(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/login');

        $response->assertRedirect('/admin');
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }
}
