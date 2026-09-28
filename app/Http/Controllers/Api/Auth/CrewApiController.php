<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CrewApiController extends Controller
{
    /**
     * Register a new Crew Member / Professional for Mobile App.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:professionals,username'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:professionals,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'category' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $apiToken = Str::random(60);

        $fullNameClean = ucwords(mb_strtolower(trim($request->full_name)));
        $nameParts = explode(' ', $fullNameClean, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $professional = Professional::create([
            'full_name' => $fullNameClean,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => trim($request->username),
            'email' => strtolower(trim($request->email)),
            'phone' => trim($request->phone),
            'category' => trim($request->category),
            'gender' => $request->gender ?? null,
            'city' => $request->city ?: 'Nairobi',
            'country' => $request->country ?: 'Kenya',
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_onboarded' => true,
            'status' => 'approved',
            'api_token' => hash('sha256', $apiToken),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Crew member registered successfully.',
            'token' => $apiToken,
            'token_type' => 'Bearer',
            'crew' => [
                'id' => $professional->id,
                'full_name' => $professional->full_name,
                'username' => $professional->username,
                'email' => $professional->email,
                'phone' => $professional->phone,
                'category' => $professional->category,
                'city' => $professional->city,
                'country' => $professional->country,
                'status' => $professional->status,
                'is_onboarded' => (bool)$professional->is_onboarded,
                'profile_photo_url' => $professional->profile_photo_url,
                'created_at' => $professional->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Login Crew Member / Professional for Mobile App.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string'], // Accepts Email or Username or Phone
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $loginInput = trim($request->login);

        $professional = Professional::where('email', strtolower($loginInput))
            ->orWhere('username', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (!$professional || !Hash::check($request->password, $professional->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The provided credentials do not match our crew records.',
            ], 401);
        }

        if ($professional->status === 'suspended' || $professional->status === 'rejected') {
            return response()->json([
                'status' => false,
                'message' => 'Your crew account is currently suspended or not approved.',
            ], 403);
        }

        $apiToken = Str::random(60);
        $professional->update([
            'api_token' => hash('sha256', $apiToken),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Crew member logged in successfully.',
            'token' => $apiToken,
            'token_type' => 'Bearer',
            'crew' => [
                'id' => $professional->id,
                'full_name' => $professional->full_name,
                'username' => $professional->username,
                'email' => $professional->email,
                'phone' => $professional->phone,
                'category' => $professional->category,
                'city' => $professional->city,
                'country' => $professional->country,
                'status' => $professional->status,
                'is_onboarded' => (bool)$professional->is_onboarded,
                'profile_photo_url' => $professional->profile_photo_url,
                'average_rating' => (float)$professional->average_rating,
                'reviews_count' => $professional->reviews_count,
                'wallet_balance' => (float)$professional->wallet_balance,
                'wallet_pending' => (float)$professional->wallet_pending,
            ],
        ], 200);
    }

    /**
     * Get Authenticated Crew Profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $professional = $this->getAuthenticatedCrew($request);

        if (!$professional) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated or invalid token.',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'crew' => [
                'id' => $professional->id,
                'full_name' => $professional->full_name,
                'first_name' => $professional->first_name,
                'last_name' => $professional->last_name,
                'username' => $professional->username,
                'email' => $professional->email,
                'phone' => $professional->phone,
                'gender' => $professional->gender,
                'category' => $professional->category,
                'city' => $professional->city,
                'state' => $professional->state,
                'country' => $professional->country,
                'skills' => $professional->skills,
                'about' => $professional->about,
                'highlight_quote' => $professional->highlight_quote,
                'hourly_rate' => (float)$professional->hourly_rate,
                'one_day_rate' => (float)$professional->one_day_rate,
                'rate_type' => $professional->rate_type,
                'currency' => $professional->currency,
                'profile_photo_url' => $professional->profile_photo_url,
                'cover_photo_url' => $professional->cover_photo_url,
                'average_rating' => (float)$professional->average_rating,
                'reviews_count' => $professional->reviews_count,
                'completed_gigs_count' => $professional->completed_gigs_count,
                'on_time_rate' => $professional->on_time_rate,
                'wallet_balance' => (float)$professional->wallet_balance,
                'wallet_pending' => (float)$professional->wallet_pending,
                'wallet_withdrawn' => (float)$professional->wallet_withdrawn,
                'payment_details' => $professional->payment_details,
                'is_onboarded' => (bool)$professional->is_onboarded,
                'status' => $professional->status,
            ],
        ], 200);
    }

    /**
     * Crew Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $professional = $this->getAuthenticatedCrew($request);

        if ($professional) {
            $professional->update(['api_token' => null]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Helper to authenticate crew token from Authorization Bearer header.
     */
    private function getAuthenticatedCrew(Request $request): ?Professional
    {
        $token = $request->bearerToken() ?: $request->header('X-API-TOKEN') ?: $request->input('api_token');
        if (!$token) {
            return null;
        }

        $hashedToken = hash('sha256', $token);
        return Professional::where('api_token', $hashedToken)->first();
    }
}
