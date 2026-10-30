<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordStrengthTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_registration_accepts_passwords_with_all_required_character_types(): void
    {
        $response = $this->post(route('client.register.submit'), [
            'name' => 'Test Client',
            'email' => 'client@example.test',
            'phone' => '1234567890',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'client@example.test']);
    }

    public function test_client_registration_rejects_passwords_missing_a_strength_requirement(): void
    {
        foreach (['Abc1!xy', 'lowercase1!', 'UPPERCASE1!', 'NoNumber!!', 'NoSymbol123'] as $index => $password) {
            $this->post(route('client.register.submit'), [
                'name' => 'Test Client',
                'email' => "client{$index}@example.test",
                'phone' => '1234567890',
                'password' => $password,
                'password_confirmation' => $password,
            ])->assertSessionHasErrors('password');
        }

        $this->assertSame(0, User::count());
    }

    public function test_crew_registration_rejects_passwords_missing_a_strength_requirement(): void
    {
        $this->post(route('crew.store'), [
            'full_name' => 'Test Crew',
            'username' => 'testcrew',
            'category' => 'Ushers',
            'email' => 'crew@example.test',
            'phone' => '1234567890',
            'password' => 'NoSymbol123',
            'password_confirmation' => 'NoSymbol123',
            'terms' => '1',
        ])->assertSessionHasErrors('password');
    }

    public function test_mobile_registration_rejects_passwords_missing_a_strength_requirement(): void
    {
        $this->postJson('/api/v1/client/register', [
            'name' => 'Test Client',
            'email' => 'mobile@example.test',
            'password' => 'NoSymbol123',
            'password_confirmation' => 'NoSymbol123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->postJson('/api/v1/crew/register', [
            'full_name' => 'Test Crew',
            'username' => 'mobilecrew',
            'email' => 'mobilecrew@example.test',
            'phone' => '1234567890',
            'category' => 'Ushers',
            'password' => 'NoSymbol123',
            'password_confirmation' => 'NoSymbol123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    public function test_registration_forms_show_live_password_requirements(): void
    {
        foreach ([
            route('client.register'),
            route('crew.create'),
            route('hire.create'),
            route('password.reset', ['token' => 'test-token', 'email' => 'client@example.test']),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Uppercase letter')
                ->assertSee('Lowercase letter')
                ->assertSee('Number')
                ->assertSee('Special character');
        }
    }

    public function test_password_and_confirmation_fields_can_be_shown_or_hidden(): void
    {
        foreach ([
            route('client.register') => ['password', 'password_confirmation'],
            route('crew.create') => ['password', 'password_confirmation'],
            route('hire.create') => ['password', 'password_confirmation'],
            route('password.reset', ['token' => 'test-token', 'email' => 'client@example.test']) => ['password', 'password_confirmation'],
        ] as $url => $inputIds) {
            $response = $this->get($url)->assertOk();

            foreach ($inputIds as $inputId) {
                $response->assertSee('data-password-visibility="' . $inputId . '"', false);
            }
        }
    }
}
