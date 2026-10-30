<?php

namespace Tests\Feature;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_signup_saves_client_identity_and_displays_it_on_the_dashboard(): void
    {
        Mail::fake();
        $this->mockSocialUser('google', 'google-client-123', 'google.client@example.test', 'Google Client', 'https://google.test/client.jpg');

        $this->withSession(['social_auth_type' => 'client'])
            ->get(route('social.callback', ['provider' => 'google']))
            ->assertRedirect(route('client.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'google.client@example.test',
            'name' => 'Google Client',
            'google_id' => 'google-client-123',
            'avatar' => 'https://google.test/client.jpg',
            'auth_provider' => 'google',
        ]);

        $this->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Your Account Profile')
            ->assertSee('Google Client')
            ->assertSee('google.client@example.test')
            ->assertSee('https://google.test/client.jpg');
    }

    public function test_google_sign_in_updates_the_existing_client_profile_instead_of_creating_a_second_account(): void
    {
        Mail::fake();
        $client = User::create([
            'name' => 'Old Google Name',
            'email' => 'google.client@example.test',
            'password' => bcrypt('StrongPass123!'),
            'role' => 'client',
        ]);

        $this->mockSocialUser('google', 'google-client-123', 'google.client@example.test', 'Google Client', 'https://google.test/client.jpg');

        $this->withSession(['social_auth_type' => 'client'])
            ->get(route('social.callback', ['provider' => 'google']))
            ->assertRedirect(route('client.dashboard'));

        $this->assertSame(1, User::where('email', 'google.client@example.test')->count());
        $this->assertSame('Google Client', $client->fresh()->name);
        $this->assertSame('google-client-123', $client->fresh()->google_id);
    }

    public function test_google_signup_saves_crew_identity_and_opens_the_crew_profile(): void
    {
        Mail::fake();
        $this->mockSocialUser('google', 'google-crew-123', 'google.crew@example.test', 'Google Crew', 'https://google.test/crew.jpg');

        $this->withSession(['social_auth_type' => 'staff'])
            ->get(route('social.callback', ['provider' => 'google']))
            ->assertRedirect(route('professional.profile'));

        $professional = Professional::where('email', 'google.crew@example.test')->firstOrFail();
        $this->assertSame('Google Crew', $professional->full_name);
        $this->assertSame('google-crew-123', $professional->google_id);
        $this->assertSame('https://google.test/crew.jpg', $professional->profile_photo);
        $this->assertNull($professional->phone);

        $this->get(route('professional.profile'))
            ->assertOk()
            ->assertSee('Google Crew')
            ->assertSee('google.crew@example.test')
            ->assertSee('https://google.test/crew.jpg');
    }

    public function test_google_crew_sign_in_does_not_open_the_client_portal_for_a_shared_email(): void
    {
        Mail::fake();
        User::create([
            'name' => 'Google Client',
            'email' => 'google.crew@example.test',
            'password' => bcrypt('StrongPass123!'),
            'role' => 'client',
        ]);
        $this->mockSocialUser('google', 'google-crew-123', 'google.crew@example.test', 'Google Crew', 'https://google.test/crew.jpg');

        $this->withSession(['social_auth_type' => 'staff'])
            ->get(route('social.callback', ['provider' => 'google']))
            ->assertRedirect(route('professional.profile'));

        $this->assertSame(1, User::where('email', 'google.crew@example.test')->count());
        $this->assertSame('Google Client', User::where('email', 'google.crew@example.test')->value('name'));
        $this->assertFalse(Auth::check());
        $this->get(route('professional.profile'))
            ->assertOk()
            ->assertSee('Google Crew')
            ->assertDontSee('Client Navigation');
    }

    public function test_google_signin_with_missing_credentials_does_not_create_a_demo_account(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $this->from(route('client.register'))
            ->get(route('social.redirect', ['provider' => 'google', 'type' => 'client']))
            ->assertRedirect(route('client.register'))
            ->assertSessionHas('error');

        $this->assertSame(0, User::count());
        $this->assertSame(0, Professional::count());
    }

    public function test_facebook_signup_saves_client_identity_and_logs_in(): void
    {
        Mail::fake();
        $this->mockSocialUser('facebook', 'facebook-client-123', 'facebook.client@example.test', 'Facebook Client', 'https://facebook.test/client.jpg');

        $this->withSession(['social_auth_type' => 'client'])
            ->get(route('social.callback', ['provider' => 'facebook']))
            ->assertRedirect(route('client.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'facebook.client@example.test',
            'name' => 'Facebook Client',
            'facebook_id' => 'facebook-client-123',
            'avatar' => 'https://facebook.test/client.jpg',
            'auth_provider' => 'facebook',
        ]);
        $this->assertAuthenticated();
    }

    public function test_facebook_signin_with_missing_credentials_does_not_create_a_demo_account(): void
    {
        config(['services.facebook.client_id' => null, 'services.facebook.client_secret' => null]);

        $this->from(route('client.register'))
            ->get(route('social.redirect', ['provider' => 'facebook', 'type' => 'client']))
            ->assertRedirect(route('client.register'))
            ->assertSessionHas('error');

        $this->assertSame(0, User::count());
        $this->assertSame(0, Professional::count());
    }

    private function mockSocialUser(string $providerName, string $id, string $email, string $name, string $avatar): void
    {
        $socialUser = Mockery::mock(SocialiteUser::class);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->shouldReceive('getEmail')->andReturn($email);
        $socialUser->shouldReceive('getName')->andReturn($name);
        $socialUser->shouldReceive('getAvatar')->andReturn($avatar);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with($providerName)
            ->andReturn($provider);
    }
}
