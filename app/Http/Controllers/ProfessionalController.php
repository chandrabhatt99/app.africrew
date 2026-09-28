<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    /**
     * Display public catalog of vetted crew members.
     */
    public function publicIndex(Request $request): View
    {
        $category = $request->query('category');
        $search = $request->query('search');
        $gender = $request->query('gender');
        $minAge = $request->query('min_age');
        $maxAge = $request->query('max_age');
        $city = $request->query('city');
        $state = $request->query('state');
        $country = $request->query('country');
        $language = $request->query('language');
        $skill = $request->query('skill');
        $rateType = $request->query('rate_type');
        $currency = $request->query('currency');
        $expRange = $request->query('exp_range');
        $minRating = $request->query('min_rating');
        $sort = $request->query('sort', 'best_match');

        $query = Professional::where('status', 'approved')->where('is_onboarded', true);

        if (!empty($category) && $category !== 'All') {
            $query->where(function ($q) use ($category) {
                $q->where('category', $category)
                  ->orWhere('category', 'like', '%' . trim(rtrim($category, 's')) . '%');
            });
        }

        if (!empty($gender) && $gender !== 'Any') {
            $query->where('gender', $gender);
        }

        if (!empty($country)) {
            $query->where('country', 'like', "%{$country}%");
        }

        if (!empty($state)) {
            $query->where('state', 'like', "%{$state}%");
        }

        if (!empty($city)) {
            $query->where('city', 'like', "%{$city}%");
        }

        if (!empty($language)) {
            $query->where(function ($q) use ($language) {
                $q->where('languages', 'like', "%{$language}%");
            });
        }

        if (!empty($skill)) {
            $query->where('skills', 'like', "%{$skill}%");
        }

        if (!empty($rateType) && $rateType !== 'All') {
            $query->where('rate_type', $rateType);
        }

        if (!empty($currency) && $currency !== 'All') {
            $query->where('currency', $currency);
        }

        if (!empty($minAge)) {
            $maxDob = now()->subYears((int) $minAge)->format('Y-m-d');
            $query->where('date_of_birth', '<=', $maxDob);
        }

        if (!empty($maxAge)) {
            $minDob = now()->subYears((int) $maxAge + 1)->format('Y-m-d');
            $query->where('date_of_birth', '>=', $minDob);
        }

        if (!empty($expRange)) {
            if ($expRange === '1-3') {
                $query->whereBetween('experience_years', [1, 3]);
            } elseif ($expRange === '4-7') {
                $query->whereBetween('experience_years', [4, 7]);
            } elseif ($expRange === '8+') {
                $query->where('experience_years', '>=', 8);
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($sort === 'rating') {
            $query->orderByDesc('id'); // Will be sorted by avg rating in collection if needed
        } elseif ($sort === 'experience') {
            $query->orderByDesc('experience_years');
        } else {
            $query->orderByDesc('experience_years')->orderByDesc('id');
        }

        if (!empty($minRating)) {
            $ratingVal = (float) $minRating;
            $query->whereHas('reviews', function ($rq) use ($ratingVal) {
                $rq->select('professional_id')
                   ->groupBy('professional_id')
                   ->havingRaw('AVG(rating) >= ?', [$ratingVal]);
            });
        }

        $professionals = $query->paginate(10)->withQueryString();

        $categories = \App\Models\Category::where('is_active', true)->get();

        $availableCities = Professional::where('status', 'approved')
            ->where('is_onboarded', true)
            ->when(!empty($category) && $category !== 'All', function ($q) use ($category) {
                $q->where(function ($sq) use ($category) {
                    $sq->where('category', $category)
                       ->orWhere('category', 'like', '%' . trim(rtrim($category, 's')) . '%');
                });
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->pluck('city')
            ->unique()
            ->values();

        return view('crew.index', compact(
            'professionals', 'category', 'search', 'categories', 'availableCities',
            'gender', 'minAge', 'maxAge', 'city', 'state', 'country', 'language', 'skill', 'rateType', 'currency',
            'expRange', 'minRating', 'sort'
        ));
    }

    /**
     * Display an individual vetted staff profile page (e.g. /crew/Alpha, /crew/Patrick or /crew/1).
     */
    public function publicShow(Request $request, $professional)
    {
        $model = null;

        // 1. Try resolving by username
        $model = Professional::where('username', 'LIKE', $professional)->first();

        // 2. Try resolving by numeric ID
        if (!$model && is_numeric($professional)) {
            $model = Professional::find($professional);
        }

        // 3. Try resolving by full name
        if (!$model) {
            $model = Professional::where('full_name', 'LIKE', $professional)->first();
        }

        $sessionProfId = session('professional_id');
        $isAdmin = auth()->check() && (auth()->user()->is_admin ?? false);

        // Check approval unless viewing own profile or logged in as admin
        if ($model && $model->status !== 'approved' && $sessionProfId != $model->id && !$isAdmin) {
            $model = null;
        }

        if (!$model) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Professional staff profile not found or pending approval.'], 404);
            }
            abort(404, 'Professional staff profile not found or pending approval.');
        }

        $model->load(['reviews' => function ($query) {
            $query->latest();
        }]);

        $professional = $model;

        if ($request->wantsJson() || $request->ajax() || $request->query('json') === '1') {
            return response()->json([
                'id' => $professional->id,
                'full_name' => $professional->full_name,
                'first_name' => strtok($professional->full_name, ' '),
                'username' => $professional->username ?: Str::slug($professional->full_name),
                'category' => $professional->category ?: 'Event Usher & Host',
                'city' => $professional->city ?: 'Nairobi',
                'country' => $professional->country ?: 'Kenya',
                'experience_years' => $professional->experience_years ?: 5,
                'average_rating' => number_format($professional->average_rating ?: 4.9, 1),
                'reviews_count' => $professional->reviews_count,
                'completed_gigs_count' => $professional->completed_gigs_count ?: 142,
                'on_time_rate' => $professional->on_time_rate ?: '98%',
                'about' => $professional->about ?: "Hello, I'm {$professional->full_name}. With over {$professional->experience_years} years of experience across corporate galas, product launches, and high-profile conferences, I bring professionalism and elegance to every event.",
                'highlight_quote' => $professional->highlight_quote ?: '"Guests rarely remember who showed them their seat — but they always remember how they were treated."',
                'profile_photo_url' => $professional->profile_photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
                'cover_photo_url' => $professional->cover_photo_url ?: asset('images/hero_event_ushers.jpg'),
                'full_day_rate' => number_format($professional->full_day_rate ?: 8500),
                'currency' => $professional->currency ?: 'KES',
                'skills' => is_array($professional->skills) ? implode(', ', $professional->skills) : ($professional->skills ?: 'Guest check-in, Registration, VIP Assistance'),
                'reviews' => $professional->reviews->take(5)->map(function ($r) {
                    return [
                        'client_name' => $r->client_name,
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                        'date' => $r->created_at->format('M Y'),
                    ];
                }),
            ]);
        }

        // Check if current user can review (completed booking check)
        $canReview = false;
        $user = auth()->user();
        if ($user) {
            $canReview = \App\Models\StaffingAssignment::where('professional_id', $professional->id)
                ->where('status', 'completed')
                ->whereHas('staffingRequest', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhere('email', $user->email);
                })->exists();
        }

        return view('crew.show', compact('professional', 'canReview'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('join-crew', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:professionals,username'],
            'category' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:professionals,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
            'social_links' => ['nullable', 'array'],
            'terms' => ['accepted'],
        ]);

        $professional = Professional::create([
            'full_name' => ucwords(mb_strtolower(trim($data['full_name']))),
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'category' => $data['category'],
            'social_links' => $data['social_links'] ?? [],
            'password' => Hash::make($data['password']),
            'verification_token' => null,
            'email_verified_at' => now(),
            'is_onboarded' => true,
            'status' => 'pending',
            'country' => 'Kenya',
            'city' => 'Nairobi',
        ]);

        $request->session()->regenerate();
        $request->session()->put('professional_id', $professional->id);

        return redirect()->route('professional.profile')->with('success', "Welcome to AfriCrew, {$professional->full_name}! Your staff account has been created. Please complete your profile details below.");
    }

    /**
     * Store a client review for a professional.
     */
    public function storeReview(Request $request, $professional): RedirectResponse
    {
        $prof = Professional::findOrFail($professional);

        $user = auth()->user();
        $clientEmail = $request->input('client_email', $user ? $user->email : null);

        // Requirement 9.2: Only allow reviews after booking and completion
        $hasCompletedBooking = \App\Models\StaffingAssignment::where('professional_id', $prof->id)
            ->where('status', 'completed')
            ->whereHas('staffingRequest', function ($q) use ($user, $clientEmail) {
                if ($user) {
                    $q->where('user_id', $user->id)->orWhere('email', $user->email);
                } elseif ($clientEmail) {
                    $q->where('email', $clientEmail);
                }
            })->exists();

        if (!$hasCompletedBooking) {
            return redirect()->back()->with('review_error', 'Reviews are restricted to clients who have completed an event booking with this professional.');
        }

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'client_company' => ['nullable', 'string', 'max:150'],
            'event_name' => ['nullable', 'string', 'max:200'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $prof->reviews()->create($validated);

        return redirect()->back()->with('review_success', 'Thank you! Your verified client review has been published.');
    }
}
