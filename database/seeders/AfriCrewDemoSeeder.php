<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AfriCrewDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Account
        User::updateOrCreate(
            ['email' => 'admin@africrew.com'],
            [
                'name' => 'AfriCrew Admin',
                'password' => Hash::make('Admin@123'),
                'is_admin' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Default System Categories with Category-Wise Skills
        $categoriesWithSkills = [
            [
                'category' => ['name' => 'Ushers', 'slug' => 'ushers', 'icon' => '💃', 'description' => 'Event ushering, VIP seating delegation, and guest management.'],
                'skills' => [
                    'VIP Guest Hospitality',
                    'Seating Arrangement & Delegation',
                    'Badge Scanning & Access Verification',
                    'Red Carpet & Crowd Guidance',
                    'Gift Bag & Program Distribution'
                ]
            ],
            [
                'category' => ['name' => 'Host', 'slug' => 'host', 'icon' => '🤝', 'description' => 'Protocol officers, event hosts, and registration desk coordinators.'],
                'skills' => [
                    'Guest Registration & Check-in',
                    'Protocol & Executive Escort',
                    'Front Desk Help Desk',
                    'Multilingual Information Desk',
                    'VIP Lounge Attendant'
                ]
            ],
            [
                'category' => ['name' => 'Anchor / MC', 'slug' => 'anchor-mc', 'icon' => '🎙️', 'description' => 'Professional event emcees, announcers, and stage hosts.'],
                'skills' => [
                    'Stage Hosting & Master of Ceremonies',
                    'Panel Discussion Moderation',
                    'Crowd Engagement & Warm-up',
                    'Corporate Gala Emcee',
                    'Live Event Announcements'
                ]
            ],
            [
                'category' => ['name' => 'DJ / Sound Engineer', 'slug' => 'dj-sound', 'icon' => '🎧', 'description' => 'Event disk jockeys and live audio engineers.'],
                'skills' => [
                    'Live DJ Set Performance',
                    'PA System Setup & Sound Check',
                    'Wireless Microphone Management',
                    'Audio Mixing & Equalization',
                    'Stage Lighting Coordination'
                ]
            ],
            [
                'category' => ['name' => 'Security Staff', 'slug' => 'security-staff', 'icon' => '🛡️', 'description' => 'Venue access control, bouncers, and crowd safety officers.'],
                'skills' => [
                    'VIP Bodyguard Escort',
                    'Crowd Control & Gate Security',
                    'Metal Detector & Search Check',
                    'Emergency Evacuation Control',
                    'Perimeter Security Monitoring'
                ]
            ],
            [
                'category' => ['name' => 'Photographer / Videographer', 'slug' => 'media-crew', 'icon' => '📸', 'description' => 'Event photography, videography, and media coverage.'],
                'skills' => [
                    'Event Red Carpet Photography',
                    '4K Live Event Videography',
                    'Drone Aerial Shot Capture',
                    'Same-Day Photo Editing',
                    'Social Media Highlights Reel'
                ]
            ],
        ];

        foreach ($categoriesWithSkills as $item) {
            $category = Category::updateOrCreate(
                ['slug' => $item['category']['slug']],
                $item['category']
            );

            foreach ($item['skills'] as $skillName) {
                Skill::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($skillName)
                    ],
                    [
                        'name' => $skillName,
                        'description' => "Official skill for {$category->name}",
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
