<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Message;
use App\Models\Professional;
use App\Models\Review;
use App\Models\StaffingAssignment;
use App\Models\StaffingRequest;
use App\Models\SuggestedSkill;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Mail\RefereeConsentRequestMail;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CrewMobileController extends Controller
{
    /**
     * Helper to retrieve authenticated crew member from Bearer token.
     */
    protected function getCrew(Request $request): ?Professional
    {
        $token = $request->bearerToken() ?: $request->header('X-API-TOKEN') ?: $request->input('api_token');
        if (!$token) {
            return null;
        }

        $hashedToken = hash('sha256', $token);
        return Professional::where('api_token', $hashedToken)->first();
    }

    /**
     * Forgot Password for Crew Mobile.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $professional = Professional::where('email', strtolower(trim($request->email)))->first();

        if (!$professional) {
            return response()->json([
                'status' => false,
                'message' => 'No crew account found with this email address.',
            ], 404);
        }

        $resetToken = Str::random(60);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $professional->email],
            ['token' => Hash::make($resetToken), 'created_at' => now()]
        );

        return response()->json([
            'status' => true,
            'message' => 'Password reset instructions have been sent to your email.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Reset Password for Crew Mobile.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', strtolower(trim($request->email)))
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired password reset token.',
            ], 400);
        }

        $professional = Professional::where('email', strtolower(trim($request->email)))->first();
        if ($professional) {
            $professional->password = Hash::make($request->password);
            $professional->save();
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $professional->email)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Your password has been reset successfully. You can now log in.',
        ]);
    }

    /**
     * Check Profile Pipeline Status & Completion.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $crew->id,
                'full_name' => $crew->full_name,
                'username' => $crew->username,
                'status' => $crew->status,
                'pipeline_status' => $crew->pipeline_status,
                'completion_percentage' => $crew->completion_percentage,
                'next_requirement' => $crew->next_incomplete_requirement,
                'is_onboarded' => (bool)$crew->is_onboarded,
                'email_verified' => !is_null($crew->email_verified_at),
                'interview' => [
                    'status' => $crew->interview_status,
                    'date' => $crew->interview_date?->format('Y-m-d'),
                    'time' => $crew->interview_time,
                    'link' => $crew->interview_link,
                    'notes' => $crew->interview_notes,
                ],
                'wallet' => [
                    'available_balance' => (float)$crew->wallet_balance,
                    'pending_escrow' => (float)$crew->wallet_pending,
                    'total_withdrawn' => (float)$crew->wallet_withdrawn,
                    'currency' => $crew->currency ?: 'KES',
                ],
            ],
        ]);
    }

    /**
     * Update Crew Profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => ['nullable', 'string', 'max:150'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date'],
            'category' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'other_cities' => ['nullable', 'array'],
            'preferred_locations' => ['nullable'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'half_day_rate' => ['nullable', 'numeric', 'min:0'],
            'full_day_rate' => ['nullable', 'numeric', 'min:0'],
            'one_day_rate' => ['nullable', 'numeric', 'min:0'],
            'two_day_rate' => ['nullable', 'numeric', 'min:0'],
            'rehearsal_rate' => ['nullable', 'numeric', 'min:0'],
            'working_hours' => ['nullable', 'integer', 'min:1', 'max:24'],
            'rehearsal_option' => ['nullable', 'string', 'in:yes,included,not_applicable'],
            'has_multi_day_discount' => ['nullable', 'boolean'],
            'multi_day_discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'multi_day_min_days' => ['nullable', 'integer', 'min:2', 'max:30'],
            'rate_type' => ['nullable', 'string', 'in:fixed,negotiable'],
            'currency' => ['nullable', 'string', 'in:USD,KES,UGX,TZS,RWF'],
            'booking_policy' => ['nullable', 'string'],
            'availability' => ['nullable', 'string'],
            'availability_dates' => ['nullable', 'array'],
            'blocked_dates' => ['nullable', 'array'],
            'assignment_types' => ['nullable', 'array'],
            'skills' => ['nullable'],
            'featured_skills' => ['nullable', 'array'],
            'about' => ['nullable', 'string', 'max:3000'],
            'my_update' => ['nullable', 'string', 'max:200'],
            'lets_connect' => ['nullable', 'string', 'max:200'],
            'experience_summary' => ['nullable', 'string', 'max:3000'],
            'highlight_quote' => ['nullable', 'string', 'max:1000'],
            'social_links' => ['nullable', 'array'],
            'show_education_on_cv' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (isset($data['full_name']) && !empty($data['full_name'])) {
            $nameClean = ucwords(mb_strtolower(trim($data['full_name'])));
            $parts = explode(' ', $nameClean, 2);
            $data['full_name'] = $nameClean;
            $data['first_name'] = $parts[0] ?? '';
            $data['last_name'] = $parts[1] ?? '';
        }

        if (isset($data['preferred_locations']) && is_string($data['preferred_locations'])) {
            $data['preferred_locations'] = array_map('trim', explode(',', $data['preferred_locations']));
        }

        if (isset($data['skills'])) {
            $skillsArr = is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', (string)$data['skills']));
            $skillsArr = array_slice(array_values(array_filter($skillsArr)), 0, 10);
            $data['skills'] = implode(', ', $skillsArr);
        }

        if (isset($data['featured_skills']) && is_array($data['featured_skills'])) {
            $data['featured_skills'] = array_slice(array_values(array_unique(array_filter($data['featured_skills']))), 0, 3);
        }

        $crew->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'crew' => $crew->fresh(),
        ]);
    }

    /**
     * Manage Detailed Expertise Areas (with icon, name, description, and image).
     */
    public function updateExpertises(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'expertises' => ['required', 'array'],
            'expertises.*.name' => ['required', 'string', 'max:100'],
            'expertises.*.description' => ['nullable', 'string', 'max:1000'],
            'expertises.*.icon' => ['nullable', 'string', 'max:50'],
            'expertises.*.image' => ['nullable', 'string'],
            'expertise_images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $expertises = [];
        foreach ($request->input('expertises', []) as $idx => $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (is_array($item) && !empty(trim($item['name'] ?? ''))) {
                $imgUrl = $item['image'] ?? null;
                if ($request->hasFile("expertise_images.{$idx}")) {
                    $imgUrl = $request->file("expertise_images.{$idx}")->store('professionals/expertises', 'public');
                }

                $expertises[] = [
                    'name' => trim($item['name']),
                    'description' => trim($item['description'] ?? ''),
                    'icon' => trim($item['icon'] ?? 'ri-star-line'),
                    'image' => $imgUrl,
                ];
            }
        }

        $crew->expertises = $expertises;
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Detailed Expertise Areas saved successfully.',
            'expertises' => $crew->expertises,
        ]);
    }

    /**
     * Upload Portfolio / Gallery Photo.
     */
    public function uploadGalleryPhoto(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $path = $request->file('photo')->store('professionals/gallery', 'public');
        $gallery = is_array($crew->gallery_photos) ? $crew->gallery_photos : [];
        $gallery[] = $path;
        $crew->gallery_photos = $gallery;
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Portfolio photo added successfully.',
            'photo_url' => get_storage_url($path),
            'gallery_photos' => array_map(fn($p) => get_storage_url($p), $crew->gallery_photos),
        ]);
    }

    /**
     * Delete Portfolio / Gallery Photo.
     */
    public function deleteGalleryPhoto(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $photoPath = $request->input('photo_path');
        if (!$photoPath) {
            return response()->json(['status' => false, 'message' => 'Photo path is required.'], 422);
        }

        $gallery = is_array($crew->gallery_photos) ? $crew->gallery_photos : [];
        $updated = [];
        foreach ($gallery as $item) {
            if ($item === $photoPath || get_storage_url($item) === $photoPath) {
                if (Storage::disk('public')->exists($item)) {
                    Storage::disk('public')->delete($item);
                }
            } else {
                $updated[] = $item;
            }
        }

        $crew->gallery_photos = array_values($updated);
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Photo removed from portfolio.',
            'gallery_photos' => array_map(fn($p) => get_storage_url($p), $crew->gallery_photos),
        ]);
    }

    /**
     * Update Education Records & Certificates.
     */
    public function updateEducation(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'education' => ['required', 'array'],
            'education.*.degree' => ['required', 'string', 'max:150'],
            'education.*.institution' => ['required', 'string', 'max:150'],
            'education.*.year' => ['nullable', 'string', 'max:30'],
            'education.*.document' => ['nullable', 'string'],
            'education_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'show_education_on_cv' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $records = [];
        foreach ($request->input('education', []) as $idx => $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (is_array($item) && !empty(trim($item['degree'] ?? ''))) {
                $docPath = $item['document'] ?? null;
                if ($request->hasFile("education_documents.{$idx}")) {
                    $docPath = $request->file("education_documents.{$idx}")->store('professionals/education', 'public');
                }
                $records[] = [
                    'degree' => trim($item['degree']),
                    'institution' => trim($item['institution'] ?? ''),
                    'year' => trim($item['year'] ?? ''),
                    'document' => $docPath,
                ];
            }
        }

        $crew->education = $records;
        if ($request->has('show_education_on_cv')) {
            $crew->show_education_on_cv = $request->boolean('show_education_on_cv');
        }
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Education records updated successfully.',
            'education' => $crew->education,
            'show_education_on_cv' => (bool)$crew->show_education_on_cv,
        ]);
    }

    /**
     * Update Experience Records & Verification Documents.
     */
    public function updateExperience(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'experience_records' => ['required', 'array'],
            'experience_records.*.role' => ['required', 'string', 'max:150'],
            'experience_records.*.company' => ['required', 'string', 'max:150'],
            'experience_records.*.duration' => ['nullable', 'string', 'max:50'],
            'experience_records.*.description' => ['nullable', 'string', 'max:2000'],
            'experience_records.*.document' => ['nullable', 'string'],
            'experience_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $records = [];
        foreach ($request->input('experience_records', []) as $idx => $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (is_array($item) && !empty(trim($item['role'] ?? ''))) {
                $docPath = $item['document'] ?? null;
                if ($request->hasFile("experience_documents.{$idx}")) {
                    $docPath = $request->file("experience_documents.{$idx}")->store('professionals/experience', 'public');
                }
                $records[] = [
                    'role' => trim($item['role']),
                    'company' => trim($item['company'] ?? ''),
                    'duration' => trim($item['duration'] ?? ''),
                    'description' => trim($item['description'] ?? ''),
                    'document' => $docPath,
                ];
            }
        }

        $crew->experience_records = $records;
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Work experience records updated successfully.',
            'experience_records' => $crew->experience_records,
        ]);
    }

    /**
     * Update Referees with Verification Email Dispatch.
     */
    public function updateReferees(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'referees' => ['required', 'array'],
            'referees.*.name' => ['required', 'string', 'max:150'],
            'referees.*.phone' => ['required', 'string', 'max:30'],
            'referees.*.email' => ['nullable', 'email', 'max:150'],
            'referees.*.organization' => ['nullable', 'string', 'max:150'],
            'referees.*.relationship' => ['nullable', 'string', 'max:100'],
            'referees.*.period' => ['nullable', 'string', 'max:100'],
            'referees.*.crew_role' => ['nullable', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $existingReferees = is_array($crew->referees) ? $crew->referees : [];
        $cleanedRef = [];

        foreach ($request->input('referees', []) as $idx => $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (is_array($item) && !empty(trim($item['name'] ?? ''))) {
                $email = trim($item['email'] ?? '');

                // Match existing referee record to preserve token & verification state
                $matchedExisting = null;
                if (!empty($email)) {
                    foreach ($existingReferees as $ex) {
                        $exItem = is_string($ex) ? json_decode($ex, true) : $ex;
                        if (is_array($exItem) && !empty($exItem['email']) && strtolower($exItem['email']) === strtolower($email)) {
                            $matchedExisting = $exItem;
                            break;
                        }
                    }
                }
                if (!$matchedExisting && isset($existingReferees[$idx])) {
                    $matchedExisting = is_string($existingReferees[$idx]) ? json_decode($existingReferees[$idx], true) : $existingReferees[$idx];
                }

                $consentToken = $matchedExisting['consent_token'] ?? Str::random(40);
                $status = $matchedExisting['status'] ?? (!empty($email) ? 'requested' : 'requested');

                $refItem = [
                    'name' => trim($item['name'] ?? ''),
                    'organization' => trim($item['organization'] ?? ''),
                    'relationship' => trim($item['relationship'] ?? ''),
                    'period' => trim($item['period'] ?? ($matchedExisting['period'] ?? '')),
                    'crew_role' => trim($item['crew_role'] ?? ($matchedExisting['crew_role'] ?? '')),
                    'email' => $email,
                    'phone' => trim($item['phone'] ?? ''),
                    'status' => $status,
                    'consent_token' => $consentToken,
                    'consent_sent_at' => $matchedExisting['consent_sent_at'] ?? null,
                    'verified_at' => $matchedExisting['verified_at'] ?? null,
                    'unverified_at' => $matchedExisting['unverified_at'] ?? null,
                    'unverified_reason' => $matchedExisting['unverified_reason'] ?? null,
                    'unverified_notes' => $matchedExisting['unverified_notes'] ?? null,
                    'show_on_cv' => !empty($matchedExisting['show_on_cv']),
                    'rating' => $matchedExisting['rating'] ?? null,
                    'comments' => $matchedExisting['comments'] ?? null,
                    'suggested_correction' => $matchedExisting['suggested_correction'] ?? null,
                ];

                // Auto-send verification email if new or unsent
                $isNewEmail = !empty($email) && (empty($matchedExisting['email']) || strtolower($matchedExisting['email']) !== strtolower($email));
                $neverSent = !empty($email) && empty($matchedExisting['consent_sent_at']);

                if (($isNewEmail || $neverSent) && $status !== 'verified') {
                    $reviewUrl = route('referee.review', ['token' => $consentToken]);
                    try {
                        Mail::to($email)->send(new RefereeConsentRequestMail(
                            refereeName: $refItem['name'],
                            refereeOrg: $refItem['organization'] ?: 'Professional Reference',
                            crewName: $crew->full_name,
                            crewCategory: $refItem['crew_role'] ?: ($crew->category ?: 'Crew Member'),
                            reviewUrl: $reviewUrl,
                            period: $refItem['period'] ?: 'recent projects',
                            companyOrProject: $refItem['organization'] ?: 'Event Project'
                        ));
                        $refItem['status'] = 'requested';
                        $refItem['consent_sent_at'] = now()->toDateTimeString();
                        Log::info("Referee verification email auto-sent to {$email} for {$crew->full_name} via Mobile API");
                    } catch (\Throwable $e) {
                        Log::error("Failed auto-sending referee email to {$email}: " . $e->getMessage());
                    }
                }

                $cleanedRef[] = $refItem;
            }
        }

        $crew->referees = array_values($cleanedRef);
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Referees updated successfully. Verification emails sent to provided referees.',
            'referees' => $crew->referees,
        ]);
    }

    /**
     * Resend Verification Request to Referee via Mobile API.
     */
    public function resendRefereeVerification(Request $request, int $index): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $referees = is_array($crew->referees) ? $crew->referees : [];
        if (!isset($referees[$index])) {
            return response()->json(['status' => false, 'message' => 'Referee not found.'], 404);
        }

        $ref = is_string($referees[$index]) ? json_decode($referees[$index], true) : $referees[$index];
        $email = trim($ref['email'] ?? '');

        if (empty($email)) {
            return response()->json(['status' => false, 'message' => 'Referee does not have an email address specified.'], 422);
        }

        if (empty($ref['consent_token'])) {
            $ref['consent_token'] = Str::random(40);
        }

        $reviewUrl = route('referee.review', ['token' => $ref['consent_token']]);
        try {
            Mail::to($email)->send(new RefereeConsentRequestMail(
                refereeName: $ref['name'] ?? 'Referee',
                refereeOrg: $ref['organization'] ?? 'Professional Reference',
                crewName: $crew->full_name,
                crewCategory: $ref['crew_role'] ?? ($crew->category ?: 'Crew Member'),
                reviewUrl: $reviewUrl,
                period: $ref['period'] ?? 'recent projects',
                companyOrProject: $ref['organization'] ?? 'Event Project'
            ));

            $ref['status'] = 'requested';
            $ref['consent_sent_at'] = now()->toDateTimeString();
            $referees[$index] = $ref;
            $crew->referees = $referees;
            $crew->save();

            return response()->json([
                'status' => true,
                'message' => "Verification email successfully resent to {$email}.",
                'referee' => $ref,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload Verification Documents (ID, Resume, Profile Photo, Cover Photo).
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'government_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $updated = [];
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('professionals/avatars', 'public');
            $crew->profile_photo = $path;
            $updated['profile_photo'] = get_storage_url($path);
        }
        if ($request->hasFile('cover_photo')) {
            $path = $request->file('cover_photo')->store('professionals/covers', 'public');
            $crew->cover_photo = $path;
            $updated['cover_photo'] = get_storage_url($path);
        }
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('professionals/resumes', 'public');
            $crew->resume = $path;
            $updated['resume'] = get_storage_url($path);
        }
        if ($request->hasFile('government_id')) {
            $path = $request->file('government_id')->store('professionals/documents', 'public');
            $crew->government_id = $path;
            $updated['government_id'] = get_storage_url($path);
        }

        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Documents uploaded successfully.',
            'files' => $updated,
        ]);
    }

    /**
     * Suggest New Skill for Admin Review.
     */
    public function suggestSkill(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'skill_name' => ['required', 'string', 'max:100'],
            'category_name' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $suggested = SuggestedSkill::create([
            'professional_id' => $crew->id,
            'name' => trim($request->skill_name),
            'category' => trim($request->category_name ?: $crew->category ?: 'General'),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Skill suggestion submitted for admin review.',
            'suggested_skill' => $suggested,
        ], 201);
    }

    /**
     * Get Crew Shift Assignments / Gigs.
     */
    public function getShifts(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = StaffingAssignment::with(['staffingRequest.user', 'staffingRequest.proposals'])
            ->where('professional_id', $crew->id);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $assignments = $query->latest()->get();

        $shifts = $assignments->map(function ($assignment) use ($crew) {
            $req = $assignment->staffingRequest;
            $rate = $req->budget ?? $crew->one_day_rate ?? 5000;
            return [
                'assignment_id' => $assignment->id,
                'status' => $assignment->status, // assigned, accepted, declined, completed, checked_in
                'notes' => $assignment->notes,
                'assigned_at' => $assignment->created_at->toIso8601String(),
                'event' => $req ? [
                    'id' => $req->id,
                    'event_name' => $req->event_name,
                    'event_type' => $req->event_type,
                    'category' => $req->category,
                    'location' => $req->location,
                    'event_date' => $req->event_date?->format('Y-m-d'),
                    'dates' => $req->dates,
                    'start_time' => $req->start_time,
                    'end_time' => $req->end_time,
                    'shift_duration' => $req->shift_duration,
                    'payout_amount' => (float)$rate,
                    'currency' => $req->currency ?: 'KES',
                    'client_name' => $req->company_name ?: $req->full_name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Get Single Shift Details.
     */
    public function getShiftDetail(Request $request, $id): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $assignment = StaffingAssignment::with(['staffingRequest.user', 'staffingRequest.proposals'])
            ->where('professional_id', $crew->id)
            ->where('id', $id)
            ->first();

        if (!$assignment) {
            return response()->json(['status' => false, 'message' => 'Shift not found.'], 404);
        }

        $req = $assignment->staffingRequest;
        $contract = Contract::where('staffing_request_id', $req->id)->where('professional_id', $crew->id)->first();

        return response()->json([
            'status' => true,
            'shift' => [
                'assignment_id' => $assignment->id,
                'status' => $assignment->status,
                'notes' => $assignment->notes,
                'event' => [
                    'id' => $req->id,
                    'event_name' => $req->event_name,
                    'event_type' => $req->event_type,
                    'category' => $req->category,
                    'location' => $req->location,
                    'event_date' => $req->event_date?->format('Y-m-d'),
                    'dates' => $req->dates,
                    'start_time' => $req->start_time,
                    'end_time' => $req->end_time,
                    'shift_duration' => $req->shift_duration,
                    'requirements' => $req->requirements,
                    'payout_amount' => (float)($req->budget ?? $crew->one_day_rate ?? 5000),
                    'currency' => $req->currency ?: 'KES',
                    'client' => [
                        'name' => $req->full_name,
                        'company' => $req->company_name,
                        'phone' => $req->phone,
                    ],
                ],
                'contract' => $contract ? [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'status' => $contract->status,
                    'crew_signed_at' => $contract->crew_signed_at?->toIso8601String(),
                    'terms' => $contract->terms,
                ] : null,
            ],
        ]);
    }

    /**
     * Respond to Shift Offer (Accept or Decline).
     */
    public function respondShift(Request $request, $id): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'response' => ['required', 'in:accept,decline'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $assignment = StaffingAssignment::with('staffingRequest')
            ->where('professional_id', $crew->id)
            ->where('id', $id)
            ->first();

        if (!$assignment) {
            return response()->json(['status' => false, 'message' => 'Shift assignment not found.'], 404);
        }

        $status = $request->response === 'accept' ? 'accepted' : 'declined';
        $assignment->status = $status;
        if ($request->has('notes')) {
            $assignment->notes = $request->notes;
        }
        $assignment->save();

        if ($assignment->staffingRequest) {
            if ($status === 'accepted') {
                $assignment->staffingRequest->update(['status' => 'confirmed']);
                EmailNotificationService::sendCrewConfirmedToClient(
                    $assignment->staffingRequest,
                    $crew->full_name
                );
            } elseif ($status === 'declined') {
                $hasOther = $assignment->staffingRequest->assignments()
                    ->where('id', '!=', $assignment->id)
                    ->whereIn('status', ['assigned', 'accepted'])
                    ->exists();
                if (!$hasOther) {
                    $assignment->staffingRequest->update(['status' => 'new']);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => $status === 'accepted' ? 'Shift accepted successfully!' : 'Shift declined.',
            'assignment' => $assignment,
        ]);
    }

    /**
     * Shift Check-in (Clock In on Event Day).
     */
    public function checkInShift(Request $request, $id): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $assignment = StaffingAssignment::where('professional_id', $crew->id)->where('id', $id)->first();
        if (!$assignment) {
            return response()->json(['status' => false, 'message' => 'Shift assignment not found.'], 404);
        }

        $assignment->status = 'checked_in';
        $assignment->notes = ($assignment->notes ? $assignment->notes . ' | ' : '') . 'Checked in at ' . now()->toDateTimeString();
        $assignment->save();

        return response()->json([
            'status' => true,
            'message' => 'Checked in successfully! Have a great shift.',
            'assignment' => $assignment,
        ]);
    }

    /**
     * Complete Shift / Mark as Fulfilled.
     */
    public function completeShift(Request $request, $id): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $assignment = StaffingAssignment::with('staffingRequest')->where('professional_id', $crew->id)->where('id', $id)->first();
        if (!$assignment) {
            return response()->json(['status' => false, 'message' => 'Shift assignment not found.'], 404);
        }

        $assignment->status = 'completed';
        $assignment->save();

        if ($assignment->staffingRequest) {
            $assignment->staffingRequest->update(['status' => 'completed']);
            EmailNotificationService::sendEventCompleteToClient($assignment->staffingRequest);
        }

        return response()->json([
            'status' => true,
            'message' => 'Shift marked as completed. Funds will be released upon client confirmation.',
            'assignment' => $assignment,
        ]);
    }

    /**
     * Sign Digital Contract as Crew Member.
     */
    public function signContract(Request $request, $contractId): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $contract = Contract::where('id', $contractId)->where('professional_id', $crew->id)->first();
        if (!$contract) {
            return response()->json(['status' => false, 'message' => 'Contract not found.'], 404);
        }

        $signature = $request->input('signature', $crew->full_name);
        $contract->crew_signed_at = now();
        $contract->crew_signature = $signature;
        $contract->status = $contract->client_signed_at ? 'active' : 'signed_by_crew';
        $contract->save();

        return response()->json([
            'status' => true,
            'message' => 'Contract signed successfully.',
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'status' => $contract->status,
                'crew_signed_at' => $contract->crew_signed_at->toIso8601String(),
                'signature' => $contract->crew_signature,
            ],
        ]);
    }

    /**
     * Get Wallet Balance & Payout Overview.
     */
    public function getWallet(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $available = (float)$crew->wallet_balance;
        $pending = (float)$crew->wallet_pending;
        $maxAdvance = $available + ($pending * 0.5);

        return response()->json([
            'status' => true,
            'wallet' => [
                'available_balance' => $available,
                'pending_escrow' => $pending,
                'total_withdrawn' => (float)$crew->wallet_withdrawn,
                'max_withdrawable_with_advance' => $maxAdvance,
                'currency' => $crew->currency ?: 'KES',
                'payment_details' => $crew->payment_details,
            ],
        ]);
    }

    /**
     * Save Payout Account Details (M-Pesa or Bank).
     */
    public function savePayoutMethod(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:mobile_money,bank_account'],
            'mobile_money.provider' => ['nullable', 'string', 'in:mpesa,airtel_money'],
            'mobile_money.phone_number' => ['nullable', 'string', 'max:20'],
            'mobile_money.account_name' => ['nullable', 'string', 'max:100'],
            'bank_account.bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account.account_name' => ['nullable', 'string', 'max:100'],
            'bank_account.account_number' => ['nullable', 'string', 'max:50'],
            'bank_account.branch_code' => ['nullable', 'string', 'max:50'],
            'bank_account.swift_ifsc' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $existing = is_array($crew->payment_details) ? $crew->payment_details : [];

        if ($request->type === 'mobile_money') {
            $existing['mobile_money'] = $request->input('mobile_money', []);
        } else {
            $existing['bank_account'] = $request->input('bank_account', []);
        }

        $crew->payment_details = $existing;
        $crew->save();

        return response()->json([
            'status' => true,
            'message' => 'Payout method saved successfully.',
            'payment_details' => $crew->payment_details,
        ]);
    }

    /**
     * Request Wallet Withdrawal / Payout.
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:10'],
            'payout_source' => ['nullable', 'string', 'in:saved_mobile,saved_bank,custom'],
            'payout_method' => ['nullable', 'string', 'in:mpesa,airtel_money,bank_transfer,saved_mobile,saved_bank'],
            'account_details' => ['nullable', 'string', 'max:255'],
            'is_advance' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $payoutSource = $request->input('payout_source', 'saved_mobile');
        $paymentDetails = is_array($crew->payment_details) ? $crew->payment_details : [];

        $payoutMethod = $data['payout_method'] ?? 'mpesa';
        $accountDetails = $data['account_details'] ?? '';

        if ($payoutSource === 'saved_mobile' || $payoutMethod === 'saved_mobile') {
            $mobile = $paymentDetails['mobile_money'] ?? [];
            $provider = strtolower($mobile['provider'] ?? 'mpesa');
            $payoutMethod = (str_contains($provider, 'airtel')) ? 'airtel_money' : 'mpesa';
            $providerLabel = (str_contains($provider, 'airtel')) ? 'Airtel Money' : 'M-Pesa';
            $phoneNo = !empty($mobile['phone_number']) ? $mobile['phone_number'] : $crew->phone;
            $nameVal = !empty($mobile['account_name']) ? $mobile['account_name'] : $crew->full_name;
            $accountDetails = "{$providerLabel}: {$phoneNo} ({$nameVal})";
        } elseif ($payoutSource === 'saved_bank' || $payoutMethod === 'saved_bank') {
            $bank = $paymentDetails['bank_account'] ?? [];
            $payoutMethod = 'bank_transfer';
            $extra = !empty($bank['branch_code']) ? ' | Branch/Code: ' . $bank['branch_code'] : '';
            $bankName = $bank['bank_name'] ?? 'Bank Account';
            $accNo = $bank['account_number'] ?? '';
            $holder = $bank['account_name'] ?? $crew->full_name;
            $accountDetails = "Bank: {$bankName} | Acc: {$accNo} | Name: {$holder}{$extra}";
        }

        if (empty($accountDetails)) {
            return response()->json([
                'status' => false,
                'message' => 'Please provide or save valid payout account details before submitting a withdrawal.',
            ], 422);
        }

        $maxAdvanceAllowed = (float)$crew->wallet_balance + ((float)$crew->wallet_pending * 0.5);
        if ((float)$data['amount'] > $maxAdvanceAllowed) {
            return response()->json([
                'status' => false,
                'message' => 'Requested amount exceeds your maximum limit. You can withdraw up to 100% of Available Balance + up to 50% Advance on Escrow funds (KES ' . number_format($maxAdvanceAllowed, 2) . ').',
            ], 400);
        }

        $withdrawal = WithdrawalRequest::create([
            'professional_id' => $crew->id,
            'amount' => $data['amount'],
            'payout_method' => $payoutMethod,
            'account_details' => $accountDetails . ($request->boolean('is_advance') ? ' (50% Advance Request)' : ''),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal request submitted successfully.',
            'withdrawal' => $withdrawal,
        ], 201);
    }

    /**
     * Get Payout / Withdrawal Transaction History.
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $withdrawals = WithdrawalRequest::where('professional_id', $crew->id)
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => true,
            'transactions' => $withdrawals,
        ]);
    }

    /**
     * Get Reviews Received by Crew Member.
     */
    public function getReviews(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $reviews = Review::where('professional_id', $crew->id)->latest()->get();
        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        return response()->json([
            'status' => true,
            'average_rating' => (float)$avgRating,
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews,
        ]);
    }

    /**
     * Toggle Review Visibility on Public Profile.
     */
    public function toggleReviewVisibility(Request $request, $id): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $review = Review::where('id', $id)->where('professional_id', $crew->id)->first();
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }

        $review->is_visible = !$review->is_visible;
        $review->save();

        return response()->json([
            'status' => true,
            'message' => $review->is_visible ? 'Review is now visible on profile.' : 'Review is now hidden from profile.',
            'review' => $review,
        ]);
    }

    /**
     * Get Conversations List for Crew Member.
     */
    public function getConversations(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('email', $crew->email)->first();
        if (!$user) {
            return response()->json(['status' => true, 'conversations' => []]);
        }

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver', 'staffingRequest'])
            ->latest()
            ->get();

        $conversations = [];
        foreach ($messages as $msg) {
            $otherUser = $msg->sender_id === $user->id ? $msg->receiver : $msg->sender;
            if (!$otherUser) continue;

            $key = $otherUser->id;
            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'user_id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'company' => $otherUser->company_name,
                    'avatar' => $otherUser->avatar ? get_storage_url($otherUser->avatar) : null,
                    'last_message' => $msg->message,
                    'last_message_at' => $msg->created_at->toIso8601String(),
                    'unread_count' => Message::where('sender_id', $otherUser->id)->where('receiver_id', $user->id)->where('is_read', false)->count(),
                ];
            }
        }

        return response()->json([
            'status' => true,
            'conversations' => array_values($conversations),
        ]);
    }

    /**
     * Send Message to Client.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('email', $crew->email)->first();
        if (!$user) {
            // Create shadow user record if needed for message foreign key
            $user = User::create([
                'name' => $crew->full_name,
                'email' => $crew->email,
                'password' => $crew->password,
                'phone' => $crew->phone,
                'role' => 'professional',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'receiver_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:2000'],
            'staffing_request_id' => ['nullable', 'exists:staffing_requests,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'staffing_request_id' => $request->staffing_request_id,
            'message' => trim($request->message),
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully.',
            'data' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'created_at' => $msg->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Propose Custom Rate / Quotation to Client.
     */
    public function proposeRate(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('email', $crew->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $crew->full_name,
                'email' => $crew->email,
                'password' => $crew->password,
                'phone' => $crew->phone,
                'role' => 'professional',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'receiver_id' => ['required', 'exists:users,id'],
            'staffing_request_id' => ['required', 'exists:staffing_requests,id'],
            'proposed_rate' => ['required', 'numeric', 'min:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $currency = $crew->currency ?: 'KES';
        $note = $request->note ?: "I would like to propose a rate of {$currency} " . number_format($request->proposed_rate, 2) . " for this event.";

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'staffing_request_id' => $request->staffing_request_id,
            'message' => $note,
            'negotiated_price' => $request->proposed_rate,
            'negotiation_status' => 'pending',
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rate proposal sent to client.',
            'data' => $msg,
        ], 201);
    }

    /**
     * Get Crew Support Tickets.
     */
    public function getSupportTickets(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('email', $crew->email)->first();
        if (!$user) {
            return response()->json(['status' => true, 'tickets' => []]);
        }

        $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'status' => true,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Submit Support Ticket.
     */
    public function createSupportTicket(Request $request): JsonResponse
    {
        $crew = $this->getCrew($request);
        if (!$crew) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('email', $crew->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $crew->full_name,
                'email' => $crew->email,
                'password' => $crew->password,
                'phone' => $crew->phone,
                'role' => 'professional',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'description' => ['required', 'string', 'max:3000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $ticket = SupportTicket::create([
            'ticket_number' => 'TCK-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'subject' => trim($request->subject),
            'category' => trim($request->category),
            'priority' => $request->priority ?: 'medium',
            'status' => 'open',
            'description' => trim($request->description),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Support ticket created successfully. Our team will contact you shortly.',
            'ticket' => $ticket,
        ], 201);
    }
}
