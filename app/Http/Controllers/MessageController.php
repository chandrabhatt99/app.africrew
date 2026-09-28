<?php

namespace App\Http\Controllers;

use App\Events\NegotiationStatusUpdated;
use App\Events\NewMessageSent;
use App\Models\Message;
use App\Models\Professional;
use App\Models\Proposal;
use App\Models\StaffingAssignment;
use App\Models\StaffingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    private function getAuthenticatedUser(Request $request): ?User
    {
        if ($user = Auth::user()) {
            return $user;
        }

        if ($profId = $request->session()->get('professional_id')) {
            $prof = Professional::find($profId);
            if ($prof) {
                $user = User::where('email', $prof->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $prof->full_name,
                        'email' => $prof->email,
                        'password' => $prof->password ?? bcrypt('password'),
                        'role' => 'professional',
                    ]);
                }
                Auth::login($user);
                return $user;
            }
        }

        if ($clientId = $request->session()->get('client_id')) {
            $client = User::find($clientId);
            if ($client) {
                Auth::login($client);
                return $client;
            }
        }

        return null;
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return redirect()->route('staff.login');
        }

        $prof = null;
        if ($profId = session('professional_id')) {
            $prof = Professional::find($profId);
            if ($prof && !$prof->is_onboarded) {
                return redirect()->route('professional.profile')->with('warning', 'Please complete all 5 profile onboarding sections before accessing Messages.');
            }
        } else {
            $prof = Professional::where('email', $user->email)->first();
        }

        // Fetch proposals / staffing requests involving this user/professional
        $proposals = collect([]);
        if ($prof) {
            $proposals = Proposal::where('professional_id', $prof->id)
                ->with(['staffingRequest.user', 'professional'])
                ->latest()
                ->get();
        } else {
            $proposals = Proposal::whereHas('staffingRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with(['staffingRequest.user', 'professional'])->latest()->get();
        }

        $conversationsMap = [];

        // Build conversation threads from proposals
        foreach ($proposals as $prop) {
            $req = $prop->staffingRequest;
            if (!$req) continue;

            $threadKey = 'req_' . $req->id;
            $counterpartName = 'Client';
            $counterpartRole = 'Client';
            $counterpartUserId = null;
            $counterpartPhone = $req->phone_number ?: ($req->user?->phone ?: '919924424746');
            $counterpartPhoto = null;

            if ($prof) {
                // Crew user: counterpart is Client
                $counterpartName = $req->user?->name ?: ($req->full_name ?: 'Event Client');
                $counterpartRole = 'Client';
                $counterpartUserId = $req->user_id;
                $counterpartPhoto = null;
            } else {
                // Client user: counterpart is Crew
                $counterpartName = $prop->professional?->full_name ?: 'Crew Member';
                $counterpartRole = 'Crew';
                $cpUser = User::where('email', $prop->professional?->email)->first();
                $counterpartUserId = $cpUser?->id;
                $counterpartPhone = $prop->professional?->phone ?: '919924424746';
                $counterpartPhoto = $prop->professional?->profile_photo;
            }

            $lastMsg = Message::where('staffing_request_id', $req->id)->latest()->first();

            $unreadCount = Message::where('staffing_request_id', $req->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            $conversationsMap[$threadKey] = [
                'id' => $threadKey,
                'staffing_request_id' => $req->id,
                'proposal' => $prop,
                'title' => $req->event_name ?: ($req->title ?: "Order #{$req->id}"),
                'counterpart_name' => $counterpartName,
                'counterpart_role' => $counterpartRole,
                'counterpart_user_id' => $counterpartUserId,
                'counterpart_phone' => $counterpartPhone,
                'counterpart_photo' => $counterpartPhoto,
                'last_message' => $lastMsg?->message ?: '[unsupported]',
                'last_message_at' => $lastMsg?->created_at ?: $prop->created_at,
                'status' => $prop->status,
                'counter_amount' => $prop->counter_amount > 0 ? $prop->counter_amount : (($prop->custom_quote_amount ?: 0) + ($prop->travel_fee ?: 0)),
                'unread_count' => $unreadCount,
            ];
        }

        // Build conversation threads from direct messages
        $allUserMessages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        foreach ($allUserMessages as $msg) {
            if ($msg->staffing_request_id) {
                $threadKey = 'req_' . $msg->staffing_request_id;
                if (!isset($conversationsMap[$threadKey])) {
                    $req = StaffingRequest::find($msg->staffing_request_id);
                    if ($req) {
                        $otherUserId = ($msg->sender_id === $user->id) ? $msg->receiver_id : $msg->sender_id;
                        $otherUser = User::find($otherUserId);
                        $conversationsMap[$threadKey] = [
                            'id' => $threadKey,
                            'staffing_request_id' => $req->id,
                            'proposal' => Proposal::where('staffing_request_id', $req->id)->first(),
                            'title' => $req->event_name ?: "Order #{$req->id}",
                            'counterpart_name' => $otherUser?->name ?: ($prof ? 'Client' : 'Crew Member'),
                            'counterpart_role' => $otherUser?->is_admin ? 'Support' : ($prof ? 'Client' : 'Crew'),
                            'counterpart_user_id' => $otherUserId,
                            'counterpart_phone' => $otherUser?->phone ?: '919924424746',
                            'counterpart_photo' => null,
                            'last_message' => $msg->message ?: '[unsupported]',
                            'last_message_at' => $msg->created_at,
                            'status' => $req->status,
                            'counter_amount' => $req->budget,
                            'unread_count' => Message::where('staffing_request_id', $req->id)->where('receiver_id', $user->id)->where('is_read', false)->count(),
                        ];
                    }
                }
            } else {
                $otherUserId = ($msg->sender_id === $user->id) ? $msg->receiver_id : $msg->sender_id;
                if ($otherUserId) {
                    $threadKey = 'user_' . $otherUserId;
                    if (!isset($conversationsMap[$threadKey])) {
                        $otherUser = User::find($otherUserId);
                        $conversationsMap[$threadKey] = [
                            'id' => $threadKey,
                            'staffing_request_id' => null,
                            'proposal' => null,
                            'title' => $otherUser?->name ?: 'Direct Chat',
                            'counterpart_name' => $otherUser?->name ?: 'Direct Contact',
                            'counterpart_role' => $otherUser?->is_admin ? 'Support Desk' : 'Contact',
                            'counterpart_user_id' => $otherUserId,
                            'counterpart_phone' => $otherUser?->phone ?: '919924424746',
                            'counterpart_photo' => null,
                            'last_message' => $msg->message ?: '[unsupported]',
                            'last_message_at' => $msg->created_at,
                            'status' => 'direct',
                            'counter_amount' => null,
                            'unread_count' => Message::where('sender_id', $otherUserId)->where('receiver_id', $user->id)->where('is_read', false)->count(),
                        ];
                    }
                }
            }
        }

        $conversations = collect(array_values($conversationsMap))->sortByDesc('last_message_at')->values();

        // Determine active conversation
        $selectedRequestId = $request->query('request_id');
        $selectedThreadId = $request->query('thread');

        $activeConversation = null;
        if ($selectedThreadId) {
            $activeConversation = $conversations->firstWhere('id', $selectedThreadId);
        } elseif ($selectedRequestId) {
            $activeConversation = $conversations->firstWhere('staffing_request_id', $selectedRequestId);
        }

        if (!$activeConversation && $conversations->count() > 0) {
            $activeConversation = $conversations->first();
        }

        // Fetch messages for active conversation thread
        $messages = collect([]);
        if ($activeConversation) {
            if (!empty($activeConversation['staffing_request_id'])) {
                Message::where('staffing_request_id', $activeConversation['staffing_request_id'])
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = Message::where('staffing_request_id', $activeConversation['staffing_request_id'])
                    ->with(['sender', 'receiver'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            } elseif (!empty($activeConversation['counterpart_user_id'])) {
                $cpId = $activeConversation['counterpart_user_id'];
                Message::where('sender_id', $cpId)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = Message::where(function ($q) use ($user, $cpId) {
                    $q->where(function ($q2) use ($user, $cpId) {
                        $q2->where('sender_id', $user->id)->where('receiver_id', $cpId);
                    })->orWhere(function ($q2) use ($user, $cpId) {
                        $q2->where('sender_id', $cpId)->where('receiver_id', $user->id);
                    });
                })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
            }
        }

        $admins = User::where('is_admin', true)->get();

        return view('professional.messages', compact('user', 'messages', 'admins', 'conversations', 'activeConversation', 'prof', 'selectedRequestId'));
    }

    public function fetchLatest(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lastId = $request->query('last_id', 0);
        $requestId = $request->query('request_id');

        $messagesQuery = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
        ->where('id', '>', $lastId);

        if ($requestId) {
            $messagesQuery->where('staffing_request_id', $requestId);
        }

        $messages = $messagesQuery->with(['sender', 'receiver', 'proposal.staffingRequest'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                $msg->attachment_url = get_storage_url($msg->attachment_path);
                return $msg;
            });

        // Return latest proposal statuses
        $prof = Professional::where('email', $user->email)->first();
        $proposals = collect([]);
        if ($prof) {
            $proposals = Proposal::where('professional_id', $prof->id)
                ->with(['staffingRequest', 'professional'])
                ->latest()
                ->get();
        } else {
            $proposals = Proposal::whereHas('staffingRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with(['staffingRequest', 'professional'])->latest()->get();
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'proposals' => $proposals,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            abort(403);
        }

        $data = $request->validate([
            'receiver_id' => ['nullable', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'staffing_request_id' => ['nullable', 'exists:staffing_requests,id'],
            'proposal_id' => ['nullable', 'exists:proposals,id'],
        ]);

        if (empty($data['message']) && !$request->hasFile('attachment')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please enter a message or choose a file attachment.'], 422);
            }
            return back()->with('error', 'Please enter a message or choose a file attachment.');
        }

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('message_attachments', 'public');
        }

        $receiverId = $data['receiver_id'] ?? null;

        // Dynamically resolve receiver ID if not explicitly provided
        if (!$receiverId && !empty($data['staffing_request_id'])) {
            $sReq = StaffingRequest::find($data['staffing_request_id']);
            if ($sReq) {
                // Ensure client user exists
                if (!$sReq->user_id && !empty($sReq->email)) {
                    $clientUser = User::where('email', $sReq->email)->first();
                    if (!$clientUser) {
                        $clientUser = User::create([
                            'name' => $sReq->full_name ?: 'Client',
                            'email' => $sReq->email,
                            'password' => bcrypt('password'),
                            'role' => 'client',
                        ]);
                    }
                    $sReq->update(['user_id' => $clientUser->id]);
                }

                $prof = Professional::where('email', $user->email)->first();
                if ($prof) {
                    // Sender is Crew -> Receiver is Client
                    $receiverId = $sReq->user_id;
                } else {
                    // Sender is Client -> Receiver is Crew
                    $prop = Proposal::where('staffing_request_id', $sReq->id)->first();
                    if ($prop && $prop->professional) {
                        $receiverId = User::where('email', $prop->professional->email)->first()?->id;
                    }
                }
            }
        }

        if (!$receiverId && !empty($data['proposal_id'])) {
            $prop = Proposal::with(['staffingRequest', 'professional'])->find($data['proposal_id']);
            if ($prop) {
                if ($user->id === $prop->staffingRequest?->user_id) {
                    $receiverId = User::where('email', $prop->professional?->email)->first()?->id;
                } else {
                    $receiverId = $prop->staffingRequest?->user_id;
                }
            }
        }

        if (!$receiverId) {
            $receiverId = User::where('is_admin', true)->first()?->id;
        }

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'staffing_request_id' => $data['staffing_request_id'] ?? null,
            'proposal_id' => $data['proposal_id'] ?? null,
            'message' => $data['message'] ?? ($attachmentName ? "📎 Attachment: {$attachmentName}" : ''),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        try {
            event(new NewMessageSent($msg));
        } catch (\Throwable $e) {
            // Fallback if broadcast server is offline
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg->load(['sender', 'receiver'])]);
        }

        return back()->with('success', 'Message sent successfully!');
    }

    public function proposePrice(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            abort(403);
        }

        $data = $request->validate([
            'proposal_id' => ['nullable', 'exists:proposals,id'],
            'staffing_request_id' => ['nullable', 'exists:staffing_requests,id'],
            'custom_quote_amount' => ['required', 'numeric', 'min:1'],
            'travel_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $proposal = null;
        if (!empty($data['proposal_id'])) {
            $proposal = Proposal::with(['staffingRequest', 'professional'])->findOrFail($data['proposal_id']);
        } elseif (!empty($data['staffing_request_id'])) {
            $prof = Professional::where('email', $user->email)->first();
            $proposal = Proposal::firstOrCreate([
                'staffing_request_id' => $data['staffing_request_id'],
                'professional_id' => $prof?->id,
            ], [
                'custom_quote_amount' => $data['custom_quote_amount'],
                'travel_fee' => $data['travel_fee'] ?? 0,
                'status' => 'countered',
            ]);
            $proposal->load(['staffingRequest', 'professional']);
        }

        if (!$proposal) {
            return back()->with('error', 'Proposal or Request context missing.');
        }

        $travelFee = $data['travel_fee'] ?? 0;
        $totalAmount = $data['custom_quote_amount'] + $travelFee;

        // Determine counterpart receiver ID
        $receiverId = null;
        if ($user->id === $proposal->staffingRequest?->user_id) {
            // User is Client, receiver is Crew
            $receiverId = User::where('email', $proposal->professional?->email)->first()?->id;
        } else {
            // User is Crew, receiver is Client
            $receiverId = $proposal->staffingRequest?->user_id;
            if (!$receiverId && $proposal->staffingRequest?->email) {
                $clientUser = User::where('email', $proposal->staffingRequest->email)->first();
                if (!$clientUser) {
                    $clientUser = User::create([
                        'name' => $proposal->staffingRequest->full_name ?: 'Client',
                        'email' => $proposal->staffingRequest->email,
                        'password' => bcrypt('password'),
                        'role' => 'client',
                    ]);
                }
                $proposal->staffingRequest->update(['user_id' => $clientUser->id]);
                $receiverId = $clientUser->id;
            }
            if (!$receiverId) {
                $receiverId = User::where('is_admin', true)->first()?->id;
            }
        }

        // Update proposal status
        $proposal->update([
            'custom_quote_amount' => $data['custom_quote_amount'],
            'travel_fee' => $travelFee,
            'counter_amount' => $totalAmount,
            'status' => 'countered',
            'notes' => $data['notes'] ?? $proposal->notes,
        ]);

        $senderName = $user->name;
        $noteText = !empty($data['notes']) ? " Note: {$data['notes']}" : "";
        $formattedTotal = number_format($totalAmount, 2);
        $formattedBase = number_format($data['custom_quote_amount'], 2);
        $formattedTravel = number_format($travelFee, 2);
        $msgContent = "💼 Counter Price Offered by {$senderName}: Total KES {$formattedTotal} (Base: KES {$formattedBase} + Travel: KES {$formattedTravel}).{$noteText}";

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'staffing_request_id' => $proposal->staffing_request_id,
            'proposal_id' => $proposal->id,
            'message' => $msgContent,
            'negotiated_price' => $totalAmount,
            'negotiation_status' => 'countered',
            'price_breakdown' => [
                'base_rate' => $data['custom_quote_amount'],
                'travel_fee' => $travelFee,
                'total' => $totalAmount,
                'offered_by' => $user->id,
            ],
        ]);

        try {
            event(new NegotiationStatusUpdated($proposal, 'countered', $totalAmount));
            event(new NewMessageSent($msg));
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'proposal' => $proposal, 'message' => $msg]);
        }

        return back()->with('success', "Price offer of KES {$formattedTotal} submitted in chat!");
    }

    public function acceptPrice(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            abort(403);
        }

        $data = $request->validate([
            'proposal_id' => ['nullable', 'exists:proposals,id'],
            'staffing_request_id' => ['nullable', 'exists:staffing_requests,id'],
        ]);

        $proposal = null;
        if (!empty($data['proposal_id'])) {
            $proposal = Proposal::with(['staffingRequest', 'professional'])->findOrFail($data['proposal_id']);
        } elseif (!empty($data['staffing_request_id'])) {
            $prof = Professional::where('email', $user->email)->first();
            $proposal = Proposal::where('staffing_request_id', $data['staffing_request_id'])->latest()->first();
        }

        if (!$proposal) {
            return back()->with('error', 'Proposal record not found to accept.');
        }

        $agreedTotal = ($proposal->custom_quote_amount ?: 0) + ($proposal->travel_fee ?: 0);
        if ($proposal->counter_amount > 0) {
            $agreedTotal = $proposal->counter_amount;
        }

        // Lock proposal & staffing request
        $proposal->update([
            'status' => 'accepted',
        ]);

        if ($proposal->staffingRequest) {
            $proposal->staffingRequest->update([
                'status' => 'assigned',
                'budget' => $agreedTotal,
            ]);

            // Create or update StaffingAssignment
            StaffingAssignment::updateOrCreate(
                [
                    'staffing_request_id' => $proposal->staffing_request_id,
                    'professional_id' => $proposal->professional_id,
                ],
                [
                    'status' => 'accepted',
                ]
            );
        }

        // Counterpart receiver ID
        $receiverId = null;
        if ($user->id === $proposal->staffingRequest?->user_id) {
            $receiverId = User::where('email', $proposal->professional?->email)->first()?->id;
        } else {
            $receiverId = $proposal->staffingRequest?->user_id ?? User::where('is_admin', true)->first()?->id;
        }

        $formattedAgreed = number_format($agreedTotal, 2);
        $msgContent = "🎉 BOOKING CONFIRMED & LOCKED! Agreed Negotiated Price: KES {$formattedAgreed}. Both parties have agreed to terms.";

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'staffing_request_id' => $proposal->staffing_request_id,
            'proposal_id' => $proposal->id,
            'message' => $msgContent,
            'negotiated_price' => $agreedTotal,
            'negotiation_status' => 'accepted',
            'price_breakdown' => [
                'base_rate' => $proposal->custom_quote_amount,
                'travel_fee' => $proposal->travel_fee,
                'total' => $agreedTotal,
                'accepted_by' => $user->id,
            ],
        ]);

        try {
            event(new NegotiationStatusUpdated($proposal, 'accepted', $agreedTotal));
            event(new NewMessageSent($msg));
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'proposal' => $proposal, 'message' => $msg]);
        }

        return back()->with('success', "Negotiated price of KES {$formattedAgreed} accepted! Booking is now confirmed.");
    }
}
