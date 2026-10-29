<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RefereeConsentController extends Controller
{
    /**
     * Show the reference review form
     */
    public function review(string $token)
    {
        $token = trim($token);
        if (empty($token)) {
            abort(404, 'Invalid verification token.');
        }

        $professional = Professional::where('referees', 'LIKE', '%' . $token . '%')->first();

        if (!$professional || !is_array($professional->referees)) {
            return view('referee.expired');
        }

        $refereeData = null;
        foreach ($professional->referees as $ref) {
            $item = is_string($ref) ? json_decode($ref, true) : $ref;
            if (is_array($item) && ($item['consent_token'] ?? '') === $token) {
                $refereeData = $item;
                break;
            }
        }

        if (!$refereeData) {
            return view('referee.expired');
        }

        return view('referee.review', [
            'professional' => $professional,
            'referee' => $refereeData,
            'token' => $token,
        ]);
    }

    /**
     * Handle referee submission (verify or unverify)
     */
    public function submitReview(Request $request, string $token)
    {
        $token = trim($token);
        if (empty($token)) {
            abort(404, 'Invalid verification token.');
        }

        $professional = Professional::where('referees', 'LIKE', '%' . $token . '%')->first();

        if (!$professional || !is_array($professional->referees)) {
            return view('referee.expired');
        }

        $referees = $professional->referees;
        $matchedIdx = null;
        $refereeData = null;

        foreach ($referees as $idx => $ref) {
            $item = is_string($ref) ? json_decode($ref, true) : $ref;
            if (is_array($item) && ($item['consent_token'] ?? '') === $token) {
                $matchedIdx = $idx;
                $refereeData = $item;
                break;
            }
        }

        if ($matchedIdx === null) {
            return view('referee.expired');
        }

        $action = $request->input('action', 'verify');

        if ($action === 'verify') {
            $refereeData['status'] = 'verified';
            $refereeData['is_confirmed'] = true;
            $refereeData['verified_at'] = now()->toDateTimeString();
            $refereeData['show_on_cv'] = (bool)$request->has('show_on_cv');
            $refereeData['suggested_correction'] = trim($request->input('suggested_correction', ''));
            
            $rating = $request->input('rating');
            $refereeData['rating'] = (!empty($rating) && is_numeric($rating) && $rating >= 1 && $rating <= 5) ? (int)$rating : null;
            $refereeData['comments'] = trim($request->input('comments', ''));

            // Clear unverified fields
            $refereeData['unverified_reason'] = null;
            $refereeData['unverified_notes'] = null;

            $referees[$matchedIdx] = $refereeData;
            $professional->update(['referees' => $referees]);

            return view('referee.confirmed', [
                'professional' => $professional,
                'referee' => $refereeData,
            ]);
        } else {
            // Action is unverify
            $reason = $request->input('unverified_reason');
            if (!in_array($reason, ['employment_not_confirmed', 'name_removal_request'])) {
                $reason = 'employment_not_confirmed';
            }

            $refereeData['status'] = 'unverified';
            $refereeData['is_confirmed'] = false;
            $refereeData['unverified_at'] = now()->toDateTimeString();
            $refereeData['unverified_reason'] = $reason;
            $refereeData['unverified_notes'] = trim($request->input('unverified_notes', ''));
            $refereeData['show_on_cv'] = false;

            $referees[$matchedIdx] = $refereeData;
            $professional->update(['referees' => $referees]);

            return view('referee.unverified', [
                'professional' => $professional,
                'referee' => $refereeData,
            ]);
        }
    }

    public function confirmConsent(string $token)
    {
        return redirect()->route('referee.review', ['token' => $token]);
    }

    public function declineConsent(string $token)
    {
        return redirect()->route('referee.review', ['token' => $token]);
    }
}
