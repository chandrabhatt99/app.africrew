<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StaffingRequest;
use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_create_staffing_request_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/staffing-requests/create');

        $response->assertStatus(200);
        $response->assertSee('Create Crew Request');
    }

    public function test_admin_can_create_new_staffing_request(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/staffing-requests', [
            'full_name' => 'Admin Client',
            'company_name' => 'Apex Events',
            'email' => 'client@apex.com',
            'phone' => '+23480000000',
            'event_name' => 'Lagos Fashion Summit',
            'category' => 'Ushers',
            'staff_count' => 10,
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'location' => 'Eko Hotel Convention Center',
            'requirements' => 'Formal black attire',
            'status' => 'new',
        ]);

        $this->assertDatabaseHas('staffing_requests', [
            'event_name' => 'Lagos Fashion Summit',
            'email' => 'client@apex.com',
            'category' => 'Ushers',
        ]);

        $staffingRequest = StaffingRequest::where('email', 'client@apex.com')->first();
        $response->assertRedirect('/admin/staffing-requests/' . $staffingRequest->id);
    }

    public function test_admin_can_view_create_professional_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/professionals/create');

        $response->assertStatus(200);
        $response->assertSee('Onboard New Crew Member');
    }

    public function test_admin_can_onboard_new_crew_member(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/professionals', [
            'full_name' => 'David Crewman',
            'email' => 'david@crew.com',
            'phone' => '+23481111111',
            'password' => 'password123',
            'city' => 'Lagos',
            'country' => 'Nigeria',
            'category' => 'Ushers',
            'experience_years' => 3,
            'availability' => 'available',
            'status' => 'approved',
            'skills' => 'VIP escorting, bilingual',
        ]);

        $this->assertDatabaseHas('professionals', [
            'email' => 'david@crew.com',
            'category' => 'Ushers',
            'status' => 'approved',
        ]);

        $professional = Professional::where('email', 'david@crew.com')->first();
        $response->assertRedirect('/admin/professionals/' . $professional->id);
    }
}
