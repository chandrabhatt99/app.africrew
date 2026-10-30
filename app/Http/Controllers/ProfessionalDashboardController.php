<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\StaffingAssignment;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfessionalDashboardController extends Controller
{
    private function getAuthenticatedProfessional(Request $request): ?Professional
    {
        $professionalId = $request->session()->get('professional_id');
        if (!$professionalId) {
            return null;
        }

        return Professional::find($professionalId);
    }

    private function ensureOnboarded(Professional $professional): ?RedirectResponse
    {
        if (is_null($professional->email_verified_at)) {
            return redirect()->route('staff.verify.notice')->with('error', 'Please verify your email address to continue.');
        }

        $isComplete = ($professional->completion_percentage >= 60) || (bool)$professional->is_onboarded;

        // If staff has not completed profile setup and is attempting to access non-profile pages, force redirect to profile wizard
        if (!$isComplete && !request()->routeIs('professional.profile', 'professional.profile.update', 'professional.suggest-skill', 'professional.referees.request-verification', 'professional.submit-review-request')) {
            return redirect()->route('professional.profile')->with('warning', 'Please complete your profile sections (Personal, About You, Experience, Portfolio, Locations & Rates) before accessing other navigation tabs.');
        }

        return null;
    }

    public function index(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login')->with('error', 'Please log in to access the Crew Portal.');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        $assignments = StaffingAssignment::with(['staffingRequest.proposals'])
            ->where('professional_id', $professional->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('status', 'assigned')->count(),
            'accepted' => $assignments->where('status', 'accepted')->count(),
            'completed' => $assignments->where('status', 'completed')->count(),
        ];

        $activeProposals = \App\Models\Proposal::where('professional_id', $professional->id)
            ->with(['staffingRequest.user', 'professional'])
            ->latest()
            ->get();

        $user = \App\Models\User::where('email', $professional->email)->first();
        $recentMessages = collect([]);
        if ($user) {
            $recentMessages = \App\Models\Message::where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->with(['sender', 'receiver', 'proposal.staffingRequest'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('professional.dashboard', compact('professional', 'assignments', 'stats', 'activeProposals', 'recentMessages', 'user'));
    }

    public function shifts(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        $assignments = StaffingAssignment::with(['staffingRequest.proposals'])
            ->where('professional_id', $professional->id)
            ->latest()
            ->get();

        $oneDayRate = $professional->one_day_rate ?? 5000.00;

        $totalRealizedEarnings = 0;
        $upcomingExpectedEarnings = 0;
        $pendingOfferEarnings = 0;

        foreach ($assignments as $job) {
            $estPay = $job->staffingRequest->budget ?? $oneDayRate;
            if ($job->status === 'completed') {
                $totalRealizedEarnings += $estPay;
            } elseif ($job->status === 'accepted') {
                $upcomingExpectedEarnings += $estPay;
            } elseif ($job->status === 'assigned') {
                $pendingOfferEarnings += $estPay;
            }
        }

        $earnings = [
            'realized' => $totalRealizedEarnings,
            'upcoming' => $upcomingExpectedEarnings,
            'pending' => $pendingOfferEarnings,
        ];

        return view('professional.shifts', compact('professional', 'assignments', 'earnings'));
    }

    public function onboarding(Request $request): RedirectResponse
    {
        return redirect()->route('professional.profile');
    }

    public function storeOnboarding(Request $request): RedirectResponse
    {
        return $this->updateProfile($request);
    }

    public function profile(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        $assignments = StaffingAssignment::with('staffingRequest')
            ->where('professional_id', $professional->id)
            ->latest()
            ->get();

        $acceptedOffers = $assignments->where('status', 'accepted');

        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('professional.profile', compact('professional', 'assignments', 'acceptedOffers', 'categories'));
    }

    public function messages(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        return view('professional.messages', compact('professional'));
    }

    public function notifications(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        $notifications = collect();

        // 1. Shift Assignments
        $assignments = StaffingAssignment::with('staffingRequest')
            ->where('professional_id', $professional->id)
            ->latest()
            ->get();

        foreach ($assignments as $job) {
            $req = $job->staffingRequest;
            $eventName = $req ? ($req->title ?: 'Event Shift Request') : 'Event Shift Request';
            $budget = $req ? ($req->budget ? 'KES ' . number_format($req->budget, 2) : 'Standard Rate') : 'Standard Rate';
            $clientName = $req ? ($req->contact_person ?: ($req->user->name ?? 'Client')) : 'Event Organizer';

            if ($job->status === 'assigned') {
                $notifications->push([
                    'id' => 'shift_' . $job->id,
                    'type' => 'shifts',
                    'icon' => '📅',
                    'color' => 'amber',
                    'title' => 'New Shift Booking Request: ' . $eventName,
                    'description' => "{$clientName} requested to book you for this event. Offered Compensation: {$budget}.",
                    'time' => $job->created_at ? $job->created_at->diffForHumans() : 'Recently',
                    'is_unread' => true,
                    'action_url' => route('professional.dashboard'),
                    'action_text' => 'Respond to Shift Offer →'
                ]);
            } elseif ($job->status === 'accepted') {
                $notifications->push([
                    'id' => 'shift_acc_' . $job->id,
                    'type' => 'shifts',
                    'icon' => '✅',
                    'color' => 'emerald',
                    'title' => 'Shift Offer Confirmed: ' . $eventName,
                    'description' => "You have accepted the shift assignment for {$eventName}. Get ready for the event date.",
                    'time' => $job->updated_at ? $job->updated_at->diffForHumans() : 'Recently',
                    'is_unread' => false,
                    'action_url' => route('professional.shifts'),
                    'action_text' => 'View Shift Schedule →'
                ]);
            }
        }

        // 2. Withdrawal / Payout Updates
        $withdrawals = \App\Models\WithdrawalRequest::where('professional_id', $professional->id)
            ->latest()
            ->get();

        foreach ($withdrawals as $w) {
            $statusLabel = ucfirst($w->status);
            $color = $w->status === 'approved' ? 'emerald' : ($w->status === 'rejected' ? 'rose' : 'blue');
            $notifications->push([
                'id' => 'withdrawal_' . $w->id,
                'type' => 'payouts',
                'icon' => $w->status === 'approved' ? '💰' : ($w->status === 'rejected' ? '⚠️' : '⏳'),
                'color' => $color,
                'title' => "Payout Request {$statusLabel}: KES " . number_format($w->amount, 2),
                'description' => "Your withdrawal request for KES " . number_format($w->amount, 2) . " via {$w->account_details} is currently {$w->status}.",
                'time' => $w->created_at ? $w->created_at->diffForHumans() : 'Recently',
                'is_unread' => $w->status === 'pending',
                'action_url' => route('professional.wallet'),
                'action_text' => 'View Wallet →'
            ]);
        }

        // 3. System Account & Verification Notification
        if ($professional->status === 'approved') {
            $notifications->push([
                'id' => 'sys_approved',
                'type' => 'system',
                'icon' => '🛡️',
                'color' => 'emerald',
                'title' => 'Crew Profile Verified & Live',
                'description' => 'Your government ID and ushering skills profile have been verified by AfriCrew Admin. Your public profile is active for event bookings!',
                'time' => $professional->updated_at ? $professional->updated_at->diffForHumans() : 'Active',
                'is_unread' => false,
                'action_url' => route('crew.show', $professional),
                'action_text' => 'View Live Profile →'
            ]);
        } else {
            $notifications->push([
                'id' => 'sys_pending',
                'type' => 'system',
                'icon' => '⏳',
                'color' => 'amber',
                'title' => 'Account Verification Under Review',
                'description' => 'Your AfriCrew staff application is currently under review by our admin team. Ensure your 5-step profile details are complete.',
                'time' => $professional->created_at ? $professional->created_at->diffForHumans() : 'Recently',
                'is_unread' => true,
                'action_url' => route('professional.profile'),
                'action_text' => 'Check Profile Setup →'
            ]);
        }

        // Default welcome alert
        $notifications->push([
            'id' => 'sys_welcome',
            'type' => 'system',
            'icon' => '💡',
            'color' => 'blue',
            'title' => 'Welcome to AfriCrew Crew Portal',
            'description' => 'Set up your daily labour rates, availability calendar, and work experience to receive premium event shift invites.',
            'time' => '1 day ago',
            'is_unread' => false,
            'action_url' => route('professional.profile'),
            'action_text' => 'Update Labour Charges →'
        ]);

        return view('professional.notifications', compact('professional', 'notifications'));
    }

    public function updateJobStatus(Request $request, StaffingAssignment $job): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional || (int)$job->professional_id !== (int)$professional->id) {
            return back()->withErrors(['job' => 'Unauthorized action for this shift assignment.']);
        }

        $data = $request->validate([
            'status' => ['required', 'in:assigned,accepted,declined,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $job->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $job->notes,
        ]);

        if ($job->staffingRequest) {
            if ($data['status'] === 'accepted') {
                $job->staffingRequest->update(['status' => 'assigned']);
                \App\Services\EmailNotificationService::sendCrewConfirmedToClient(
                    $job->staffingRequest,
                    $job->professional?->full_name
                );
            } elseif ($data['status'] === 'declined') {
                $hasOtherAssignments = $job->staffingRequest->assignments()
                    ->where('id', '!=', $job->id)
                    ->whereIn('status', ['assigned', 'accepted'])
                    ->exists();

                if (!$hasOtherAssignments) {
                    $job->staffingRequest->update(['status' => 'new']);
                }
            } elseif ($data['status'] === 'completed') {
                $job->staffingRequest->update(['status' => 'completed']);
                \App\Services\EmailNotificationService::sendEventCompleteToClient($job->staffingRequest);
            }
        }

        $msg = $data['status'] === 'accepted' 
            ? 'Shift offer ACCEPTED! The event manager has been notified.'
            : ($data['status'] === 'declined' ? 'Shift offer DECLINED.' : 'Shift status updated.');

        return back()->with('success', $msg);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $data = $request->validate([
            'full_name' => ['nullable', 'string', 'max:150'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'category' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'other_cities' => ['nullable', 'array'],
            'preferred_locations' => ['nullable'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
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
            'multi_day_discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'booking_policy' => ['nullable', 'string'],
            'availability' => ['nullable', 'string'],
            'availability_dates' => ['nullable', 'array'],
            'blocked_dates' => ['nullable', 'array'],
            'assignment_types' => ['nullable', 'array'],
            'skills' => ['nullable', 'string', 'max:2000'],
            'featured_skills' => ['nullable', 'array'],
            'expertises' => ['nullable', 'array'],
            'services' => ['nullable', 'array'],
            'about' => ['nullable', 'string', 'max:3000'],
            'my_update' => ['nullable', 'string', 'max:200'],
            'lets_connect' => ['nullable', 'string', 'max:200'],
            'experience_summary' => ['nullable', 'string', 'max:3000'],
            'highlight_quote' => ['nullable', 'string', 'max:1000'],
            'social_links' => ['nullable', 'array'],
            'education' => ['nullable', 'array'],
            'show_education_on_cv' => ['nullable', 'boolean'],
            'experience_records' => ['nullable', 'array'],
            'referees' => ['nullable', 'array'],
            'education_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'experience_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'government_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'gallery_photos.*' => ['nullable', 'image', 'max:5120'],
            'expertise_images.*' => ['nullable', 'image', 'max:5120'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        if (isset($data['preferred_locations']) && is_string($data['preferred_locations'])) {
            $data['preferred_locations'] = array_map('trim', explode(',', $data['preferred_locations']));
        }

        if ($request->has('skills_arr') && is_array($request->skills_arr)) {
            $skillsArr = array_values(array_filter(array_map('trim', $request->skills_arr)));
            $data['skills'] = implode(', ', array_slice($skillsArr, 0, 3));
        } elseif (isset($data['skills'])) {
            $skillsArr = is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', (string)$data['skills']));
            $skillsArr = array_values(array_filter($skillsArr));
            $data['skills'] = implode(', ', array_slice($skillsArr, 0, 3));
        }

        if (isset($data['featured_skills']) && is_array($data['featured_skills'])) {
            $data['featured_skills'] = array_slice(array_values(array_unique(array_filter($data['featured_skills']))), 0, 3);
        }

        // Process rich expertises - strictly capped to 4 cards
        if (isset($data['expertises']) && is_array($data['expertises'])) {
            $cleanedExpertises = [];
            foreach (array_slice($data['expertises'], 0, 4) as $idx => $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }
                if (is_array($item) && !empty(trim($item['name'] ?? ''))) {
                    $imgUrl = $item['image'] ?? null;
                    if ($request->hasFile("expertise_images.{$idx}")) {
                        $imgUrl = $request->file("expertise_images.{$idx}")->store('professionals/expertises', 'public');
                    }
                    $kSkills = $item['skills'] ?? ($item['key_skills'] ?? []);
                    if (is_string($kSkills)) {
                        $kSkills = array_map('trim', explode(',', $kSkills));
                    }
                    $kSkills = array_slice(array_values(array_filter((array)$kSkills)), 0, 3);

                    $cleanedExpertises[] = [
                        'name' => trim($item['name']),
                        'description' => trim($item['description'] ?? ''),
                        'icon' => trim($item['icon'] ?? '⚡'),
                        'image' => $imgUrl,
                        'skills' => $kSkills,
                    ];
                }
            }
            $data['expertises'] = array_slice(array_values($cleanedExpertises), 0, 4);
        }

        if (isset($data['other_cities']) && is_array($data['other_cities'])) {
            $data['other_cities'] = array_slice(array_values(array_unique(array_filter($data['other_cities']))), 0, 3);
        }

        if (isset($request->availability_dates)) {
            $availInput = $request->availability_dates;
            if (is_string($availInput)) {
                $decoded = json_decode($availInput, true);
                $data['availability_dates'] = is_array($decoded) ? $decoded : [];
            } elseif (is_array($availInput)) {
                $data['availability_dates'] = $availInput;
            }
        }

        if (isset($request->blocked_dates)) {
            $blockedInput = $request->blocked_dates;
            if (is_string($blockedInput)) {
                $decoded = json_decode($blockedInput, true);
                $data['blocked_dates'] = is_array($decoded) ? $decoded : [];
            } elseif (is_array($blockedInput)) {
                $data['blocked_dates'] = $blockedInput;
            }
        }

        // Clean education array items & handle verification document uploads
        $existingEdu = is_array($professional->education) ? $professional->education : [];
        if (isset($data['education']) && is_array($data['education'])) {
            $cleanedEdu = [];
            foreach ($data['education'] as $idx => $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }
                if (is_array($item) && (!empty(trim($item['institution'] ?? '')) || !empty(trim($item['degree'] ?? '')))) {
                    // Check if a new file was uploaded for this education record
                    if ($request->hasFile("education_documents.{$idx}")) {
                        $item['document'] = $request->file("education_documents.{$idx}")->store("professionals/education_docs", 'public');
                    } elseif (isset($existingEdu[$idx]['document']) && empty($item['document'])) {
                        $item['document'] = $existingEdu[$idx]['document'];
                    }
                    $cleanedEdu[] = $item;
                }
            }
            $data['education'] = array_values($cleanedEdu);
        }

        // Clean referees array items (confidential, seen only by admin to verify work)
        $existingReferees = is_array($professional->referees) ? $professional->referees : [];
        if (isset($data['referees']) && is_array($data['referees'])) {
            $cleanedRef = [];
            foreach ($data['referees'] as $idx => $ref) {
                if (is_string($ref)) {
                    $ref = json_decode($ref, true);
                }
                if (is_array($ref) && !empty(trim($ref['name'] ?? ''))) {
                    $email = trim($ref['email'] ?? '');

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

                    $consentToken = $matchedExisting['consent_token'] ?? \Illuminate\Support\Str::random(40);
                    $status = $matchedExisting['status'] ?? (!empty($email) ? 'requested' : 'requested');

                    $refItem = [
                        'name' => trim($ref['name'] ?? ''),
                        'organization' => trim($ref['organization'] ?? ''),
                        'relationship' => trim($ref['relationship'] ?? ''),
                        'period' => trim($ref['period'] ?? ($matchedExisting['period'] ?? '')),
                        'crew_role' => trim($ref['crew_role'] ?? ($matchedExisting['crew_role'] ?? '')),
                        'email' => $email,
                        'phone' => trim($ref['phone'] ?? ''),
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

                    // Automatically send verification email if referee has email and was not yet verified or sent
                    $isNewEmail = !empty($email) && (empty($matchedExisting['email']) || strtolower($matchedExisting['email']) !== strtolower($email));
                    $neverSent = !empty($email) && empty($matchedExisting['consent_sent_at']);

                    if (($isNewEmail || $neverSent) && $status !== 'verified') {
                        $reviewUrl = route('referee.review', ['token' => $consentToken]);
                        try {
                            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\RefereeConsentRequestMail(
                                refereeName: $refItem['name'],
                                refereeOrg: $refItem['organization'] ?: 'Professional Reference',
                                crewName: $professional->full_name,
                                crewCategory: $refItem['crew_role'] ?: ($professional->category ?: 'Crew Member'),
                                reviewUrl: $reviewUrl,
                                period: $refItem['period'] ?: 'recent projects',
                                companyOrProject: $refItem['organization'] ?: 'Event Project'
                            ));
                            $refItem['status'] = 'requested';
                            $refItem['consent_sent_at'] = now()->toDateTimeString();
                            \Illuminate\Support\Facades\Log::info("Referee verification email auto-sent to {$email} for {$professional->full_name}");
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error("Failed auto-sending referee email to {$email}: " . $e->getMessage());
                        }
                    }

                    $cleanedRef[] = $refItem;
                }
            }
            $data['referees'] = array_values($cleanedRef);
        }

        // Clean experience array items & handle verification document uploads
        $existingExp = is_array($professional->experience_records) ? $professional->experience_records : [];
        if (isset($data['experience_records']) && is_array($data['experience_records'])) {
            $cleanedExp = [];
            foreach ($data['experience_records'] as $eIdx => $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }
                if (is_array($item) && (!empty(trim($item['employer'] ?? '')) || !empty(trim($item['role'] ?? '')))) {
                    // Check if a new file was uploaded for this work experience record
                    if ($request->hasFile("experience_documents.{$eIdx}")) {
                        $item['document'] = $request->file("experience_documents.{$eIdx}")->store("professionals/experience_docs", 'public');
                    } elseif (isset($existingExp[$eIdx]['document']) && empty($item['document'])) {
                        $item['document'] = $existingExp[$eIdx]['document'];
                    }
                    $cleanedExp[] = $item;
                }
            }
            $data['experience_records'] = array_values($cleanedExp);
        }

        foreach (['profile_photo', 'cover_photo', 'resume', 'government_id'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store("professionals/{$file}", 'public');
            }
        }

        $existingGallery = is_array($professional->gallery_photos) ? array_values(array_filter($professional->gallery_photos)) : [];
        if ($request->hasFile('gallery_photos')) {
            $newPaths = [];
            foreach ($request->file('gallery_photos') as $gFile) {
                if ($gFile && $gFile->isValid()) {
                    $newPaths[] = $gFile->store('professionals/gallery', 'public');
                }
            }
            if (!empty($newPaths)) {
                $merged = array_values(array_unique(array_filter(array_merge($existingGallery, $newPaths))));
                $data['gallery_photos'] = array_slice($merged, 0, 8);
            } else {
                $data['gallery_photos'] = array_slice($existingGallery, 0, 8);
            }
        } else {
            $data['gallery_photos'] = array_slice($existingGallery, 0, 8);
        }

        // Calculate completion status
        $professional->fill($data);
        $data['is_onboarded'] = ($professional->completion_percentage >= 60);

        if ($professional->status === 'approved') {
            $data['status'] = 'pending';
            $data['submitted_for_review_at'] = $professional->completion_percentage >= 60 ? now() : null;
        }

        $professional->update($data);

        // Send profile updated email notification
        EmailNotificationService::sendProfileUpdated($professional);

        $activeTab = $request->input('active_tab', 'personal');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'active_tab' => $activeTab,
                'completion_percentage' => $professional->completion_percentage,
                'next_requirement' => $professional->next_incomplete_requirement,
                'message' => 'Saved'
            ]);
        }

        if ($request->input('action') === 'submit_for_review') {
            return $this->submitReviewRequest($request);
        }

        return back()->with('active_tab', $activeTab)->with('success', 'Profile section saved successfully!');
    }

    public function suggestSkill(Request $request)
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $skillName = trim($request->input('skill_name', ''));
        if (empty($skillName)) {
            return response()->json(['error' => 'Skill name is required.'], 422);
        }

        \App\Models\SuggestedSkill::create([
            'professional_id' => $professional->id,
            'category' => $professional->category ?: 'General',
            'skill_name' => $skillName,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Skill '{$skillName}' submitted for admin review!"
        ]);
    }

    public function requestRefereeVerification(Request $request)
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $refereeIndex = $request->input('referee_index');
        $referees = is_array($professional->referees) ? $professional->referees : [];

        if (isset($referees[$refereeIndex])) {
            $ref = is_string($referees[$refereeIndex]) ? json_decode($referees[$refereeIndex], true) : $referees[$refereeIndex];
            if (empty($ref['email'])) {
                return response()->json(['error' => 'Please provide an email address for this referee before requesting verification.'], 422);
            }

            if (empty($ref['consent_token'])) {
                $ref['consent_token'] = \Illuminate\Support\Str::random(40);
            }

            $reviewUrl = route('referee.review', ['token' => $ref['consent_token']]);

            try {
                \Illuminate\Support\Facades\Mail::to($ref['email'])->send(new \App\Mail\RefereeConsentRequestMail(
                    refereeName: $ref['name'] ?? 'Referee',
                    refereeOrg: $ref['organization'] ?? ($ref['relationship'] ?? 'Professional Reference'),
                    crewName: $professional->full_name,
                    crewCategory: $ref['crew_role'] ?? ($professional->category ?: 'Event Crew'),
                    reviewUrl: $reviewUrl,
                    period: $ref['period'] ?? 'recent projects',
                    companyOrProject: $ref['organization'] ?? 'Event Project'
                ));
                \Illuminate\Support\Facades\Log::info("Referee verification email sent to {$ref['email']} for professional {$professional->id}");
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send referee consent email to {$ref['email']}: " . $e->getMessage());
            }

            $ref['status'] = 'requested';
            $ref['consent_sent_at'] = now()->toDateTimeString();
            $referees[$refereeIndex] = $ref;
            $professional->update(['referees' => $referees]);

            return response()->json([
                'success' => true,
                'status' => 'requested',
                'message' => "Verification request email sent to {$ref['email']}! Once your referee reviews and verifies, their verified reference status will update automatically."
            ]);
        }

        return response()->json(['error' => 'Referee not found.'], 404);
    }

    public function submitReviewRequest(Request $request): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if (!$professional->email_verified_at) {
            return back()->withErrors(['profile' => 'Verify your email address before submitting your profile for review.']);
        }

        if ($professional->completion_percentage < 60) {
            return back()->withErrors(['profile' => 'Your profile must be at least 60% complete before you can submit it for review.']);
        }

        $professional->update([
            'submitted_for_review_at' => now(),
            'status' => 'pending',
            'is_onboarded' => true,
        ]);

        return back()->with('success', 'Your profile has been submitted for administrator review. Any profile updates will be approved before becoming public.');
    }

    public function reviewsTab(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $reviews = $professional->reviews()->latest()->get();
        return view('professional.reviews', compact('professional', 'reviews'));
    }

    public function respondToReview(Request $request, $review): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $rev = \App\Models\Review::where('id', $review)->where('professional_id', $professional->id)->firstOrFail();
        $responseText = trim($request->input('response_text', ''));

        if (!empty($responseText)) {
            $rev->update([
                'response_text' => $responseText,
                'responded_at' => now(),
            ]);
        }

        return back()->with('success', 'Your response to the client review has been published.');
    }

    public function reportReview(Request $request, $review): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);
        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $rev = \App\Models\Review::where('id', $review)->where('professional_id', $professional->id)->firstOrFail();
        $reason = trim($request->input('report_reason', 'Spam / Inappropriate Content'));

        $rev->update([
            'is_reported' => true,
            'report_reason' => $reason,
        ]);

        return back()->with('success', 'Review has been reported to admin moderation for investigation.');
    }

    public function deleteGalleryPhoto(Request $request): JsonResponse|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('staff.login');
        }

        $photoPath = $request->input('photo_path');
        if ($photoPath && is_array($professional->gallery_photos)) {
            $updated = [];
            foreach ($professional->gallery_photos as $p) {
                if ($p === $photoPath || get_storage_url($p) === $photoPath || basename($p) === basename($photoPath)) {
                    if (Storage::disk('public')->exists($p)) {
                        Storage::disk('public')->delete($p);
                    }
                } else {
                    $updated[] = $p;
                }
            }
            $professional->update(['gallery_photos' => array_values($updated)]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Photo removed from portfolio gallery.',
                'gallery_photos' => $professional->fresh()->gallery_photos ?? []
            ]);
        }

        return back()->with('active_tab', 'photos')->with('success', 'Photo removed from portfolio gallery.');
    }

    public function wallet(Request $request): View|RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        if ($redirect = $this->ensureOnboarded($professional)) {
            return $redirect;
        }

        $withdrawals = \App\Models\WithdrawalRequest::where('professional_id', $professional->id)
            ->latest()
            ->get();

        $pendingEscrowJobs = StaffingAssignment::with('staffingRequest')
            ->where('professional_id', $professional->id)
            ->whereHas('staffingRequest', function ($query) {
                $query->where('payment_status', 'paid_to_admin')
                      ->where('status', '!=', 'completed');
            })
            ->get();

        return view('professional.wallet', compact('professional', 'withdrawals', 'pendingEscrowJobs'));
    }

    public function updatePaymentDetails(Request $request): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $data = $request->validate([
            'account_type' => ['required', 'string', 'in:mobile_money,bank_account'],
            
            // Mobile Money inputs
            'mobile_provider' => ['nullable', 'string', 'max:50'],
            'mobile_number' => ['nullable', 'string', 'max:50'],
            'mobile_name' => ['nullable', 'string', 'max:150'],

            // Bank Account inputs
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'branch_code' => ['nullable', 'string', 'max:100'],
            'swift_ifsc' => ['nullable', 'string', 'max:100'],
        ]);

        $existing = is_array($professional->payment_details) ? $professional->payment_details : [];

        $mobileDetails = [
            'provider' => $data['mobile_provider'] ?? ($existing['mobile_money']['provider'] ?? 'mpesa'),
            'phone_number' => $data['mobile_number'] ?? ($existing['mobile_money']['phone_number'] ?? ''),
            'account_name' => $data['mobile_name'] ?? ($existing['mobile_money']['account_name'] ?? $professional->full_name),
        ];

        $bankDetails = [
            'bank_name' => $data['bank_name'] ?? ($existing['bank_account']['bank_name'] ?? ''),
            'account_number' => $data['account_number'] ?? ($existing['bank_account']['account_number'] ?? ''),
            'account_name' => $data['bank_account_name'] ?? ($existing['bank_account']['account_name'] ?? $professional->full_name),
            'branch_code' => $data['branch_code'] ?? ($existing['bank_account']['branch_code'] ?? ''),
            'swift_ifsc' => $data['swift_ifsc'] ?? ($existing['bank_account']['swift_ifsc'] ?? ''),
        ];

        $updatedDetails = [
            'account_type' => $data['account_type'],
            'mobile_money' => $mobileDetails,
            'bank_account' => $bankDetails,
        ];

        $professional->update([
            'payment_details' => $updatedDetails,
        ]);

        return back()->with('success', 'Payout account details saved to system successfully!');
    }

    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
            'payout_source' => ['nullable', 'string', 'in:saved_mobile,saved_bank,custom'],
            'payout_method' => ['nullable', 'string', 'in:mpesa,airtel_money,bank_transfer,saved_mobile,saved_bank'],
            'account_details' => ['nullable', 'string', 'max:255'],
            'is_advance' => ['nullable', 'boolean'],
        ]);

        $payoutSource = $request->input('payout_source', 'custom');
        $paymentDetails = is_array($professional->payment_details) ? $professional->payment_details : [];

        $payoutMethod = $data['payout_method'] ?? 'mpesa';
        $accountDetails = $data['account_details'] ?? '';

        if ($payoutSource === 'saved_mobile' || $payoutMethod === 'saved_mobile') {
            $mobile = $paymentDetails['mobile_money'] ?? [];
            $provider = strtolower($mobile['provider'] ?? 'mpesa');
            $payoutMethod = (str_contains($provider, 'airtel')) ? 'airtel_money' : 'mpesa';
            $providerLabel = (str_contains($provider, 'airtel')) ? 'Airtel Money' : 'M-Pesa';
            
            $phoneNo = !empty($mobile['phone_number']) ? $mobile['phone_number'] : $professional->phone;
            $nameVal = !empty($mobile['account_name']) ? $mobile['account_name'] : $professional->full_name;
            $accountDetails = "{$providerLabel}: {$phoneNo} ({$nameVal})";
        } elseif ($payoutSource === 'saved_bank' || $payoutMethod === 'saved_bank') {
            $bank = $paymentDetails['bank_account'] ?? [];
            $payoutMethod = 'bank_transfer';
            $extra = !empty($bank['branch_code']) ? ' | Branch/Code: ' . $bank['branch_code'] : (!empty($bank['swift_ifsc']) ? ' | IFSC/SWIFT: ' . $bank['swift_ifsc'] : '');
            
            $bankName = $bank['bank_name'] ?? 'Bank Account';
            $accNo = $bank['account_number'] ?? '';
            $holder = $bank['account_name'] ?? $professional->full_name;
            $accountDetails = "Bank: {$bankName} | Acc: {$accNo} | Name: {$holder}{$extra}";
        }

        if (empty($accountDetails)) {
            return back()->withErrors(['account_details' => 'Please provide or save valid payout account details before submitting a withdrawal.'])->withInput();
        }

        $maxAdvanceAllowed = (float) $professional->wallet_balance + ((float) $professional->wallet_pending * 0.5);

        if ((float) $data['amount'] > $maxAdvanceAllowed) {
            return back()->withErrors([
                'amount' => 'Requested amount exceeds your maximum limit. You can withdraw up to 100% of Available Balance + up to 50% Advance on Escrow funds (KES ' . number_format($maxAdvanceAllowed, 2) . ').'
            ])->withInput();
        }

        \App\Models\WithdrawalRequest::create([
            'professional_id' => $professional->id,
            'amount' => $data['amount'],
            'payout_method' => $payoutMethod,
            'account_details' => $accountDetails . ($request->boolean('is_advance') ? ' (50% Advance Request)' : ''),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully using your selected payout account!');
    }

    public function toggleReviewVisibility(Request $request, \App\Models\Review $review): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional || $review->professional_id !== $professional->id) {
            return back()->with('error', 'Unauthorized to modify review visibility.');
        }

        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        $statusLabel = $review->is_visible ? 'VISIBLE on your public profile' : 'HIDDEN from your public profile';
        return back()->with('success', "Review visibility updated: Review is now {$statusLabel}.");
    }
}
