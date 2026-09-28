<!-- FLOATING MULTI-CREW SELECTION BASKET / TEAM DRAWER (PDF PAGE 4 SPECIFICATION NOTE) -->
<div id="multi-crew-basket-drawer" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-8 z-40 hidden max-w-xl w-full bg-slate-900/95 backdrop-blur-xl border border-slate-700 text-white rounded-3xl p-4 shadow-2xl transition-all duration-300 transform translate-y-0">
    <div class="flex items-center justify-between gap-4">
        
        <!-- Left: Selected Crew Thumbnails & Badge -->
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-r from-rose-500 to-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-md">
                <span id="basket-crew-count">0</span>
            </div>
            
            <div class="min-w-0">
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 block leading-tight">SELECTED TEAM</div>
                <div class="flex items-center gap-1.5 pt-0.5" id="basket-avatars-container">
                    <!-- Avatars injected here -->
                </div>
            </div>
        </div>

        <!-- Right: Actions (Hire Team & Clear) -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="proceedToHireTeam()" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-xs shadow-lg shadow-pink-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                <span>HIRE TEAM</span>
                <span>→</span>
            </button>
            <button type="button" onclick="clearTeamBasket()" class="p-2.5 rounded-2xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-all text-xs" title="Clear Selection">
                ✕
            </button>
        </div>

    </div>
</div>

<script>
let teamBasket = JSON.parse(localStorage.getItem('africrew_team_basket') || '[]');

document.addEventListener('DOMContentLoaded', () => {
    renderTeamBasketUI();
});

function addCrewToTeamBasket(crew) {
    if (!crew || !crew.id) return;
    const exists = teamBasket.some(item => item.id === crew.id);
    if (!exists) {
        teamBasket.push({
            id: crew.id,
            name: crew.full_name,
            photo: crew.profile_photo_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
            category: crew.category || 'Event Usher'
        });
        localStorage.setItem('africrew_team_basket', JSON.stringify(teamBasket));
        renderTeamBasketUI();

        Swal.fire({
            icon: 'success',
            title: 'Added to Your Team!',
            text: `${crew.full_name} has been added to your hiring team request.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'bottom-end'
        });
    } else {
        Swal.fire({
            icon: 'info',
            title: 'Already in Team',
            text: `${crew.full_name} is already in your selected team basket.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'bottom-end'
        });
    }
}

function removeCrewFromBasket(id) {
    teamBasket = teamBasket.filter(item => item.id !== id);
    localStorage.setItem('africrew_team_basket', JSON.stringify(teamBasket));
    renderTeamBasketUI();
}

function clearTeamBasket() {
    teamBasket = [];
    localStorage.removeItem('africrew_team_basket');
    renderTeamBasketUI();
}

function renderTeamBasketUI() {
    const drawer = document.getElementById('multi-crew-basket-drawer');
    const countEl = document.getElementById('basket-crew-count');
    const container = document.getElementById('basket-avatars-container');

    if (!drawer || !countEl || !container) return;

    if (teamBasket.length === 0) {
        drawer.classList.add('hidden');
        return;
    }

    drawer.classList.remove('hidden');
    countEl.textContent = teamBasket.length;

    let html = '';
    teamBasket.forEach((member, index) => {
        if (index < 4) {
            html += `<div class="relative group inline-block shrink-0">
                <img src="${member.photo}" alt="${member.name}" class="w-7 h-7 rounded-xl object-cover border border-amber-400 shadow-xs">
                <span class="text-[10px] text-white font-extrabold truncate max-w-[80px] hidden sm:inline ml-1">${member.name.split(' ')[0]}</span>
            </div>`;
        }
    });

    if (teamBasket.length > 4) {
        html += `<span class="text-xs font-bold text-amber-400">+${teamBasket.length - 4} more</span>`;
    }

    container.innerHTML = html;
}

function proceedToHireTeam() {
    if (teamBasket.length === 0) return;
    const ids = teamBasket.map(item => item.id).join(',');
    window.location.href = `/hire-staff?requested_professional_ids=${ids}`;
}
</script>
