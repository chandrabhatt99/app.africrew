@extends('admin.layout')

@section('content')
<div class="space-y-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>⚡ Category-Wise Crew Skills</span>
                <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 font-extrabold text-xs">
                    {{ $skills->count() }} Skills Total
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Admin can add and categorize skills. Crew members choose skills according to their category during registration and profile updates.
            </p>
        </div>

        <button onclick="toggleAddSkillModal()" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-2 shrink-0">
            <span>+ Add New Skill</span>
        </button>
    </div>

    <!-- Category Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100">
        <a href="{{ route('admin.skills.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ empty($selectedCategoryId) || $selectedCategoryId === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            🌟 All Categories ({{ \App\Models\Skill::count() }})
        </a>

        @foreach($categories as $cat)
            <a href="{{ route('admin.skills.index', ['category_id' => $cat->id]) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 {{ (string)$selectedCategoryId === (string)$cat->id ? 'bg-amber-500 text-slate-950 font-black shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                <span>{{ $cat->icon ?: '🏷️' }}</span>
                <span>{{ $cat->name }}</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-extrabold ml-1">
                    {{ $cat->skills_count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Filtered Skills Count</span>
            <div class="text-2xl font-black text-slate-900">{{ $skills->count() }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Active Crew Skills</span>
            <div class="text-2xl font-black text-emerald-600">{{ $skills->where('is_active', true)->count() }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Total System Categories</span>
            <div class="text-2xl font-black text-amber-600">{{ $categories->count() }}</div>
        </div>
    </div>

    <!-- Skills Table -->
    <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">
                        <th class="py-4 px-6">Skill Name</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6">Description</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($skills as $skill)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div>
                                    <span class="font-extrabold text-slate-900 text-sm block">{{ $skill->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $skill->slug }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($skill->category)
                                    <span class="px-3 py-1 rounded-xl bg-amber-500/10 text-amber-900 font-extrabold text-xs inline-flex items-center gap-1.5 border border-amber-500/20">
                                        <span>{{ $skill->category->icon ?: '🏷️' }}</span>
                                        <span>{{ $skill->category->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 font-bold">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 max-w-xs text-slate-600 leading-relaxed">
                                {{ $skill->description ?: 'No description provided' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($skill->is_active)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-900 font-black text-[10px] uppercase">
                                        ✓ Active
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-200 text-slate-600 font-bold text-[10px] uppercase">
                                        Disabled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <!-- Edit Skill Button -->
                                <button type="button" 
                                        onclick="openEditSkillModal({{ $skill->id }}, '{{ addslashes($skill->name) }}', {{ $skill->category_id }}, '{{ addslashes($skill->description ?? '') }}', {{ $skill->is_active ? 1 : 0 }})" 
                                        class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition-all">
                                    ✏️ Edit
                                </button>

                                <!-- Toggle Status Form -->
                                <form method="POST" action="{{ route('admin.skills.toggle', $skill) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition-all">
                                        {{ $skill->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete skill {{ $skill->name }}?')" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs transition-all">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-bold">
                                No skills found for this selection. Click "+ Add New Skill" to add skills category-wise.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add New Skill -->
<div id="modal-add-skill" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900">Add Skill Under Category</h3>
            <button onclick="toggleAddSkillModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.skills.store') }}" class="space-y-4 text-xs font-bold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Target Category *</label>
                <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$selectedCategoryId === (string)$cat->id ? 'selected' : '' }}>
                            {{ $cat->icon ?: '🏷️' }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Skill Name *</label>
                <input type="text" name="name" required placeholder="e.g. VIP Table Delegation / Sound Mixing" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" placeholder="Describe the responsibilities or requirements for this skill..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer pt-2">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-amber-500 rounded">
                <span class="text-slate-800">Activate for Crew Selection</span>
            </label>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleAddSkillModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-md">Add Skill ⚡</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Skill -->
<div id="modal-edit-skill" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900">Edit Skill Details</h3>
            <button onclick="toggleEditSkillModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form id="form-edit-skill" method="POST" action="" class="space-y-4 text-xs font-bold">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-slate-700 mb-1">Target Category *</label>
                <select id="edit-category-id" name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon ?: '🏷️' }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Skill Name *</label>
                <input type="text" id="edit-name" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Description (Optional)</label>
                <textarea id="edit-description" name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer pt-2">
                <input type="checkbox" id="edit-is-active" name="is_active" value="1" class="w-4 h-4 text-amber-500 rounded">
                <span class="text-slate-800">Activate for Crew Selection</span>
            </label>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleEditSkillModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-md">Save Changes ⚡</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAddSkillModal() {
    const modal = document.getElementById('modal-add-skill');
    if (modal) modal.classList.toggle('hidden');
}

function toggleEditSkillModal() {
    const modal = document.getElementById('modal-edit-skill');
    if (modal) modal.classList.toggle('hidden');
}

function openEditSkillModal(id, name, categoryId, description, isActive) {
    const form = document.getElementById('form-edit-skill');
    form.action = `/admin/skills/${id}`;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-category-id').value = categoryId;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-is-active').checked = Boolean(isActive);

    toggleEditSkillModal();
}
</script>
@endsection
