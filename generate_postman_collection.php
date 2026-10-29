<?php

/**
 * AfriCrew Mobile API Postman Collection Generator (v2.1.0)
 * Generates comprehensive Postman collection covering both Client and Crew Mobile flows.
 */

$collection = [
    'info' => [
        '_postman_id' => 'africrew-mobile-api-v1-' . uniqid(),
        'name' => 'AfriCrew Mobile Apps API Collection (Client & Crew Complete Suite)',
        'description' => "Complete Postman API Collection for AfriCrew Mobile Applications.\n\nCovers the entire lifecycle for both:\n1. Client Mobile Application (Discovery, Booking, M-Pesa Escrow, Contracts, Messaging, Reviews)\n2. Crew Mobile Application (Onboarding, Status, Profile Builder, Expertises, Shifts, Wallet/Payouts, Messaging, Support)\n3. Shared Catalog & M-Pesa Payment Gateway\n\nTokens are automatically captured in {{clientToken}} and {{crewToken}} upon login or registration.",
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        [
            'key' => 'baseUrl',
            'value' => 'http://127.0.0.1:8000',
            'type' => 'string',
            'description' => 'Target server URL (use http://127.0.0.1:8000 for local development, or https://africrew-crm.databrainit.com for production)',
        ],
        [
            'key' => 'clientToken',
            'value' => '',
            'type' => 'string',
            'description' => 'Bearer token for authenticated Client',
        ],
        [
            'key' => 'crewToken',
            'value' => '',
            'type' => 'string',
            'description' => 'Bearer token for authenticated Crew Member / Professional',
        ],
        [
            'key' => 'bookingId',
            'value' => '1',
            'type' => 'string',
            'description' => 'Sample booking / staffing request ID',
        ],
        [
            'key' => 'contractId',
            'value' => '1',
            'type' => 'string',
            'description' => 'Sample contract ID',
        ],
        [
            'key' => 'shiftId',
            'value' => '1',
            'type' => 'string',
            'description' => 'Sample shift assignment ID',
        ],
        [
            'key' => 'crewUsername',
            'value' => 'joyce01',
            'type' => 'string',
            'description' => 'Sample crew username for profile lookup',
        ],
    ],
    'item' => [],
];

// Helper to make bearer header
function bearerHeader($varName = 'clientToken') {
    return [
        [
            'key' => 'Authorization',
            'value' => 'Bearer {{' . $varName . '}}',
            'type' => 'text',
        ],
        [
            'key' => 'Accept',
            'value' => 'application/json',
            'type' => 'text',
        ],
    ];
}

function jsonHeader($authVar = null) {
    $headers = [
        [
            'key' => 'Accept',
            'value' => 'application/json',
            'type' => 'text',
        ],
        [
            'key' => 'Content-Type',
            'value' => 'application/json',
            'type' => 'text',
        ],
    ];
    if ($authVar) {
        $headers[] = [
            'key' => 'Authorization',
            'value' => 'Bearer {{' . $authVar . '}}',
            'type' => 'text',
        ];
    }
    return $headers;
}

function tokenCaptureScript($varName) {
    return [
        'listen' => 'test',
        'script' => [
            'type' => 'text/javascript',
            'exec' => [
                'var jsonData = pm.response.json();',
                'if (jsonData && jsonData.token) {',
                '    pm.environment.set("' . $varName . '", jsonData.token);',
                '    pm.collectionVariables.set("' . $varName . '", jsonData.token);',
                '    console.log("Token saved to ' . $varName . ': " + jsonData.token);',
                '}',
            ],
        ],
    ];
}

// =========================================================================
// 1. CLIENT MOBILE APP FOLDER
// =========================================================================
$clientFolder = [
    'name' => '1. Client Mobile App',
    'description' => 'Endpoints for Event Organizers, Corporate Clients, and Individual Hosts to browse, hire, contract, and pay crew.',
    'item' => [
        // Auth Subfolder
        [
            'name' => '1.1 Authentication & Profile',
            'item' => [
                [
                    'name' => 'Client Register',
                    'event' => [tokenCaptureScript('clientToken')],
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Sarah Mutua',
                                'company_name' => 'AfriEvents Ltd',
                                'email' => 'sarah.client@example.com',
                                'phone' => '+254711223344',
                                'password' => 'password123',
                                'password_confirmation' => 'password123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/register',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'register'],
                        ],
                        'description' => 'Register a new Client account. Automatically saves {{clientToken}}.',
                    ],
                ],
                [
                    'name' => 'Client Login',
                    'event' => [tokenCaptureScript('clientToken')],
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'email' => 'sarah.client@example.com',
                                'password' => 'password123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/login',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'login'],
                        ],
                        'description' => 'Log in as a Client. Automatically saves {{clientToken}}.',
                    ],
                ],
                [
                    'name' => 'Get Client Profile',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/profile',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'profile'],
                        ],
                        'description' => 'Retrieve current client profile details and active booking summary.',
                    ],
                ],
                [
                    'name' => 'Update Client Profile',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Sarah Mutua-Kariuki',
                                'company_name' => 'AfriEvents International',
                                'phone' => '+254711998877',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/profile',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'profile'],
                        ],
                        'description' => 'Update client name, company name, phone, or avatar.',
                    ],
                ],
                [
                    'name' => 'Client Forgot Password',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'email' => 'sarah.client@example.com',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/forgot-password',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'forgot-password'],
                        ],
                        'description' => 'Initiate password reset flow for client.',
                    ],
                ],
                [
                    'name' => 'Client Reset Password',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'email' => 'sarah.client@example.com',
                                'token' => 'sample_reset_token',
                                'password' => 'newpassword123',
                                'password_confirmation' => 'newpassword123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/reset-password',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'reset-password'],
                        ],
                        'description' => 'Submit new password with verification token.',
                    ],
                ],
                [
                    'name' => 'Client Logout',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/logout',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'logout'],
                        ],
                        'description' => 'Revoke current client API token.',
                    ],
                ],
            ],
        ],

        // Talent Discovery Subfolder
        [
            'name' => '1.2 Talent Discovery & Profiles',
            'item' => [
                [
                    'name' => 'Browse / Search Crew Directory',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/crew?category=Ushers&city=Nairobi&sort=rating_desc&page=1',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'crew'],
                            'query' => [
                                ['key' => 'category', 'value' => 'Ushers', 'description' => 'Filter by category (e.g. Ushers, Hospitality, Sound Engineers)'],
                                ['key' => 'city', 'value' => 'Nairobi', 'description' => 'Filter by city'],
                                ['key' => 'country', 'value' => 'Kenya', 'disabled' => true],
                                ['key' => 'skill', 'value' => 'Guest Check-in', 'disabled' => true],
                                ['key' => 'min_rate', 'value' => '1000', 'disabled' => true],
                                ['key' => 'max_rate', 'value' => '10000', 'disabled' => true],
                                ['key' => 'search', 'value' => 'Joyce', 'disabled' => true],
                                ['key' => 'sort', 'value' => 'rating_desc', 'description' => 'rating_desc, rate_asc, rate_desc, newest'],
                                ['key' => 'page', 'value' => '1'],
                            ],
                        ],
                        'description' => 'Search and filter vetted event crew with pagination and sorting.',
                    ],
                ],
                [
                    'name' => 'Get Detailed Crew Profile',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/crew/{{crewUsername}}',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'crew', '{{crewUsername}}'],
                        ],
                        'description' => 'Get rich crew profile including Bio, Rates, Detailed Expertise (with icons and images), Portfolio Gallery, Work History, Education, and Verified Client Reviews. Note: Confidential referees are excluded from public client viewing.',
                    ],
                ],
                [
                    'name' => 'Get Saved Favorite Crew',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/favorites',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'favorites'],
                        ],
                        'description' => 'List all crew members bookmarked/favorited by client.',
                    ],
                ],
                [
                    'name' => 'Toggle Favorite Crew',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/favorites/1/toggle',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'favorites', '1', 'toggle'],
                        ],
                        'description' => 'Bookmark or remove crew member from client favorites list.',
                    ],
                ],
            ],
        ],

        // Bookings & Shifts Subfolder
        [
            'name' => '1.3 Bookings & Gigs Management',
            'item' => [
                [
                    'name' => 'Create Booking / Staffing Request',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'event_name' => 'East Africa Tech Summit 2026',
                                'event_type' => 'Corporate Conference',
                                'category' => 'Ushers',
                                'staff_count' => 3,
                                'location' => 'Sarit Expo Centre, Nairobi',
                                'event_date' => '2026-11-15',
                                'start_time' => '08:00',
                                'end_time' => '17:00',
                                'shift_duration' => '9 hours',
                                'budget' => 6000.00,
                                'currency' => 'KES',
                                'rate_type' => 'fixed',
                                'requirements' => 'Professional VIP guest reception, badge scanning, and hall coordination.',
                                'crew_ids' => [1],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/bookings',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'bookings'],
                        ],
                        'description' => 'Create a new staffing request for an event and optionally assign crew members.',
                    ],
                ],
                [
                    'name' => 'List Client Bookings',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/bookings?status=all&page=1',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'bookings'],
                            'query' => [
                                ['key' => 'status', 'value' => 'all', 'description' => 'new, confirmed, completed, cancelled, all'],
                                ['key' => 'page', 'value' => '1'],
                            ],
                        ],
                        'description' => 'Fetch all bookings created by the authenticated client.',
                    ],
                ],
                [
                    'name' => 'Get Booking Details',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/bookings/{{bookingId}}',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'bookings', '{{bookingId}}'],
                        ],
                        'description' => 'Detailed view of an event booking including assigned crew, contract status, and payment summary.',
                    ],
                ],
                [
                    'name' => 'Cancel Booking',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/bookings/{{bookingId}}/cancel',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'bookings', '{{bookingId}}', 'cancel'],
                        ],
                        'description' => 'Cancel an active or pending booking.',
                    ],
                ],
            ],
        ],

        // Digital Contracts Subfolder
        [
            'name' => '1.4 Digital Contracts',
            'item' => [
                [
                    'name' => 'Sign Digital Contract (Client)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'signature' => 'Sarah Mutua (Authorized Director)',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/contracts/{{contractId}}/sign',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'contracts', '{{contractId}}', 'sign'],
                        ],
                        'description' => 'Digitally sign a staffing agreement contract as client.',
                    ],
                ],
            ],
        ],

        // Reviews Subfolder
        [
            'name' => '1.5 Verified Reviews & Ratings',
            'item' => [
                [
                    'name' => 'Submit Review for Crew',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'rating' => 5,
                                'comment' => 'Outstanding professional! Punctual, articulate, and handled VIP delegates effortlessly.',
                                'event_name' => 'East Africa Tech Summit 2026',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/crew/1/reviews',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'crew', '1', 'reviews'],
                        ],
                        'description' => 'Submit a 5-star rating and written review for a crew member after an event.',
                    ],
                ],
            ],
        ],

        // Messaging Subfolder
        [
            'name' => '1.6 In-App Direct Chat',
            'item' => [
                [
                    'name' => 'List Client Conversations',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/messages',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'messages'],
                        ],
                        'description' => 'List active chat threads with crew members.',
                    ],
                ],
                [
                    'name' => 'Send Message to Crew',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'receiver_id' => 1,
                                'message' => 'Hello Joyce, looking forward to working with you at the Tech Summit!',
                                'staffing_request_id' => 1,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/client/messages',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'client', 'messages'],
                        ],
                        'description' => 'Send real-time chat message to a crew member.',
                    ],
                ],
            ],
        ],

        // M-Pesa Escrow Subfolder
        [
            'name' => '1.7 M-Pesa Escrow Payment',
            'item' => [
                [
                    'name' => 'Initiate STK Push (Lipa na M-Pesa)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'phone_number' => '254712345678',
                                'amount' => 6000,
                                'staffing_request_id' => 1,
                                'account_reference' => 'AFRICREW-101',
                                'transaction_desc' => 'Escrow Booking Payment',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/mpesa/stkpush',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'mpesa', 'stkpush'],
                        ],
                        'description' => 'Send M-Pesa STK PIN prompt to client phone to fund event escrow.',
                    ],
                ],
                [
                    'name' => 'Check STK Payment Status',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('clientToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/mpesa/status/ws_CO_SAMPLE_CHECKOUT_ID',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'mpesa', 'status', 'ws_CO_SAMPLE_CHECKOUT_ID'],
                        ],
                        'description' => 'Poll payment status after STK Push prompt is sent to client phone.',
                    ],
                ],
                [
                    'name' => 'Simulate Payment Success (Sandbox/Local)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('clientToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'checkout_request_id' => 'ws_CO_SAMPLE_CHECKOUT_ID',
                                'mpesa_receipt_number' => 'QWE123RTY',
                                'staffing_request_id' => 1,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/mpesa/simulate-success',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'mpesa', 'simulate-success'],
                        ],
                        'description' => 'Simulate successful M-Pesa callback for local development.',
                    ],
                ],
            ],
        ],
    ],
];

// =========================================================================
// 2. CREW MOBILE APP FOLDER
// =========================================================================
$crewFolder = [
    'name' => '2. Crew Mobile App',
    'description' => 'Complete mobile API suite for Crew / Professionals: profile setup, expertises, shift offers, wallet, contract signing, and chat.',
    'item' => [
        // Crew Auth Subfolder
        [
            'name' => '2.1 Authentication & Status',
            'item' => [
                [
                    'name' => 'Crew Register',
                    'event' => [tokenCaptureScript('crewToken')],
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'full_name' => 'Joyce Omwamba',
                                'username' => 'joyce' . rand(10, 99),
                                'email' => 'joyce' . rand(100, 999) . '@example.com',
                                'phone' => '+254700112233',
                                'category' => 'Ushers',
                                'gender' => 'Female',
                                'city' => 'Nairobi',
                                'country' => 'Kenya',
                                'password' => 'password123',
                                'password_confirmation' => 'password123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/register',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'register'],
                        ],
                        'description' => 'Register a new Crew Member. Automatically saves {{crewToken}}.',
                    ],
                ],
                [
                    'name' => 'Crew Login',
                    'event' => [tokenCaptureScript('crewToken')],
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'login' => 'joyce01', // accepts email or username or phone
                                'password' => 'password123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/login',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'login'],
                        ],
                        'description' => 'Log in as a Crew Member using email, username, or phone. Saves {{crewToken}}.',
                    ],
                ],
                [
                    'name' => 'Get Crew Profile',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile'],
                        ],
                        'description' => 'Get full profile of authenticated crew member including rates, expertises, education, and wallet.',
                    ],
                ],
                [
                    'name' => 'Check Pipeline Status & Progress',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/status',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'status'],
                        ],
                        'description' => 'Quick status check returning profile completion %, interview schedule, approval pipeline status, and wallet snapshot.',
                    ],
                ],
                [
                    'name' => 'Crew Forgot Password',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'email' => 'joyce01@example.com',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/forgot-password',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'forgot-password'],
                        ],
                        'description' => 'Request password reset for crew member.',
                    ],
                ],
                [
                    'name' => 'Crew Reset Password',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader(),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'email' => 'joyce01@example.com',
                                'token' => 'sample_reset_token',
                                'password' => 'newpassword123',
                                'password_confirmation' => 'newpassword123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/reset-password',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'reset-password'],
                        ],
                        'description' => 'Set new password with token.',
                    ],
                ],
                [
                    'name' => 'Crew Logout',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/logout',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'logout'],
                        ],
                        'description' => 'Revoke current crew session token.',
                    ],
                ],
            ],
        ],

        // Profile Builder Subfolder
        [
            'name' => '2.2 Profile Builder & Settings',
            'item' => [
                [
                    'name' => 'Update Bio, Rates & Core Details',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'full_name' => 'Joyce Omwamba',
                                'phone' => '+254700112233',
                                'gender' => 'Female',
                                'city' => 'Nairobi',
                                'country' => 'Kenya',
                                'one_day_rate' => 5000.00,
                                'two_day_rate' => 9000.00,
                                'rehearsal_rate' => 2000.00,
                                'working_hours' => 8,
                                'currency' => 'KES',
                                'skills' => 'Guest Welcoming, VIP Protocol, Ticket Scanning, Crowd Management',
                                'featured_skills' => ['VIP Protocol', 'Crowd Management'],
                                'about' => 'Senior Lead Usher with over 5 years experience across high-level diplomatic, corporate, and private luxury events in East Africa.',
                                'my_update' => 'Available for Q4 corporate conferences and international summits.',
                                'lets_connect' => 'Ready to elevate your event? Let us collaborate to ensure a flawless guest experience.',
                                'experience_summary' => 'Executed 120+ events with 99.8% satisfaction rating.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/update',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'update'],
                        ],
                        'description' => 'Update personal information, day rates, working hours, core skills, bio, daily update, and invitation text.',
                    ],
                ],
                [
                    'name' => 'Update Detailed Expertise Areas (Icon + Header + Content + Image)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'expertises' => [
                                    [
                                        'name' => 'VIP Protocol & Delegations',
                                        'description' => 'Coordination for ministerial delegations, high net-worth individuals, and keynote dignitaries.',
                                        'icon' => 'ri-vip-crown-line',
                                        'image' => null,
                                    ],
                                    [
                                        'name' => 'POS & Digital Ticket Scanning',
                                        'description' => 'High-speed attendee badge validation using RFID, QR, and offline-capable scanners.',
                                        'icon' => 'ri-qr-code-line',
                                        'image' => null,
                                    ],
                                    [
                                        'name' => 'Crowd Logistics & Hall Directing',
                                        'description' => 'Smooth auditorium seating management, emergency exits oversight, and orderly dismissals.',
                                        'icon' => 'ri-group-line',
                                        'image' => null,
                                    ],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/expertises',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'expertises'],
                        ],
                        'description' => 'Save detailed expertise areas with Icon, Header/Name, Description Content, and optional showcase image.',
                    ],
                ],
                [
                    'name' => 'Upload Portfolio / Gallery Photo',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'body' => [
                            'mode' => 'formdata',
                            'formdata' => [
                                [
                                    'key' => 'photo',
                                    'type' => 'file',
                                    'src' => '',
                                    'description' => 'Select an event action photo (JPEG, PNG, max 5MB)',
                                ],
                            ],
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/gallery',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'gallery'],
                        ],
                        'description' => 'Upload a photo to your Instagram-style portfolio gallery grid.',
                    ],
                ],
                [
                    'name' => 'Delete Portfolio Photo',
                    'request' => [
                        'method' => 'DELETE',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'photo_path' => 'professionals/gallery/sample_photo.jpg',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/gallery',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'gallery'],
                        ],
                        'description' => 'Remove an image from crew portfolio.',
                    ],
                ],
                [
                    'name' => 'Update Education & Qualifications',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'show_education_on_cv' => true,
                                'education' => [
                                    [
                                        'degree' => 'Bachelor of Hospitality & Event Management',
                                        'institution' => 'Kenyatta University',
                                        'year' => '2021',
                                        'document' => null,
                                    ],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/education',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'education'],
                        ],
                        'description' => 'Save educational degrees, institutions, graduation years, and optional certificates.',
                    ],
                ],
                [
                    'name' => 'Update Work Experience Records',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'experience_records' => [
                                    [
                                        'role' => 'Lead Protocol Usher',
                                        'company' => 'Nairobi Convention Center',
                                        'duration' => '2022 - Present',
                                        'description' => 'Managed 15 ushers across international summits and presidential galas.',
                                        'document' => null,
                                    ],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/experience',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'experience'],
                        ],
                        'description' => 'Save verified work experience records with roles, companies, and durations.',
                    ],
                ],
                [
                    'name' => 'Update Confidential Referees (Admin Verification Only)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'referees' => [
                                    [
                                        'name' => 'David Kariuki',
                                        'phone' => '+254722334455',
                                        'email' => 'david@eventshub.ke',
                                        'organization' => 'Events Hub Kenya',
                                        'relationship' => 'Event Operations Director',
                                    ],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/referees',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'referees'],
                        ],
                        'description' => 'Submit professional referees for AfriCrew background verification. (Strictly hidden from public client view).',
                    ],
                ],
                [
                    'name' => 'Upload ID, Resume & Photos',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'body' => [
                            'mode' => 'formdata',
                            'formdata' => [
                                [
                                    'key' => 'profile_photo',
                                    'type' => 'file',
                                    'src' => '',
                                    'description' => 'Avatar profile photo (JPEG/PNG)',
                                ],
                                [
                                    'key' => 'cover_photo',
                                    'type' => 'file',
                                    'src' => '',
                                    'description' => 'Cover banner photo',
                                ],
                                [
                                    'key' => 'resume',
                                    'type' => 'file',
                                    'src' => '',
                                    'description' => 'Curriculum Vitae / Resume (PDF)',
                                ],
                                [
                                    'key' => 'government_id',
                                    'type' => 'file',
                                    'src' => '',
                                    'description' => 'National ID / Passport (Confidential to admin for KYC)',
                                ],
                            ],
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/profile/upload-documents',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'profile', 'upload-documents'],
                        ],
                        'description' => 'Upload Avatar photo, Cover photo, Resume CV, and Government ID document for KYC.',
                    ],
                ],
                [
                    'name' => 'Suggest New Skill to Admin',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'skill_name' => 'Simultaneous Translation Assistance',
                                'category_name' => 'Ushers',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/skills/suggest',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'skills', 'suggest'],
                        ],
                        'description' => 'Propose a new skill to AfriCrew administrators for platform-wide addition.',
                    ],
                ],
            ],
        ],

        // Shifts & Gig Management Subfolder
        [
            'name' => '2.3 Shifts & Event Gigs',
            'item' => [
                [
                    'name' => 'Get My Shift Assignments',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/shifts?status=assigned',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'shifts'],
                            'query' => [
                                ['key' => 'status', 'value' => 'assigned', 'description' => 'assigned (pending offers), accepted, checked_in, completed, declined'],
                            ],
                        ],
                        'description' => 'List all gig offers and confirmed shifts for the crew member.',
                    ],
                ],
                [
                    'name' => 'Get Shift Details',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/shifts/{{shiftId}}',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'shifts', '{{shiftId}}'],
                        ],
                        'description' => 'Detailed view of an event shift including client details, timings, location, and contract.',
                    ],
                ],
                [
                    'name' => 'Respond to Shift Offer (Accept or Decline)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'response' => 'accept', // accept or decline
                                'notes' => 'Confirmed availability for shift.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/shifts/{{shiftId}}/respond',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'shifts', '{{shiftId}}', 'respond'],
                        ],
                        'description' => 'Accept or decline an event shift offer sent by a client or admin.',
                    ],
                ],
                [
                    'name' => 'Shift Check-In (Clock In on Event Day)',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/shifts/{{shiftId}}/check-in',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'shifts', '{{shiftId}}', 'check-in'],
                        ],
                        'description' => 'Clock-in upon arrival at event venue.',
                    ],
                ],
                [
                    'name' => 'Complete Shift (Mark Shift Fulfilled)',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/shifts/{{shiftId}}/complete',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'shifts', '{{shiftId}}', 'complete'],
                        ],
                        'description' => 'Mark shift as completed upon finishing duties to trigger escrow release.',
                    ],
                ],
                [
                    'name' => 'Sign Digital Contract (Crew)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'signature' => 'Joyce Omwamba',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/contracts/{{contractId}}/sign',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'contracts', '{{contractId}}', 'sign'],
                        ],
                        'description' => 'Digitally sign shift contract as crew member.',
                    ],
                ],
            ],
        ],

        // Wallet, Escrow & Payouts Subfolder
        [
            'name' => '2.4 Wallet, Earnings & Payouts',
            'item' => [
                [
                    'name' => 'Get Wallet Overview',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/wallet',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'wallet'],
                        ],
                        'description' => 'Get available balance, pending escrow funds, total withdrawn, and maximum 50% advance withdrawal limit.',
                    ],
                ],
                [
                    'name' => 'Save Payout Method (M-Pesa)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'type' => 'mobile_money',
                                'mobile_money' => [
                                    'provider' => 'mpesa',
                                    'phone_number' => '+254700112233',
                                    'account_name' => 'Joyce Omwamba',
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/wallet/payout-method',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'wallet', 'payout-method'],
                        ],
                        'description' => 'Save Safaricom M-Pesa phone number for instant mobile wallet disbursements.',
                    ],
                ],
                [
                    'name' => 'Save Payout Method (Bank Account)',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'type' => 'bank_account',
                                'bank_account' => [
                                    'bank_name' => 'Equity Bank Kenya',
                                    'account_name' => 'Joyce Omwamba',
                                    'account_number' => '0180299887766',
                                    'branch_code' => '068',
                                    'swift_ifsc' => 'EQBLKENA',
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/wallet/payout-method',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'wallet', 'payout-method'],
                        ],
                        'description' => 'Save commercial bank account details for direct wire transfers.',
                    ],
                ],
                [
                    'name' => 'Request Payout / Advance Withdrawal',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'amount' => 3000.00,
                                'payout_source' => 'saved_mobile', // saved_mobile, saved_bank, custom
                                'payout_method' => 'mpesa',
                                'is_advance' => true, // supports up to 50% advance on pending escrow
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/wallet/withdraw',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'wallet', 'withdraw'],
                        ],
                        'description' => 'Submit a withdrawal request (including up to 50% advance on pending escrow gigs).',
                    ],
                ],
                [
                    'name' => 'Get Payout Transaction History',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/wallet/transactions?page=1',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'wallet', 'transactions'],
                            'query' => [
                                ['key' => 'page', 'value' => '1'],
                            ],
                        ],
                        'description' => 'List past withdrawal requests with status (pending, approved, processed, rejected).',
                    ],
                ],
            ],
        ],

        // Reviews Subfolder
        [
            'name' => '2.5 Reviews & Profile Ratings',
            'item' => [
                [
                    'name' => 'Get My Received Reviews',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/reviews',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'reviews'],
                        ],
                        'description' => 'List all client reviews left on your profile with average rating.',
                    ],
                ],
                [
                    'name' => 'Toggle Review Visibility',
                    'request' => [
                        'method' => 'POST',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/reviews/1/toggle-visibility',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'reviews', '1', 'toggle-visibility'],
                        ],
                        'description' => 'Show or hide a specific review from your public profile.',
                    ],
                ],
            ],
        ],

        // In-App Chat & Rate Proposals Subfolder
        [
            'name' => '2.6 In-App Chat & Rate Negotiation',
            'item' => [
                [
                    'name' => 'List Crew Conversations',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/messages',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'messages'],
                        ],
                        'description' => 'List chat conversations with event clients.',
                    ],
                ],
                [
                    'name' => 'Send Message to Client',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'receiver_id' => 1,
                                'message' => 'Hello Sarah, I received the event details. What time is the pre-briefing on Saturday?',
                                'staffing_request_id' => 1,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/messages',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'messages'],
                        ],
                        'description' => 'Send direct chat message to client regarding an event.',
                    ],
                ],
                [
                    'name' => 'Propose Custom Rate / Quotation to Client',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'receiver_id' => 1,
                                'staffing_request_id' => 1,
                                'proposed_rate' => 6500.00,
                                'note' => 'I would love to lead your ushering team. Since this is an 11-hour shift including rehearsal, I propose KES 6,500.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/messages/propose-rate',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'messages', 'propose-rate'],
                        ],
                        'description' => 'Send a formal rate counter-proposal / custom quotation to an event organizer.',
                    ],
                ],
            ],
        ],

        // Support & Help Desk Subfolder
        [
            'name' => '2.7 Support & Help Desk',
            'item' => [
                [
                    'name' => 'Get My Support Tickets',
                    'request' => [
                        'method' => 'GET',
                        'header' => bearerHeader('crewToken'),
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/support/tickets',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'support', 'tickets'],
                        ],
                        'description' => 'List all inquiries and support tickets filed with AfriCrew support.',
                    ],
                ],
                [
                    'name' => 'Create Support Ticket',
                    'request' => [
                        'method' => 'POST',
                        'header' => jsonHeader('crewToken'),
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'subject' => 'Question regarding event overtime payment',
                                'category' => 'Payouts',
                                'priority' => 'medium',
                                'description' => 'The Saturday gala ran 2 hours past the scheduled time. How do we claim overtime pay?',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => [
                            'raw' => '{{baseUrl}}/api/v1/crew/support/tickets',
                            'host' => ['{{baseUrl}}'],
                            'path' => ['api', 'v1', 'crew', 'support', 'tickets'],
                        ],
                        'description' => 'Submit a ticket to AfriCrew administrative team.',
                    ],
                ],
            ],
        ],
    ],
];

// =========================================================================
// 3. COMMON CATALOG & SYSTEM LOOKUPS FOLDER
// =========================================================================
$commonFolder = [
    'name' => '3. Common Catalog & System Lookups',
    'description' => 'Public lookups for categories, category-wise skills, and system parameters.',
    'item' => [
        [
            'name' => 'Get Categories with Skills',
            'request' => [
                'method' => 'GET',
                'header' => [
                    ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text'],
                ],
                'url' => [
                    'raw' => '{{baseUrl}}/api/v1/categories-with-skills',
                    'host' => ['{{baseUrl}}'],
                    'path' => ['api', 'v1', 'categories-with-skills'],
                ],
                'description' => 'Returns all talent categories alongside their approved skills for mobile dropdowns.',
            ],
        ],
        [
            'name' => 'Get Skills for Specific Category',
            'request' => [
                'method' => 'GET',
                'header' => [
                    ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text'],
                ],
                'url' => [
                    'raw' => '{{baseUrl}}/api/v1/categories/Ushers/skills',
                    'host' => ['{{baseUrl}}'],
                    'path' => ['api', 'v1', 'categories', 'Ushers', 'skills'],
                ],
                'description' => 'Returns list of skills specifically mapped to a given category.',
            ],
        ],
    ],
];

$collection['item'] = [
    $clientFolder,
    $crewFolder,
    $commonFolder,
];

$jsonContent = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

$targetPath = __DIR__ . '/public/africrew_mobile_api_collection.json';
file_put_contents($targetPath, $jsonContent);

echo "Successfully generated Postman Collection at: " . $targetPath . " (" . strlen($jsonContent) . " bytes)\n";
