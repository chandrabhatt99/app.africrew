<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Professional;
use App\Models\Proposal;
use App\Models\StaffingRequest;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_toggle_favorite_crew(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $professional = Professional::create([
            'full_name' => 'Jane Talent',
            'email' => 'jane@talent.com',
            'password' => bcrypt('password'),
            'category' => 'Ushers',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($client)->post("/client/favorites/{$professional->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('client_favorites', [
            'user_id' => $client->id,
            'professional_id' => $professional->id,
        ]);
    }

    public function test_contract_can_be_generated_and_signed(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $request = StaffingRequest::create([
            'user_id' => $client->id,
            'full_name' => 'Client User',
            'email' => 'client@test.com',
            'phone' => '+2348000000',
            'event_name' => 'Gala Night',
            'category' => 'Ushers',
            'staff_count' => 2,
            'location' => 'Lagos',
            'event_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => 'assigned',
        ]);

        $this->get("/requests/{$request->id}/contract");
        $contract = Contract::where('staffing_request_id', $request->id)->first();
        $this->assertNotNull($contract);

        $response = $this->post("/contracts/{$contract->id}/sign", [
            'sign_type' => 'client',
            'signature_name' => 'Client Signature Name',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'client_signature' => 'Client Signature Name',
        ]);
    }

    public function test_crew_can_submit_proposal_and_client_respond(): void
    {
        $user = User::factory()->create(['email' => 'crew@pro.com', 'role' => 'professional']);
        $professional = Professional::create([
            'full_name' => 'Crew Pro',
            'email' => 'crew@pro.com',
            'password' => bcrypt('password'),
            'category' => 'Security',
            'status' => 'approved',
        ]);

        $request = StaffingRequest::create([
            'user_id' => $user->id,
            'full_name' => 'Client User',
            'email' => 'client@test.com',
            'phone' => '+2348000000',
            'event_name' => 'Concert',
            'category' => 'Security',
            'staff_count' => 5,
            'location' => 'Abuja',
            'event_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($user)->post("/requests/{$request->id}/proposals", [
            'custom_quote_amount' => 450.00,
            'travel_fee' => 50.00,
            'notes' => 'Includes logistics',
        ]);

        $response->assertSessionHas('success');
        $proposal = Proposal::where('staffing_request_id', $request->id)->first();
        $this->assertNotNull($proposal);
        $this->assertEquals(450.00, $proposal->custom_quote_amount);

        $client = User::factory()->create(['role' => 'client']);
        $resp = $this->actingAs($client)->post("/proposals/{$proposal->id}/respond", [
            'action' => 'accept',
        ]);

        $resp->assertSessionHas('success');
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'accepted',
        ]);
    }

    public function test_support_ticket_creation_and_admin_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/professional/support', [
            'subject' => 'Payment Delay',
            'category' => 'Payments',
            'priority' => 'high',
            'description' => 'Help with payout clearance.',
        ]);

        $response->assertSessionHas('success');
        $ticket = SupportTicket::where('user_id', $user->id)->first();
        $this->assertNotNull($ticket);

        $admin = User::factory()->create(['is_admin' => true]);
        $resp = $this->actingAs($admin)->post("/admin/tickets/{$ticket->id}", [
            'admin_response' => 'Payout processed.',
            'status' => 'resolved',
        ]);

        $resp->assertSessionHas('success');
        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
        ]);
    }

    public function test_subadmin_creation_and_permission_assignment(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/subadmins', [
            'name' => 'Sub Admin Manager',
            'email' => 'subadmin@test.com',
            'password' => 'password123',
            'permissions' => ['manage_requests', 'manage_payments'],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'subadmin@test.com',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_create_coupon(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('coupons', [
            'code' => 'SAVE10',
            'value' => 10.00,
        ]);
    }

    public function test_csv_report_export(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/reports/requests/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
