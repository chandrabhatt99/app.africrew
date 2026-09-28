<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\StaffingAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $filledCount = 0;
        if (!empty($professional->full_name) && !empty($professional->phone) && !empty($professional->category) && !empty($professional->gender)) $filledCount++;
        if (!empty($professional->education) || !empty($professional->experience_records)) $filledCount++;
        if (!empty($professional->profile_photo) || !empty($professional->gallery_photos)) $filledCount++;
        if (!empty($professional->booking_policy) || !empty($professional->availability)) $filledCount++;
        if (!empty($professional->one_day_rate) || !empty($professional->hourly_rate)) $filledCount++;

        $isComplete = ($filledCount >= 5) && (bool)$professional->is_onboarded;

        // If staff has not completed profile setup and is attempting to access non-profile pages, force redirect to profile wizard
        if (!$isComplete && !request()->routeIs('professional.profile', 'professional.profile.update')) {
            return redirect()->route('professional.profile')->with('warning', 'Please complete all 5 profile onboarding sections (Personal, Experience, Photos, Availability, Labour Charges) before accessing other navigation tabs.');
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
            'preferred_locations' => ['nullable'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'one_day_rate' => ['nullable', 'numeric', 'min:0'],
            'two_day_rate' => ['nullable', 'numeric', 'min:0'],
            'rehearsal_rate' => ['nullable', 'numeric', 'min:0'],
            'rate_type' => ['nullable', 'string', 'in:fixed,negotiable'],
            'currency' => ['nullable', 'string', 'in:USD,KES'],
            'multi_day_discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'booking_policy' => ['nullable', 'string'],
            'availability' => ['nullable', 'string'],
            'availability_dates' => ['nullable', 'array'],
            'skills' => ['nullable', 'string', 'max:2000'],
            'services' => ['nullable', 'array'],
            'about' => ['nullable', 'string', 'max:3000'],
            'highlight_quote' => ['nullable', 'string', 'max:1000'],
            'social_links' => ['nullable', 'array'],
            'education' => ['nullable', 'array'],
            'experience_records' => ['nullable', 'array'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'government_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'gallery_photos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        if (isset($data['preferred_locations']) && is_string($data['preferred_locations'])) {
            $data['preferred_locations'] = array_map('trim', explode(',', $data['preferred_locations']));
        }

        if (isset($data['skills'])) {
            $skillsArr = is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', (string)$data['skills']));
            $skillsArr = array_values(array_filter($skillsArr));
            if (count($skillsArr) > 3) {
                $skillsArr = array_slice($skillsArr, 0, 3);
            }
            $data['skills'] = implode(', ', $skillsArr);
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

        if (!empty($professional->full_name)) {
            $data['full_name'] = $professional->full_name;
            $data['first_name'] = $professional->first_name;
            $data['last_name'] = $professional->last_name;
        } elseif (!empty($data['first_name']) || !empty($data['last_name'])) {
            $firstName = ucwords(mb_strtolower(trim($data['first_name'] ?? '')));
            $lastName = ucwords(mb_strtolower(trim($data['last_name'] ?? '')));
            $data['first_name'] = $firstName;
            $data['last_name'] = $lastName;
            $data['full_name'] = trim($firstName . ' ' . $lastName);
        } elseif (!empty($data['full_name'])) {
            $data['full_name'] = ucwords(mb_strtolower(trim($data['full_name'])));
        }

        if (!empty($professional->username)) {
            $data['username'] = $professional->username;
        }
        if (!empty($professional->email)) {
            $data['email'] = $professional->email;
        }
        if (!empty($professional->phone)) {
            $data['phone'] = $professional->phone;
        }
        if (!empty($professional->category)) {
            $data['category'] = $professional->category;
        }

        // Clean education array items (remove empty cards)
        if (isset($data['education']) && is_array($data['education'])) {
            $cleanedEdu = [];
            foreach ($data['education'] as $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }
                if (is_array($item) && (!empty(trim($item['institution'] ?? '')) || !empty(trim($item['degree'] ?? '')))) {
                    $cleanedEdu[] = $item;
                }
            }
            $data['education'] = array_values($cleanedEdu);
        }

        // Clean services array items
        if (!empty($data['services']) && is_array($data['services'])) {
            $cleanedServices = [];
            foreach ($data['services'] as $item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    if (is_array($decoded) && !empty($decoded['title'])) $cleanedServices[] = $decoded;
                } elseif (is_array($item) && !empty($item['title'])) {
                    $cleanedServices[] = $item;
                }
            }
            $data['services'] = $cleanedServices;
        }

        // Clean experience array items (remove empty cards)
        if (isset($data['experience_records']) && is_array($data['experience_records'])) {
            $cleanedExp = [];
            foreach ($data['experience_records'] as $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }
                if (is_array($item) && (!empty(trim($item['employer'] ?? '')) || !empty(trim($item['role'] ?? '')))) {
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

        $existingGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
        if ($request->hasFile('gallery_photos')) {
            $newPaths = [];
            foreach ($request->file('gallery_photos') as $gFile) {
                if ($gFile && $gFile->isValid()) {
                    $newPaths[] = $gFile->store('professionals/gallery', 'public');
                }
            }
            if (!empty($newPaths)) {
                $merged = array_values(array_unique(array_filter(array_merge($existingGallery, $newPaths))));
                $data['gallery_photos'] = array_slice($merged, 0, 4);
            } else {
                $data['gallery_photos'] = array_slice(array_values(array_unique(array_filter($existingGallery))), 0, 4);
            }
        } else {
            $data['gallery_photos'] = array_slice(array_values(array_unique(array_filter($existingGallery))), 0, 4);
        }

        // Calculate dynamic profile completion status (all 5 sections)
        $filledCount = 0;
        $fullNameVal = $data['full_name'] ?? $professional->full_name;
        $phoneVal = $data['phone'] ?? $professional->phone;
        if (!empty($fullNameVal) && !empty($phoneVal)) $filledCount++;

        $eduVal = $data['education'] ?? $professional->education;
        $expVal = $data['experience_records'] ?? $professional->experience_records;
        if (!empty($eduVal) || !empty($expVal)) $filledCount++;

        $pPhotoVal = $data['profile_photo'] ?? $professional->profile_photo;
        $gPhotosVal = $data['gallery_photos'] ?? $professional->gallery_photos;
        if (!empty($pPhotoVal) || !empty($gPhotosVal)) $filledCount++;

        $bPolicyVal = $data['booking_policy'] ?? $professional->booking_policy;
        $availVal = $data['availability'] ?? $professional->availability;
        if (!empty($bPolicyVal) || !empty($availVal)) $filledCount++;

        $oneDayRateVal = $data['one_day_rate'] ?? $professional->one_day_rate;
        $hourlyRateVal = $data['hourly_rate'] ?? $professional->hourly_rate;
        if (!empty($oneDayRateVal) || !empty($hourlyRateVal)) $filledCount++;

        $data['is_onboarded'] = ($filledCount >= 5);

        $professional->update($data);

        $activeTab = $request->input('active_tab', 'personal');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'active_tab' => $activeTab,
                'message' => 'Profile updated successfully'
            ]);
        }

        return back()->with('active_tab', $activeTab);
    }

    public function deleteGalleryPhoto(Request $request): RedirectResponse
    {
        $professional = $this->getAuthenticatedProfessional($request);

        if (!$professional) {
            return redirect()->route('staff.login');
        }

        $photoPath = $request->input('photo_path');
        if ($photoPath && is_array($professional->gallery_photos)) {
            $updated = array_values(array_filter($professional->gallery_photos, function ($p) use ($photoPath) {
                return $p !== $photoPath;
            }));
            $professional->update(['gallery_photos' => $updated]);
        }

        return back()->with('success', 'Photo removed from portfolio gallery.');
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
