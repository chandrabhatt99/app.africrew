@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 sm:px-6">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header Controls -->
        <div class="flex items-center justify-between">
            <a href="javascript:history.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
                ← Back to Portal
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-amber-400 font-bold text-xs hover:bg-slate-800 shadow-sm flex items-center gap-2">
                🖨️ Print / Save PDF Agreement
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Document Sheet Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 sm:p-12 shadow-lg space-y-8 print:shadow-none print:border-none">
            
            <!-- Contract Header -->
            <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-10 w-auto mb-2">
                    <h1 class="text-2xl font-black text-slate-950 uppercase tracking-tight">Digital Event Services Agreement</h1>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Agreement Reference: <strong>{{ $contract->contract_number }}</strong></p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $contract->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        Status: {{ str_replace('_', ' ', strtoupper($contract->status)) }}
                    </span>
                    <div class="text-[11px] text-slate-400 mt-1">Generated Date: {{ $contract->created_at->format('M j, Y') }}</div>
                </div>
            </div>

            <!-- Event & Parties Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200/80 text-xs">
                <div>
                    <h3 class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Event Order Details</h3>
                    <div class="font-extrabold text-slate-900 text-sm">{{ $contract->staffingRequest->event_name }}</div>
                    <div class="text-slate-600 mt-1">📅 Date: {{ $contract->staffingRequest->event_date?->format('D, M j, Y') }}</div>
                    <div class="text-slate-600">📍 Venue: {{ $contract->staffingRequest->location }}</div>
                    <div class="text-amber-700 font-bold mt-1">💰 Total Budget: ${{ number_format($contract->staffingRequest->budget ?: 250, 2) }}</div>
                </div>

                <div>
                    <h3 class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Hiring Client</h3>
                    <div class="font-bold text-slate-900">{{ $contract->staffingRequest->full_name }}</div>
                    <div class="text-slate-600">{{ $contract->staffingRequest->company_name ?: 'Individual Booking' }}</div>
                    <div class="text-slate-600">{{ $contract->staffingRequest->email }}</div>
                </div>
            </div>

            <!-- Agreement Terms -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Terms & Conditions</h3>
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 font-mono text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $contract->terms }}
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-6 border-t border-slate-200">
                
                <!-- Client Signature Box -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase text-slate-500">Client Digital Signature</h4>
                    @if($contract->client_signed_at)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-2">
                            <div class="font-serif italic text-lg font-bold text-emerald-950">{{ $contract->client_signature }}</div>
                            <div class="text-[10px] text-emerald-700 font-semibold">Digitally Signed on {{ $contract->client_signed_at->format('M j, Y g:i A') }}</div>
                            <div class="text-[9px] text-emerald-600 font-mono">IP Timestamp Recorded</div>
                            
                            <div class="pt-2 border-t border-emerald-200">
                                <a href="{{ route('client.dashboard') }}#payment-section" class="w-full inline-block text-center py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all">
                                    Proceed to Escrow Payment 💳 →
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center space-y-3">
                            <p class="text-xs text-slate-400 italic">Pending Client Signature</p>
                            <form method="POST" action="{{ route('contracts.sign', $contract->id) }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="sign_type" value="client">
                                <input type="text" name="signature_name" placeholder="Type full legal name to sign" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold text-xs rounded-xl shadow-xs">
                                    Sign as Client
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Crew Signature Box -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase text-slate-500">Crew / Professional Signature</h4>
                    @if($contract->crew_signed_at)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-1">
                            <div class="font-serif italic text-lg font-bold text-emerald-950">{{ $contract->crew_signature }}</div>
                            <div class="text-[10px] text-emerald-700 font-semibold">Digitally Signed on {{ $contract->crew_signed_at->format('M j, Y g:i A') }}</div>
                            <div class="text-[9px] text-emerald-600 font-mono">IP Timestamp Recorded</div>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center space-y-3">
                            <p class="text-xs text-slate-400 italic">Pending Crew Member Signature</p>
                            <form method="POST" action="{{ route('contracts.sign', $contract->id) }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="sign_type" value="crew">
                                <input type="text" name="signature_name" placeholder="Type full legal name to sign" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                                <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-xs">
                                    Sign as Crew Member
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
