@extends('layouts.staff')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

@php
    $isThreadSelected = request()->has('thread') || request()->has('request_id');
@endphp

<!-- Outer Desktop & Mobile Responsive Messenger Container -->
<div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl overflow-hidden h-[calc(100vh-6.5rem)] sm:h-[calc(100vh-7.5rem)] lg:h-[calc(100vh-8.5rem)] min-h-[500px] grid grid-cols-1 lg:grid-cols-12 mb-4 sm:mb-8">

    <!-- LEFT COLUMN: WhatsApp Web Style Inbox Sidebar (Full width on mobile if no active thread, 4 cols on lg) -->
    <div class="{{ $isThreadSelected ? 'hidden lg:flex' : 'flex' }} lg:col-span-4 border-r border-slate-200/80 flex-col h-full bg-white min-h-0">
        
        <!-- Header & Search Bar -->
        <div class="p-3.5 sm:p-4 border-b border-slate-100 bg-white space-y-2.5 sm:space-y-3 shrink-0">
            <div class="flex items-center justify-between">
                <h2 class="text-lg sm:text-xl font-bold text-[#111b21] tracking-tight">Messages</h2>
            </div>

            <!-- WhatsApp Search Capsule Input -->
            <div class="relative">
                <input type="text" id="conversation-search" onkeyup="filterConversations()" placeholder="Search chats..." class="w-full bg-[#f0f2f5] border-0 rounded-full pl-9 pr-4 py-2 sm:py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 sm:top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- Scrollable Conversation List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-0.5 min-h-0">
            @forelse($conversations ?? [] as $conv)
                @php
                    $isActive = isset($activeConversation) && $activeConversation['id'] === $conv['id'];
                    $prop = $conv['proposal'];
                    $status = $conv['status'];
                    $msgTime = !empty($conv['last_message_at']) ? date('h:i A', strtotime($conv['last_message_at'])) : '';
                    $unread = $conv['unread_count'] ?? 0;
                @endphp
                <a href="{{ route('messages') }}?thread={{ $conv['id'] }}" 
                   class="conv-card block p-2.5 sm:p-3 rounded-xl border-l-4 transition-all text-decoration-none {{ $isActive ? 'border-[#00a884] bg-[#f0f2f5]' : 'border-transparent hover:bg-slate-50' }}"
                   data-search="{{ strtolower($conv['counterpart_name'] . ' ' . $conv['title']) }}">
                    
                    <div class="flex items-center gap-3">
                        <!-- Soft Green Circular User Avatar -->
                        <div class="relative shrink-0">
                            @if(!empty($conv['counterpart_photo']))
                                <img src="{{ get_storage_url($conv['counterpart_photo']) }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border border-emerald-200/50">
                            @else
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#dcfce7] text-[#16a34a] flex items-center justify-center border border-emerald-200/50 shadow-2xs">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#16a34a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Content Details -->
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5 sm:mb-1">
                                <h4 class="text-xs sm:text-sm font-bold text-[#111b21] truncate">{{ $conv['counterpart_name'] }}</h4>
                                <span class="text-[10px] sm:text-xs text-slate-400 font-normal shrink-0">{{ $msgTime }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] sm:text-xs text-slate-500 font-normal truncate flex-1 leading-snug">
                                    {{ $conv['last_message'] ?: '[unsupported]' }}
                                </p>
                                @if($unread > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-[#22c55e] text-white text-[10px] sm:text-[11px] font-bold min-w-[18px] text-center shrink-0 shadow-2xs">
                                        {{ $unread }}
                                    </span>
                                @elseif($status === 'accepted')
                                    <span class="w-4 h-4 rounded-full bg-emerald-500 text-white font-bold text-[9px] flex items-center justify-center shrink-0">✓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-16 text-slate-400 text-xs italic space-y-2">
                    <span class="text-3xl block">💬</span>
                    <p class="font-bold text-slate-700">No Chats Found</p>
                    <p class="text-[11px]">When clients book you or send messages, chat threads appear here.</p>
                </div>
            @endforelse
        </div>

    </div>

    <!-- RIGHT COLUMN: WhatsApp Web Desktop & Mobile Chat Window (Full width on mobile if active thread, 8 cols on lg) -->
    <div class="{{ $isThreadSelected ? 'flex' : 'hidden lg:flex' }} lg:col-span-8 flex-col h-full bg-[#efeae2] min-h-0">
        
        @if(isset($activeConversation) && $activeConversation)
            @php
                $activeProp = $activeConversation['proposal'];
                $isAccepted = $activeProp ? ($activeProp->status === 'accepted' || ($activeProp->staffingRequest && $activeProp->staffingRequest->status === 'accepted')) : false;
                $activePrice = $activeConversation['counter_amount'] ?? ($activeProp?->counter_amount > 0 ? $activeProp->counter_amount : (($activeProp?->custom_quote_amount ?: 0) + ($activeProp?->travel_fee ?: 0)));
                $counterpartPhone = $activeConversation['counterpart_phone'] ?? '919924424746';
            @endphp

            <!-- WhatsApp Top Chat Header Bar -->
            <div class="p-2.5 sm:p-3.5 px-3 sm:px-5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 shadow-2xs z-10">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <!-- Mobile Back Button -->
                    <a href="{{ route('messages') }}" class="lg:hidden p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-full transition-all text-decoration-none shrink-0" title="Back to Chats List">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>

                    <div class="relative shrink-0">
                        @if(!empty($activeConversation['counterpart_photo']))
                            <img src="{{ get_storage_url($activeConversation['counterpart_photo']) }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-emerald-200/50">
                        @else
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#dcfce7] text-[#16a34a] flex items-center justify-center border border-emerald-200/50 shadow-2xs">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#16a34a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap min-w-0">
                            <h3 class="text-xs sm:text-sm font-bold text-[#111b21] leading-tight truncate">{{ $activeConversation['counterpart_name'] }}</h3>
                            <span class="text-[10px] sm:text-xs font-normal text-slate-400 truncate hidden sm:inline">{{ $counterpartPhone }}</span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-[#22c55e] font-semibold mt-0.5">Online</p>
                    </div>
                </div>

                <!-- Right Header Action Icons removed per user request -->
            </div>

            <!-- Compact Price Negotiation Banner inside Chat Header -->
            @if($activeProp)
                @php
                    $isClient = $user->role === 'client' || ($activeProp->staffingRequest && $activeProp->staffingRequest->user_id === $user->id);
                    
                    // Find the last counter offer message in the chat stream (if any)
                    $lastCounterMsg = $messages->where('negotiation_status', 'countered')->last();
                    
                    if ($lastCounterMsg) {
                        // Counter offer was made; sender is offerer
                        $isOfferSender = ($lastCounterMsg->sender_id === $user->id);
                    } else {
                        // Initial quote state; client submitted the booking request with initial rate
                        $isOfferSender = $isClient;
                    }
                @endphp

                @if($isAccepted)
                    <div class="bg-emerald-50 border-b border-emerald-200 p-2 sm:p-2.5 px-3 sm:px-5 flex flex-wrap items-center justify-between gap-2 shrink-0">
                        <div>
                            <span class="text-[11px] sm:text-xs font-bold text-emerald-950">🤝 Agreed Rate: <strong class="text-emerald-700 font-extrabold">KES {{ number_format($activePrice, 2) }}</strong></span>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] sm:text-xs flex items-center gap-1 shadow-2xs">
                                ✓ Booking Rate Locked
                            </span>
                        </div>
                    </div>
                @elseif($isOfferSender)
                    <div class="bg-amber-50 border-b border-amber-200 p-2 sm:p-2.5 px-3 sm:px-5 flex flex-wrap items-center justify-between gap-2 shrink-0">
                        <div>
                            <span class="text-[11px] sm:text-xs font-bold text-amber-950">🤝 Rate Proposal: <strong class="text-emerald-700 font-extrabold">KES {{ number_format($activePrice, 2) }}</strong></span>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded-lg bg-amber-200/80 text-amber-950 font-bold text-[11px] sm:text-xs flex items-center gap-1 shadow-2xs border border-amber-300">
                                ⏳ Awaiting {{ $isClient ? 'Crew' : 'Client' }} Response
                            </span>
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 border-b border-amber-200 p-2 sm:p-2.5 px-3 sm:px-5 flex flex-wrap items-center justify-between gap-2 shrink-0">
                        <div>
                            <span class="text-[11px] sm:text-xs font-bold text-amber-950">🤝 Rate Proposal: <strong class="text-emerald-700 font-extrabold">KES {{ number_format($activePrice, 2) }}</strong></span>
                        </div>

                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <button type="button" 
                                onclick="openCounterModal({{ $activeProp->id }}, {{ $activeProp->custom_quote_amount ?: 3500 }}, {{ $activeProp->travel_fee ?: 0 }})" 
                                class="px-2.5 sm:px-3 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[11px] sm:text-xs shadow-2xs border-0 transition-all cursor-pointer">
                                💬 Counter
                            </button>

                            <form method="POST" action="{{ route('messages.acceptPrice') }}">
                                @csrf
                                <input type="hidden" name="proposal_id" value="{{ $activeProp->id }}">
                                <button type="submit" onclick="return confirm('Accept this price quote and confirm event booking?')" class="px-3 sm:px-3.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] sm:text-xs shadow-2xs transition-all border-0 cursor-pointer">
                                    ✓ Accept Quote
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Chat Stream with WhatsApp Wallpaper Background -->
            <div id="message-stream" 
                 class="flex-1 overflow-y-auto custom-scrollbar p-3 sm:p-5 lg:p-6 space-y-3 relative min-h-0" 
                 style="background-color: #efeae2; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px;">
                
                @if(session('success'))
                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs max-w-md mx-auto text-center shadow-2xs">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @forelse($messages as $msg)
                    @php
                        $isSelf = $msg->sender_id === $user->id;
                        $msgTime = $msg->created_at ? $msg->created_at->format('h:i A') : 'Just now';
                    @endphp
                    <div class="flex flex-col {{ $isSelf ? 'items-end' : 'items-start' }}" id="msg-{{ $msg->id }}">
                        
                        <!-- Responsive WhatsApp Message Bubble -->
                        <div class="max-w-[88%] sm:max-w-md lg:max-w-xl p-3 sm:p-4 text-xs shadow-2xs relative break-words text-wrap {{ $isSelf ? 'bg-[#d9fdd3] text-slate-900 rounded-2xl rounded-tr-none border border-emerald-200/60' : 'bg-white text-slate-900 rounded-2xl rounded-tl-none border border-slate-200/60' }}">
                            
                            <!-- Sender Name Label (if incoming) -->
                            @if(!$isSelf)
                                <div class="text-[11px] font-bold text-amber-700 mb-1 flex items-center justify-between gap-3">
                                    <span>{{ $msg->sender?->name ?: $activeConversation['counterpart_name'] }}</span>
                                </div>
                            @endif

                            <!-- Negotiation Status Badge inside Chat Bubble -->
                            @if($msg->negotiation_status === 'countered')
                                <div class="mb-2 p-2 sm:p-2.5 rounded-xl bg-amber-500/15 border border-amber-400/30 text-amber-950 font-bold text-[10px] sm:text-[11px] flex items-center justify-between gap-2">
                                    <span>💼 Counter Price:</span>
                                    <span class="font-extrabold text-emerald-800">KES {{ number_format($msg->negotiated_price ?: 0, 2) }}</span>
                                </div>
                            @elseif($msg->negotiation_status === 'accepted')
                                <div class="mb-2 p-2 sm:p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-600/30 text-emerald-950 font-extrabold text-[10px] sm:text-[11px] flex items-center justify-between gap-2">
                                    <span>🎉 Booking Price Locked:</span>
                                    <span class="font-extrabold text-emerald-800">KES {{ number_format($msg->negotiated_price ?: 0, 2) }}</span>
                                </div>
                            @endif

                            <!-- Message Body Text -->
                            @if(!empty($msg->message))
                                <p class="leading-relaxed whitespace-pre-wrap text-slate-900 font-normal text-xs break-words overflow-hidden">{!! nl2br(e($msg->message)) !!}</p>
                            @endif

                            <!-- File Attachment Download Pill -->
                            @if($msg->attachment_path)
                                <a href="{{ get_storage_url($msg->attachment_path) }}" target="_blank" download class="flex items-center gap-2 p-2 sm:p-2.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-900 text-xs font-bold my-1.5 text-decoration-none hover:bg-slate-200 transition-all max-w-full">
                                    <span class="text-base shrink-0">📎</span>
                                    <span class="truncate flex-1 min-w-0">{{ $msg->attachment_name ?: 'Download File Attachment' }}</span>
                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-md shrink-0">Download ↓</span>
                                </a>
                            @endif

                            <span class="text-[10px] text-slate-400 font-normal block text-right mt-1 sm:mt-1.5">{{ $msgTime }}</span>
                        </div>
                    </div>
                @empty
                    <div id="no-messages-prompt" class="text-center py-16 text-slate-400 text-xs italic space-y-2">
                        <span class="text-3xl block">💬</span>
                        <p class="font-bold text-slate-700">Start the Conversation</p>
                        <p class="text-[11px]">Send a message below to discuss shift details, timings, or event logistics.</p>
                    </div>
                @endforelse
            </div>

            <!-- WhatsApp Input Composer Footer Bar -->
            <div class="p-2 sm:p-3 bg-[#f0f2f5] border-t border-slate-200/80 shrink-0">
                <form id="chat-send-form" method="POST" action="{{ route('messages.send') }}" enctype="multipart/form-data" class="flex items-center gap-2 sm:gap-3 relative">
                    @csrf
                    @if(!empty($activeConversation['staffing_request_id']))
                        <input type="hidden" name="staffing_request_id" value="{{ $activeConversation['staffing_request_id'] }}">
                    @endif
                    @if(!empty($activeConversation['counterpart_user_id']))
                        <input type="hidden" name="receiver_id" value="{{ $activeConversation['counterpart_user_id'] }}">
                    @endif
                    @if(!empty($activeConversation['proposal']))
                        <input type="hidden" name="proposal_id" value="{{ $activeConversation['proposal']->id }}">
                    @endif

                    <input type="file" id="chat-attachment-input" name="attachment" onchange="previewSelectedAttachment(this)" class="hidden">
                    <div id="attachment-preview-badge" class="hidden absolute -top-12 left-0 right-0 items-center justify-between bg-amber-50 border border-amber-200 p-2 px-3.5 rounded-xl text-xs font-bold text-amber-900 shadow-2xs">
                        <span class="flex items-center gap-2 truncate min-w-0">
                            <span class="text-base">📎</span>
                            <span id="attachment-name-text" class="text-slate-900 truncate"></span>
                        </span>
                        <button type="button" onclick="clearSelectedAttachment()" class="text-rose-600 font-bold hover:text-rose-700 p-1 text-xs">✕</button>
                    </div>

                    <div id="emoji-picker-popup" class="hidden absolute bottom-14 sm:bottom-16 left-2 z-30 bg-white border border-slate-200 p-3 rounded-2xl shadow-xl grid grid-cols-6 gap-2 text-lg">
                        <button type="button" onclick="insertEmoji('👋')" class="hover:scale-125 transition-transform">👋</button>
                        <button type="button" onclick="insertEmoji('👍')" class="hover:scale-125 transition-transform">👍</button>
                        <button type="button" onclick="insertEmoji('😊')" class="hover:scale-125 transition-transform">😊</button>
                        <button type="button" onclick="insertEmoji('🤝')" class="hover:scale-125 transition-transform">🤝</button>
                        <button type="button" onclick="insertEmoji('💼')" class="hover:scale-125 transition-transform">💼</button>
                        <button type="button" onclick="insertEmoji('✅')" class="hover:scale-125 transition-transform">✅</button>
                        <button type="button" onclick="insertEmoji('⚡')" class="hover:scale-125 transition-transform">⚡</button>
                        <button type="button" onclick="insertEmoji('🎉')" class="hover:scale-125 transition-transform">🎉</button>
                        <button type="button" onclick="insertEmoji('📍')" class="hover:scale-125 transition-transform">📍</button>
                        <button type="button" onclick="insertEmoji('❤️')" class="hover:scale-125 transition-transform">❤️</button>
                        <button type="button" onclick="insertEmoji('🙏')" class="hover:scale-125 transition-transform">🙏</button>
                        <button type="button" onclick="insertEmoji('💬')" class="hover:scale-125 transition-transform">💬</button>
                    </div>

                    <!-- Emoji & Attachment Buttons -->
                    <button type="button" onclick="toggleEmojiPicker()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full text-slate-500 hover:text-slate-800 flex items-center justify-center text-base sm:text-lg hover:bg-slate-200/60 transition-all shrink-0" title="Add Emoji">😊</button>
                    <button type="button" onclick="document.getElementById('chat-attachment-input').click()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full text-slate-500 hover:text-slate-800 flex items-center justify-center text-base sm:text-lg hover:bg-slate-200/60 transition-all shrink-0" title="Attach Document or Photo">📎</button>

                    <!-- Text Input Capsule -->
                    <input type="text" id="chat-input" name="message" placeholder="Type a message or add caption for media" class="flex-grow text-xs sm:text-sm px-3.5 sm:px-5 py-2.5 sm:py-3 border-0 rounded-full focus:outline-none font-medium bg-white text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500/20 shadow-2xs transition-all min-w-0">

                    <!-- Green Circular Send Button -->
                    <button type="submit" class="w-9 h-9 sm:w-11 sm:h-11 bg-[#00a884] hover:bg-[#008f70] text-white font-bold rounded-full shadow-md shrink-0 flex items-center justify-center transition-all" title="Send Message">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 transform rotate-90" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
                    </button>
                </form>
            </div>

        @else
            <!-- Placeholder when no thread is selected -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 sm:p-16 text-center space-y-3">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#dcfce7] text-[#16a34a] font-bold flex items-center justify-center text-xl sm:text-2xl mx-auto shadow-2xs">
                    💬
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">AfriCrew Web Messenger</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto font-medium">Select a chat thread from the left menu to view messages, negotiate event rates, and lock shift bookings.</p>
            </div>
        @endif

    </div>

</div>

<!-- Counter-Offer Modal -->
<div id="counter-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-5 sm:p-8 max-w-md w-full shadow-2xl space-y-4 sm:space-y-5 relative mx-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">💼 Propose Counter-Offer Price</h3>
            <button type="button" onclick="closeCounterModal()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('messages.proposePrice') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="proposal_id" id="modal-proposal-id">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Base Rate Quote (KES)</label>
                <input type="number" step="50" name="custom_quote_amount" id="modal-quote-amount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Travel & Accommodation Allowance (KES)</label>
                <input type="number" step="50" name="travel_fee" id="modal-travel-fee" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Negotiated Conditions</label>
                <textarea name="notes" rows="2" placeholder="e.g. Includes full day shift + airport transport fee..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium outline-none focus:border-emerald-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeCounterModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md">Submit Counter-Offer</button>
            </div>
        </form>
    </div>
</div>

<script>
    let lastMsgId = {{ count($messages ?? []) > 0 ? $messages->last()->id : 0 }};

    function filterConversations() {
        const query = document.getElementById('conversation-search').value.toLowerCase();
        const cards = document.querySelectorAll('.conv-card');
        cards.forEach(card => {
            const data = card.getAttribute('data-search') || '';
            if (data.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function toggleEmojiPicker() {
        const popup = document.getElementById('emoji-picker-popup');
        if (popup) popup.classList.toggle('hidden');
    }

    function insertEmoji(char) {
        const input = document.getElementById('chat-input');
        if (input) {
            input.value += char;
            input.focus();
        }
        document.getElementById('emoji-picker-popup')?.classList.add('hidden');
    }

    function previewSelectedAttachment(input) {
        const badge = document.getElementById('attachment-preview-badge');
        const text = document.getElementById('attachment-name-text');
        if (input.files && input.files[0]) {
            text.textContent = input.files[0].name;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            clearSelectedAttachment();
        }
    }

    function clearSelectedAttachment() {
        const input = document.getElementById('chat-attachment-input');
        const badge = document.getElementById('attachment-preview-badge');
        if (input) input.value = '';
        if (badge) {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    function openCounterModal(propId, currentQuote, currentTravel) {
        document.getElementById('modal-proposal-id').value = propId;
        document.getElementById('modal-quote-amount').value = currentQuote || 3500;
        document.getElementById('modal-travel-fee').value = currentTravel || 0;
        document.getElementById('counter-modal').classList.replace('hidden', 'flex');
    }

    function closeCounterModal() {
        document.getElementById('counter-modal').classList.replace('flex', 'hidden');
    }

    function scrollToBottom() {
        const stream = document.getElementById('message-stream');
        if (stream) stream.scrollTop = stream.scrollHeight;
    }

    window.addEventListener('DOMContentLoaded', scrollToBottom);

    // Asynchronous Chat Form Submission Handler
    const chatForm = document.getElementById('chat-send-form');
    if (chatForm) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageInput = document.getElementById('chat-input');
            const fileInput = document.getElementById('chat-attachment-input');

            if (!messageInput.value.trim() && (!fileInput || !fileInput.files || !fileInput.files.length)) {
                return;
            }

            const formData = new FormData(chatForm);

            try {
                const response = await fetch(chatForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                if (data.success && data.message) {
                    const msg = data.message;
                    messageInput.value = '';
                    clearSelectedAttachment();
                    document.getElementById('emoji-picker-popup')?.classList.add('hidden');

                    if (msg.id > lastMsgId) {
                        lastMsgId = msg.id;
                        renderSingleMessage(msg);
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            } catch (err) {
                chatForm.submit();
            }
        });
    }

    function renderSingleMessage(msg) {
        const stream = document.getElementById('message-stream');
        const noMsg = document.getElementById('no-messages-prompt');
        if (noMsg) noMsg.remove();

        const isSelf = msg.sender_id === {{ $user->id }};
        const div = document.createElement('div');
        div.className = `flex flex-col ${isSelf ? 'items-end' : 'items-start'}`;
        div.id = `msg-${msg.id}`;

        let badgeHtml = '';
        if (msg.negotiation_status === 'countered') {
            badgeHtml = `<div class="mb-2 p-2 sm:p-2.5 rounded-xl bg-amber-500/15 border border-amber-400/30 text-amber-950 font-bold text-[10px] sm:text-[11px] flex items-center justify-between gap-2"><span>💼 Counter Price:</span><span class="font-extrabold text-emerald-800">KES ${parseFloat(msg.negotiated_price || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>`;
        } else if (msg.negotiation_status === 'accepted') {
            badgeHtml = `<div class="mb-2 p-2 sm:p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-600/30 text-emerald-950 font-extrabold text-[10px] sm:text-[11px] flex items-center justify-between gap-2"><span>🎉 Booking Price Locked:</span><span class="font-extrabold text-emerald-800">KES ${parseFloat(msg.negotiated_price || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>`;
            setTimeout(() => location.reload(), 1500);
        }

        let attachmentHtml = '';
        const fileUrl = msg.attachment_url || (msg.attachment_path ? `/storage/${msg.attachment_path}` : null);
        if (fileUrl) {
            attachmentHtml = `
                <a href="${fileUrl}" target="_blank" download class="flex items-center gap-2 p-2 sm:p-2.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-900 text-xs font-bold my-1.5 text-decoration-none hover:bg-slate-200 transition-all max-w-full">
                    <span class="text-base shrink-0">📎</span>
                    <span class="truncate flex-1 min-w-0">${msg.attachment_name || 'Download File Attachment'}</span>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-md shrink-0">Download ↓</span>
                </a>
            `;
        }

        const msgText = msg.message ? `<p class="leading-relaxed whitespace-pre-wrap text-slate-900 font-normal text-xs break-words overflow-hidden">${msg.message}</p>` : '';
        const timeStr = 'Just now';
        const checkmarks = isSelf ? '<span class="text-[#00a884] font-bold text-xs ml-1">✓✓</span>' : '';
        const senderHeader = !isSelf ? `<div class="text-[11px] font-bold text-amber-700 mb-1">${msg.sender ? msg.sender.name : 'Participant'}</div>` : '';

        div.innerHTML = `
            <div class="max-w-[88%] sm:max-w-md lg:max-w-xl p-3 sm:p-4 text-xs shadow-2xs relative break-words text-wrap ${isSelf ? 'bg-[#d9fdd3] text-slate-900 rounded-2xl rounded-tr-none border border-emerald-200/60' : 'bg-white text-slate-900 rounded-2xl rounded-tl-none border border-slate-200/60'}">
                ${senderHeader}
                ${badgeHtml}
                ${msgText}
                ${attachmentHtml}
                <div class="text-[10px] text-slate-400 font-normal flex items-center justify-end gap-1 mt-1 sm:mt-1.5">
                    <span>${timeStr}</span>
                    ${checkmarks}
                </div>
            </div>
        `;
        stream.appendChild(div);
        scrollToBottom();
    }

    // Auto-Fetch Stream Polling
    setInterval(async () => {
        try {
            const reqParam = '{{ isset($activeConversation) && !empty($activeConversation["staffing_request_id"]) ? "&request_id=".$activeConversation["staffing_request_id"] : "" }}';
            const res = await fetch(`{{ route('messages.fetch') }}?last_id=${lastMsgId}${reqParam}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.id > lastMsgId) {
                        lastMsgId = msg.id;
                        renderSingleMessage(msg);
                    }
                });
            }
        } catch (e) {}
    }, 3000);
</script>
@endsection
