<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $professionals = Professional::where('status', 'approved')
            ->orderByDesc('experience_years')
            ->take(10)
            ->get();

        if ($professionals->isEmpty()) {
            $professionals = Professional::where('status', 'approved')
                ->latest()
                ->take(10)
                ->get();
        }

        $categories = \App\Models\Category::where('is_active', true)->get();

        // Get approved crew with non-empty city
        $approvedCrew = Professional::where('status', 'approved')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->get(['id', 'category', 'city']);

        $parentCategoryNames = [
            'Ushers',
            'Technical Crew',
            'Decorations',
            'Entertainers',
            'Multi-Media & Brand Activations'
        ];

        $allCategoryNames = array_unique(array_merge(['All'], $parentCategoryNames, $categories->pluck('name')->toArray()));

        $allCities = $approvedCrew->pluck('city')->unique()->values()->all();
        if (empty($allCities)) {
            $allCities = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret'];
        }

        $categoryLocations = [
            'All' => $allCities
        ];

        foreach ($allCategoryNames as $catName) {
            if ($catName === 'All') continue;

            $matchingCities = $approvedCrew->filter(function ($crew) use ($catName) {
                $crewCat = strtolower(trim($crew->category ?? ''));
                $searchCat = strtolower(trim($catName));

                if ($crewCat === $searchCat) return true;
                if (!empty($crewCat) && !empty($searchCat) && (str_contains($crewCat, $searchCat) || str_contains($searchCat, $crewCat))) return true;

                // Category & Parent Category variations
                if ((str_contains($searchCat, 'usher') || str_contains($searchCat, 'host') || str_contains($searchCat, 'protocol')) && (str_contains($crewCat, 'usher') || str_contains($crewCat, 'host') || str_contains($crewCat, 'protocol'))) return true;
                if ((str_contains($searchCat, 'tech') || str_contains($searchCat, 'sound') || str_contains($searchCat, 'av') || str_contains($searchCat, 'rigger')) && (str_contains($crewCat, 'tech') || str_contains($crewCat, 'sound') || str_contains($crewCat, 'av') || str_contains($crewCat, 'rigger') || str_contains($crewCat, 'dj'))) return true;
                if ((str_contains($searchCat, 'decor') || str_contains($searchCat, 'stage') || str_contains($searchCat, 'floral')) && (str_contains($crewCat, 'decor') || str_contains($crewCat, 'stage') || str_contains($crewCat, 'design'))) return true;
                if ((str_contains($searchCat, 'entertainer') || str_contains($searchCat, 'mc') || str_contains($searchCat, 'anchor') || str_contains($searchCat, 'dj') || str_contains($searchCat, 'band')) && (str_contains($crewCat, 'entertain') || str_contains($crewCat, 'mc') || str_contains($crewCat, 'anchor') || str_contains($crewCat, 'dj') || str_contains($crewCat, 'music'))) return true;
                if ((str_contains($searchCat, 'media') || str_contains($searchCat, 'brand') || str_contains($searchCat, 'photo') || str_contains($searchCat, 'video')) && (str_contains($crewCat, 'media') || str_contains($crewCat, 'brand') || str_contains($crewCat, 'photo') || str_contains($crewCat, 'video') || str_contains($crewCat, 'ambassador'))) return true;

                return false;
            })->pluck('city')->unique()->values()->all();

            $categoryLocations[$catName] = empty($matchingCities) ? $allCities : $matchingCities;
        }

        $allApprovedCrewList = Professional::where('status', 'approved')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'username' => $p->username,
                'category' => $p->category ?: 'Event Staff',
                'city' => $p->city ?: 'Nairobi',
                'country' => $p->country ?: 'Kenya',
                'experience_years' => $p->experience_years ?: 0,
                'profile_photo_url' => $p->profile_photo_url,
                'hourly_rate' => $p->hourly_rate ?: 0,
                'one_day_rate' => $p->one_day_rate ?: ($p->full_day_rate ?: 0),
                'currency' => $p->currency ?: 'KES',
                'average_rating' => number_format($p->average_rating, 1),
                'raw_rating' => (float) $p->average_rating,
                'reviews_count' => (int) $p->reviews_count,
                'completed_gigs_count' => $p->completed_gigs_count ?: 0,
                'skills' => $p->skills ?: '',
            ];
        })->values();

        $locations = $allCities;

        return view('home', compact('professionals', 'categories', 'locations', 'categoryLocations', 'allApprovedCrewList'));
    }
}


