@extends('admin.layout', ['title' => 'Sub-Admin Management & RBAC Roles'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Sub-Admin Management & Access Control</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Create sub-admin staff accounts and delegate granular role permissions.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-slate-900 text-amber-400 font-black text-xs">
            Sub-Admins: {{ $subadmins->count() }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Create Sub-Admin Form (Col Span 5) -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-slate-900">Create New Sub-Admin Account</h3>
            <form method="POST" action="{{ route('admin.subadmins.store') }}" class="space-y-4 text-xs font-bold">
                @csrf
                <div>
                    <label class="block text-slate-600 mb-1">Full Name</label>
                    <input type="text" name="name" placeholder="e.g. Operations Assistant" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-slate-600 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="ops.assistant@africrew.com" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-slate-600 mb-1">Temporary Password</label>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">Assigned Permissions (RBAC)</label>
                    <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        @foreach($availablePermissions as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer text-slate-700">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded text-amber-500 focus:ring-amber-400">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black rounded-xl text-xs shadow-xs">
                    Create Sub-Admin 🛡️
                </button>
            </form>
        </div>

        <!-- Sub-Admin List (Col Span 7) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-slate-900">Active Administrator Accounts</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-black uppercase text-slate-400 border-b border-slate-200">
                            <th class="pb-3">Admin User</th>
                            <th class="pb-3">Permissions</th>
                            <th class="pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @foreach($subadmins as $adm)
                            <tr>
                                <td class="py-3 font-bold text-slate-900">
                                    <div>{{ $adm->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $adm->email }}</div>
                                </td>
                                <td class="py-3">
                                    @if(empty($adm->permissions))
                                        <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-[10px]">
                                            Super Admin (All)
                                        </span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($adm->permissions as $p)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[9px]">
                                                    {{ $p }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    @if($adm->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.subadmins.destroy', $adm->id) }}" onsubmit="return confirm('Remove sub-admin account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[10px] font-bold">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-emerald-700 font-bold">You (Current)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
