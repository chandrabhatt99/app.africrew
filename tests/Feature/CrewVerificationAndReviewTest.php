<?php

namespace Tests\Feature;

use App\Mail\ApplicationReceivedMail;
use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CrewVerificationAndReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_email_verifies_the_account_and_redirects_to_the_profile(): void
    {
        Mail::fake();

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

        $professional = Professional::where('email', 'crew@example.test')->firstOrFail();

        Mail::assertSent(ApplicationReceivedMail::class, function (ApplicationReceivedMail $mail) use ($professional): bool {
            return str_contains($mail->verificationUrl, route('staff.verify.email', ['token' => $professional->verification_token]))
                && str_contains($mail->render(), 'Verify email and continue');
        });

        $this->get(route('staff.verify.email', ['token' => $professional->verification_token]))
            ->assertRedirect(route('professional.profile'))
            ->assertSessionHas('professional_id', $professional->id);

        $this->assertNotNull($professional->fresh()->email_verified_at);
        $this->get(route('professional.profile'))->assertOk();
    }

    public function test_profile_review_is_rejected_until_completion_reaches_sixty_percent(): void
    {
        $professional = $this->createProfessional();

        $this->withSession(['professional_id' => $professional->id])
            ->post(route('professional.submit-review-request'))
            ->assertSessionHasErrors('profile');

        $this->assertNull($professional->fresh()->submitted_for_review_at);
        $this->assertSame('pending', $professional->fresh()->status);
    }

    public function test_profile_at_least_sixty_percent_complete_can_be_submitted_for_review(): void
    {
        Mail::fake();

        $professional = $this->createProfessional([
            'about' => 'Experienced event crew member',
            'skills' => 'Guest support',
        ]);

        $this->assertGreaterThanOrEqual(60, $professional->completion_percentage);

        $this->withSession(['professional_id' => $professional->id])
            ->post(route('professional.profile.update'), ['action' => 'submit_for_review'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('pending', $professional->fresh()->status);
        $this->assertNotNull($professional->fresh()->submitted_for_review_at);
    }

    public function test_verified_pending_crew_can_log_in_and_continue_their_profile(): void
    {
        $professional = $this->createProfessional();

        $this->post(route('staff.login.submit'), [
            'email' => $professional->email,
            'password' => 'StrongPass123!',
        ])->assertRedirect(route('professional.profile'))
            ->assertSessionHas('professional_id', $professional->id);
    }

    public function test_edits_to_an_approved_profile_require_admin_review_again(): void
    {
        Mail::fake();

        $professional = $this->createProfessional([
            'about' => 'Experienced event crew member',
            'skills' => 'Guest support',
            'status' => 'approved',
            'is_onboarded' => true,
        ]);

        $this->withSession(['professional_id' => $professional->id])
            ->post(route('professional.profile.update'), [
                'about' => 'Experienced event crew member with updated details',
                'action' => 'save',
            ])->assertRedirect();

        $this->assertSame('pending', $professional->fresh()->status);
        $this->assertNotNull($professional->fresh()->submitted_for_review_at);
    }

    private function createProfessional(array $overrides = []): Professional
    {
        return Professional::create(array_merge([
            'full_name' => 'Test Crew',
            'username' => 'crew-'.uniqid(),
            'email' => uniqid('crew-').'@example.test',
            'phone' => '+254712345678',
            'password' => Hash::make('StrongPass123!'),
            'category' => 'Ushers',
            'country' => 'Kenya',
            'city' => 'Nairobi',
            'email_verified_at' => now(),
            'status' => 'pending',
        ], $overrides));
    }
}
