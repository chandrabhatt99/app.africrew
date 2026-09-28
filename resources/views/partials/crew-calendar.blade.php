@php
    // Format assignments into date-keyed array for fast lookup
    $bookingEventsByDate = [];
    if (isset($assignments) && $assignments->count() > 0) {
        foreach ($assignments as $job) {
            $req = $job->staffingRequest;
            if ($req && $req->event_date) {
                $dateKey = date('Y-m-d', strtotime($req->event_date));
                if (!isset($bookingEventsByDate[$dateKey])) {
                    $bookingEventsByDate[$dateKey] = [];
                }
                $bookingEventsByDate[$dateKey][] = [
                    'id' => $job->id,
                    'request_id' => $req->id,
                    'event_name' => $req->event_name ?? 'Event Shift Request',
                    'client_name' => $req->full_name ?? 'Client Request',
                    'location' => $req->location ?? 'Venue Location',
                    'category' => $req->category ?? 'Event Usher',
                    'shift_duration' => str_replace('_', ' ', ucfirst($req->shift_duration ?? '1 Day')),
                    'budget' => $req->budget ?? ($professional->one_day_rate ?? 5000),
                    'status' => $job->status ?? 'assigned',
                    'phone' => $req->phone ?? '',
                ];
            }
        }
    }
@endphp

<div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
    
    <!-- Calendar Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-800 text-[10px] font-black uppercase mb-1">
                📅 Crew Availability & Booking Calendar
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Shift Booking Calendar</h3>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">See which days you are booked (with shift duration & event details), and which days you are <strong>Free / Available</strong>.</p>
        </div>

        <!-- Month Navigation Buttons -->
        <div class="flex items-center gap-3 self-start sm:self-center">
            <button type="button" onclick="changeCrewMonth(-1)" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center gap-1 border border-slate-200">
                <span>←</span>
                <span>Prev</span>
            </button>

            <span id="crew-calendar-month-year" class="text-sm font-black text-slate-900 min-w-[130px] text-center uppercase tracking-wider">
                September 2026
            </span>

            <button type="button" onclick="changeCrewMonth(1)" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center gap-1 border border-slate-200">
                <span>Next</span>
                <span>→</span>
            </button>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="flex items-center gap-4 text-xs font-bold flex-wrap bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
        <span class="text-slate-400 uppercase tracking-wider text-[10px] font-black">Legend:</span>
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            <span class="text-slate-700">Free / Available Day</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            <span class="text-slate-700">Booked Shift Day (Click to view shift details)</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
            <span class="text-slate-700">Multiple Bookings</span>
        </div>
    </div>

    <!-- Days of Week Bar -->
    <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center text-[11px] font-black uppercase tracking-wider text-slate-400 py-2 border-b border-slate-100">
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div class="text-amber-600">Sat</div>
        <div class="text-amber-600">Sun</div>
    </div>

    <!-- Calendar Grid Container -->
    <div id="crew-calendar-grid" class="grid grid-cols-7 gap-1.5 sm:gap-2 text-xs">
        <!-- Rendered via JavaScript -->
    </div>

</div>

<!-- Interactive Day Details Modal -->
<div id="crew-day-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span id="crew-modal-date-title" class="text-sm font-black text-slate-900 block">Thursday, 18 September 2026</span>
                <span id="crew-modal-status-badge" class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                    Status: Free
                </span>
            </div>
            <button type="button" onclick="closeCrewDayModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-extrabold text-sm flex items-center justify-center">✕</button>
        </div>

        <div id="crew-modal-body-content" class="space-y-4 max-h-96 overflow-y-auto pr-1">
            <!-- Dynamic Event Content -->
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
            <button type="button" onclick="closeCrewDayModal()" class="px-5 py-2.5 rounded-2xl bg-slate-900 text-amber-400 font-black text-xs hover:bg-slate-800 transition-all">
                Close Inspector
            </button>
        </div>
    </div>
</div>

<script>
    const crewBookingsData = @json($bookingEventsByDate);
    let crewCurrentDate = new Date();

    function renderCrewCalendar() {
        const year = crewCurrentDate.getFullYear();
        const month = crewCurrentDate.getMonth();
        
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById('crew-calendar-month-year').innerText = `${monthNames[month]} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Monday-based indexing: 0 = Mon, 6 = Sun
        let startingDay = firstDayOfMonth.getDay() - 1;
        if (startingDay < 0) startingDay = 6;

        const grid = document.getElementById('crew-calendar-grid');
        grid.innerHTML = '';

        // Previous month filler padding
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startingDay - 1; i >= 0; i--) {
            const padDay = prevMonthLastDay - i;
            const padCell = document.createElement('div');
            padCell.className = 'p-2 sm:p-3 min-h-[75px] sm:min-h-[90px] rounded-2xl bg-slate-50/50 border border-slate-100 text-slate-300 text-xs opacity-40 font-medium';
            padCell.innerHTML = `<span class="block font-bold">${padDay}</span>`;
            grid.appendChild(padCell);
        }

        const nowObj = new Date();
        nowObj.setHours(0,0,0,0);
        const todayStr = nowObj.toISOString().split('T')[0];

        // Render Current Month Days
        for (let day = 1; day <= daysInMonth; day++) {
            const dateObj = new Date(year, month, day);
            dateObj.setHours(0,0,0,0);

            // Format YYYY-MM-DD in local time
            const yyyy = dateObj.getFullYear();
            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dateObj.getDate()).padStart(2, '0');
            const dateKey = `${yyyy}-${mm}-${dd}`;

            const dayEvents = crewBookingsData[dateKey] || [];
            const isToday = (dateKey === todayStr);
            const isPastOrToday = (dateObj <= nowObj);

            const dayCell = document.createElement('div');
            let cellClasses = 'p-2 sm:p-3 min-h-[75px] sm:min-h-[95px] rounded-2xl border transition-all flex flex-col justify-between group ';

            if (isPastOrToday) {
                // Past & Today dates disabled/removed from selection
                cellClasses += 'bg-slate-100/60 border-slate-200/80 text-slate-400 cursor-not-allowed opacity-50 select-none';
            } else if (dayEvents.length > 0) {
                // Booked Day
                if (dayEvents.length === 1) {
                    cellClasses += 'bg-amber-500/10 border-amber-400/80 hover:border-amber-500 hover:shadow-md cursor-pointer';
                } else {
                    cellClasses += 'bg-indigo-50 border-indigo-300 hover:border-indigo-500 hover:shadow-md cursor-pointer';
                }
            } else {
                // Free / Available Day
                cellClasses += 'bg-emerald-50/40 border-emerald-200/80 hover:bg-emerald-50 hover:border-emerald-400 hover:shadow-xs cursor-pointer';
            }

            if (isToday) {
                cellClasses += ' ring-2 ring-slate-400 font-black';
            }

            dayCell.className = cellClasses;
            if (!isPastOrToday) {
                dayCell.onclick = () => openCrewDayModal(dateKey, dayEvents, dateObj);
            }

            let contentHTML = `
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm ${isToday ? 'bg-amber-500 text-slate-950 px-1.5 py-0.5 rounded-md text-[10px]' : ''}">${day}</span>
                    ${isToday ? '<span class="text-[9px] font-black uppercase text-amber-800">TODAY</span>' : ''}
                </div>
            `;

            if (dayEvents.length > 0) {
                const evCount = dayEvents.length;
                const firstEv = dayEvents[0];
                contentHTML += `
                    <div class="mt-1 space-y-1">
                        <span class="px-2 py-0.5 rounded-md ${evCount > 1 ? 'bg-indigo-600 text-white' : 'bg-amber-500 text-slate-950'} text-[9px] font-black block truncate shadow-2xs">
                            ⚡ ${evCount} ${evCount > 1 ? 'Bookings' : 'Booking'}
                        </span>
                        <span class="text-[9.5px] font-extrabold text-slate-900 truncate block leading-tight">
                            ${firstEv.event_name}
                        </span>
                        <span class="text-[9px] text-amber-800 font-bold block truncate">
                            ⏱️ ${firstEv.shift_duration}
                        </span>
                    </div>
                `;
            } else if (isPastOrToday) {
                contentHTML += `
                    <div class="mt-1 text-[9px] font-bold text-slate-400 opacity-60">
                        Unavailable
                    </div>
                `;
            } else {
                contentHTML += `
                    <div class="mt-1 text-[9px] font-extrabold text-emerald-700 opacity-80 group-hover:opacity-100 transition-opacity">
                        ✓ Free / Available
                    </div>
                `;
            }

            dayCell.innerHTML = contentHTML;
            grid.appendChild(dayCell);
        }
    }

    function changeCrewMonth(direction) {
        crewCurrentDate.setMonth(crewCurrentDate.getMonth() + direction);
        renderCrewCalendar();
    }

    function openCrewDayModal(dateKey, dayEvents, dateObj) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateFormatted = dateObj.toLocaleDateString('en-US', options);
        
        document.getElementById('crew-modal-date-title').innerText = dateFormatted;

        const badge = document.getElementById('crew-modal-status-badge');
        const body = document.getElementById('crew-modal-body-content');

        if (dayEvents.length > 0) {
            badge.className = 'inline-block mt-1 px-3 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500 text-slate-950';
            badge.innerText = `⚡ ${dayEvents.length} Confirmed Shift Booking(s)`;

            let html = '';
            dayEvents.forEach((ev, idx) => {
                html += `
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="font-black text-slate-900 text-sm">#${idx + 1} ${ev.event_name}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-black uppercase">
                                ${ev.status.toUpperCase()}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Client Name</span>
                                <span class="font-black text-slate-900">${ev.client_name}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Shift Duration</span>
                                <span class="font-black text-amber-700">⏱️ ${ev.shift_duration}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Venue / Location</span>
                                <span class="font-bold text-slate-800">📍 ${ev.location}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Escrow Pay</span>
                                <span class="font-black text-emerald-600">KES ${Number(ev.budget).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            body.innerHTML = html;
        } else {
            badge.className = 'inline-block mt-1 px-3 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500 text-white';
            badge.innerText = '✓ FREE / AVAILABLE DAY';

            body.innerHTML = `
                <div class="p-6 rounded-2xl bg-emerald-50/60 border border-emerald-200 text-center space-y-2">
                    <span class="text-3xl block">✨</span>
                    <h4 class="text-sm font-black text-emerald-950">You Are 100% Available On This Day</h4>
                    <p class="text-xs text-emerald-800 leading-relaxed max-w-xs mx-auto font-medium">No bookings scheduled for ${dateFormatted}. Event organizers and clients can hire you directly for shifts on this date.</p>
                </div>
            `;
        }

        document.getElementById('crew-day-modal').classList.replace('hidden', 'flex');
    }

    function closeCrewDayModal() {
        document.getElementById('crew-day-modal').classList.replace('flex', 'hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCrewCalendar();
    });
</script>
