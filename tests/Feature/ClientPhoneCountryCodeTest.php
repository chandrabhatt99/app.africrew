<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientPhoneCountryCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_registration_saves_the_phone_number_in_e164_format(): void
    {
        Mail::fake();

        $this->post(route('client.register.submit'), [
            'name' => 'Test Client',
            'email' => 'client@example.test',
            'phone_country_iso2' => 'KE',
            'phone' => '+254712345678',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ])->assertRedirect(route('client.dashboard'));

        $this->assertSame('+254712345678', User::where('email', 'client@example.test')->value('phone'));
    }

    public function test_client_registration_rejects_a_phone_number_that_is_invalid_for_the_selected_country(): void
    {
        $this->from(route('client.register'))->post(route('client.register.submit'), [
            'name' => 'Test Client',
            'email' => 'client@example.test',
            'phone_country_iso2' => 'US',
            'phone' => '+254712345678',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ])->assertSessionHasErrors('phone');
    }

    public function test_client_registration_form_shows_a_phone_country_code_selector(): void
    {
        $this->get(route('client.register'))
            ->assertOk()
            ->assertSee('client-phone-input')
            ->assertSee('name="phone_country_iso2"', false)
            ->assertSee('intl-tel-input@25.12.2')
            ->assertSee('separateDialCode: true');
    }
}
