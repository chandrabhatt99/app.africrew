<?php

namespace Tests\Feature;

use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewPhoneCountryCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_crew_registration_saves_the_phone_number_in_e164_format(): void
    {
        $this->post(route('crew.store'), [
            'full_name' => 'Test Crew',
            'username' => 'testcrew',
            'category' => 'Ushers',
            'email' => 'crew@example.test',
            'phone_country_iso2' => 'KE',
            'phone' => '+254712345678',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'terms' => '1',
        ])->assertRedirect(route('staff.verify.notice'));

        $this->assertSame('+254712345678', Professional::where('email', 'crew@example.test')->value('phone'));
    }

    public function test_crew_registration_rejects_a_phone_number_that_is_invalid_for_the_selected_country(): void
    {
        $this->post(route('crew.store'), [
            'full_name' => 'Test Crew',
            'username' => 'testcrew',
            'category' => 'Ushers',
            'email' => 'crew@example.test',
            'phone_country_iso2' => 'US',
            'phone' => '+254712345678',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'terms' => '1',
        ])->assertSessionHasErrors('phone');
    }

    public function test_crew_signup_form_shows_country_code_selector(): void
    {
        $this->get(route('crew.create'))
            ->assertOk()
            ->assertSee('crew-phone-input')
            ->assertSee('name="phone_country_iso2"', false)
            ->assertSee('intl-tel-input@25.12.2')
            ->assertSee('separateDialCode: true')
            ->assertSee('w-full');
    }
}
