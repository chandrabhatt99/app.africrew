@extends('admin.layout')

@section('content')
<div class="space-y-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>🏷️ Crew Professional Categories</span>
                <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 font-extrabold text-xs">
                    {{ $categories->count() }} Managed
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Admin controls category pricing (Min/Max rates), skills, registration status, and staffing pool.
            </p>
        </div>

        <button onclick="toggleAddCategoryModal()" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-2 shrink-0">
            <span>+ Add New Category</span>
        </button>
    </div>

    <!-- Success Message Alert -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-black">✕</button>
        </div>
    @endif

    <!-- Category Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Total Categories</span>
            <div class="text-2xl font-black text-slate-900">{{ $categories->count() }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Active Registration Categories</span>
            <div class="text-2xl font-black text-emerald-600">{{ $categories->where('is_active', true)->count() }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400">Total Registered Crew</span>
            <div class="text-2xl font-black text-amber-600">{{ array_sum($categoryCounts) }}</div>
        </div>
    </div>

    <!-- Category Table -->
    <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">
                        <th class="py-4 px-6">Category Name</th>
                        <th class="py-4 px-6">Rate Limits (Min - Max)</th>
                        <th class="py-4 px-6 text-center">Category Skills</th>
                        <th class="py-4 px-6 text-center">Registered Crew</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-900 font-black flex items-center justify-center text-base shrink-0">
                                        {{ $category->icon ?: '🏷️' }}
                                    </span>
                                    <div>
                                        <span class="font-extrabold text-slate-900 text-sm block">{{ $category->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $category->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-900 font-extrabold text-xs">
                                    <span>💲</span>
                                    <span>${{ number_format($category->min_rate ?: 0, 2) }}</span>
                                    <span class="text-slate-400">–</span>
                                    <span>${{ number_format($category->max_rate ?: 0, 2) }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('admin.skills.index', ['category_id' => $category->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-900 font-black text-xs border border-amber-500/20 transition-all">
                                    <span>⚡ {{ $category->skills()->count() }} Skills</span>
                                    <span>→</span>
                                </a>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-black text-xs">
                                    {{ $categoryCounts[$category->name] ?? 0 }} Crew
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($category->is_active)
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
                                <button type="button" 
                                        onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->icon ?? '') }}', '{{ addslashes($category->description ?? '') }}', {{ $category->min_rate ?: 0 }}, {{ $category->max_rate ?: 0 }}, {{ $category->is_active ? 1 : 0 }})" 
                                        class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition-all">
                                    ✏️ Edit Rate / Details
                                </button>

                                <a href="{{ route('admin.skills.index', ['category_id' => $category->id]) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition-all inline-block">
                                    ⚡ Manage Skills
                                </a>

                                <!-- Toggle Status Form -->
                                <form method="POST" action="{{ route('admin.categories.toggle', $category) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition-all">
                                        {{ $category->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete category {{ $category->name }}?')" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs transition-all">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-bold">
                                No professional categories added yet. Click "+ Add New Category" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add New Category -->
<div id="modal-add-category" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900">Add Professional Crew Category</h3>
            <button onclick="toggleAddCategoryModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4 text-xs font-bold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. VIP Hostesses" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Icon / Emoji (Optional)</label>
                <input type="text" name="icon" placeholder="👑" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Minimum Rate ($/hr or $/day)</label>
                    <input type="number" step="0.01" min="0" name="min_rate" placeholder="15.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Maximum Rate ($/hr or $/day)</label>
                    <input type="number" step="0.01" min="0" name="max_rate" placeholder="150.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Category Description</label>
                <textarea name="description" rows="3" placeholder="Brief summary of duties and staffing role..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer pt-2">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-amber-500 rounded">
                <span class="text-slate-800">Activate for Crew Registration & Client Booking</span>
            </label>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleAddCategoryModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-md">Create Category ⚡</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Category & Rate Limits -->
<div id="modal-edit-category" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900">Edit Category & Set Rate Limits</h3>
            <button onclick="toggleEditCategoryModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form id="form-edit-category" method="POST" action="" class="space-y-4 text-xs font-bold">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-slate-700 mb-1">Category Name *</label>
                <input type="text" id="edit-cat-name" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Icon / Emoji (Optional)</label>
                <input type="text" id="edit-cat-icon" name="icon" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Minimum Rate ($)</label>
                    <input type="number" step="0.01" min="0" id="edit-cat-min-rate" name="min_rate" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Maximum Rate ($)</label>
                    <input type="number" step="0.01" min="0" id="edit-cat-max-rate" name="max_rate" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Category Description</label>
                <textarea id="edit-cat-description" name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer pt-2">
                <input type="checkbox" id="edit-cat-is-active" name="is_active" value="1" class="w-4 h-4 text-amber-500 rounded">
                <span class="text-slate-800">Activate for Crew Registration & Client Booking</span>
            </label>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleEditCategoryModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-md">Save Rate Limits ⚡</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAddCategoryModal() {
    const modal = document.getElementById('modal-add-category');
    if (modal) modal.classList.toggle('hidden');
}

function toggleEditCategoryModal() {
    const modal = document.getElementById('modal-edit-category');
    if (modal) modal.classList.toggle('hidden');
}

function openEditCategoryModal(id, name, icon, description, minRate, maxRate, isActive) {
    const form = document.getElementById('form-edit-category');
    form.action = `/admin/categories/${id}`;
    document.getElementById('edit-cat-name').value = name;
    document.getElementById('edit-cat-icon').value = icon;
    document.getElementById('edit-cat-description').value = description;
    document.getElementById('edit-cat-min-rate').value = minRate;
    document.getElementById('edit-cat-max-rate').value = maxRate;
    document.getElementById('edit-cat-is-active').checked = Boolean(isActive);

    toggleEditCategoryModal();
}
</script>
@endsection
