<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientApiController extends Controller
{
    /**
     * Register a new Client for the Mobile App.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $apiToken = Str::random(60);

        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'role' => 'client',
            'api_token' => hash('sha256', $apiToken),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Client registered successfully.',
            'token' => $apiToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $user->company_name,
                'role' => $user->role,
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Login Client for the Mobile App.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Admin accounts must use the Admin Web Portal.',
            ], 403);
        }

        $apiToken = Str::random(60);
        $user->update([
            'api_token' => hash('sha256', $apiToken),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Client logged in successfully.',
            'token' => $apiToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $user->company_name,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * Get Authenticated Client Profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedClient($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated or invalid token.',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $user->company_name,
                'role' => $user->role,
                'total_bookings' => $user->staffingRequests()->count(),
                'saved_crew_count' => $user->favorites()->count(),
            ],
        ], 200);
    }

    /**
     * Client Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedClient($request);

        if ($user) {
            $user->update(['api_token' => null]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Helper to authenticate client token from Authorization Bearer header.
     */
    private function getAuthenticatedClient(Request $request): ?User
    {
        $token = $request->bearerToken() ?: $request->header('X-API-TOKEN') ?: $request->input('api_token');
        if (!$token) {
            return null;
        }

        $hashedToken = hash('sha256', $token);
        return User::where('api_token', $hashedToken)->first();
    }
}
