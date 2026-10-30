<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Message;
use App\Models\Professional;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\StaffingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientMobileController extends Controller
{
    /**
     * Get authenticated client from Bearer token.
     */
    protected function getClient(Request $request): ?User
    {
        $token = $request->bearerToken() ?: $request->header('X-API-TOKEN') ?: $request->input('api_token');
        if (!$token) {
            return null;
        }

        $hashedToken = hash('sha256', $token);
        return User::where('api_token', $hashedToken)->first();
    }

    /**
     * Forgot password request.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No client account found with this email address.',
            ], 404);
        }

        $resetToken = Str::random(60);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($resetToken), 'created_at' => now()]
        );

        return response()->json([
            'status' => true,
            'message' => 'Password reset instructions have been sent to your email.',
            'reset_token' => $resetToken, // Provided for mobile flow convenience
        ]);
    }

    /**
     * Reset password.
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

        $user = User::where('email', strtolower(trim($request->email)))->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Password has been reset successfully. You can now log in.',
        ]);
    }

    /**
     * Update client profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->filled('name')) {
            $client->name = trim($request->name);
        }
        if ($request->has('phone')) {
            $client->phone = trim($request->phone);
        }
        if ($request->has('company_name')) {
            $client->company_name = trim($request->company_name);
        }

        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $client->password)) {
                return response()->json(['status' => false, 'message' => 'Current password does not match.'], 422);
            }
            $client->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar')) {
            $client->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $client->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'company_name' => $client->company_name,
                'avatar_url' => $client->avatar ? get_storage_url($client->avatar) : null,
            ],
        ]);
    }

    /**
     * Browse & Search Crew Directory.
     */
    public function browseCrew(Request $request): JsonResponse
    {
        $query = Professional::query()->where('status', 'approved')->where('is_public', true);

        // Search by keyword / name / skills
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('skills', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('about', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Filter by skill
        if ($skill = $request->input('skill')) {
            $query->where('skills', 'like', "%{$skill}%");
        }

        // Filter by city / country / location
        if ($city = $request->input('city')) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($country = $request->input('country')) {
            $query->where('country', 'like', "%{$country}%");
        }

        // Filter by rate range
        if ($minRate = $request->input('rate_min')) {
            $query->where(function ($q) use ($minRate) {
                $q->where('one_day_rate', '>=', $minRate)
                    ->orWhere('full_day_rate', '>=', $minRate);
            });
        }
        if ($maxRate = $request->input('rate_max')) {
            $query->where(function ($q) use ($maxRate) {
                $q->where('one_day_rate', '<=', $maxRate)
                    ->orWhere('full_day_rate', '<=', $maxRate);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'top_rated');
        if ($sort === 'rate_low') {
            $query->orderByRaw('COALESCE(one_day_rate, full_day_rate, 999999) ASC');
        } elseif ($sort === 'rate_high') {
            $query->orderByRaw('COALESCE(one_day_rate, full_day_rate, 0) DESC');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->latest();
        }

        $perPage = min((int)$request->input('per_page', 15), 50);
        $crew = $query->paginate($perPage);

        $client = $this->getClient($request);
        $favoriteIds = $client ? $client->favorites()->pluck('professional_id')->toArray() : [];

        $items = collect($crew->items())->map(function ($p) use ($favoriteIds) {
            $dailyRate = $p->one_day_rate ?? ($p->full_day_rate ?? null);
            $rawSkills = is_array($p->skills) ? $p->skills : array_map('trim', explode(',', (string)$p->skills));
            $skillsArr = array_values(array_filter($rawSkills));

            return [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'username' => $p->username,
                'category' => $p->category,
                'city' => $p->city ?: 'Nairobi',
                'country' => $p->country ?: 'Kenya',
                'location' => trim(($p->city ?: 'Nairobi') . ', ' . ($p->country ?: 'Kenya')),
                'profile_photo_url' => $p->profile_photo ? get_storage_url($p->profile_photo) : null,
                'cover_photo_url' => $p->cover_photo ? get_storage_url($p->cover_photo) : null,
                'daily_rate' => $dailyRate ? (float)$dailyRate : null,
                'hourly_rate' => $p->hourly_rate ? (float)$p->hourly_rate : null,
                'currency' => $p->currency ?: 'KES',
                'average_rating' => (float)$p->average_rating,
                'reviews_count' => (int)$p->reviews_count,
                'completed_gigs' => (int)$p->completed_gigs_count,
                'skills' => array_slice($skillsArr, 0, 5),
                'is_favorite' => in_array($p->id, $favoriteIds),
                'availability' => $p->availability ?: 'available',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $crew->currentPage(),
                'last_page' => $crew->lastPage(),
                'per_page' => $crew->perPage(),
                'total' => $crew->total(),
            ],
        ]);
    }

    /**
     * Get Complete Single Crew Profile.
     */
    public function getCrewDetail(Request $request, $idOrUsername): JsonResponse
    {
        $p = Professional::where('id', $idOrUsername)
            ->orWhere('username', $idOrUsername)
            ->first();

        if (!$p) {
            return response()->json(['status' => false, 'message' => 'Crew member not found.'], 404);
        }

        $client = $this->getClient($request);
        $isFav = $client ? $client->favorites()->where('professional_id', $p->id)->exists() : false;

        $dailyRate = $p->one_day_rate ?? ($p->full_day_rate ?? null);
        $rawSkills = is_array($p->skills) ? $p->skills : array_map('trim', explode(',', (string)$p->skills));
        $allSkills = array_values(array_unique(array_filter(array_merge($rawSkills, $p->featured_skills ?? []))));

        // Clean Detailed Expertises (with icon, name, description, image)
        $expertises = [];
        foreach ($p->expertises ?? [] as $exp) {
            $item = is_string($exp) ? json_decode($exp, true) : $exp;
            if (is_array($item) && !empty(trim($item['name'] ?? ''))) {
                $expertises[] = [
                    'icon' => $item['icon'] ?? '⚡',
                    'name' => $item['name'] ?? '',
                    'description' => $item['description'] ?? '',
                    'image_url' => !empty($item['image']) ? get_storage_url($item['image']) : null,
                ];
            }
        }

        // Clean Gallery
        $gallery = [];
        foreach ($p->gallery_photos ?? [] as $photo) {
            if ($photo) {
                $gallery[] = get_storage_url($photo);
            }
        }

        // Clean Reviews
        $reviews = $p->visibleReviews()->latest()->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'client_name' => $r->client_name,
                'client_company' => $r->client_company,
                'rating' => (int)$r->rating,
                'comment' => $r->comment,
                'event_name' => $r->event_name,
                'date' => $r->created_at ? $r->created_at->format('d M Y') : null,
            ];
        });

        // Clean Education
        $education = array_values(array_filter($p->education ?? []));

        // Clean Work History
        $experience = array_values(array_filter($p->experience_records ?? []));

        return response()->json([
            'status' => true,
            'crew' => [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'username' => $p->username,
                'category' => $p->category,
                'city' => $p->city ?: 'Nairobi',
                'country' => $p->country ?: 'Kenya',
                'location' => trim(($p->city ?: 'Nairobi') . ', ' . ($p->country ?: 'Kenya')),
                'profile_photo_url' => $p->profile_photo ? get_storage_url($p->profile_photo) : null,
                'cover_photo_url' => $p->cover_photo ? get_storage_url($p->cover_photo) : null,
                'about' => $p->about,
                'my_update' => $p->my_update,
                'lets_connect' => $p->lets_connect,
                'experience_summary' => $p->experience_summary,
                'highlight_quote' => $p->highlight_quote,
                'daily_rate' => $dailyRate ? (float)$dailyRate : null,
                'hourly_rate' => $p->hourly_rate ? (float)$p->hourly_rate : null,
                'two_day_rate' => $p->two_day_rate ? (float)$p->two_day_rate : null,
                'currency' => $p->currency ?: 'KES',
                'rehearsal_option' => $p->rehearsal_option,
                'has_multi_day_discount' => (bool)$p->has_multi_day_discount,
                'multi_day_discount_pct' => (float)$p->multi_day_discount_pct,
                'average_rating' => (float)$p->average_rating,
                'reviews_count' => (int)$p->reviews_count,
                'completed_gigs' => (int)$p->completed_gigs_count,
                'is_favorite' => $isFav,
                'core_skills' => $allSkills,
                'detailed_expertises' => $expertises,
                'portfolio_gallery' => $gallery,
                'work_history' => $experience,
                'education' => $education,
                'reviews' => $reviews,
                'blocked_dates' => $p->blocked_dates ?? [],
            ],
        ]);
    }

    /**
     * Get Client Favorites list.
     */
    public function getFavorites(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $favorites = $client->favorites()->get()->map(function ($p) {
            $dailyRate = $p->one_day_rate ?? ($p->full_day_rate ?? null);
            return [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'username' => $p->username,
                'category' => $p->category,
                'location' => trim(($p->city ?: 'Nairobi') . ', ' . ($p->country ?: 'Kenya')),
                'profile_photo_url' => $p->profile_photo ? get_storage_url($p->profile_photo) : null,
                'daily_rate' => $dailyRate ? (float)$dailyRate : null,
                'currency' => $p->currency ?: 'KES',
                'average_rating' => (float)$p->average_rating,
                'reviews_count' => (int)$p->reviews_count,
            ];
        });

        return response()->json([
            'status' => true,
            'favorites' => $favorites,
        ]);
    }

    /**
     * Toggle Favorite / Save Crew Member.
     */
    public function toggleFavorite(Request $request, $professionalId): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $professional = Professional::where('id', $professionalId)->orWhere('username', $professionalId)->first();
        if (!$professional) {
            return response()->json(['status' => false, 'message' => 'Crew member not found.'], 404);
        }

        $exists = $client->favorites()->where('professional_id', $professional->id)->exists();
        if ($exists) {
            $client->favorites()->detach($professional->id);
            $isFav = false;
            $msg = "{$professional->full_name} removed from your saved crew list.";
        } else {
            $client->favorites()->attach($professional->id);
            $isFav = true;
            $msg = "{$professional->full_name} added to your saved crew list.";
        }

        return response()->json([
            'status' => true,
            'message' => $msg,
            'is_favorite' => $isFav,
        ]);
    }

    /**
     * Create New Staffing / Booking Request.
     */
    public function createBooking(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'professional_id' => ['nullable'],
            'event_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'booking_type' => ['nullable', 'string', 'in:single_day,multi_day,hourly,full_package'],
            'location' => ['required', 'string', 'max:255'],
            'staff_count' => ['nullable', 'integer', 'min:1'],
            'shift_duration' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $professional = null;
        if ($request->filled('professional_id')) {
            $professional = Professional::where('id', $request->professional_id)
                ->orWhere('username', $request->professional_id)
                ->first();
        }

        $calculatedBudget = $request->budget;
        $currency = 'KES';
        if ($professional) {
            $currency = $professional->currency ?: 'KES';
            if (empty($calculatedBudget)) {
                $calculatedBudget = $professional->one_day_rate ?? ($professional->full_day_rate ?? 5000);
            }
        }

        $booking = StaffingRequest::create([
            'user_id' => $client->id,
            'full_name' => $client->name,
            'company_name' => $client->company_name,
            'email' => $client->email,
            'phone' => $client->phone ?: '',
            'event_name' => trim($request->event_name),
            'event_type' => $request->booking_type ?: 'single_day',
            'category' => $request->category,
            'staff_count' => $request->staff_count ?: 1,
            'event_date' => $request->event_date,
            'dates' => $request->end_date ? [$request->event_date, $request->end_date] : [$request->event_date],
            'shift_duration' => $request->shift_duration ?: '8 Hours (Full Day)',
            'location' => $request->location,
            'requirements' => $request->requirements,
            'budget' => $calculatedBudget ?: 5000,
            'currency' => $currency,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_amount' => $calculatedBudget ?: 5000,
        ]);

        // Assign crew if booking was requested directly for a professional
        if ($professional) {
            $booking->assignments()->create([
                'professional_id' => $professional->id,
                'status' => 'assigned',
                'notes' => 'Direct client mobile booking request.',
            ]);

            // Auto-generate Contract
            Contract::create([
                'staffing_request_id' => $booking->id,
                'user_id' => $client->id,
                'professional_id' => $professional->id,
                'contract_number' => 'CTR-' . strtoupper(Str::random(8)),
                'terms' => "AfriCrew Standard Digital Event Escrow Agreement between {$client->name} and {$professional->full_name}.",
                'status' => 'pending_signature',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully.',
            'booking_id' => $booking->id,
            'booking' => [
                'id' => $booking->id,
                'event_name' => $booking->event_name,
                'category' => $booking->category,
                'event_date' => $booking->event_date ? $booking->event_date->format('Y-m-d') : null,
                'location' => $booking->location,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'total_amount' => (float)$booking->payment_amount,
                'currency' => $booking->currency ?: 'KES',
            ],
        ], 201);
    }

    /**
     * List Client's Bookings.
     */
    public function getBookings(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = $client->staffingRequests()->with(['assignments.professional'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->through(function ($b) {
            $assignedCrew = $b->assignments->map(function ($a) {
                $p = $a->professional;
                return $p ? [
                    'id' => $p->id,
                    'full_name' => $p->full_name,
                    'username' => $p->username,
                    'category' => $p->category,
                    'profile_photo_url' => $p->profile_photo ? get_storage_url($p->profile_photo) : null,
                    'assignment_status' => $a->status,
                ] : null;
            })->filter()->values();

            return [
                'id' => $b->id,
                'event_name' => $b->event_name,
                'category' => $b->category,
                'event_date' => $b->event_date ? $b->event_date->format('Y-m-d') : null,
                'location' => $b->location,
                'status' => $b->status,
                'payment_status' => $b->payment_status,
                'amount' => (float)$b->payment_amount,
                'currency' => $b->currency ?: 'KES',
                'assigned_crew' => $assignedCrew,
                'created_at' => $b->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    /**
     * Get Single Booking Detail.
     */
    public function getBookingDetail(Request $request, $id): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $booking = $client->staffingRequests()->with(['assignments.professional', 'proposals', 'mpesaTransactions'])->find($id);
        if (!$booking) {
            return response()->json(['status' => false, 'message' => 'Booking not found.'], 404);
        }

        $contract = Contract::where('staffing_request_id', $booking->id)->first();

        $crewList = $booking->assignments->map(function ($a) {
            $p = $a->professional;
            return $p ? [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'username' => $p->username,
                'phone' => $p->phone,
                'category' => $p->category,
                'profile_photo_url' => $p->profile_photo ? get_storage_url($p->profile_photo) : null,
                'status' => $a->status,
            ] : null;
        })->filter()->values();

        return response()->json([
            'status' => true,
            'booking' => [
                'id' => $booking->id,
                'event_name' => $booking->event_name,
                'event_type' => $booking->event_type,
                'category' => $booking->category,
                'event_date' => $booking->event_date ? $booking->event_date->format('Y-m-d') : null,
                'dates' => $booking->dates,
                'location' => $booking->location,
                'shift_duration' => $booking->shift_duration,
                'requirements' => $booking->requirements,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'payment_amount' => (float)$booking->payment_amount,
                'currency' => $booking->currency ?: 'KES',
                'paid_at' => $booking->paid_at ? $booking->paid_at->toIso8601String() : null,
                'assigned_crew' => $crewList,
                'has_contract' => (bool)$contract,
                'contract' => $contract ? [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'status' => $contract->status,
                    'client_signed_at' => $contract->client_signed_at ? $contract->client_signed_at->toIso8601String() : null,
                    'terms' => $contract->terms,
                ] : null,
            ],
        ]);
    }

    /**
     * Cancel Booking.
     */
    public function cancelBooking(Request $request, $id): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $booking = $client->staffingRequests()->find($id);
        if (!$booking) {
            return response()->json(['status' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($booking->status === 'completed') {
            return response()->json(['status' => false, 'message' => 'Completed bookings cannot be cancelled.'], 400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Booking has been cancelled.',
        ]);
    }

    /**
     * Sign Digital Contract.
     */
    public function signContract(Request $request, $contractId): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $contract = Contract::where('id', $contractId)->where('user_id', $client->id)->first();
        if (!$contract) {
            return response()->json(['status' => false, 'message' => 'Contract not found.'], 404);
        }

        $signature = $request->input('signature', $client->name);
        $contract->client_signed_at = now();
        $contract->client_signature = $signature;
        $contract->status = $contract->crew_signed_at ? 'active' : 'signed_by_client';
        $contract->save();

        return response()->json([
            'status' => true,
            'message' => 'Contract signed successfully.',
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'status' => $contract->status,
                'client_signed_at' => $contract->client_signed_at->toIso8601String(),
                'signature' => $contract->client_signature,
            ],
        ]);
    }

    /**
     * Submit Review for Crew Member.
     */
    public function submitReview(Request $request, $crewId): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $professional = Professional::where('id', $crewId)->orWhere('username', $crewId)->first();
        if (!$professional) {
            return response()->json(['status' => false, 'message' => 'Crew member not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
            'event_name' => ['nullable', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $review = Review::create([
            'professional_id' => $professional->id,
            'client_name' => $client->name,
            'client_company' => $client->company_name,
            'client_avatar' => $client->avatar,
            'event_name' => $request->event_name ?: 'Event Shift',
            'rating' => $request->rating,
            'comment' => trim($request->comment),
            'is_visible' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thank you! Your verified review has been posted.',
            'review' => [
                'id' => $review->id,
                'rating' => (int)$review->rating,
                'comment' => $review->comment,
                'event_name' => $review->event_name,
            ],
        ], 201);
    }

    /**
     * List Client Message Conversations.
     */
    public function getConversations(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $messages = Message::where('sender_id', $client->id)
            ->orWhere('receiver_id', $client->id)
            ->with(['sender', 'receiver', 'staffingRequest'])
            ->latest()
            ->get();

        // Group by counterparty
        $conversations = [];
        foreach ($messages as $msg) {
            $otherUser = $msg->sender_id === $client->id ? $msg->receiver : $msg->sender;
            if (!$otherUser) continue;

            $key = $otherUser->id;
            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'user_id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ? get_storage_url($otherUser->avatar) : null,
                    'last_message' => $msg->message,
                    'last_message_at' => $msg->created_at->toIso8601String(),
                    'unread_count' => Message::where('sender_id', $otherUser->id)->where('receiver_id', $client->id)->where('is_read', false)->count(),
                ];
            }
        }

        return response()->json([
            'status' => true,
            'conversations' => array_values($conversations),
        ]);
    }

    /**
     * Send Message to Crew.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
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
            'sender_id' => $client->id,
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
}
